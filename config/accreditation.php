<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pendaftaran publik
    |--------------------------------------------------------------------------
    |
    | Jika false, hanya admin yang dapat membuat akun program studi (unit kerja)
    | melalui panel admin. Prodi tidak dapat mendaftar sendiri.
    |
    */

    'allow_public_registration' => env('ALLOW_PUBLIC_REGISTRATION', false),

    /*
    |--------------------------------------------------------------------------
    | Unggahan dokumen akreditasi
    |--------------------------------------------------------------------------
    |
    | max_upload_kb: batas per berkas (20480 = 20 MB).
    | post_max_size_mb: batas total request saat unggah batch; sesuaikan dengan
    | php.ini (post_max_size) agar banyak berkas bisa dikirim sekaligus.
    |
    */

    'max_upload_kb' => (int) env('ACCREDITATION_MAX_UPLOAD_KB', 20480),

    'post_max_size_mb' => (int) env('ACCREDITATION_POST_MAX_MB', 256),

    'allowed_mimes' => ['pdf', 'xlsx', 'xls'],

];
