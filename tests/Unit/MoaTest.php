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
            'pengguna_id',
            'nomor_pelacakan',
            'judul',
            'jenis_dokumen',
            'path_berkas',
            'path_berkas_ttd',
            'status',
            'catatan_admin',
        ];

        $moa = new Moa();
        $this->assertEquals($fillable, $moa->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $moa = Moa::factory()->create(['pengguna_id' => $user->id]);

        $this->assertInstanceOf(User::class, $moa->user);
        $this->assertEquals($user->id, $moa->user->id);
    }

    /** @test */
    public function it_can_have_moa_document_type()
    {
        $moa = Moa::factory()->create(['jenis_dokumen' => 'MOA']);
        $this->assertEquals('MOA', $moa->jenis_dokumen);
    }

    /** @test */
    public function it_can_have_ia_document_type()
    {
        $moa = Moa::factory()->create(['jenis_dokumen' => 'IA']);
        $this->assertEquals('IA', $moa->jenis_dokumen);
    }

    /** @test */
    public function it_has_timestamps()
    {
        $moa = Moa::factory()->create();

        $this->assertNotNull($moa->created_at);
        $this->assertNotNull($moa->updated_at);
    }
}