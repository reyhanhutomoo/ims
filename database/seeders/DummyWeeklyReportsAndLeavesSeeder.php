<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\WeeklyReports;
use App\Leave;
use App\User;
use App\Employee;
use Carbon\Carbon;

class DummyWeeklyReportsAndLeavesSeeder extends Seeder
{
    /**
     * Seed dummy data for Weekly Reports and Leaves across multiple years
     */
    public function run(): void
    {
        // Reviewer/Approver fallback (null if not available)
        $approver = User::first();
        $approverId = $approver?->id;

        // Gunakan satu karyawan saja untuk menghindari konflik unique pada factory relasi (Division, dsb)
        $employee = Employee::first();
        if (!$employee) {
            $employee = Employee::factory()->create();
        }
        $employeeId = $employee->id;

        foreach (range(2022, 2026) as $year) {
            // Weekly Reports: 12 per tahun (1 per bulan)
            for ($month = 1; $month <= 12; $month++) {
                $day = rand(1, 25);
                $created = Carbon::create($year, $month, $day, rand(8, 18), rand(0, 59));

                $statusPool = ['draft', 'disubmit', 'direview', 'disetujui', 'ditolak'];
                $status = $statusPool[array_rand($statusPool)];

                $report = WeeklyReports::create([
                    'karyawan_id'   => $employeeId,
                    'judul'         => 'Laporan Mingguan ' . $month . '/' . $year,
                    'deskripsi'     => 'Ringkasan pekerjaan minggu ke ' . (int) ceil($created->weekOfYear) . ' tahun ' . $year,
                    'file'          => 'laporan_mingguan/report_' . $year . '_' . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . '.pdf',
                    'minggu_ke'     => (int) ceil($created->weekOfYear),
                    'tahun'         => $year,
                    'nilai'         => rand(60, 100),
                    'status'        => $status,
                    'direview_oleh' => $approverId,
                    'tanggal_review'=> rand(0,1) ? $created->copy()->addDays(rand(1,10)) : null,
                    'catatan_review'=> rand(0,1) ? 'Catatan peninjauan mingguan' : null,
                    'created_at'    => $created,
                    'updated_at'    => $created,
                ]);
            }

            // Leaves: 8 per tahun
            for ($j = 1; $j <= 8; $j++) {
                $start = Carbon::create($year, rand(1, 12), rand(1, 25));
                $end   = (clone $start)->addDays(rand(1, 7));

                $statusPool = ['menunggu', 'disetujui', 'ditolak'];
                $status = $statusPool[array_rand($statusPool)];
                $approvedAt = $status === 'disetujui' ? (clone $start)->addDays(rand(0, 2)) : null;
                $approvedBy = $status === 'disetujui' ? $approverId : null;

                Leave::create([
                    'karyawan_id'        => $employeeId,
                    'jenis_cuti'         => ['sakit', 'izin', 'tahunan', 'lainnya'][array_rand(['sakit', 'izin', 'tahunan', 'lainnya'])],
                    'alasan'             => 'Keperluan pribadi ' . $j,
                    'deskripsi'          => 'Pengajuan cuti untuk keperluan pribadi/ kesehatan.',
                    'bukti'              => null,
                    'setengah_hari'      => (bool) rand(0,1),
                    'tanggal_mulai'      => $start->toDateString(),
                    'tanggal_selesai'    => $end->toDateString(),
                    'status'             => $status,
                    'disetujui_oleh'     => $approvedBy,
                    'tanggal_disetujui'  => $approvedAt,
                    'catatan_persetujuan'=> $approvedAt ? 'Disetujui oleh atasan.' : null,
                    'created_at'         => $start,
                    'updated_at'         => $start,
                ]);
            }
        }
    }
}
