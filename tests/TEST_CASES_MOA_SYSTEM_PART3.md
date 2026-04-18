# Test Cases for MOA/IA Management System - Part 3

## Frontend/UI Tests (Continued)

### 15. UI Component Tests (Continued)

```php
    /** @test */
    public function employee_show_displays_status_badges_correctly()
    {
        $employee = factory(User::class)->create();
        
        $statuses = [
            'pending' => 'badge-warning',
            'reviewed' => 'badge-info',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
        ];

        foreach ($statuses as $status => $badgeClass) {
            $moa = factory(Moa::class)->create([
                'user_id' => $employee->id,
                'status' => $status,
            ]);

            $this->browse(function (Browser $browser) use ($employee, $moa, $badgeClass) {
                $browser->loginAs($employee)
                        ->visit(route('employee.moa.show', $moa))
                        ->assertPresent(".badge.{$badgeClass}");
            });
        }
    }

    /** @test */
    public function employee_show_displays_whatsapp_contact_button()
    {
        $employee = factory(User::class)->create();
        $moa = factory(Moa::class)->create(['user_id' => $employee->id]);

        $this->browse(function (Browser $browser) use ($employee, $moa) {
            $browser->loginAs($employee)
                    ->visit(route('employee.moa.show', $moa))
                    ->assertSee('Hubungi Admin')
                    ->assertPresent('a[href*="wa.me"]');
        });
    }

    /** @test */
    public function admin_edit_shows_current_file_info()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);

        $moa = factory(Moa::class)->create([
            'signed_file_path' => 'moa_signed/existing_file.pdf',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $moa) {
            $browser->loginAs($admin)
                    ->visit(route('admin.moa.edit', $moa))
                    ->assertSee('File saat ini')
                    ->assertSee('existing_file.pdf');
        });
    }

    /** @test */
    public function public_index_displays_search_and_filters()
    {
        factory(Moa::class, 5)->create();

        $this->browse(function (Browser $browser) {
            $browser->visit(route('public.moa.index'))
                    ->assertPresent('input[name="search"]')
                    ->assertPresent('select[name="document_type"]')
                    ->assertPresent('select[name="status"]');
        });
    }

    /** @test */
    public function public_index_search_filters_results()
    {
        $moa1 = factory(Moa::class)->create(['title' => 'University of Indonesia']);
        $moa2 = factory(Moa::class)->create(['title' => 'Harvard University']);

        $this->browse(function (Browser $browser) use ($moa1, $moa2) {
            $browser->visit(route('public.moa.index'))
                    ->type('search', 'Indonesia')
                    ->press('Search')
                    ->assertSee('University of Indonesia')
                    ->assertDontSee('Harvard University');
        });
    }
}
```

---

## Edge Cases and Error Handling Tests

### 16. Edge Case Tests

