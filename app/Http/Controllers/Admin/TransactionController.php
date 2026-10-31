<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Auto-cleanup: Tandai transaksi pending yang umurnya lebih dari 2 jam sebagai failed
        Transaction::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(2))
            ->update(['status' => 'failed']);

        $query = Transaction::latest();
        
        if ($request->has('status') && in_array($request->status, ['pending', 'success', 'failed'])) {
            $query->where('status', $request->status);
        }
        
        $transactions = $query->paginate(15)->withQueryString();
        
        $totalRevenue = Transaction::where('status', 'success')->sum('amount');
        
        $starterCount = \App\Models\User::where('active_package', 'Starter')->count();
        $proCount = \App\Models\User::where('active_package', 'Pro')->count();
        $enterpriseCount = \App\Models\User::where('active_package', 'Enterprise')->count();
        
        return view('admin.transactions.index', compact('transactions', 'totalRevenue', 'starterCount', 'proCount', 'enterpriseCount'));
    }
}
