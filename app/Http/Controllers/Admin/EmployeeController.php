<?php

namespace App\Http\Controllers\Admin;

use App\Attendance;
use App\Campus;
use App\Division;
use App\Employee;
use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Carbon\Carbon;
use App\Rules\DateRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;
use function Ramsey\Uuid\v1;

class EmployeeController extends Controller
{
    public function index() {
        $employees = Employee::all();
        $campus = Campus::all();
        $division = Division::all();
        return view('admin.employees.index', compact('employees', 'campus', 'division'));
    }
    // public function create() {
    //     $data = [
    //         'departments' => Department::all(),
    //         'desgs' => ['Manajer', 'Asisten Manajer', 'Kepala Divisi', 'Staff']
    //     ];
    //     return view('admin.employees.create')->with($data);
    // }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'age' => 'required',
            'campus_id' => 'required',
            'division_id' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'start_date' => 'required',
            'end_date' => 'required',
        ],[
            'name.required' => 'Nama wajib diisi!',
            'age.required' => 'Umur wajib diisi!',
            'start_date.required' => 'Tanggal Mulai Magang wajib diisi!',
            'end_date.required' => 'Tanggal Selesai Magang wajib diisi!',
            'campus_id.required' => 'Asal Kampus wajib diisi!',
            'division_id.required' => 'Divisi wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'password.required' => 'Password wajib diisi!',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $employeeRole = Role::where('name', 'employee')->first();
        $user->roles()->attach($employeeRole);
        $employeeDetails = [
            'user_id' => $user->id, 
            'name' => $request->name, 
            'age' => $request->age,
            'campus_id' => $request->campus_id, 
            'division_id' => $request->division_id, 
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            // 'photo'  => 'user.png'
        ];
        Employee::create($employeeDetails);
        
        Alert::success('Success', 'Data berhasil ditambahkan!');
        return redirect()->route('admin.employees.index');
    }

    public function update(Request $request, $id){
        $employee = Employee::find($id);
        $this->validate($request, [
            'name' => 'required',
            'age' => 'required',
            'campus_id' => 'required',
            'division_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($employee->user->id),
            ],
        ], [
            'name.required' => 'Nama wajib diisi!',
            'age.required' => 'Umur wajib diisi!',
            'start_date.required' => 'Tanggal Mulai Magang wajib diisi!',
            'end_date.required' => 'Tanggal Selesai Magang wajib diisi!',
            'campus_id.required' => 'Asal Kampus wajib diisi!',
            'division_id.required' => 'Divisi wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
        ]);
        $employee->name = $request->name;
        $employee->age = $request->age;
        $employee->campus_id = $request->campus_id;
        $employee->division_id = $request->division_id;
        $employee->start_date = $request->start_date;
        $employee->end_date = $request->end_date;

        $employee->save();

        if ($employee->user) {
            $employee->user->name = $request->input('name');
            $employee->user->email = $request->input('email');
            if ($request->filled('password')) {
                $employee->user->password = Hash::make($request->input('password'));
            }
            $employee->user->save();
        }
        
        // $user = User::find($id);
        // if (!$user) {
        //     abort(404, 'User not found');
        // }
        // $user->name = $request->name;
        // $user->email = $request->email;
        // if ($request->filled('password')) {
        //     $user->password = Hash::make($request->password);
        // }
        // $user->save();
        
        Alert::success('Success', 'Data berhasil diubah!');
        return redirect()->route('admin.employees.index');
    }


    public function attendance(Request $request) {
        $data = [
            'date' => null,
        ];
        if($request->all()) {
            $date = Carbon::create($request->date);
            $employees = $this->attendanceByDate($date);
            $data['date'] = $date->format('d M, Y');
        } else {
            $employees = $this->attendanceByDate(Carbon::now());
        }
        $data['employees'] = $employees;
        return view('admin.employees.attendance', $data)->with($data);
    }

    public function attendanceByDate($date) {
        $employees = DB::table('employees')
        ->select('employees.id', 'employees.name', 'employees.division_id', 'division.name AS division_name')
        ->leftJoin('division', 'employees.division_id', '=', 'division.id')
        ->get();

        $attendances = Attendance::all()->filter(function($attendance, $key) use ($date){
            return $attendance->created_at->dayOfYear == $date->dayOfYear;
        });
        return $employees->map(function($employee, $key) use($attendances) {
            $attendance = $attendances->where('employee_id', $employee->id)->first();
            $employee->attendanceToday = $attendance;
            return $employee;
        });
    }

    public function updateval(Request $request, $id){
        $employees = Attendance::find($id);
        $employees->entry_status = $request->entry_status;
        $employees->exit_status = $request->exit_status;
        $employees->registered = $request->registered;
        $employees->save();

        Alert::success('Success', 'Data berhasil diubah!');
        return redirect()->route('admin.employees.attendance');
    }

    public function destroy($employee_id) {
        $employee = Employee::findOrFail($employee_id);
        $user = User::findOrFail($employee->user_id);
        // detaches all the roles
        DB::table('leaves')->where('employee_id', '=', $employee_id)->delete();
        DB::table('attendances')->where('employee_id', '=', $employee_id)->delete();
        DB::table('weeklyreports')->where('employee_id', '=', $employee_id)->delete();
        $employee->delete();
        $user->roles()->detach();
        // deletes the users
        $user->delete();
        Alert::success('Success', 'Data berhasil di hapus!');
        return redirect()->route('admin.employees.index');
    }

    public function attendanceDelete($attendance_id) {
        $attendance = Attendance::findOrFail($attendance_id);
        $attendance->delete();
        Alert::success('Success', 'Riwayat Absensi berhasil di hapus!');
        return back();
    }

    public function employeeProfile($employee_id) {
        $employee = Employee::findOrFail($employee_id);
        return view('admin.employees.profile')->with('employee', $employee);
    }
}