#### Test File: `tests/Feature/MoaEdgeCaseTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MoaEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tracking_number_handles_multiple_submissions_same_second()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $file1 = UploadedFile::fake()->create('doc1.pdf', 1024);
        $file2 = UploadedFile::fake()->create('doc2.pdf', 1024);

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

        // Submit both quickly
        $this->post(route('employee.moa.store'), $data1);
        $this->post(route('employee.moa.store'), $data2);

        $moas = Moa::orderBy('created_at')->get();
        
        // Both should have unique tracking numbers
        $this->assertNotEquals($moas[0]->tracking_number, $moas[1]->tracking_number);
    }

    /** @test */
    public function handles_very_long_title()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $longTitle = str_repeat('A', 255); // Max length

        $data = [
            'title' => $longTitle,
            'document_type' => 'MOA',
            'document_file' => UploadedFile::fake()->create('test.pdf', 1024),
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertRedirect(route('employee.moa.index'));
        $this->assertDatabaseHas('moas', ['title' => $longTitle]);
    }

    /** @test */
    public function handles_special_characters_in_title()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $specialTitle = "MOA - Test & Co. <University> 'Research' \"Project\" @2024";

        $data = [
            'title' => $specialTitle,
            'document_type' => 'MOA',
            'document_file' => UploadedFile::fake()->create('test.pdf', 1024),
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        $response->assertRedirect(route('employee.moa.index'));
        $this->assertDatabaseHas('moas', ['title' => $specialTitle]);
    }

    /** @test */
    public function handles_empty_admin_notes()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        $moa = factory(Moa::class)->create();

        $data = [
            'status' => 'approved',
            'admin_notes' => null,
        ];

        $response = $this->put(route('admin.moa.update', $moa), $data);

        $response->assertRedirect(route('admin.moa.index'));
        $moa->refresh();
        $this->assertNull($moa->admin_notes);
    }

    /** @test */
    public function handles_very_long_admin_notes()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);
        $this->actingAs($admin);

        $moa = factory(Moa::class)->create();

        $longNotes = str_repeat('This is a very long note. ', 100);

        $data = [
            'status' => 'rejected',
            'admin_notes' => $longNotes,
        ];

        $response = $this->put(route('admin.moa.update', $moa), $data);

        $response->assertRedirect(route('admin.moa.index'));
        $moa->refresh();
        $this->assertEquals($longNotes, $moa->admin_notes);
    }

    /** @test */
    public function handles_file_with_no_extension()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $file = UploadedFile::fake()->create('document', 1024);

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        // Should fail validation
        $response->assertSessionHasErrors('document_file');
    }

    /** @test */
    public function handles_zero_byte_file()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        $file = UploadedFile::fake()->create('document.pdf', 0);

        $data = [
            'title' => 'Test MOA',
            'document_type' => 'MOA',
            'document_file' => $file,
        ];

        $response = $this->post(route('employee.moa.store'), $data);

        // Should handle gracefully
        $response->assertSessionHasErrors('document_file');
    }

    /** @test */
    public function handles_concurrent_status_updates()
    {
        $admin1 = factory(User::class)->create();
        $admin2 = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin1->roles()->attach($adminRole);
        $admin2->roles()->attach($adminRole);

        $moa = factory(Moa::class)->create(['status' => 'pending']);

        // Admin 1 updates
        $this->actingAs($admin1);
        $this->put(route('admin.moa.update', $moa), [
            'status' => 'reviewed',
            'admin_notes' => 'Admin 1 review',
        ]);

        // Admin 2 updates (should overwrite)
        $this->actingAs($admin2);
        $this->put(route('admin.moa.update', $moa), [
            'status' => 'approved',
            'admin_notes' => 'Admin 2 approval',
        ]);

        $moa->refresh();
        $this->assertEquals('approved', $moa->status);
        $this->assertEquals('Admin 2 approval', $moa->admin_notes);
    }

    /** @test */
    public function handles_deleted_user_moas()
    {
        $employee = factory(User::class)->create();
        $moa = factory(Moa::class)->create(['user_id' => $employee->id]);

        $moaId = $moa->id;

        // Delete user (should cascade delete MOA)
        $employee->delete();

        $this->assertDatabaseMissing('moas', ['id' => $moaId]);
    }

    /** @test */
    public function handles_missing_file_path()
    {
        Storage::fake('public');

        $employee = factory(User::class)->create();
        $moa = factory(Moa::class)->create([
            'user_id' => $employee->id,
            'file_path' => 'moa_drafts/nonexistent.pdf',
        ]);

        $this->actingAs($employee);

        $response = $this->get(route('employee.moa.show', $moa));

        // Should still display page, but file download will fail
        $response->assertStatus(200);
    }
}
```

### 17. Performance Tests

#### Test File: `tests/Feature/MoaPerformanceTest.php`

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Moa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoaPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_page_loads_efficiently_with_many_moas()
    {
        $employee = factory(User::class)->create();
        factory(Moa::class, 100)->create(['user_id' => $employee->id]);

        $this->actingAs($employee);

        $startTime = microtime(true);
        $response = $this->get(route('employee.moa.index'));
        $endTime = microtime(true);

        $response->assertStatus(200);
        
        $loadTime = $endTime - $startTime;
        $this->assertLessThan(2, $loadTime, "Page load time exceeded 2 seconds");
    }

    /** @test */
    public function admin_index_loads_efficiently_with_many_moas()
    {
        $admin = factory(User::class)->create();
        $adminRole = factory(\App\Role::class)->create(['name' => 'admin']);
        $admin->roles()->attach($adminRole);

        factory(Moa::class, 500)->create();

        $this->actingAs($admin);

        $startTime = microtime(true);
        $response = $this->get(route('admin.moa.index'));
        $endTime = microtime(true);

        $response->assertStatus(200);
        
        $loadTime = $endTime - $startTime;
        $this->assertLessThan(3, $loadTime, "Admin page load time exceeded 3 seconds");
    }

    /** @test */
    public function public_search_performs_efficiently()
    {
        factory(Moa::class, 200)->create();

        $startTime = microtime(true);
        $response = $this->get(route('public.moa.index', ['search' => 'University']));
        $endTime = microtime(true);

        $response->assertStatus(200);
        
        $loadTime = $endTime - $startTime;
        $this->assertLessThan(2, $loadTime, "Search load time exceeded 2 seconds");
    }

    /** @test */
    public function tracking_number_generation_is_efficient()
    {
        $employee = factory(User::class)->create();
        $this->actingAs($employee);

        // Create 50 MOAs on the same day
        factory(Moa::class, 50)->create([
            'document_type' => 'MOA',
            'created_at' => now(),
        ]);

        $startTime = microtime(true);
        
        // Generate next tracking number
        $date = date('Ymd');
        $count = Moa::where('document_type', 'MOA')
                    ->whereDate('created_at', today())
                    ->count() + 1;
        $trackingNumber = 'MOA-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        
        $endTime = microtime(true);

        $generationTime = $endTime - $startTime;
        $this->assertLessThan(0.1, $generationTime, "Tracking number generation too slow");
    }
}
```

