<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use App\Role;
use App\Employee;
use App\Moa;
use App\Campus;
use App\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['nama', 'email', 'kata_sandi'];
        $user = new User();
        $this->assertEquals($fillable, $user->getFillable());
    }

    /** @test */
    public function it_hides_password_and_remember_token()
    {
        $user = User::factory()->create();
        $array = $user->toArray();
        
        $this->assertArrayNotHasKey('kata_sandi', $array);
        $this->assertArrayNotHasKey('token_ingat_saya', $array);
    }

    /** @test */
    public function it_can_have_many_roles()
    {
        $user = User::factory()->create();
        $role1 = Role::factory()->create(['nama' => 'admin']);
        $role2 = Role::factory()->create(['nama' => 'employee']);
        
        $user->peran()->attach([$role1->id, $role2->id]);
        
        $this->assertCount(2, $user->peran);
        $this->assertTrue($user->peran->contains($role1));
        $this->assertTrue($user->peran->contains($role2));
    }

    /** @test */
    public function it_can_check_if_user_has_any_roles()
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['nama' => 'admin']);
        $employeeRole = Role::factory()->create(['nama' => 'employee']);
        
        $user->peran()->attach($adminRole->id);
        
        $this->assertTrue($user->hasAnyRoles(['admin', 'manager']));
        $this->assertFalse($user->hasAnyRoles(['manager', 'supervisor']));
    }

    /** @test */
    public function it_can_check_if_user_has_specific_role()
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['nama' => 'admin']);
        
        $user->peran()->attach($adminRole->id);
        
        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('employee'));
    }

    /** @test */
    public function it_has_one_employee()
    {
        $user = User::factory()->create();
        $campus = Campus::factory()->create();
        $division = Division::factory()->create();

        $employee = Employee::create([
            'pengguna_id' => $user->id,
            'nama' => 'Employee Test',
            'usia' => 20,
            'kampus_id' => $campus->id,
            'divisi_id' => $division->id,
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now(),
        ]);
        
        $this->assertInstanceOf(Employee::class, $user->employee);
        $this->assertEquals($employee->id, $user->employee->id);
    }

    /** @test */
    public function it_can_have_many_moas()
    {
        $user = User::factory()->create();
        $moa1 = Moa::factory()->create(['pengguna_id' => $user->id]);
        $moa2 = Moa::factory()->create(['pengguna_id' => $user->id]);
        
        $this->assertCount(2, $user->moas);
        $this->assertTrue($user->moas->contains($moa1));
        $this->assertTrue($user->moas->contains($moa2));
    }

    /** @test */
    public function it_casts_email_verified_at_to_datetime()
    {
        $user = User::factory()->create([
            'email_terverifikasi_pada' => now()
        ]);
        
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_terverifikasi_pada);
    }
}