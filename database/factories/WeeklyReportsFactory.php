<?php

namespace Database\Factories;

use App\WeeklyReports;
use App\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeeklyReportsFactory extends Factory
{
    protected $model = WeeklyReports::class;

    public function definition()
    {
        return [
            'karyawan_id' => Employee::factory(),
            'judul' => $this->faker->sentence(5),
            'deskripsi' => $this->faker->paragraph,
            'file' => 'laporan_mingguan/' . $this->faker->uuid . '.pdf',
            'minggu_ke' => $this->faker->numberBetween(1, 52),
            'tahun' => (int) $this->faker->year,
            'nilai' => $this->faker->optional()->numberBetween(60, 100),
            'status' => $this->faker->randomElement(['draft', 'disubmit', 'direview', 'disetujui', 'ditolak']),
            'direview_oleh' => null,
            'tanggal_review' => $this->faker->optional()->date(),
            'catatan_review' => $this->faker->optional()->sentence(),
        ];
    }
}