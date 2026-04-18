<?php

namespace Database\Factories;

use App\Moa;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoaFactory extends Factory
{
    protected $model = Moa::class;

    public function definition()
    {
        $documentType = $this->faker->randomElement(['MOA', 'IA']);
        $date = date('Ymd');
        $count = str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);
        
        return [
            'pengguna_id' => User::factory(),
            'nomor_pelacakan' => $documentType . '-' . $date . '-' . $count,
            'judul' => $this->faker->sentence(6),
            'jenis_dokumen' => $documentType,
            'path_berkas' => 'moa_drafts/' . $this->faker->uuid . '.pdf',
            'path_berkas_ttd' => $this->faker->boolean(30) ? 'moa_signed/' . $this->faker->uuid . '.pdf' : null,
            'status' => $this->faker->randomElement(['menunggu', 'direview', 'disetujui', 'ditolak']),
            'catatan_admin' => $this->faker->boolean(40) ? $this->faker->paragraph : null,
        ];
    }
}