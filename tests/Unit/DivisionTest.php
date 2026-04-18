<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Division;
use App\User;
use App\Employee;
use App\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DivisionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['nama', 'deskripsi', 'aktif'];
        $division = new Division();
        $this->assertEquals($fillable, $division->getFillable());
    }

    /** @test */
    public function it_can_be_created_with_name()
    {
        $division = Division::factory()->create(['nama' => 'IT Department']);
        
        $this->assertEquals('IT Department', $division->nama);
        $this->assertDatabaseHas('divisi', ['nama' => 'IT Department']);
    }

    /** @test */
    public function it_can_have_many_employees()
    {
        $division = Division::factory()->create();
        $employee1 = Employee::factory()->create(['divisi_id' => $division->id]);
        $employee2 = Employee::factory()->create(['divisi_id' => $division->id]);

        $this->assertCount(2, $division->employees);
        $this->assertTrue($division->employees->contains($employee1));
        $this->assertTrue($division->employees->contains($employee2));
    }

    /** @test */
    public function it_has_timestamps()
    {
        $division = Division::factory()->create();

        $this->assertNotNull($division->created_at);
        $this->assertNotNull($division->updated_at);
    }

    /** @test */
    public function it_has_auto_incrementing_id()
    {
        $division1 = Division::factory()->create();
        $division2 = Division::factory()->create();

        $this->assertTrue($division2->id > $division1->id);
    }
}