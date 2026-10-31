<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DiscussionController extends Controller
{
    /**
     * Display the discussion form page.
     */
    public function show(): View
    {
        return view('home.discussion');
    }

    /**
     * Handle form submission — validate & save to database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'whatsapp'          => 'required|string|max:20',
            'perusahaan'        => 'required|string|max:255',
            'perusahaan_lainnya'=> 'nullable|string|max:255',
            'jabatan'           => 'required|string|max:255',
            'jabatan_lainnya'   => 'nullable|string|max:255',
            'kebutuhan'         => 'required|array|min:1|max:3',
            'kebutuhan.*'       => 'string|max:255',
            'kebutuhan_lainnya' => 'nullable|string|max:1000',
            'sistem_saat_ini'   => 'required|string',
            'investasi'         => 'required|string',
        ]);

        // Resolve "Lainnya" values
        $perusahaan = $validated['perusahaan'] === 'Lainnya'
            ? ($validated['perusahaan_lainnya'] ?? $validated['perusahaan'])
            : $validated['perusahaan'];

        $jabatan = $validated['jabatan'] === 'Lainnya'
            ? ($validated['jabatan_lainnya'] ?? $validated['jabatan'])
            : $validated['jabatan'];

        Discussion::create([
            'nama'              => $validated['nama'],
            'email'             => $validated['email'],
            'whatsapp'          => $validated['whatsapp'],
            'perusahaan'        => $perusahaan,
            'jabatan'           => $jabatan,
            'kebutuhan'         => $validated['kebutuhan'],
            'kebutuhan_lainnya' => $validated['kebutuhan_lainnya'] ?? null,
            'sistem_saat_ini'   => $validated['sistem_saat_ini'],
            'investasi'         => $validated['investasi'],
        ]);

        return redirect()->route('discussion')->with('success', 'Formulir diskusi berhasil dikirim! Tim kami akan segera menghubungi Anda.');
    }
}
