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

        $users = User::whereHas('peran', function ($query) {
            $query->where('nama', 'admin');
        })
        ->where('id', '!=', $loggedInAdminId)
        ->get();

        $role = Role::all();

        return view('admin.admin.index', compact('users', 'role'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|confirmed|min:6'
        ],[
            'name.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah dipakai!',
            'password.required' => 'Password wajib diisi!',
        ]);
        $user = User::create([
            'nama' => $request->name,
            'email' => $request->email,
            'kata_sandi' => Hash::make($request->password)
        ]);
        $adminRole = Role::where('nama', 'admin')->first();
        $user->peran()->attach($adminRole);
        
        Alert::success('Success', 'Data berhasil ditambahkan!');
        return redirect()->route('admin.admin.index');
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('pengguna')->ignore($id),
            ],
        ], [
            'name.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
        ]);

        $user = User::find($id);
        $user->nama = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->kata_sandi = Hash::make($request->password);
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
        $users->peran()->detach();
        $users->delete();
        Alert::success('Success', 'Data berhasil dihapus!');
        return redirect()->route('admin.admin.index');
    }
}