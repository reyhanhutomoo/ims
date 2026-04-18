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
use Illuminate\Support\Facades\Schema;
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
            'email' => 'required|email|unique:pengguna,email',
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
            'nama' => $request->name,
            'email' => $request->email,
            'kata_sandi' => Hash::make($request->password)
        ]);
        $employeeRole = Role::where('nama', 'employee')->first();
        $user->peran()->attach($employeeRole);
        $employeeDetails = [
            'pengguna_id' => $user->id,
            'nama' => $request->name,
            'usia' => $request->age,
            'kampus_id' => $request->campus_id,
            'divisi_id' => $request->division_id,
            'tanggal_mulai' => $request->start_date,
            'tanggal_selesai' => $request->end_date,
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
                Rule::unique('pengguna')->ignore($employee->user->id),
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
        $employee->nama = $request->name;
        $employee->usia = $request->age;
        $employee->kampus_id = $request->campus_id;
        $employee->divisi_id = $request->division_id;
        $employee->tanggal_mulai = $request->start_date;
        $employee->tanggal_selesai = $request->end_date;

        $employee->save();

        if ($employee->user) {
            $employee->user->nama = $request->input('name');
            $employee->user->email = $request->input('email');
            if ($request->filled('password')) {
                $employee->user->kata_sandi = Hash::make($request->input('password'));
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
        $employees = DB::table('karyawan')
        ->select('karyawan.id', 'karyawan.nama', 'karyawan.divisi_id', 'divisi.nama AS division_nama')
        ->leftJoin('divisi', 'karyawan.divisi_id', '=', 'divisi.id')
        ->get();

        $attendances = Attendance::all()->filter(function($attendance, $key) use ($date){
            return Carbon::parse($attendance->tanggal)->isSameDay($date);
        });
        return $employees->map(function($employee, $key) use($attendances) {
            $attendance = $attendances->where('karyawan_id', $employee->id)->first();
            $employee->attendanceToday = $attendance;
            return $employee;
        });
    }

    public function updateval(Request $request, $id){
        $employees = Attendance::find($id);
        $employees->status_masuk = $request->entry_status;
        $employees->status_keluar = $request->exit_status;
        $employees->save();

        Alert::success('Success', 'Data berhasil diubah!');
        return redirect()->route('admin.employees.attendance');
    }

    public function destroy($employee_id) {
        $employee = Employee::findOrFail($employee_id);
        $user = User::findOrFail($employee->pengguna_id);
        // detaches all the roles
        DB::table('cuti')->where('karyawan_id', '=', $employee_id)->delete();
        DB::table('kehadiran')->where('karyawan_id', '=', $employee_id)->delete();
        DB::table('laporan_mingguan')->where('karyawan_id', '=', $employee_id)->delete();
        // Hapus data expenses hanya jika tabel tersedia
        if (Schema::hasTable('expenses')) {
            if (Schema::hasColumn('expenses', 'karyawan_id')) {
                DB::table('expenses')->where('karyawan_id', '=', $employee_id)->delete();
            } elseif (Schema::hasColumn('expenses', 'employee_id')) {
                DB::table('expenses')->where('employee_id', '=', $employee_id)->delete();
            }
        }
        $employee->delete();
        $user->peran()->detach();
        // deletes the users
        $user->delete();
        Alert::success('Success', 'Data berhasil dihapus!');
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