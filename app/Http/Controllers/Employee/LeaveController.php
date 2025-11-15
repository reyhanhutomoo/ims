<?php

namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;

use App\Leave;
use App\Rules\DateRange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class LeaveController extends Controller
{
    public function index() {
        $employee = Auth::user()->employee;
        $data = [
            'employee' => $employee,
            'leaves' => $employee->leave
        ];
        return view('employee.leaves.index')->with($data);
    }
    public function create() {
        $employee = Auth::user()->employee;
        $data = [
            'employee' => $employee
        ];

        return view('employee.leaves.create')->with($data);
    }

    public function store(Request $request, $employee_id) {
            $reason = $request->input('reason');

            if ($reason === "Sakit") {
            if ($request->has('date')) {
                $chosenDate = Carbon::parse($request->input('date'));
                $currentDate = Carbon::now(); // Mendapatkan tanggal sekarang
                $daysDifference = $currentDate->diffInDays($chosenDate); // Menghitung selisih hari
            
                if ($daysDifference > 3) {
                    // Jika lebih dari 3 hari, tampilkan pesan info dan kembalikan ke halaman sebelumnya
                    Alert::info('Info', 'Izin Sakit hanya bisa diajukan maksimal H+3 sejak tanggal ketidakhadiran');
                    return back();
                }
            } else {
                Alert::error('Error', 'Tanggal tidak valid.');
                return back();
            }
            if ($request->has('date_range')) {
                [$start, $end] = explode(' - ', $request->input('date_range'));
                $chosenDate = Carbon::parse($start);

                $currentDate = Carbon::now(); // Mendapatkan tanggal sekarang
                $daysDifference = $currentDate->diffInDays($chosenDate); // Menghitung selisih hari
            
                if ($daysDifference > 3) {
                    // Jika lebih dari 3 hari, tampilkan pesan info dan kembalikan ke halaman sebelumnya
                    Alert::info('Info', 'Izin Sakit hanya bisa diajukan maksimal H+3 sejak tanggal ketidakhadiran');
                    return back();
                }
            }else {
                Alert::error('Error', 'Tanggal tidak valid.');
                return back();
            }
        }
        
        if ($reason === "Cuti") {
            $currentDate = Carbon::now();
            $chosenDate = null;

            if ($request->has('date')) {
                $chosenDate = Carbon::parse($request->input('date'));
            }else {
                Alert::error('Error', 'Tanggal tidak valid.');
                return back();
            }
            
            if ($request->has('date_range')) {
                [$start, $end] = explode(' - ', $request->input('date_range'));
                $chosenDate = Carbon::parse($start);
            }else {
                Alert::error('Error', 'Tanggal tidak valid.');
                return back();
            }
        }
        // // Periksa apakah chosenDate sudah ditentukan dan bukan null
        // if ($chosenDate && ($chosenDate->isSameDay($currentDate) || $chosenDate->isBefore($currentDate))) {
        //     // Tampilkan pesan info jika chosenDate adalah tanggal sekarang atau sebelumnya
        //     Alert::info('Info', 'Izin Cuti harus diajukan minimal 1 hari sebelum tanggal cuti dan tanggal cuti harus lebih dari hari ini.');
        //     return back();
        // }

        $data = [
            'employee' => Auth::user()->employee
        ];
        if($request->input('multiple-days') == 'yes') {
            $this->validate($request, [
                'reason' => 'required',
                'description' => 'nullable',
                'evidence' => 'required|file|mimes:pdf|max:2048',
                'date_range' => new DateRange
            ],[
                'evidence.required' => 'Wjib upload bukti!',
                'evidence.mimes' => 'Wjib format PDF!',
                'evidence.max' => 'File maksimal 2MB!',
            ]);
        } else {
            $this->validate($request, [
                'reason' => 'required',
                'description' => 'nullable',
                'evidence' => 'required|file|mimes:pdf|max:2048',
            ],[
                'evidence.required' => 'Wjib upload bukti!',
                'evidence.mimes' => 'Wjib format PDF!',
                'evidence.max' => 'File maksimal 2MB!',
            ]);
        }
        
        $values = [
            'employee_id' => $employee_id,
            'reason' => $request->input('reason'),
            'description' => $request->input('description'),
            'half_day' => $request->input('half-day')
        ];
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $currentDate = Carbon::now()->format('Y-m-d');
            $originalFileName = $file->getClientOriginalName();
            $fileName = "{$currentDate}_{$originalFileName}.pdf";
            Storage::putFileAs('public/evidence_file', $file, $fileName);
            $values['evidence'] = $fileName;
        }
        if($request->input('multiple-days') == 'yes') {
            [$start, $end] = explode(' - ', $request->input('date_range'));
            $values['start_date'] = Carbon::parse($start);
            $values['end_date'] = Carbon::parse($end);
        } else {
            $values['start_date'] = Carbon::parse($request->input('date'));
        }
        Leave::create($values);
        Alert::success('Success', 'Pengajuan Cuti Anda berhasil, tunggu persetujuan atasan.');
        return redirect()->route('employee.leaves.create')->with($data); 
    }

    public function edit($leave_id) {
        $leave = Leave::findOrFail($leave_id);
        Gate::authorize('employee-leaves-access', $leave);
        return view('employee.leaves.edit')->with('leave', $leave);
    }

    public function update(Request $request, $leave_id) {
        $leave = Leave::findOrFail($leave_id);
        Gate::authorize('employee-leaves-access', $leave);
        if($request->input('multiple-days') == 'yes') {
            $this->validate($request, [
                'reason' => 'required',
                'description' => 'required',
                'date_range' => new DateRange
            ]);
        } else {
            $this->validate($request, [
                'reason' => 'required',
                'description' => 'required'
            ]);
        }

        $leave->reason = $request->reason;
        $leave->description = $request->description;
        $leave->half_day = $request->input('half-day');
        if($request->input('multiple-days') == 'yes') {
            [$start, $end] = explode(' - ', $request->input('date_range'));
            $start = Carbon::parse($start);
            $end = Carbon::parse($end);
            $leave->start_date = $start;
            $leave->end_date = $end;
        } else {
            $leave->start_date = Carbon::parse($request->input('date'));
        }
        $leave->save();

        Alert::success('Success', 'Update Pengajuan Cuti Anda berhasil');
        return redirect()->route('employee.leaves.index');
    }

    public function destroy($id) {
        $leave = Leave::findOrFail($id);
        if ($leave->evidence) {
            Storage::delete('public/evidence_file/' . $leave->evidence);
        }
        Gate::authorize('employee-leaves-access', $leave);
        $leave->delete();

        Alert::success('Success', 'Pengajuan Cuti Anda berhasil dihapus');
        return redirect()->route('employee.leaves.index');
    }
}