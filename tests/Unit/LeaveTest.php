<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Leave;
use App\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeaveTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'karyawan_id',
            'jenis_cuti',
            'alasan',
            'deskripsi',
            'bukti',
            'setengah_hari',
            'tanggal_mulai',
            'tanggal_selesai',
            'status',
            'disetujui_oleh',
            'tanggal_disetujui',
            'catatan_persetujuan',
        ];
        $leave = new Leave();
        $this->assertEquals($fillable, $leave->getFillable());
    }

    /** @test */
    public function it_belongs_to_an_employee()
    {
        $employee = Employee::factory()->create();
        $leave = Leave::factory()->create(['karyawan_id' => $employee->id]);

        $this->assertInstanceOf(Employee::class, $leave->employee);
        $this->assertEquals($employee->id, $leave->employee->id);
    }

    /** @test */
    public function it_can_store_reason()
    {
        $leave = Leave::factory()->create(['jenis_cuti' => 'sakit']);
        $this->assertEquals('sakit', $leave->jenis_cuti);
    }

    /** @test */
    public function it_can_store_description()
    {
        $description = 'Need to take leave for medical appointment';
        $leave = Leave::factory()->create(['deskripsi' => $description]);
        $this->assertEquals($description, $leave->deskripsi);
    }

    /** @test */
    public function it_can_store_evidence()
    {
        $evidence = 'medical_certificate.pdf';
        $leave = Leave::factory()->create(['bukti' => $evidence]);
        $this->assertEquals($evidence, $leave->bukti);
    }

    /** @test */
    public function it_can_be_half_day()
    {
        $leave = Leave::factory()->create(['setengah_hari' => true]);
        $this->assertTrue($leave->setengah_hari);
    }

    /** @test */
    public function it_can_be_full_day()
    {
        $leave = Leave::factory()->create(['setengah_hari' => false]);
        $this->assertFalse($leave->setengah_hari);
    }

    /** @test */
    public function it_has_start_and_end_dates()
    {
        $startDate = now();
        $endDate = now()->addDays(3);
        
        $leave = Leave::factory()->create([
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate
        ]);

        $this->assertNotNull($leave->tanggal_mulai);
        $this->assertNotNull($leave->tanggal_selesai);
    }

    /** @test */
    public function it_has_timestamps()
    {
        $leave = Leave::factory()->create();

        $this->assertNotNull($leave->created_at);
        $this->assertNotNull($leave->updated_at);
    }

    /** @test */
    public function it_casts_dates_to_carbon_instances()
    {
        $leave = Leave::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $leave->tanggal_mulai);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $leave->tanggal_selesai);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $leave->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $leave->updated_at);
    }

    /** @test */
    public function it_can_have_null_evidence()
    {
        $leave = Leave::factory()->create(['bukti' => null]);
        $this->assertNull($leave->bukti);
    }
}