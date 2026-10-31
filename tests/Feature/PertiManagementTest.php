<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PertiManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_perti_user(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.pertis.store'), [
                'name' => 'ITB Universitas',
                'email' => 'itb@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'kode_pt' => '123456',
                'alamat' => 'Bandung',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.pertis.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'ITB Universitas',
            'email' => 'itb@example.com',
            'role' => UserRole::Perti->value,
        ]);

        $this->assertDatabaseHas('pertis', [
            'kode_pt' => '123456',
            'alamat' => 'Bandung',
        ]);
    }

    public function test_perti_can_create_prodi_user(): void
    {
        $perti = User::factory()->create([
            'role' => UserRole::Perti,
        ]);

        $response = $this->actingAs($perti)
            ->post(route('perti.prodis.store'), [
                'name' => 'S1 Informatika ITB',
                'email' => 'if@itb.example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'kode_prodi' => 'IF001',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('perti.prodis.index'));

        $user = User::where('email', 'if@itb.example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::Prodi, $user->role);

        $this->assertDatabaseHas('prodis', [
            'user_id' => $user->id,
            'perti_id' => $perti->pertiProfile->id,
            'kode_prodi' => 'IF001',
        ]);
    }

    public function test_perti_can_only_see_their_own_prodis(): void
    {
        $pertiA = User::factory()->create(['role' => UserRole::Perti]);
        $pertiB = User::factory()->create(['role' => UserRole::Perti]);

        $prodiUserA = User::factory()->create([
            'role' => UserRole::Prodi,
            'name' => 'Prodi Perti A',
        ]);
        $prodiUserA->prodiProfile()->update([
            'perti_id' => $pertiA->pertiProfile->id,
        ]);

        $prodiUserB = User::factory()->create([
            'role' => UserRole::Prodi,
            'name' => 'Prodi Perti B',
        ]);
        $prodiUserB->prodiProfile()->update([
            'perti_id' => $pertiB->pertiProfile->id,
        ]);

        $prodiA = \App\Models\Prodi::where('user_id', $prodiUserA->id)->first();
        $prodiB = \App\Models\Prodi::where('user_id', $prodiUserB->id)->first();

        // Access as Perti A
        $response = $this->actingAs($pertiA)->get(route('perti.prodis.index'));
        $response->assertOk();
        $response->assertSee('Prodi Perti A');
        $response->assertDontSee('Prodi Perti B');

        // Try to edit Prodi B as Perti A
        $responseEdit = $this->actingAs($pertiA)->get(route('perti.prodis.edit', $prodiB->id));
        $responseEdit->assertStatus(404);

        // Try to update Prodi B as Perti A
        $responseUpdate = $this->actingAs($pertiA)->put(route('perti.prodis.update', $prodiB->id), [
            'name' => 'Hacked Name',
            'email' => 'hacked@example.com',
        ]);
        $responseUpdate->assertStatus(404);
    }

    public function test_user_can_upload_profile_photo(): void
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo' => $file,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->profile_photo_path);

        $filePath = public_path('uploads/profile_photos/' . $user->profile_photo_path);
        $this->assertTrue(file_exists($filePath));

        // Cleanup
        @unlink($filePath);
    }

    public function test_admin_pdf_route_without_parameters_returns_selection_view(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.pdf'));

        $response->assertOk();
        $response->assertViewIs('admin.reports.pdf-select');
        $response->assertViewHas('pertis');
    }

    public function test_admin_pdf_route_with_perti_id_downloads_pdf(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $perti = User::factory()->create(['role' => UserRole::Perti, 'name' => 'Institut Teknologi']);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.pdf', ['perti_id' => $perti->id]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=laporan-ringkasan-akreditasi-institut-teknologi.pdf');
    }

    public function test_perti_pdf_route_downloads_pdf_with_only_its_own_data(): void
    {
        $perti = User::factory()->create(['role' => UserRole::Perti, 'name' => 'Universitas Indonesia']);

        $response = $this->actingAs($perti)
            ->get(route('perti.reports.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=laporan-ringkasan-akreditasi-universitas-indonesia.pdf');
    }

    public function test_excel_routes_do_not_exist(): void
    {
        $this->expectException(\Symfony\Component\Routing\Exception\RouteNotFoundException::class);
        route('admin.reports.excel');
    }
}