---

## Test Execution Guide

### 18. Running Tests

#### Setup Test Environment

```bash
# 1. Copy environment file
cp .env.example .env.testing

# 2. Configure test database
# Edit .env.testing:
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# 3. Install dependencies
composer install

# 4. Generate application key
php artisan key:generate --env=testing

# 5. Create storage directories
php artisan storage:link
```

#### Run All Tests

```bash
# Run all tests
php artisan test

# Or using PHPUnit directly
./vendor/bin/phpunit

# Run with coverage
php artisan test --coverage

# Run with coverage HTML report
./vendor/bin/phpunit --coverage-html coverage
```

#### Run Specific Test Suites

```bash
# Run unit tests only
php artisan test --testsuite=Unit

# Run feature tests only
php artisan test --testsuite=Feature

# Run specific test file
php artisan test tests/Unit/MoaTest.php

# Run specific test method
php artisan test --filter=it_has_fillable_attributes
```

#### Run Browser Tests (Dusk)

```bash
# Install Dusk
composer require --dev laravel/dusk

# Install Dusk
php artisan dusk:install

# Run Dusk tests
php artisan dusk

# Run specific Dusk test
php artisan dusk tests/Browser/MoaJavaScriptTest.php
```

### 19. Test Coverage Goals

#### Minimum Coverage Requirements

| Component | Target Coverage | Priority |
|-----------|----------------|----------|
| Models | 90% | High |
| Controllers | 85% | High |
| Validation | 100% | Critical |
| Authorization | 100% | Critical |
| File Operations | 80% | High |
| Views | 70% | Medium |
| JavaScript | 60% | Medium |

#### Critical Paths (Must be 100% covered)

1. **MOA Submission Flow**
   - Employee creates MOA
   - Tracking number generation
   - File upload
   - Database storage

2. **Admin Review Flow**
   - Status updates
   - Admin notes
   - Signed file upload
   - Notifications

3. **Authorization**
   - Employee access control
   - Admin access control
   - Public access control
   - Cross-user access prevention

4. **File Security**
   - Upload validation
   - File type checking
   - Size limits
   - Storage permissions

### 20. Continuous Integration Setup

#### GitHub Actions Workflow

Create `.github/workflows/tests.yml`:

