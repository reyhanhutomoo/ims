<?php

namespace Database\Factories;

use App\Attendance;
use App\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'karyawan_id' => Employee::factory(),
            'tanggal' => $this->faker->date(),
            'waktu_masuk' => $this->faker->time('H:i:s'),
            'waktu_keluar' => $this->faker->optional()->time('H:i:s'),
            'ip_masuk' => $this->faker->ipv4,
            'lokasi_masuk' => $this->faker->address,
            'ip_keluar' => $this->faker->optional()->ipv4,
            'lokasi_keluar' => $this->faker->optional()->address,
            'status_masuk' => $this->faker->randomElement(['tepat_waktu', 'terlambat']),
            'status_keluar' => $this->faker->optional()->randomElement(['tepat_waktu', 'lebih_awal']),
            'laporan_harian' => $this->faker->optional()->paragraph,
        ];
    }
}