<?php

namespace Database\Factories;

use App\Holiday;
use Illuminate\Database\Eloquent\Factories\Factory;

class HolidayFactory extends Factory
{
    protected $model = Holiday::class;

    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('now', '+3 months');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(1, 3) . ' days');
    
        return [
            'nama' => $this->faker->randomElement([
                'Tahun Baru',
                'Hari Kemerdekaan',
                'Natal',
                'Idul Fitri',
                'Libur Nasional'
            ]),
            'deskripsi' => $this->faker->optional()->sentence(),
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'jenis' => $this->faker->randomElement(['nasional', 'cuti_bersama', 'khusus']),
            'berulang_tahunan' => $this->faker->boolean(20),
        ];
    }
}