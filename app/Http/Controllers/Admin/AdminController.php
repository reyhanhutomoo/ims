<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use App\Moa;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index() {
        $year = now()->year;

        // Aggregasi per bulan untuk MOA dan IA di tahun berjalan
        $moaCounts = Moa::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->where('jenis_dokumen', 'MOA')
            ->groupBy('month')
            ->pluck('count', 'month');

        $iaCounts = Moa::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->where('jenis_dokumen', 'IA')
            ->groupBy('month')
            ->pluck('count', 'month');

        // Siapkan label dan dataset 12 bulan (Jan-Des) dengan nilai 0 jika tidak ada data
        $labels = [];
        $moaSeries = [];
        $iaSeries = [];
        $totalSeries = [];

        Carbon::setLocale('id');
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create(null, $m, 1)->translatedFormat('M');
            $moa = (int) ($moaCounts[$m] ?? 0);
            $ia = (int) ($iaCounts[$m] ?? 0);
            $moaSeries[] = $moa;
            $iaSeries[] = $ia;
            $totalSeries[] = $moa + $ia;
        }

        return view('admin.index', compact('labels', 'moaSeries', 'iaSeries', 'totalSeries', 'year'));
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
        if (!Hash::check($request->old_password, $user->kata_sandi)) {
            Alert::error('Error', 'Password Salah.');
            return back();
        }
    
        // Update password
        $user->kata_sandi = Hash::make($request->password);
        $user->save();
    
        Alert::success('Success', 'Password berhasil diubah.');
        return back(); // Ganti route dengan yang sesuai
    }
}
