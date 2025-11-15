<?php

namespace App\Http\Controllers\Employee;

use App\Campus;
use App\Department;
use App\Division;
use App\Employee;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeController extends Controller
{
    public function index() {
        $data = [
            'employee' => Auth::user()->employee
        ];
        return view('employee.index')->with($data);
    }

    public function profile() {
        $data = [
            'employee' => Auth::user()->employee
        ];
        $campus = Campus::all();
        $division = Division::all();
        return view('employee.profile', compact('campus', 'division'))->with($data);
    }

    public function updatePhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // sesuaikan dengan kebutuhan
        ],[
            'photo.required' => 'Wajib pilih satu foto!',
            'photo.image' => 'File yang diupload hanya png, jpeg, jpg!',
            'photo.max' => 'Foto maksimal 2MB',
        ]);

        $employee = Employee::findOrFail($id);

        if ($employee->photo) {
            // Hapus foto lama
            Storage::delete('public/photos/' . $employee->photo);
        }
    
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            Storage::putFileAs('public/photos', $file, $fileName);
    
            // Simpan nama file foto baru ke dalam database
            $employee->photo = $fileName;
            $employee->save();
        }
        Alert::success('Success', 'Photo berhasil diubah.');
        return redirect()->route('employee.profile', $employee->id);
    }

    public function update_password(Request $request) {
        $user = Auth::user();

        // Validate input
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password'
        ],[
            'old_password.required' => 'Password lama wajib diisi!',
            'password.required' => 'Password baru wajib diisi!',
            'password.min' => 'Password minimal 8 karakter!',
            'password_confirmation.required' => 'Konfirmasi Password wajib diisi!',
            'password_confirmation.same' => 'Konfirmasi Password harus sama!',
        ]);
    
        // Check if old password matches
        if (!Hash::check($request->old_password, $user->password)) {
            Alert::error('Error', 'Password Salah.');
            return back();
        }
    
        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
    
        Alert::success('Success', 'Password berhasil diubah.');
        return back(); // Ganti route dengan yang sesuai
    }

    // public function profile_edit($employee_id) {
    //     $data = [
    //         'employee' => Employee::findOrFail($employee_id),
    //         'desgs' => ['Manajer', 'Asistent Manajer', 'Projek Manajer', 'Staff']
    //     ];
    //     Gate::authorize('employee-profile-access', intval($employee_id));
    //     return view('employee.profile-edit')->with($data);
    // }

    // public function profile_update(Request $request, $employee_id) {
    //     Gate::authorize('employee-profile-access', intval($employee_id));
    //     $this->validate($request, [
    //         'first_name' => 'required',
    //         'last_name' => 'required',
    //         'photo' => 'image|nullable'
    //     ]);
    //     $employee = Employee::findOrFail($employee_id);
    //     $employee->first_name = $request->first_name;
    //     $employee->last_name = $request->last_name;
    //     $employee->dob = $request->dob;
    //     $employee->sex = $request->gender;
    //     $employee->join_date = $request->join_date;
    //     $employee->desg = $request->desg;
    //     $employee->department_id = $request->department_id;
    //     if ($request->hasFile('photo')) {
    //         // Deleting the old image
    //         if ($employee->photo != 'user.png') {
    //             $old_filepath = public_path(DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'employee_photos'.DIRECTORY_SEPARATOR. $employee->photo);
    //             if(file_exists($old_filepath)) {
    //                 unlink($old_filepath);
    //             }    
    //         }
    //         // GET FILENAME
    //         $filename_ext = $request->file('photo')->getClientOriginalName();
    //         // GET FILENAME WITHOUT EXTENSION
    //         $filename = pathinfo($filename_ext, PATHINFO_FILENAME);
    //         // GET EXTENSION
    //         $ext = $request->file('photo')->getClientOriginalExtension();
    //         //FILNAME TO STORE
    //         $filename_store = $filename.'_'.time().'.'.$ext;
    //         // UPLOAD IMAGE
    //         // $path = $request->file('photo')->storeAs('public'.DIRECTORY_SEPARATOR.'employee_photos', $filename_store);
    //         // add new file name
    //         $image = $request->file('photo');
    //         $image_resize = Image::make($image->getRealPath());              
    //         $image_resize->resize(300, 300);
    //         $image_resize->save(public_path(DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.$filename_store));
    //         $employee->photo = $filename_store;
    //     }
    //     $employee->save();
    //     Alert::success('Success', 'Profil Anda Berhasil diupdate !');
    //     return redirect()->route('employee.profile');
    // }
}