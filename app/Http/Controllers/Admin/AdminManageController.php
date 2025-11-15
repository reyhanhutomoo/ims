<?php

namespace App\Http\Controllers\Admin;

use App\Employee;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdminManageController extends Controller
{
    public function index(){
        $loggedInAdminId = Auth::id();

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })
        ->where('id', '!=', $loggedInAdminId)
        ->get();

        $role = Role::all();

        return view('admin.admin.index', compact('users', 'role'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6'
        ],[
            'name.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah dipakai!',
            'password.required' => 'Password wajib diisi!',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $adminRole = Role::where('name', 'admin')->first();
        $user->roles()->attach($adminRole);
        
        Alert::success('Success', 'Data berhasil ditambahkan!');
        return redirect()->route('admin.admin.index');
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
        ], [
            'name.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        // if ($user->employee) {
        //     $user->employee->name = $request->input('name');
        //     $user->employee->save();
        // }

        Alert::success('Success', 'Data berhasil diubah!');
        return redirect()->route('admin.admin.index');
    }

    public function destroy($id) {
        $users = User::findOrFail($id);
        DB::table('users')->where('id', '=', $id)->delete();
        $users->roles()->detach();
        $users->delete();
        Alert::success('Success', 'Data berhasil di hapus!');
        return redirect()->route('admin.admin.index');
    }
}