<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Employee;
use App\User;
use App\Campus;
use App\Division;
use App\Attendance;
use App\Leave;
use App\WeeklyReports;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['pengguna_id', 'nama', 'usia', 'kampus_id', 'divisi_id', 'tanggal_mulai', 'tanggal_selesai', 'nomor_telepon', 'alamat', 'foto', 'status'];
        $employee = new Employee();
        $this->assertEquals($fillable, $employee->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['pengguna_id' => $user->id]);

        $this->assertInstanceOf(User::class, $employee->user);
        $this->assertEquals($user->id, $employee->user->id);
    }

    /** @test */
    public function it_belongs_to_a_campus()
    {
        $campus = Campus::factory()->create();
        $employee = Employee::factory()->create(['kampus_id' => $campus->id]);

        $this->assertInstanceOf(Campus::class, $employee->campus);
        $this->assertEquals($campus->id, $employee->campus->id);
    }

    /** @test */
    public function it_belongs_to_a_division()
    {
        $division = Division::factory()->create();
        $employee = Employee::factory()->create(['divisi_id' => $division->id]);

        $this->assertInstanceOf(Division::class, $employee->division);
        $this->assertEquals($division->id, $employee->division->id);
    }

    /** @test */
    public function it_can_have_many_attendances()
    {
        $employee = Employee::factory()->create();
        $attendance1 = Attendance::factory()->create(['karyawan_id' => $employee->id]);
        $attendance2 = Attendance::factory()->create(['karyawan_id' => $employee->id]);

        $this->assertCount(2, $employee->attendance);
        $this->assertTrue($employee->attendance->contains($attendance1));
        $this->assertTrue($employee->attendance->contains($attendance2));
    }

    /** @test */
    public function it_can_have_many_leaves()
    {
        $employee = Employee::factory()->create();
        $leave1 = Leave::factory()->create(['karyawan_id' => $employee->id]);
        $leave2 = Leave::factory()->create(['karyawan_id' => $employee->id]);

        $this->assertCount(2, $employee->leave);
        $this->assertTrue($employee->leave->contains($leave1));
        $this->assertTrue($employee->leave->contains($leave2));
    }

    /** @test */
    public function it_can_have_many_weekly_reports()
    {
        $employee = Employee::factory()->create();
        $report1 = WeeklyReports::factory()->create(['karyawan_id' => $employee->id]);
        $report2 = WeeklyReports::factory()->create(['karyawan_id' => $employee->id]);

        $this->assertCount(2, $employee->weeklyreports);
        $this->assertTrue($employee->weeklyreports->contains($report1));
        $this->assertTrue($employee->weeklyreports->contains($report2));
    }

    /** @test */
    public function it_has_timestamps()
    {
        $employee = Employee::factory()->create();

        $this->assertNotNull($employee->created_at);
        $this->assertNotNull($employee->updated_at);
    }

    /** @test */
    public function it_can_store_age()
    {
        $employee = Employee::factory()->create(['usia' => 25]);
        $this->assertEquals(25, $employee->usia);
    }

    /** @test */
    public function it_can_store_start_and_end_dates()
    {
        $startDate = '2024-01-01';
        $endDate = '2024-12-31';
        
        $employee = Employee::factory()->create([
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate
        ]);
        
        $this->assertEquals($startDate, $employee->tanggal_mulai->toDateString());
        $this->assertEquals($endDate, $employee->tanggal_selesai->toDateString());
    }
}