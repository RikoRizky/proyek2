<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\UserRole;
use App\Jobs\SendBroadcastEmail;

class BroadcastController extends Controller
{
    public function index()
    {
        return view('admin.broadcast.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:all,perti,prodi',
        ]);

        $query = User::query()->where('role', '!=', UserRole::Admin);

        if ($request->recipient_type === 'perti') {
            $query->where('role', UserRole::Perti);
        } elseif ($request->recipient_type === 'prodi') {
            $query->where('role', UserRole::Prodi);
        }

        $users = $query->get(['email']);

        $count = 0;
        foreach ($users as $user) {
            if ($user->email) {
                SendBroadcastEmail::dispatch($user->email, $request->subject, $request->message);
                $count++;
            }
        }

        return redirect()->route('admin.broadcast.index')->with('success', "Notifikasi sedang dikirim ke {$count} pengguna di latar belakang (queue).");
    }
}