```yaml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: test_db
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
    - uses: actions/checkout@v2

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '7.4'
        extensions: mbstring, dom, fileinfo, mysql
        coverage: xdebug

    - name: Copy .env
      run: php -r "file_exists('.env') || copy('.env.example', '.env');"

    - name: Install Dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

    - name: Generate key
      run: php artisan key:generate

    - name: Directory Permissions
      run: chmod -R 777 storage bootstrap/cache

    - name: Create Database
      run: |
        mkdir -p database
        touch database/database.sqlite

    - name: Execute tests (Unit and Feature tests) via PHPUnit
      env:
        DB_CONNECTION: sqlite
        DB_DATABASE: database/database.sqlite
      run: vendor/bin/phpunit --coverage-text

    - name: Upload coverage to Codecov
      uses: codecov/codecov-action@v2
      with:
        files: ./coverage.xml
        fail_ci_if_error: true
```

### 21. Test Data Factories

#### Create MOA Factory

Create `database/factories/MoaFactory.php`:

```php
<?php

use App\Moa;
use App\User;
use Faker\Generator as Faker;

$factory->define(Moa::class, function (Faker $faker) {
    $documentType = $faker->randomElement(['MOA', 'IA']);
    $date = date('Ymd');
    $count = str_pad($faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);
    
    return [
        'user_id' => factory(User::class),
        'tracking_number' => $documentType . '-' . $date . '-' . $count,
        'title' => $faker->sentence(6),
        'document_type' => $documentType,
        'file_path' => 'moa_drafts/' . $faker->uuid . '.pdf',
        'signed_file_path' => $faker->boolean(30) ? 'moa_signed/' . $faker->uuid . '.pdf' : null,
        'status' => $faker->randomElement(['pending', 'reviewed', 'approved', 'rejected']),
        'admin_notes' => $faker->boolean(40) ? $faker->paragraph : null,
    ];
});
```

### 22. Test Utilities and Helpers

#### Create Test Helper

Create `tests/TestHelpers.php`:

```php
<?php

namespace Tests;

use App\User;
use App\Role;
use Illuminate\Http\UploadedFile;

trait TestHelpers
{
    protected function createEmployee()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create(['name' => 'employee']);
        $user->roles()->attach($role);
        return $user;
    }

    protected function createAdmin()
    {
        $user = factory(User::class)->create();
        $role = factory(Role::class)->create(['name' => 'admin']);
        $user->roles()->attach($role);
        return $user;
    }

    protected function createPdfFile($name = 'document.pdf', $sizeInKb = 1024)
    {
        return UploadedFile::fake()->create($name, $sizeInKb);
    }

    protected function createMoaData($overrides = [])
    {
        return array_merge([
            'title' => 'Test MOA - University - Research',
            'document_type' => 'MOA',
            'document_file' => $this->createPdfFile(),
        ], $overrides);
    }
}
```

---

## Summary

### Total Test Cases: 150+

#### Breakdown by Category:
- **Unit Tests**: 25 test cases
- **Controller Tests**: 35 test cases
- **Feature/Integration Tests**: 20 test cases
- **Validation Tests**: 15 test cases
- **Security Tests**: 15 test cases
- **File Operation Tests**: 20 test cases
- **Database Tests**: 10 test cases
- **UI/Browser Tests**: 10 test cases
- **Edge Cases**: 10 test cases
- **Performance Tests**: 5 test cases

### Estimated Test Execution Time:
- Unit Tests: ~30 seconds
- Feature Tests: ~2 minutes
- Browser Tests: ~5 minutes
- **Total**: ~7-8 minutes

### Next Steps:
1. Implement test factories
2. Set up CI/CD pipeline
3. Run initial test suite
4. Fix failing tests
5. Achieve target coverage
6. Document test results
7. Set up automated testing on commits

---

## Maintenance

### Regular Test Maintenance Tasks:

1. **Weekly**:
   - Review test coverage reports
   - Update failing tests
   - Add tests for new features

2. **Monthly**:
   - Review and refactor slow tests
   - Update test data factories
   - Clean up obsolete tests

3. **Quarterly**:
   - Full test suite audit
   - Performance optimization
   - Documentation updates

### Test Quality Checklist:

- [ ] All tests are independent
- [ ] Tests use descriptive names
- [ ] Tests follow AAA pattern (Arrange, Act, Assert)
- [ ] No hardcoded values
- [ ] Proper use of factories
- [ ] Database cleanup after tests
- [ ] File cleanup after tests
- [ ] Proper error assertions
- [ ] Edge cases covered
- [ ] Performance considerations

---

**End of Test Cases Documentation**