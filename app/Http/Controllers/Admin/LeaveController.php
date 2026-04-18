<?php

namespace App\Http\Controllers\Admin;

use App\Attendance;
use App\Department;
use App\Employee;
use App\Http\Controllers\Controller;
use App\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class LeaveController extends Controller
{
    public function index() {
        // $leaves = Leave::all();
        // $leaves = $leaves->map(function($leave, $key) {
        //     $employee = Employee::find($leave->employee_id);
        //     $leave->employee = $employee;
        //     return $leave;
        // });
        // return view('admin.leaves.index')->with('leaves', $leaves);
        $leaves = Leave::with('employee')->get();
        return view('admin.leaves.index', compact('leaves'));
    }

    public function update(Request $request, $leave_id){

        $this->validate($request, [
            'status' => 'required|in:approved,declined,pending' // Hanya terima atau tolak yang valid
        ]);
    
        $leave = Leave::find($leave_id);
    
        if (!$leave) {
            Alert::error('Error', 'Cuti tidak ditemukan.');
            return back();
        }
    
        $oldStatus = $leave->status;
        $newStatus = $request->status;
    
        // Update status di tabel Leave
        $leave->status = $newStatus;
        $leave->save();
    
        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            // Jika status baru adalah 'diterima' dan sebelumnya bukan 'diterima'
    
            // Ambil data yang diperlukan dari Leave
            $employee_id = $leave->karyawan_id;
            $registered = $leave->alasan;
    
            // Cek apakah terdapat rentang tanggal (start_date dan end_date diisi)
            if ($leave->tanggal_mulai && $leave->tanggal_selesai) {
                $startDate = Carbon::parse($leave->tanggal_mulai);
                $endDate = Carbon::parse($leave->tanggal_selesai);
    
                // Loop untuk setiap tanggal dalam rentang
                while ($startDate->lte($endDate)) {
                    // Buat entri baru di tabel Attendance
                    $attendance = new Attendance();
                    $attendance->karyawan_id = $employee_id;
                    $attendance->laporan_harian = $registered;
                    $attendance->tanggal = $startDate->toDateString();
                    $attendance->save();
    
                    // Tambahkan 1 hari ke tanggal start untuk iterasi berikutnya
                    $startDate->addDay();
                }
            } else {
                // Jika tidak terdapat rentang tanggal, buat entri tunggal di Attendance
                $attendance = new Attendance();
                $attendance->karyawan_id = $employee_id;
                $attendance->laporan_harian = $registered;
                $attendance->tanggal = Carbon::parse($leave->tanggal_mulai)->toDateString(); // Gunakan tanggal_mulai dari Leave
                $attendance->save();
            }
        }
    
        Alert::success('Success', 'Status Cuti Berhasil Diubah');
        return back();
    }
}