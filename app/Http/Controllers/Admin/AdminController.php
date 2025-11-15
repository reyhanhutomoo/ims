<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class AdminController extends Controller
{
    public function index() {
        return view('admin.index');
    }

    public function reset_password() {
        return view('auth.reset-password');
    }

    public function update_password(Request $request) {
        $user = Auth::user();

        // Validate input
        $request->validate([
            'old_password' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ],[
            'old_password.required' => 'Password lama wajib diisi!',
            'password.required' => 'Password baru wajib diisi!',
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
}