<?php

namespace Database\Factories;

use App\Leave;
use App\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveFactory extends Factory
{
    protected $model = Leave::class;

    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 7) . ' days');
    
        return [
            'karyawan_id' => Employee::factory(),
            'jenis_cuti' => $this->faker->randomElement(['sakit', 'izin', 'tahunan', 'lainnya']),
            'alasan' => $this->faker->sentence(),
            'deskripsi' => $this->faker->paragraph,
            'bukti' => $this->faker->optional()->imageUrl(),
            'setengah_hari' => $this->faker->boolean(30),
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'status' => $this->faker->randomElement(['menunggu', 'disetujui', 'ditolak']),
            'disetujui_oleh' => null,
            'tanggal_disetujui' => $this->faker->optional()->date(),
            'catatan_persetujuan' => $this->faker->optional()->sentence(),
        ];
    }
}