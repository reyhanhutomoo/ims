<?php

namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;

use App\Attendance;
use App\Holiday;
use App\Rules\DateRange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\Location;
use RealRashid\SweetAlert\Facades\Alert;

class AttendanceController extends Controller
{
    public function getIp(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }
    }

    public function location(Request $request)
    {
        try {
            $lat = $request->lat;
            $lon = $request->lon;

            if (!is_numeric($lat) || !is_numeric($lon)) {
                return response()->json(['message' => 'Koordinat tidak valid'], 422);
            }

            // Use Nominatim reverse with JSON format for a simpler response structure
            $response = Http::withHeaders([
                'User-Agent' => 'IMS Attendance/1.0 (+https://example.com)'
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lon,
                'zoom' => 16,
                'addressdetails' => 1,
            ]);

            if (!$response->ok()) {
                return response()->json(['message' => 'Gagal mengambil lokasi: HTTP ' . $response->status()], $response->status());
            }

            $json = $response->json();
            // Prefer display_name; fallback to a composed address
            $display = $json['display_name'] ?? null;
            if (!$display) {
                $addr = $json['address'] ?? [];
                $parts = [
                    $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['hamlet'] ?? null,
                    $addr['state'] ?? null,
                    $addr['country'] ?? null,
                ];
                $display = implode(', ', array_filter($parts)) ?: 'Lokasi tidak diketahui';
            }

            return response($display, 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // public function location(Request $request)
    // {
    //     $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
    //         'format' => 'geojson',
    //         'lat' => $request->lat,
    //         'lon' => $request->lon,
    //     ]);

    //     $properties = $response->json()['features'][0]['properties'];

    //     $address = [
    //         'city' => $properties['address']['city'] ?? '',
    //         'state' => $properties['address']['state'] ?? '',
    //         'country' => $properties['address']['country'] ?? '',
    //     ];

    //     return implode(', ', array_filter($address)); 
    // }

    // Opens view for attendance register form
    public function create() {
        $employee = Auth::user()->employee;
        $data = [
            'employee' => $employee,
            'attendance' => null,
            'registered_attendance' => null
        ];
        $last_attendance = $employee->attendance->last();
        if($last_attendance) {
            if($last_attendance->created_at->format('d') == Carbon::now()->format('d')){
                $data['attendance'] = $last_attendance;
                // Tanda sudah registrasi: belum melakukan keluar (waktu_keluar masih null)
                if(is_null($last_attendance->waktu_keluar)) {
                    $data['registered_attendance'] = 'yes';
                }
            }
        }
        return view('employee.attendance.create')->with($data);   
    }

    // Simpan data record absensi
    public function store(Request $request, $employee_id) {
        $entry_ip = $request->ip();
        $entry_location = $request->entry_location;

        // Penentuan status tanpa validasi IP/lokasi (fitur ip_lokasi dihapus)
        $entry_status = 'Valid';

        $attendance = new Attendance([
            'karyawan_id' => $employee_id,
            'tanggal' => Carbon::now()->toDateString(),
            'waktu_masuk' => Carbon::now()->toTimeString(),
            'ip_masuk' => $entry_ip,
            'lokasi_masuk' => $entry_location,
            'status_masuk' => $entry_status,
        ]);
        $attendance->save();
        if(date('h')<=9) {
            Alert::success('Success', 'Absensi Anda berhasil direkam sistem');
        } else {
            Alert::success('Success', 'Absensi Anda berhasil direkam sistem dengan catatan keterlambatan');
        }
        return redirect()->route('employee.attendance.create')->with('employee', Auth::user()->employee);
    }

    // Hapus data record absensi
    public function update(Request $request, $attendance_id) {

        if ($request->has('daily_report') && trim($request->daily_report) === '') {
            return redirect()->back()->withErrors(['daily_report' => 'Laporan Harian harus diisi ketika melakukan Absen Keluar.']);
        }

        $attendance = Attendance::findOrFail($attendance_id);
        $exit_ip = $request->ip();
        $exit_location = $request->exit_location;

        // Penentuan status tanpa validasi IP/lokasi (fitur ip_lokasi dihapus)
        $exit_status = 'Valid';
        
        // Status 'registered' dihapus pada skema baru; gunakan status_masuk/keluar pada tabel kehadiran.

        $attendance->ip_keluar = $exit_ip;
        $attendance->lokasi_keluar = $exit_location;
        $attendance->status_keluar = $exit_status;
        $attendance->laporan_harian = $request->daily_report;
        $attendance->waktu_keluar = Carbon::now()->toTimeString();
        $attendance->save();
        Alert::success('Success', 'Absensi Anda berhasil diakhiri');
        return redirect()->route('employee.attendance.create')->with('employee', Auth::user()->employee);
    }

    public function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }

    public function index() {
        $employee = Auth::user()->employee;
        $attendances = $employee->attendance;
        $filter = false;
        if(request()->all()) {
            $this->validate(request(), ['date_range' => new DateRange]);
            if($attendances) {
                [$start, $end] = explode(' - ', request()->input('date_range'));
                $start = Carbon::parse($start);
                $end = Carbon::parse($end)->addDay();
                $filtered_attendances = $this->attendanceOfRange($attendances, $start, $end);
                $leaves = $this->leavesOfRange($employee->leave, $start, $end);
                $holidays = $this->holidaysOfRange(Holiday::all(), $start, $end);
                $attendances = collect();
                $count = $filtered_attendances->count();
                if($count) {
                    $first_day = $filtered_attendances->first()->created_at->dayOfYear;
                    $attendances = $this->get_filtered_attendances($start, $end, $filtered_attendances, $first_day, $count, $leaves, $holidays);
                }
                else{
                    while($start->lessThan($end)) {
                        $attendances->add($this->attendanceIfNotPresent($start, $leaves, $holidays));
                        $start->addDay();
                    }
                }
                $filter = true;
            }   
        }
        if ($attendances)
            $attendances = $attendances->reverse()->values();
        $data = [
            'employee' => $employee,
            'attendances' => $attendances,
            'filter' => $filter
        ];
        return view('employee.attendance.index')->with($data);
    }

    public function get_filtered_attendances($start, $end, $filtered_attendances, $first_day, $count, $leaves, $holidays) {
        $found_start = false;
        $key = 1;
        $attendances = collect();
        while($start->lessThan($end)) {
            if (!$found_start) {
                if($first_day == $start->dayOfYear()) {
                    $found_start = true;
                    $attendances->add($filtered_attendances->first());
                } else {
                    $attendances->add($this->attendanceIfNotPresent($start, $leaves, $holidays));
                }
            } else {
                // iterating over the 2nd to .. n dates
                if ($key < $count) {
                    if($start->dayOfYear() != $filtered_attendances->get($key)->created_at->dayOfYear) {
                        $attendances->add($this->attendanceIfNotPresent($start, $leaves, $holidays));
                    }
                    else {
                        $attendances->add($filtered_attendances->get($key));
                        $key++;
                    }
                }
                else {
                    $attendances->add($this->attendanceIfNotPresent($start, $leaves, $holidays));
                }
            }
            $start->addDay();
        }
        return $attendances;
    }

    public function checkLeave($leaves, $date) {
        if ($leaves->count() != 0) {
            $leaves = $leaves->filter(function($leave, $key) use ($date) {
                // checks if the end date has a value
                if($leave->tanggal_selesai) {
                    // if it does then checks if the $date falls between the leave range
                    $condition1 = intval($date->dayOfYear) >= intval($leave->tanggal_mulai->dayOfYear);
                    $condition2 = intval($date->dayOfYear) <= intval($leave->tanggal_selesai->dayOfYear);
                    return $condition1 && $condition2;
                }
                // else checks if this day is a leave
                return $date->dayOfYear == $leave->tanggal_mulai->dayOfYear;
            });
        }
        return $leaves->count();
    }

    public function checkHoliday($holidays, $date) {
        if ($holidays->count() != 0) {
            $holidays = $holidays->filter(function($holiday, $key) use ($date) {
                // checks if the end date has a value
                if($holiday->tanggal_selesai) {
                    // if it does then checks if the $date falls between the holiday range
                    $condition1 = intval($date->dayOfYear) >= intval($holiday->tanggal_mulai->dayOfYear);
                    $condition2 = intval($date->dayOfYear) <= intval($holiday->tanggal_selesai->dayOfYear);
                    return $condition1 && $condition2;
                }
                // else checks if this day is a holiday
                return $date->dayOfYear == $holiday->tanggal_mulai->dayOfYear;
            });
        }
        return $holidays->count();
    }

    public function attendanceIfNotPresent($start, $leaves, $holidays) {
        $attendance = new Attendance();
        $attendance->created_at = $start;
        if($this->checkHoliday($holidays, $start)) {
            $attendance->registered = 'hari libur';
        } elseif($start->dayOfWeek == 0) {
            $attendance->registered = 'minggu';
        } elseif($this->checkLeave($leaves, $start)) {
            $attendance->registered = 'cuti';
        } else {
            $attendance->registered = 'absen';
        }
        return $attendance;
    }

    public function leavesOfRange($leaves, $start, $end) {
        return $leaves->filter(function($leave, $key) use ($start, $end) {
            // checks if the start date is between the range
            $condition1 = (intval($start->dayOfYear) <= intval($leave->tanggal_mulai->dayOfYear)) && (intval($end->dayOfYear) >= intval($leave->tanggal_mulai->dayOfYear));
            // checks if the end date is between the range
            $condition2 = false;
            if($leave->tanggal_selesai)
                $condition2 = (intval($start->dayOfYear) <= intval($leave->tanggal_selesai->dayOfYear)) && (intval($end->dayOfYear) >= intval($leave->tanggal_selesai->dayOfYear));
            // checks if the leave status is approved
            $condition3 = $leave->status == 'diterima';
            // combining all the conditions
            return  ($condition1 || $condition2) && $condition3;
        });
    }

    public function attendanceOfRange($attendances, $start, $end) {
        return $attendances->filter(function($attendance, $key) use ($start, $end) {
                    $date = Carbon::parse($attendance->created_at);
                    if ((intval($date->dayOfYear) >= intval($start->dayOfYear)) && (intval($date->dayOfYear) <= intval($end->dayOfYear)))
                        return true;
                })->values();
    }

    public function holidaysOfRange($holidays, $start, $end) {
        return $holidays->filter(function($holiday, $key) use ($start, $end) {
            // checks if the start date is between the range
            $condition1 = (intval($start->dayOfYear) <= intval($holiday->tanggal_mulai->dayOfYear)) && (intval($end->dayOfYear) >= intval($holiday->tanggal_mulai->dayOfYear));
            // checks if the end date is between the range
            $condition2 = false;
            if($holiday->tanggal_selesai)
                $condition2 = (intval($start->dayOfYear) <= intval($holiday->tanggal_selesai->dayOfYear)) && (intval($end->dayOfYear) >= intval($holiday->tanggal_selesai->dayOfYear));
            return  ($condition1 || $condition2);
        });
    }

}