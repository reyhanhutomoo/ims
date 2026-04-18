<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Campus;
use App\User;
use App\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CampusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['nama', 'alamat', 'kota', 'provinsi', 'kode_pos', 'telepon', 'aktif'];
        $campus = new Campus();
        $this->assertEquals($fillable, $campus->getFillable());
    }

    /** @test */
    public function it_can_be_created_with_name()
    {
        $campus = Campus::factory()->create(['nama' => 'Main Campus']);
        
        $this->assertEquals('Main Campus', $campus->nama);
        $this->assertDatabaseHas('kampus', ['nama' => 'Main Campus']);
    }

    /** @test */
    public function it_has_timestamps()
    {
        $campus = Campus::factory()->create();

        $this->assertNotNull($campus->created_at);
        $this->assertNotNull($campus->updated_at);
    }

    /** @test */
    public function it_has_auto_incrementing_id()
    {
        $campus1 = Campus::factory()->create();
        $campus2 = Campus::factory()->create();

        $this->assertTrue($campus2->id > $campus1->id);
    }

    /** @test */
    public function it_can_have_employees()
    {
        $campus = Campus::factory()->create();
        $employee1 = Employee::factory()->create(['kampus_id' => $campus->id]);
        $employee2 = Employee::factory()->create(['kampus_id' => $campus->id]);
 
        $employees = Employee::where('kampus_id', $campus->id)->get();
        
        $this->assertCount(2, $employees);
        $this->assertTrue($employees->contains($employee1));
        $this->assertTrue($employees->contains($employee2));
    }
}