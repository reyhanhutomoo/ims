<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Role;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_created_with_name()
    {
        $role = Role::factory()->create(['nama' => 'admin']);
        
        $this->assertEquals('admin', $role->nama);
        $this->assertDatabaseHas('peran', ['nama' => 'admin']);
    }

    /** @test */
    public function it_can_have_many_users()
    {
        $role = Role::factory()->create(['nama' => 'admin']);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $role->users()->attach([$user1->id, $user2->id]);
        
        $this->assertCount(2, $role->users);
        $this->assertTrue($role->users->contains($user1));
        $this->assertTrue($role->users->contains($user2));
    }

    /** @test */
    public function it_has_timestamps()
    {
        $role = Role::factory()->create();

        $this->assertNotNull($role->created_at);
        $this->assertNotNull($role->updated_at);
    }

    /** @test */
    public function it_has_auto_incrementing_id()
    {
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();

        $this->assertTrue($role2->id > $role1->id);
    }

    /** @test */
    public function it_can_create_different_role_types()
    {
        $adminRole = Role::factory()->create(['nama' => 'admin']);
        $employeeRole = Role::factory()->create(['nama' => 'employee']);
        $managerRole = Role::factory()->create(['nama' => 'manager']);
        
        $this->assertEquals('admin', $adminRole->nama);
        $this->assertEquals('employee', $employeeRole->nama);
        $this->assertEquals('manager', $managerRole->nama);
    }

    /** @test */
    public function users_can_have_multiple_roles()
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['nama' => 'admin']);
        $managerRole = Role::factory()->create(['nama' => 'manager']);
        
        $user->peran()->attach([$adminRole->id, $managerRole->id]);
        
        $this->assertCount(2, $user->peran);
        $this->assertTrue($user->peran->contains($adminRole));
        $this->assertTrue($user->peran->contains($managerRole));
    }
}