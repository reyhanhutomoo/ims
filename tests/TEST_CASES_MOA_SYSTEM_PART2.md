
# Test Cases for MOA/IA Management System - Part 2

## Continuation of Validation Tests

### 7. Form Validation Tests (Continued)

```php
    /** @test */
    public function admin_signed_file_must_be_pdf()
    {
        Storage::fake('public');

        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        $moa = factory(\App\Moa::class)->create();

        $data = [
            'status' => 'approved',
            'signed_file' => UploadedFile::fake()->create('signed.docx'),
        ];

        $response = $this->put(route('admin.moa.update', $moa), $data);

        $response->assertSessionHasErrors('signed_file');
    }

    /** @test */
    public function admin_signed_file_must_not_exceed_5mb()
    {
        Storage::fake('public');

        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        $moa = factory(\App\Moa::class)->create();

        $data = [
            'status' => 'approved',
            'signed_file' => UploadedFile::fake()->create('signed.pdf', 6000),
        ];

        $response = $this->put(route('admin.moa.update', $moa), $data);

        $response->assertSessionHasErrors('signed_file');
    }
}
```

---

## Security & Authorization Tests

### 8. Authorization Tests

#### Test File: `tests/Feature/MoaAuthorizationTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Moa;
use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoaAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_access_employee_moa_routes()
    {
        $routes = [
            ['get', route('employee.moa.index')],
            ['get', route('employee.moa.create')],
        ];

        foreach ($routes as [$method, $route]) {
            $response = $this->$method($route);
            $response->assertRedirect(route('login'));
        }
    }

    /** @test */
    public function guest_cannot_access_admin_moa_routes()
    {
        $moa = factory(Moa::class)->create();

        $routes = [
            ['get', route('admin.moa.index')],
            ['get', route('admin.moa.show', $moa)],
            ['get', route('admin.moa.edit', $moa)],
        ];

        foreach ($routes as [$method, $route]) {
            $response = $this->$method($route);
            $response->assertRedirect(route('login'));
        }
    }

    /** @test */
    public function employee_cannot_access_admin_routes()
    {
        $employee = factory(User::class)->create();
        $employeeRole = factory(Role::class)->create(['name' => 'employee']);
        $employee->roles()->attach($employeeRole);
        $this->actingAs($employee);

        $moa = factory(Moa::class)->create();

        $response = $this->get(route('admin.moa.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function employee_cannot_view_other_users_moas()
    {
        $employee1 = factory(User::class)->create();
        $employee2 = factory(User::class)->create();

        $moa = factory(Moa::class)->create(['user_id' => $employee2->id]);

        $this->actingAs($employee1);

        $response = $this->get(route('employee.moa.show', $moa));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_all_users_moas()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);

        $employee = factory(User::class)->create();
        $moa = factory(Moa::class)->create(['user_id' => $employee->id]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.moa.show', $moa));
        $response->assertStatus(200);
    }

    /** @test */
    public function public_can_access_public_routes_without_auth()
    {
        $moa = factory(Moa::class)->create();

        $response = $this->get(route('public.moa.index'));
        $response->assertStatus(200);

        $response = $this->get(route('public.moa.show', $moa->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_delete_moas()
    {
        $employee = factory(User::class)->create();
        $moa = factory(Moa::class)->create(['user_id' => $employee->id]);

        $this->actingAs($employee);

        // Employee routes don't include destroy, so this should 404
        $response = $this->delete(route('admin.moa.destroy', $moa));
        $response->assertStatus(403);
    }

    /** @test */
    public function only_admin_can_delete_moas()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);

        $moa = factory(Moa::class)->create();

        $this->actingAs($admin);

        $response = $this->delete(route('admin.moa.destroy', $moa));
        $response->assertRedirect(route('admin.moa.index'));
        $this->assertDatabaseMissing('moas', ['id' => $moa->id]);
    }
}
```

### 9. CSRF Protection Tests

#### Test File: `tests/Feature/MoaCsrfProtectionTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoaCsrfProtectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function post_requests_require_csrf_token()
    {
        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $response = $this->post(route('employee.moa.store'), [
            'title' => 'Test',
            'document_type' => 'MOA',
        ], ['X-CSRF-TOKEN' => '']);

        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function put_requests_require_csrf_token()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        $moa = factory(Moa::class)->create();

        $response = $this->put(route('admin.moa.update', $moa), [
            'status' => 'approved',
        ], ['X-CSRF-TOKEN' => '']);

        $response->assertStatus(419);
    }

    /** @test */
    public function delete_requests_require_csrf_token()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        $moa = factory(Moa::class)->create();

        $response = $this->delete(route('admin.moa.destroy', $moa), [], ['X-CSRF-TOKEN' => '']);

        $response->assertStatus(419);
    }
}
```

---

## File Upload/Download Tests

### 10. File Upload Tests

#### Test File: `tests/Feature/MoaFileUploadTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MoaFileUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function employee_can_upload_pdf_file()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertRedirect(route('employee.moa.index'));
        Storage::disk('public')->assertExists('moa_drafts/' . $file->hashName());
    }

    /** @test */
    public function employee_can_upload_doc_file()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $file = UploadedFile::fake()->create('document.doc', 1024);

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertRedirect(route('employee.moa.index'));
        Storage::disk('public')->assertExists('moa_drafts/' . $file->hashName());
    }

    /** @test */
    public function employee_can_upload_docx_file()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $file = UploadedFile::fake()->create('document.docx', 1024);

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertRedirect(route('employee.moa.index'));
        Storage::disk('public')->assertExists('moa_drafts/' . $file->hashName());
    }

    /** @test */
    public function file_is_stored_in_correct_directory()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $this->post(route('employee.moa.store'), $data);

        $moa = Moa::latest()->first();
        $this->assertStringStartsWith('moa_drafts/', $moa->file_path);
    }

    /** @test */
    public function admin_can_upload_signed_pdf()
    {
        Storage::fake('public');

        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        $moa = factory(Moa::class)->create();
        $signedFile = UploadedFile::fake()->create('signed.pdf', 2048);

        $data = [
            'status' => 'approved',
            'signed_file' => $signedFile,
        ];

        $response = $this->put(route('admin.moa.update', $moa), $data);

        $moa->refresh();
        $this->assertNotNull($moa->signed_file_path);
        Storage::disk('public')->assertExists($moa->signed_file_path);
    }

    /** @test */
    public function signed_file_is_stored_in_correct_directory()
    {
        Storage::fake('public');

        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        $moa = factory(Moa::class)->create();
        $signedFile = UploadedFile::fake()->create('signed.pdf', 2048);

        $data = [
            'status' => 'approved',
            'signed_file' => $signedFile,
        ];

        $this->put(route('admin.moa.update', $moa), $data);

        $moa->refresh();
        $this->assertStringStartsWith('moa_signed/', $moa->signed_file_path);
    }

    /** @test */
    public function uploading_new_signed_file_deletes_old_file()
    {
        Storage::fake('public');

        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        // Create MOA with existing signed file
        $oldFile = UploadedFile::fake()->create('old_signed.pdf', 1024);
        $oldPath = $oldFile->store('moa_signed', 'public');

        $moa = factory(Moa::class)->create(['signed_file_path' => $oldPath]);

        // Upload new signed file
        $newFile = UploadedFile::fake()->create('new_signed.pdf', 2048);

        $data = [
            'status' => 'approved',
            'signed_file' => $newFile,
        ];

        $this->put(route('admin.moa.update', $moa), $data);

        // Old file should be deleted
        Storage::disk('public')->assertMissing($oldPath);

        // New file should exist
        $moa->refresh();
        Storage::disk('public')->assertExists($moa->signed_file_path);
    }

    /** @test */
    public function file_upload_handles_special_characters_in_filename()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $file = UploadedFile::fake()->create('document with spaces & special!chars.pdf', 1024);

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertRedirect(route('employee.moa.index'));
        
        $moa = Moa::latest()->first();
        $this->assertNotNull($moa->file_path);
        Storage::disk('public')->assertExists($moa->file_path);
    }
}
```

### 11. File Download Tests

#### Test File: `tests/Feature/MoaFileDownloadTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MoaFileDownloadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function employee_can_download_their_draft_file()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $file = UploadedFile::fake()->create('draft.pdf', 1024);
        $filePath = $file->store('moa_drafts', 'public');

        $moa = factory(Moa::class)->create([
            'user_id' => $employee->id,
            'file_path' => $filePath,
        ]);

        $this->actingAs($employee);

        $response = $this->get(Storage::url($moa->file_path));
        $response->assertStatus(200);
    }

    /** @test */
    public function employee_can_download_signed_file_when_available()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $signedFile = UploadedFile::fake()->create('signed.pdf', 2048);
        $signedPath = $signedFile->store('moa_signed', 'public');

        $moa = factory(Moa::class)->create([
            'user_id' => $employee->id,
            'signed_file_path' => $signedPath,
        ]);

        $this->actingAs($employee);

        $response = $this->get(Storage::url($moa->signed_file_path));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_download_any_draft_file()
    {
        Storage::fake('public');

        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);

        $employee = factory(User::class)->create();
        $file = UploadedFile::fake()->create('draft.pdf', 1024);
        $filePath = $file->store('moa_drafts', 'public');

        $moa = factory(Moa::class)->create([
            'user_id' => $employee->id,
            'file_path' => $filePath,
        ]);

        $this->actingAs($admin);

        $response = $this->get(Storage::url($moa->file_path));
        $response->assertStatus(200);
    }

    /** @test */
    public function public_can_download_signed_files_of_approved_moas()
    {
        Storage::fake('public');

        $signedFile = UploadedFile::fake()->create('signed.pdf', 2048);
        $signedPath = $signedFile->store('moa_signed', 'public');

        $moa = factory(Moa::class)->create([
            'status' => 'approved',
            'signed_file_path' => $signedPath,
        ]);

        $response = $this->get(Storage::url($moa->signed_file_path));
        $response->assertStatus(200);
    }
}
```

---

## Database Tests

### 12. Migration Tests

#### Test File: `tests/Feature/MoaMigrationTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class MoaMigrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function moas_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('moas'));

        $columns = [
            'id',
            'user_id',
            'tracking_number',
            'title',
            'document_type',
            'file_path',
            'signed_file_path',
            'status',
            'admin_notes',
            'created_at',
            'updated_at',
        ];

        foreach ($columns as $column) {
            $this->assertTrue(
                Schema::hasColumn('moas', $column),
                "Column '{$column}' does not exist in moas table"
            );
        }
    }

    /** @test */
    public function tracking_number_column_is_unique()
    {
        $this->assertTrue(
            Schema::hasColumn('moas', 'tracking_number')
        );

        // Check if unique constraint exists by trying to insert duplicate
        $this->expectException(\Illuminate\Database\QueryException::class);

        \App\Moa::create([
            'user_id' => 1,
            'tracking_number' => 'TEST-001',
            'title' => 'Test 1',
            'document_type' => 'MOA',
            'file_path' => 'test1.pdf',
            'status' => 'pending',
        ]);

        \App\Moa::create([
            'user_id' => 2,
            'tracking_number' => 'TEST-001', // Duplicate
            'title' => 'Test 2',
            'document_type' => 'MOA',
            'file_path' => 'test2.pdf',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function user_id_has_foreign_key_constraint()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        \App\Moa::create([
            'user_id' => 99999, // Non-existent user
            'tracking_number' => 'TEST-001',
            'title' => 'Test',
            'document_type' => 'MOA',
            'file_path' => 'test.pdf',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function deleting_user_cascades_to_moas()
    {
        $user = factory(\App\User::class)->create();
        $moa = factory(\App\Moa::class)->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('moas', ['id' => $moa->id]);

        $user->delete();

        $this->assertDatabaseMissing('moas', ['id' => $moa->id]);
    }

    /** @test */
    public function status_column_has_default_value()
    {
        $user = factory(\App\User::class)->create();

        $moa = \App\Moa::create([
            'user_id' => $user->id,
            'tracking_number' => 'TEST-001',
            'title' => 'Test',
            'document_type' => 'MOA',
            'file_path' => 'test.pdf',
            // status not provided
        ]);

        $this->assertEquals('pending', $moa->status);
    }
}
```

### 13. Database Seeder Tests

#### Test File: `tests/Feature/MoaSeederTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Moa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoaSeederTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function moa_factory_creates_valid_moa()
    {
        $moa = factory(Moa::class)->create();

        $this->assertNotNull($moa->id);
        $this->assertNotNull($moa->user_id);
        $this->assertNotNull($moa->tracking_number);
        $this->assertNotNull($moa->title);
        $this->assertNotNull($moa->document_type);
        $this->assertNotNull($moa->file_path);
        $this->assertNotNull($moa->status);
    }

    /** @test */
    public function moa_factory_can_create_multiple_moas()
    {
        $moas = factory(Moa::class, 10)->create();

        $this->assertCount(10, $moas);
        $this->assertEquals(10, Moa::count());
    }

    /** @test */
    public function moa_factory_respects_custom_attributes()
    {
        $moa = factory(Moa::class)->create([
            'document_type' => 'IA',
            'status' => 'approved',
        ]);

        $this->assertEquals('IA', $moa->document_type);
        $this->assertEquals('approved', $moa->status);
    }
}
```

---

## Frontend/UI Tests

### 14. JavaScript Functionality Tests

#### Test File: `tests/Browser/MoaJavaScriptTest.php`

```php
<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MoaJavaScriptTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function copy_tracking_number_button_works()
    {
        $employee = factory(User::class)->create();
        $moa = factory(Moa::class)->create([
            'user_id' => $employee->id,
            'tracking_number' => 'MOA-20251127-001',
        ]);

        $this->browse(function (Browser $browser) use ($employee, $moa) {
            $browser->loginAs($employee)
                    ->visit(route('employee.moa.show', $moa))
                    ->assertSee($moa->tracking_number)
                    ->click('button[onclick="copyTracking()"]')
                    ->pause(1000)
                    ->assertSee('Berhasil'); // Success message
        });
    }

    /** @test */
    public function file_input_displays_selected_filename()
    {
        $employee = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($employee) {
            $browser->loginAs($employee)
                    ->visit(route('employee.moa.create'))
                    ->attach('document_file', __DIR__.'/../../storage/test_files/test.pdf')
                    ->pause(500)
                    ->assertSee('test.pdf');
        });
    }

    /** @test */
    public function admin_file_upload_shows_file_info()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);

        $moa = factory(Moa::class)->create();

        $this->browse(function (Browser $browser) use ($admin, $moa) {
            $browser->loginAs($admin)
                    ->visit(route('admin.moa.edit', $moa))
                    ->attach('signed_file', __DIR__.'/../../storage/test_files/signed.pdf')
                    ->pause(500)
                    ->assertSee('File dipilih')
                    ->assertSee('signed.pdf');
        });
    }

    /** @test */
    public function admin_file_upload_validates_file_size()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);

        $moa = factory(Moa::class)->create();

        $this->browse(function (Browser $browser) use ($admin, $moa) {
            $browser->loginAs($admin)
                    ->visit(route('admin.moa.edit', $moa))
                    ->attach('signed_file', __DIR__.'/../../storage/test_files/large_file.pdf') // > 5MB
                    ->pause(500)
                    ->assertSee('Ukuran file terlalu besar');
        });
    }

    /** @test */
    public function admin_file_upload_validates_file_type()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);

        $moa = factory(Moa::class)->create();

        $this->browse(function (Browser $browser) use ($admin, $moa) {
            $browser->loginAs($admin)
                    ->visit(route('admin.moa.edit', $moa))
                    ->attach('signed_file', __DIR__.'/../../storage/test_files/document.docx')
                    ->pause(500)
                    ->assertSee('File harus berupa PDF');
        });
    }

    /** @test */
    public function admin_form_shows_confirmation_before_submit()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);

        $moa = factory(Moa::class)->create();

        $this->browse(function (Browser $browser) use ($admin, $moa) {
            $browser->loginAs($admin)
                    ->visit(route('admin.moa.edit', $moa))
                    ->attach('signed_file', __DIR__.'/../../storage/test_files/signed.pdf')
                    ->press('Update Status')
                    ->assertDialogOpened('Anda yakin ingin mengupload file ini?');
        });
    }
}
```

### 15. UI Component Tests

#### Test File: `tests/Browser/MoaUIComponentTest.php`

```php
<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MoaUIComponentTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function employee_index_displays_moa_list_correctly()
    {
        $employee = factory(User::class)->create();
        $moas = factory(Moa::class, 3)->create(['user_id' => $employee->id]);

        $this->browse(function (Browser $browser) use ($employee, $moas) {
            $browser->loginAs($employee)
                    ->visit(route('employee.moa.index'))
                    ->assertSee('Status Pengajuan MOA/IA')
                    ->assertSee($moas[0]->title)
                    ->assertSee($moas[1]->title)
                    ->assertSee($moas[2]->title);
        });
    }

    /** @test */
    public function employee_show_displays_tracking_number_alert()
    {
        $employee = factory(User::class)->create();
        $moa = factory(Moa::class)->create([
            'user_id' => $employee->id,
            'tracking_number' => 'MOA-20251127-001',
        ]);

        $this->browse(function (Browser $browser) use ($employee, $moa) {
            $browser->loginAs($employee)
                    ->visit(route('employee.moa.show', $moa))
                    ->assertSee('Nomor Tracking:')
                    ->assertSee($moa->tracking_number)
                    ->assertPresent('.alert.alert-info');
        });
    }

    /** @test */
    public function employee_show_displays_status_badges_correctly()
    {
        $employee =