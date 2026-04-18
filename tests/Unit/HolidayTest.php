<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Holiday;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HolidayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['nama', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai', 'jenis', 'berulang_tahunan'];
        $holiday = new Holiday();
        $this->assertEquals($fillable, $holiday->getFillable());
    }

    /** @test */
    public function it_can_be_created_with_name()
    {
        $holiday = Holiday::factory()->create(['nama' => 'Tahun Baru']);
        
        $this->assertEquals('Tahun Baru', $holiday->nama);
        $this->assertDatabaseHas('hari_libur', ['nama' => 'Tahun Baru']);
    }

    /** @test */
    public function it_has_start_and_end_dates()
    {
        $startDate = now();
        $endDate = now()->addDays(2);
        
        $holiday = Holiday::factory()->create([
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate
        ]);

        $this->assertNotNull($holiday->tanggal_mulai);
        $this->assertNotNull($holiday->tanggal_selesai);
    }

    /** @test */
    public function it_casts_dates_to_carbon_instances()
    {
        $holiday = Holiday::factory()->create();
        
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $holiday->tanggal_mulai);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $holiday->tanggal_selesai);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $holiday->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $holiday->updated_at);
    }

    /** @test */
    public function it_has_timestamps()
    {
        $holiday = Holiday::factory()->create();

        $this->assertNotNull($holiday->created_at);
        $this->assertNotNull($holiday->updated_at);
    }

    /** @test */
    public function it_can_store_single_day_holiday()
    {
        $date = now();
        $holiday = Holiday::factory()->create([
            'nama' => 'Hari Kemerdekaan',
            'tanggal_mulai' => $date,
            'tanggal_selesai' => $date
        ]);
        
        $this->assertEquals($holiday->tanggal_mulai->toDateString(), $holiday->tanggal_selesai->toDateString());
    }

    /** @test */
    public function it_can_store_multi_day_holiday()
    {
        $startDate = now();
        $endDate = now()->addDays(3);
        
        $holiday = Holiday::factory()->create([
            'nama' => 'Libur Natal',
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate
        ]);
        
        $this->assertTrue($holiday->tanggal_selesai->greaterThan($holiday->tanggal_mulai));
    }

    /** @test */
    public function it_can_create_multiple_holidays()
    {
        $holiday1 = Holiday::factory()->create(['nama' => 'Tahun Baru']);
        $holiday2 = Holiday::factory()->create(['nama' => 'Hari Kemerdekaan']);
        $holiday3 = Holiday::factory()->create(['nama' => 'Natal']);

        $this->assertCount(3, Holiday::all());
    }
}