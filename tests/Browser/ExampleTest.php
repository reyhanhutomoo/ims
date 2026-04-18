<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Moa;
use App\Role;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    // 1. Pastiin halaman udah ke-load sempurna
                    ->waitForText('INTERNSHIP MANAGEMENT SYSTEM', 5)
                    ->assertSee('INTERNSHIP MANAGEMENT SYSTEM')

                    // 2. Klik tombol login pake selector class (pake titik di depan)
                    // Karena class-nya banyak, kita pake satu yang paling unik: .btn-login
                    ->click('.btn-login')

                    // 3. Nunggu proses pindah halaman (pake interval biar nggak kecepetan)
                    ->pause(1000) 
                    ->assertPathIs('/login')

                    // 4. Pastiin di halaman login ada teks verifikasi
                    // Kalau di halaman login lo ada tulisan "Login" atau "Masuk", sesuain di sini
                    ->assertSee('Login'); 
        });
    }

    public function testMOAEmployeeDashboardAccess()
    {
        $employee = \App\User::factory()->create([
            'email' => 'employee@example.com',
            // Default kata_sandi dari factory sudah 'password'
        ]);
        $employeeRole = \App\Role::factory()->create(['nama' => 'employee']);
        $employee->peran()->attach($employeeRole);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'employee@example.com')
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1000)
                    ->assertPathIs('/employee');
                    // ->assertSee('Dashboard Employee')
                    // ->assertSee('MOA/IA Management');
        });
    }

    public function testMOASubmissionForm()
    {
        $employee = \App\User::factory()->create();
        $employeeRole = \App\Role::factory()->create(['nama' => 'employee']);
        $employee->peran()->attach($employeeRole);

        $this->browse(function (Browser $browser) use ($employee) {
            $browser->loginAs($employee)
                    ->visit('/employee/moa/create')
                    ->assertSee('Form Pengajuan MOA/IA')
                    ->assertPresent('input[name="title"]')
                    ->assertPresent('select[name="document_type"]')
                    ->assertPresent('input[name="document_file"]')
                    ->assertSee('Kirim Pengajuan');
        });
    }

    public function testMOAFileUpload()
    {
        $this->browse(function (Browser $browser) {
            $testFile = __DIR__.'/../../storage/test_files/test.pdf';
            
            // Create test file if it doesn't exist
            if (!file_exists(dirname($testFile))) {
                mkdir(dirname($testFile), 0755, true);
            }
            if (!file_exists($testFile)) {
                file_put_contents($testFile, 'Test PDF content');
            }

            $employee = \App\User::factory()->create();
            $employeeRole = \App\Role::factory()->create(['nama' => 'employee']);
            $employee->peran()->attach($employeeRole);

            $browser->loginAs($employee)
                    ->visit('/employee/moa/create')
                    ->type('title', 'MOA Test - University Collaboration')
                    ->select('document_type', 'MOA')
                    ->attach('document_file', $testFile)
                    ->pause(500)
                    ->assertSee('test.pdf')
                    ->press('Kirim Pengajuan')
                    ->pause(1000)
                    ->assertSee('Pengajuan berhasil');
        });
    }

    public function testMOATrackingNumberDisplay()
    {
        $user = \App\User::factory()->create();
        $employeeRole = \App\Role::factory()->create(['nama' => 'employee']);
        $user->peran()->attach($employeeRole);
        
        $moa = \App\Moa::factory()->create([
            'pengguna_id' => $user->id,
            'nomor_pelacakan' => 'MOA-20251127-001',
        ]);

        $this->browse(function (Browser $browser) use ($user, $moa) {
            $browser->loginAs($user)
                    ->visit("/employee/moa/{$moa->id}")
                    ->assertSee('Detail Pengajuan')
                    ->assertSee('Nomor Tracking')
                    ->assertSee('MOA-20251127-001')
                    ->assertPresent('.alert.alert-info');
        });
    }

    public function testMOAStatusBadges()
    {
        $user = \App\User::factory()->create();
        $employeeRole = \App\Role::factory()->create(['nama' => 'employee']);
        $user->peran()->attach($employeeRole);
        
        $statuses = [
            'menunggu' => 'badge-warning',
            'direview' => 'badge-info',
            'disetujui' => 'badge-success',
            'ditolak' => 'badge-danger',
        ];

        foreach ($statuses as $status => $badgeClass) {
            $moa = \App\Moa::factory()->create([
                'pengguna_id' => $user->id,
                'status' => $status,
            ]);

            $this->browse(function (Browser $browser) use ($user, $moa, $badgeClass) {
                $browser->loginAs($user)
                        ->visit("/employee/moa/{$moa->id}")
                        ->assertPresent(".badge.{$badgeClass}");
            });
        }
    }

    public function testAdminMOAReviewPage()
    {
        $admin = \App\User::factory()->create();
        $adminRole = \App\Role::factory()->create(['nama' => 'admin']);
        $admin->peran()->attach($adminRole);
        
        $moa = \App\Moa::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $moa) {
            $browser->loginAs($admin)
                    ->visit("/admin/moa/{$moa->id}/edit")
                    ->assertSee('Review MOA/IA')
                    ->assertPresent('select[name="status"]')
                    ->assertPresent('textarea[name="admin_notes"]')
                    ->assertPresent('input[name="signed_file"]')
                    ->assertSee('Update Status');
        });
    }

    public function testPublicMOAList()
    {
        \App\Moa::factory()->count(3)->create(['status' => 'disetujui']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/public/moa')
                    ->assertSee('Status Pengajuan MoA/IA')
                    ->assertPresent('input[name="search"]')
                    ->assertPresent('select[name="document_type"]')
                    ->assertPresent('select[name="status"]')
                    ->assertSee('Filter');
        });
    }

    public function testPublicMOASearch()
    {
        $moa1 = \App\Moa::factory()->create([
            'judul' => 'University of Indonesia Collaboration',
            'status' => 'disetujui',
        ]);
        $moa2 = \App\Moa::factory()->create([
            'judul' => 'Harvard University Research',
            'status' => 'disetujui',
        ]);

        $this->browse(function (Browser $browser) use ($moa1, $moa2) {
            $browser->visit('/public/moa')
                    ->type('search', 'Indonesia')
                    ->press('Filter')
                    ->assertSee('University of Indonesia Collaboration')
                    ->assertDontSee('Harvard University Research');
        });
    }

    public function testMOAValidationErrors()
    {
        $employee = \App\User::factory()->create();
        $employeeRole = \App\Role::factory()->create(['nama' => 'employee']);
        $employee->peran()->attach($employeeRole);

        $this->browse(function (Browser $browser) use ($employee) {
            $browser->loginAs($employee)
                    ->visit('/employee/moa/create')
                    ->press('Kirim Pengajuan')
                    ->pause(500)
                    ->assertSee('Judul harus diisi')
                    ->assertSee('Jenis dokumen harus diisi')
                    ->assertSee('File dokumen harus diisi');
        });
    }
}