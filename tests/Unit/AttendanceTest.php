<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Attendance;
use App\Employee;
use App\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['karyawan_id', 'tanggal', 'waktu_masuk', 'waktu_keluar', 'ip_masuk', 'lokasi_masuk', 'ip_keluar', 'lokasi_keluar', 'status_masuk', 'status_keluar', 'laporan_harian'];
        $attendance = new Attendance();
        $this->assertEquals($fillable, $attendance->getFillable());
    }

    /** @test */
    public function it_belongs_to_an_employee()
    {
        $employee = Employee::factory()->create();
        $attendance = Attendance::factory()->create(['karyawan_id' => $employee->id]);

        $this->assertInstanceOf(Employee::class, $attendance->employee);
        $this->assertEquals($employee->id, $attendance->employee->id);
    }

    /** @test */
    public function it_can_store_entry_ip()
    {
        $attendance = Attendance::factory()->create(['ip_masuk' => '192.168.1.1']);
        $this->assertEquals('192.168.1.1', $attendance->ip_masuk);
    }

    /** @test */
    public function it_can_store_entry_location()
    {
        $location = 'Office Building A';
        $attendance = Attendance::factory()->create(['lokasi_masuk' => $location]);
        $this->assertEquals($location, $attendance->lokasi_masuk);
    }

    /** @test */
    public function it_can_store_entry_status()
    {
        $attendance = Attendance::factory()->create(['status_masuk' => 'tepat_waktu']);
        $this->assertEquals('tepat_waktu', $attendance->status_masuk);
    }

    /** @test */
    public function it_can_store_exit_status()
    {
        $attendance = Attendance::factory()->create(['status_keluar' => 'tepat_waktu']);
        $this->assertEquals('tepat_waktu', $attendance->status_keluar);
    }

    /** @test */
    public function it_can_store_daily_report()
    {
        $report = 'Completed all assigned tasks for today';
        $attendance = Attendance::factory()->create(['laporan_harian' => $report]);
        $this->assertEquals($report, $attendance->laporan_harian);
    }

    /** @test */
    public function it_has_timestamps()
    {
        $attendance = Attendance::factory()->create();

        $this->assertNotNull($attendance->created_at);
        $this->assertNotNull($attendance->updated_at);
    }

    /** @test */
    public function it_can_have_null_exit_status()
    {
        $attendance = Attendance::factory()->create(['status_keluar' => null]);
        $this->assertNull($attendance->status_keluar);
    }

    /** @test */
    public function it_can_have_null_daily_report()
    {
        $attendance = Attendance::factory()->create(['laporan_harian' => null]);
        $this->assertNull($attendance->laporan_harian);
    }
}