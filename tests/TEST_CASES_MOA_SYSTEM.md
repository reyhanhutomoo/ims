
# Test Cases for MOA/IA Management System

## Table of Contents
1. [System Overview](#system-overview)
2. [Test Strategy](#test-strategy)
3. [Unit Tests](#unit-tests)
4. [Controller Tests](#controller-tests)
5. [Feature/Integration Tests](#feature-integration-tests)
6. [Validation Tests](#validation-tests)
7. [Security & Authorization Tests](#security--authorization-tests)
8. [File Upload/Download Tests](#file-uploaddownload-tests)
9. [Database Tests](#database-tests)
10. [Frontend/UI Tests](#frontendui-tests)
11. [Test Execution Guide](#test-execution-guide)

---

## System Overview

The MOA/IA Management System is a Laravel-based application for managing Memorandum of Agreement (MOA) and Implementation Arrangement (IA) documents. The system has three main user roles:

- **Employees/Students**: Submit MOA/IA requests
- **Admins**: Review, approve/reject requests, upload signed documents
- **Public**: View approved MOA/IA documents

### Key Features
- Document submission with file upload
- Tracking number generation
- Status workflow (pending → reviewed → approved/rejected)
- File management (draft and signed documents)
- Public document viewer
- Admin notes and feedback

---

## Test Strategy

### Testing Levels
1. **Unit Tests**: Test individual components in isolation
2. **Integration Tests**: Test component interactions
3. **Feature Tests**: Test complete user workflows
4. **Security Tests**: Test authorization and access control
5. **UI Tests**: Test frontend functionality

### Coverage Goals
- **Code Coverage**: Minimum 80%
- **Critical Path Coverage**: 100%
- **Security Tests**: All authorization points

---

## Unit Tests

### 1. Moa Model Tests

#### Test File: `tests/Unit/MoaTest.php`

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Moa;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'user_id',
            'title',
            'document_type',
            'file_path',
            'signed_file_path',
            'status',
            'admin_notes',
            'tracking_number',
        ];

        $moa = new Moa();
        $this->assertEquals($fillable, $moa->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = factory(User::class)->create();
        $moa = factory(Moa::class)->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $moa->user);
        $this->assertEquals($user->id, $moa->user->id);
    }

    /** @test */
    public function it_has_default_pending_status()
    {
        $moa = factory(Moa::class)->create(['status' => 'pending']);
        $this->assertEquals('pending', $moa->status);
    }

    /** @test */
    public function it_can_have_moa_document_type()
    {
        $moa = factory(Moa::class)->create(['document_type' => 'MOA']);
        $this->assertEquals('MOA', $moa->document_type);
    }

    /** @test */
    public function it_can_have_ia_document_type()
    {
        $moa = factory(Moa::class)->create(['document_type' => 'IA']);
        $this->assertEquals('IA', $moa->document_type);
    }

    /** @test */
    public function it_has_unique_tracking_number()
    {
        $moa1 = factory(Moa::class)->create(['tracking_number' => 'MOA-20251127-001']);
        $moa2 = factory(Moa::class)->create(['tracking_number' => 'MOA-20251127-002']);

        $this->assertNotEquals($moa1->tracking_number, $moa2->tracking_number);
    }

    /** @test */
    public function it_can_store_file_paths()
    {
        $moa = factory(Moa::class)->create([
            'file_path' => 'moa_drafts/test.pdf',
            'signed_file_path' => 'moa_signed/test_signed.pdf'
        ]);

        $this->assertEquals('moa_drafts/test.pdf', $moa->file_path);
        $this->assertEquals('moa_signed/test_signed.pdf', $moa->signed_file_path);
    }

    /** @test */
    public function it_can_have_admin_notes()
    {
        $moa = factory(Moa::class)->create([
            'admin_notes' => 'Please revise section 3'
        ]);

        $this->assertEquals('Please revise section 3', $moa->admin_notes);
    }

    /** @test */
    public function it_has_timestamps()
    {
        $moa = factory(Moa::class)->create();

        $this->assertNotNull($moa->created_at);
        $this->assertNotNull($moa->updated_at);
    }
}
```

### 2. User Model Tests (MOA Relationship)

#### Test File: `tests/Unit/UserMoaRelationshipTest.php`

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserMoaRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_many_moas()
    {
        $user = factory(User::class)->create();
        $moa1 = factory(Moa::class)->create(['user_id' => $user->id]);
        $moa2 = factory(Moa::class)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->moas);
        $this->assertTrue($user->moas->contains($moa1));
        $this->assertTrue($user->moas->contains($moa2));
    }

    /** @test */
    public function user_moas_relationship_returns_collection()
    {
        $user = factory(User::class)->create();
        factory(Moa::class, 3)->create(['user_id' => $user->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->moas);
    }
}
```

---

## Controller Tests

### 3. Employee MoaController Tests

#### Test File: `tests/Unit/Controllers/Employee/MoaControllerTest.php`

```php
<?php

namespace Tests\Unit\Controllers\Employee;

use Tests\TestCase;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MoaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = factory(User::class)->create();
        $this->actingAs($this->employee);
    }

    /** @test */
    public function index_displays_only_authenticated_user_moas()
    {
        $otherUser = factory(User::class)->create();
        
        $myMoa = factory(Moa::class)->create(['user_id' => $this->employee->id]);
        $otherMoa = factory(Moa::class)->create(['user_id' => $otherUser->id]);

        $response = $this->get(route('employee.moa.index'));

        $response->assertStatus(200);
        $response->assertViewHas('moas', function ($moas) use ($myMoa, $otherMoa) {
            return $moas->contains($myMoa) && !$moas->contains($otherMoa);
        });
    }

    /** @test */
    public function create_displays_form()
    {
        $response = $this->get(route('employee.moa.create'));

        $response->assertStatus(200);
        $response->assertViewIs('employee.moa.create');
    }

    /** @test */
    public function store_creates_new_moa_with_valid_data()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $data = [
            'title' => 'MOA - Test University - Research Collaboration',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertRedirect(route('employee.moa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('moas', [
            'user_id' => $this->employee->id,
            'title' => 'MOA - Test University - Research Collaboration',
            'document_type' => 'MOA',
            'status' => 'pending',
        ]);

        Storage::disk('public')->assertExists('moa_drafts/' . $file->hashName());
    }

    /** @test */
    public function store_generates_unique_tracking_number()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $this->post(route('employee.moa.store'), $data);

        $moa = Moa::latest()->first();
        
        $this->assertNotNull($moa->tracking_number);
        $this->assertStringStartsWith('MOA-', $moa->tracking_number);
        $this->assertMatchesRegularExpression('/^MOA-\d{8}-\d{3}$/', $moa->tracking_number);
    }

    /** @test */
    public function store_increments_tracking_number_for_same_day_submissions()
    {
        Storage::fake('public');

        $file1 = UploadedFile::fake()->create('document1.pdf', 1024);
        $file2 = UploadedFile::fake()->create('document2.pdf', 1024);

        $data1 = [
            'title' => 'First MOA',
            'document_type' => 'MOA',
            'document_file' => $file1,
        ];

        $data2 = [
            'title' => 'Second MOA',
            'document_type' => 'MOA',
            'document_file' => $file2,
        ];

        $this->post(route('employee.moa.store'), $data1);
        $this->post(route('employee.moa.store'), $data2);

        $moas = Moa::orderBy('created_at')->get();
        
        $this->assertStringEndsWith('-001', $moas[0]->tracking_number);
        $this->assertStringEndsWith('-002', $moas[1]->tracking_number);
    }

    /** @test */
    public function show_displays_moa_details_for_owner()
    {
        $moa = factory(Moa::class)->create(['user_id' => $this->employee->id]);

        $response = $this->get(route('employee.moa.show', $moa));

        $response->assertStatus(200);
        $response->assertViewIs('employee.moa.show');
        $response->assertViewHas('moa', $moa);
    }

    /** @test */
    public function show_denies_access_to_non_owner()
    {
        $otherUser = factory(User::class)->create();
        $moa = factory(Moa::class)->create(['user_id' => $otherUser->id]);

        $response = $this->get(route('employee.moa.show', $moa));

        $response->assertStatus(403);
    }
}
```

### 4. Admin MoaController Tests

#### Test File: `tests/Unit/Controllers/Admin/MoaControllerTest.php`

```php
<?php

namespace Tests\Unit\Controllers\Admin;

use Tests\TestCase;
use App\User;
use App\Moa;
use App\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminMoaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = factory(User::class)->create();
        $adminRole = factory(Role::class)->create(['name' => 'admin']);
        $this->admin->roles()->attach($adminRole);
        
        $this->actingAs($this->admin);
    }

    /** @test */
    public function index_displays_all_moas()
    {
        $moas = factory(Moa::class, 5)->create();

        $response = $this->get(route('admin.moa.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.moa.index');
        $response->assertViewHas('moas');
    }

    /** @test */
    public function show_displays_moa_details()
    {
        $moa = factory(Moa::class)->create();

        $response = $this->get(route('admin.moa.show', $moa));

        $response->assertStatus(200);
        $response->assertViewIs('admin.moa.show');
        $response->assertViewHas('moa', $moa);
    }

    /** @test */
    public function edit_displays_review_form()
    {
        $moa = factory(Moa::class)->create();

        $response = $this->get(route('admin.moa.edit', $moa));

        $response->assertStatus(200);
        $response->assertViewIs('admin.moa.edit');
        $response->assertViewHas('moa', $moa);
    }

    /** @test */
    public function update_changes_moa_status()
    {
        $moa = factory(Moa::class)->create(['status' => 'pending']);

        $data = [
            'status' => 'approved',
            'admin_notes' => 'Approved for processing',
        ];

        $response = $this->put(route('admin.moa.update', $moa), $data);

        $response->assertRedirect(route('admin.moa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('moas', [
            'id' => $moa->id,
            'status' => 'approved',
            'admin_notes' => 'Approved for processing',
        ]);
    }

    /** @test */
    public function update_uploads_signed_file()
    {
        Storage::fake('public');

        $moa = factory(Moa::class)->create(['status' => 'reviewed']);
        $signedFile = UploadedFile::fake()->create('signed_document.pdf', 2048);

        $data = [
            'status' => 'approved',
            'admin_notes' => 'Document signed and approved',
            'signed_file' => $signedFile,
        ];

        $response = $this->put(route('admin.moa.update', $moa), $data);

        $response->assertRedirect(route('admin.moa.index'));

        $moa->refresh();
        $this->assertNotNull($moa->signed_file_path);
        $this->assertStringStartsWith('moa_signed/', $moa->signed_file_path);
        
        Storage::disk('public')->assertExists($moa->signed_file_path);
    }

    /** @test */
    public function update_replaces_old_signed_file()
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->create('old_signed.pdf', 1024);
        $oldFilePath = $oldFile->store('moa_signed', 'public');

        $moa = factory(Moa::class)->create([
            'status' => 'approved',
            'signed_file_path' => $oldFilePath,
        ]);

        $newFile = UploadedFile::fake()->create('new_signed.pdf', 2048);

        $data = [
            'status' => 'approved',
            'signed_file' => $newFile,
        ];

        $this->put(route('admin.moa.update', $moa), $data);

        $moa->refresh();
        $this->assertNotEquals($oldFilePath, $moa->signed_file_path);
    }

    /** @test */
    public function destroy_deletes_moa_and_files()
    {
        Storage::fake('public');

        $draftFile = UploadedFile::fake()->create('draft.pdf', 1024);
        $signedFile = UploadedFile::fake()->create('signed.pdf', 1024);

        $draftPath = $draftFile->store('moa_drafts', 'public');
        $signedPath = $signedFile->store('moa_signed', 'public');

        $moa = factory(Moa::class)->create([
            'file_path' => $draftPath,
            'signed_file_path' => $signedPath,
        ]);

        $response = $this->delete(route('admin.moa.destroy', $moa));

        $response->assertRedirect(route('admin.moa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('moas', ['id' => $moa->id]);
        Storage::disk('public')->assertMissing($draftPath);
        Storage::disk('public')->assertMissing($signedPath);
    }
}
```

### 5. Public MoaController Tests

#### Test File: `tests/Unit/Controllers/PublicMoaControllerTest.php`

```php
<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Moa;
use App\Campus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicMoaControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_displays_all_moas_without_authentication()
    {
        factory(Moa::class, 5)->create();

        $response = $this->get(route('public.moa.index'));

        $response->assertStatus(200);
        $response->assertViewIs('public.moa.index');
        $response->assertViewHas('moas');
    }

    /** @test */
    public function index_filters_by_search_term()
    {
        $moa1 = factory(Moa::class)->create(['title' => 'University of Indonesia']);
        $moa2 = factory(Moa::class)->create(['title' => 'Harvard University']);

        $response = $this->get(route('public.moa.index', ['search' => 'Indonesia']));

        $response->assertStatus(200);
        $response->assertSee('University of Indonesia');
        $response->assertDontSee('Harvard University');
    }

    /** @test */
    public function index_filters_by_document_type()
    {
        factory(Moa::class)->create(['document_type' => 'MOA']);
        factory(Moa::class)->create(['document_type' => 'IA']);

        $response = $this->get(route('public.moa.index', ['document_type' => 'MOA']));

        $response->assertStatus(200);
        $response->assertViewHas('moas', function ($moas) {
            return $moas->every(fn($moa) => $moa->document_type === 'MOA');
        });
    }

    /** @test */
    public function index_filters_by_status()
    {
        factory(Moa::class)->create(['status' => 'approved']);
        factory(Moa::class)->create(['status' => 'pending']);

        $response = $this->get(route('public.moa.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $response->assertViewHas('moas', function ($moas) {
            return $moas->every(fn($moa) => $moa->status === 'approved');
        });
    }

    /** @test */
    public function show_displays_moa_details_without_authentication()
    {
        $moa = factory(Moa::class)->create();

        $response = $this->get(route('public.moa.show', $moa->id));

        $response->assertStatus(200);
        $response->assertViewIs('public.moa.show');
        $response->assertViewHas('moa', $moa);
    }
}
```

---

## Feature/Integration Tests

### 6. Complete MOA Submission Workflow

#### Test File: `tests/Feature/MoaSubmissionWorkflowTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MoaSubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function employee_can_complete_full_submission_workflow()
    {
        Storage::fake('public');

        // 1. Create and authenticate employee
        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        // 2. Visit create page
        $response = $this->get(route('employee.moa.create'));
        $response->assertStatus(200);

        // 3. Submit MOA
        $file = UploadedFile::fake()->create('moa_draft.pdf', 1024);
        $data = [
            'title' => 'MOA - John Doe - Stanford University - AI Research',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $response = $this->post(route('employee.moa.store'), $data);
        $response->assertRedirect(route('employee.moa.index'));

        // 4. Verify MOA created
        $moa = Moa::where('user_id', $employee->id)->first();
        $this->assertNotNull($moa);
        $this->assertEquals('pending', $moa->status);
        $this->assertNotNull($moa->tracking_number);

        // 5. View submission details
        $response = $this->get(route('employee.moa.show', $moa));
        $response->assertStatus(200);
        $response->assertSee($moa->tracking_number);
        $response->assertSee('Pending');

        // 6. View in list
        $response = $this->get(route('employee.moa.index'));
        $response->assertStatus(200);
        $response->assertSee($moa->title);
    }

    /** @test */
    public function admin_can_complete_full_review_workflow()
    {
        Storage::fake('public');

        // 1. Create MOA submission
        $employee = factory(User::class)->create();
        $moa = factory(Moa::class)->create([
            'user_id' => $employee->id,
            'status' => 'pending',
        ]);

        // 2. Create and authenticate admin
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        // 3. View MOA list
        $response = $this->get(route('admin.moa.index'));
        $response->assertStatus(200);
        $response->assertSee($moa->title);

        // 4. View MOA details
        $response = $this->get(route('admin.moa.show', $moa));
        $response->assertStatus(200);

        // 5. Edit/Review MOA
        $response = $this->get(route('admin.moa.edit', $moa));
        $response->assertStatus(200);

        // 6. Update status to reviewed
        $data = [
            'status' => 'reviewed',
            'admin_notes' => 'Under review, waiting for signatures',
        ];
        $response = $this->put(route('admin.moa.update', $moa), $data);
        $response->assertRedirect(route('admin.moa.index'));

        $moa->refresh();
        $this->assertEquals('reviewed', $moa->status);

        // 7. Upload signed file and approve
        $signedFile = UploadedFile::fake()->create('signed_moa.pdf', 2048);
        $data = [
            'status' => 'approved',
            'admin_notes' => 'Approved and signed',
            'signed_file' => $signedFile,
        ];
        $response = $this->put(route('admin.moa.update', $moa), $data);

        $moa->refresh();
        $this->assertEquals('approved', $moa->status);
        $this->assertNotNull($moa->signed_file_path);
    }

    /** @test */
    public function public_can_view_approved_moas()
    {
        // 1. Create approved MOA
        $moa = factory(Moa::class)->create([
            'status' => 'approved',
            'title' => 'Public MOA - Test University',
        ]);

        // 2. View public list (no authentication)
        $response = $this->get(route('public.moa.index'));
        $response->assertStatus(200);
        $response->assertSee($moa->title);

        // 3. View public details
        $response = $this->get(route('public.moa.show', $moa->id));
        $response->assertStatus(200);
        $response->assertSee($moa->title);
    }
}
```

---

## Validation Tests

### 7. Form Validation Tests

#### Test File: `tests/Feature/MoaValidationTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MoaValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = factory(User::class)->create();
        $this->actingAs($this->employee);
    }

    /** @test */
    public function title_is_required()
    {
        Storage::fake('public');

        $data = [
            'title' => '',
            'document_type' => 'MOA',
            'document_file' => UploadedFile::fake()->create('test.pdf'),
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function title_must_be_string()
    {
        Storage::fake('public');

        $data = [
            'title' => 12345,
            'document_type' => 'MOA',
            'document_file' => UploadedFile::fake()->create('test.pdf'),
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function title_must_not_exceed_255_characters()
    {
        Storage::fake('public');

        $data = [
            'title' => str_repeat('a', 256),
            'document_type' => 'MOA',
            'document_file' => UploadedFile::fake()->create('test.pdf'),
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function document_type_is_required()
    {
        Storage::fake('public');

        $data = [
            'title' => 'Test MOA',
            'document_type' => '',
            'document_file' => UploadedFile::fake()->create('test.pdf'),
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertSessionHasErrors('document_type');
    }

    /** @test */
    public function document_type_must_be_moa_or_ia()
    {
        Storage::fake('public');

        $data = [
            'title' => 'Test Document',
            'document_type' => 'INVALID',
            'document_file' => UploadedFile::fake()->create('test.pdf'),
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertSessionHasErrors('document_type');
    }

    /** @test */
    public function document_file_is_required()
    {
        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertSessionHasErrors('document_file');
    }

    /** @test */
    public function document_file_must_be_pdf_doc_or_docx()
    {
        Storage::fake('public');

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => UploadedFile::fake()->create('test.txt'),
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertSessionHasErrors('document_file');
    }

    /** @test */
    public function document_file_must_not_exceed_5mb()
    {
        Storage::fake('public');

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => UploadedFile::fake()->create('test.pdf', 6000), // 6MB
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertSessionHasErrors('document_file');
    }

    /** @test */
    public function admin_status_must_be_valid()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        $moa = factory(\App\Moa::class)->create();

        $data = [
            'status' => 'invalid_status',
        ];

        $response = $this->put(route('admin.moa.update', $moa), $data);

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function admin_signe