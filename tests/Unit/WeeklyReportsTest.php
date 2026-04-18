<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\WeeklyReports;
use App\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WeeklyReportsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'karyawan_id',
            'judul',
            'deskripsi',
            'file',
            'minggu_ke',
            'tahun',
            'nilai',
            'status',
            'direview_oleh',
            'tanggal_review',
            'catatan_review',
        ];
        $report = new WeeklyReports();
        $this->assertEquals($fillable, $report->getFillable());
    }

    /** @test */
    public function it_belongs_to_an_employee()
    {
        $employee = Employee::factory()->create();
        $report = WeeklyReports::factory()->create(['karyawan_id' => $employee->id]);

        $this->assertInstanceOf(Employee::class, $report->employee);
        $this->assertEquals($employee->id, $report->employee->id);
    }

    /** @test */
    public function it_can_store_title()
    {
        $title = 'Weekly Report - Week 1';
        $report = WeeklyReports::factory()->create(['judul' => $title]);
        $this->assertEquals($title, $report->judul);
    }

    /** @test */
    public function it_can_store_file_path()
    {
        $filePath = 'weekly_reports/report_2024.pdf';
        $report = WeeklyReports::factory()->create(['file' => $filePath]);
        $this->assertEquals($filePath, $report->file);
    }

    /** @test */
    public function it_can_store_value()
    {
        $value = 'completed';
        $report = WeeklyReports::factory()->create(['nilai' => $value]);
        $this->assertEquals($value, $report->nilai);
    }

    /** @test */
    public function it_has_timestamps()
    {
        $report = WeeklyReports::factory()->create();

        $this->assertNotNull($report->created_at);
        $this->assertNotNull($report->updated_at);
    }

    /** @test */
    public function it_has_auto_incrementing_id()
    {
        $report1 = WeeklyReports::factory()->create();
        $report2 = WeeklyReports::factory()->create();

        $this->assertTrue($report2->id > $report1->id);
    }

    /** @test */
    public function employee_can_have_multiple_weekly_reports()
    {
        $employee = Employee::factory()->create();
        $report1 = WeeklyReports::factory()->create(['karyawan_id' => $employee->id]);
        $report2 = WeeklyReports::factory()->create(['karyawan_id' => $employee->id]);
        $report3 = WeeklyReports::factory()->create(['karyawan_id' => $employee->id]);

        $this->assertCount(3, $employee->weeklyreports);
        $this->assertTrue($employee->weeklyreports->contains($report1));
        $this->assertTrue($employee->weeklyreports->contains($report2));
        $this->assertTrue($employee->weeklyreports->contains($report3));
    }
}