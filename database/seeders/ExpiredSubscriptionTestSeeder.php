<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpiredSubscriptionTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat User Perti yang kedaluwarsa
        $pertiUser = \App\Models\User::firstOrCreate(
            ['email' => 'perti.expired@gmail.com'],
            [
                'name' => 'Universitas Kedaluwarsa',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => \App\Enums\UserRole::Perti,
                'active_package' => 'Starter',
                'package_valid_until' => now()->subDays(10), // Kedaluwarsa 10 hari lalu
                'email_verified_at' => now(),
            ]
        );

        $pertiProfile = \App\Models\Perti::firstOrCreate(
            ['user_id' => $pertiUser->id],
            [
                'kode_pt' => 'EXP-001'
            ]
        );

        // 2. Buat User Prodi yang terkait dengan Perti tersebut
        $prodiUser = \App\Models\User::firstOrCreate(
            ['email' => 'prodi.expired@gmail.com'],
            [
                'name' => 'Program Studi Kedaluwarsa',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => \App\Enums\UserRole::Prodi,
                'email_verified_at' => now(),
            ]
        );

        $prodiProfile = \App\Models\Prodi::firstOrCreate(
            ['user_id' => $prodiUser->id],
            [
                'perti_id' => $pertiProfile->id,
                'kode_prodi' => 'EXP-PRODI-1'
            ]
        );

        // 3. Ambil 3 Requirement (Syarat) pertama dari Modul Pertama
        $module = \App\Models\Module::first();
        if (!$module) {
            $module = \App\Models\Module::create(['name' => 'Kriteria 1: Visi, Misi, Tujuan, dan Strategi', 'description' => '...']);
        }
        
        $requirements = \App\Models\Requirement::where('module_id', $module->id)->take(3)->get();
        
        if ($requirements->count() < 3) {
            for ($i = $requirements->count() + 1; $i <= 3; $i++) {
                $requirements->push(\App\Models\Requirement::create([
                    'module_id' => $module->id,
                    'name' => 'Syarat ' . $i,
                ]));
            }
        }

        // Hapus data dummy sebelumnya jika ada untuk requirement tersebut agar bersih
        \App\Models\Submission::whereIn('requirement_id', $requirements->pluck('id'))
            ->where('user_id', $prodiUser->id)
            ->delete();

        // 4. Buat 3 Submission dengan status berbeda-beda
        // Status 1: Menunggu Validasi (pending) - Belum divalidasi sama sekali
        \App\Models\Submission::create([
            'requirement_id' => $requirements[0]->id,
            'user_id' => $prodiUser->id,
            'file_path' => 'dummy-path.pdf',
            'original_filename' => 'dokumen_pending.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'status' => 'pending',
            'version' => 1,
            'is_latest' => true,
            'created_at' => now()->subDays(11) // Diunggah SEBELUM kedaluwarsa
        ]);

        // Status 2: Disetujui (approved) - Tanpa validation notes
        \App\Models\Submission::create([
            'requirement_id' => $requirements[1]->id,
            'user_id' => $prodiUser->id,
            'file_path' => 'dummy-path-2.pdf',
            'original_filename' => 'dokumen_acc.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'status' => 'approved',
            'version' => 1,
            'is_latest' => true,
            'validated_at' => now()->subDays(12),
            'validation_notes' => null, // Gada catatan kalau sudah di-acc
            'created_at' => now()->subDays(15)
        ]);

        // Status 3: Revisi (revision) - Dengan validation notes
        \App\Models\Submission::create([
            'requirement_id' => $requirements[2]->id,
            'user_id' => $prodiUser->id,
            'file_path' => 'dummy-path-3.pdf',
            'original_filename' => 'dokumen_salah.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 512,
            'status' => 'revision',
            'version' => 1,
            'is_latest' => true,
            'validated_at' => now()->subDays(11),
            'validation_notes' => 'Tolong perbaiki halaman 3, ada tabel yang terpotong.',
            'created_at' => now()->subDays(13)
        ]);
    }
}
