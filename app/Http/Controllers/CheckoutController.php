<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CheckoutController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (auth()->check() && auth()->user()->role === \App\Enums\UserRole::Prodi) {
                    return redirect()->route('dashboard')->with('error', 'Pembayaran dan perpanjangan langganan hanya dapat dilakukan oleh akun Universitas (Perti). Silakan hubungi Administrator institusi Anda.');
                }
                return $next($request);
            }),
        ];
    }

    public function showForm($package)
    {
        $validPackages = ['Starter', 'Pro', 'Enterprise'];
        if (!in_array($package, $validPackages)) {
            return redirect()->route('harga')->with('error', 'Paket tidak valid.');
        }

        return view('checkout.index', compact('package'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'package' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
        ]);

        $package = $request->package;
        $prices = [
            'Starter' => 1500000,
            'Pro' => 3500000,
            'Enterprise' => 7500000,
        ];

        if (!array_key_exists($package, $prices)) {
            return redirect()->route('harga')->with('error', 'Paket tidak valid.');
        }

        $orderId = 'TRX-' . time() . '-' . uniqid();
        $amount = $prices[$package];

        // Create transaction record
        $transaction = \App\Models\Transaction::create([
            'order_id' => $orderId,
            'package_name' => $package,
            'amount' => $amount,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'status' => 'pending',
            'registration_token' => \Illuminate\Support\Str::random(60),
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        return redirect()->route('checkout.payment', $transaction->order_id);
    }

    public function payment($orderId)
    {
        $transaction = \App\Models\Transaction::where('order_id', $orderId)->firstOrFail();

        if ($transaction->status === 'success') {
            return redirect()->route('checkout.finish')->with('success', 'Pembayaran sudah selesai.');
        }

        // Set up Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->amount,
            ],
            'customer_details' => [
                'first_name' => $transaction->customer_name,
                'email' => $transaction->customer_email,
            ],
            'item_details' => [
                [
                    'id' => 'PKG-'.$transaction->package_name,
                    'price' => $transaction->duration_years ? ($transaction->amount / $transaction->duration_years) : $transaction->amount,
                    'quantity' => $transaction->duration_years ?: 1,
                    'name' => 'Paket Langganan ' . $transaction->package_name,
                ]
            ],
            'custom_expiry' => [
                'expiry_duration' => 2,
                'unit' => 'hour'
            ]
        ];

        try {
            if ($transaction->snap_token) {
                $snapToken = $transaction->snap_token;
            } else {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $transaction->update(['snap_token' => $snapToken]);
            }
            return view('checkout.payment', compact('transaction', 'snapToken'));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function finish(Request $request)
    {
        // Midtrans redirects here after payment (can be success, pending, failed)
        $orderId = $request->order_id;
        $transaction = \App\Models\Transaction::where('order_id', $orderId)->first();

        // Cek status ke Midtrans langsung jika di database masih pending
        // (Berfungsi sebagai penawar jika webhook telat atau tidak masuk karena localhost)
        if ($transaction && $transaction->status === 'pending') {
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            
            try {
                $status = \Midtrans\Transaction::status($orderId);
                $trxStatus = $status->transaction_status;
                
                if ($trxStatus == 'settlement' || ($trxStatus == 'capture' && $status->fraud_status != 'challenge')) {
                    $transaction->update(['status' => 'success']);
                    
                    try {
                        \Illuminate\Support\Facades\Mail::to($transaction->customer_email)->send(new \App\Mail\PaymentSuccessMail($transaction));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send payment success email: ' . $e->getMessage());
                    }

                    // Jika perpanjangan akun
                    if ($transaction->user_id) {
                        $user = \App\Models\User::find($transaction->user_id);
                        if ($user) {
                            $currentValidUntil = $user->package_valid_until;
                            $newValidUntil = now()->addYears($transaction->duration_years ?? 1);
                            if ($currentValidUntil && $currentValidUntil->isFuture()) {
                                $newValidUntil = $currentValidUntil->addYears($transaction->duration_years ?? 1);
                            }
                            $user->update([
                                'active_package' => $transaction->package_name,
                                'package_valid_until' => $newValidUntil,
                            ]);
                            $transaction->update(['is_registered' => true]);
                        }
                    }
                } elseif ($trxStatus == 'deny' || $trxStatus == 'cancel' || $trxStatus == 'expire') {
                    $transaction->update(['status' => 'failed']);
                }
            } catch (\Exception $e) {
                // Biarkan pending jika API gagal dijangkau
            }
        }

        return view('checkout.finish', compact('transaction'));
    }

    public function upgradePackages()
    {
        $packages = [
            [
                'name' => 'Starter',
                'price_label' => 'Rp 1.500.000',
                'price' => 1500000,
                'description' => 'Institusi kecil yang baru mencoba',
                'features' => [
                    'Maksimal 3 Akun Prodi',
                    'Upload Dokumen via Link (Google Drive)',
                    'Dashboard Monitoring Progress'
                ]
            ],
            [
                'name' => 'Pro',
                'price_label' => 'Rp 3.500.000',
                'price' => 3500000,
                'featured' => true,
                'description' => 'Terbaik untuk institusi menengah',
                'features' => [
                    'Semua Fitur Paket Starter',
                    'Maksimal 10 Akun Prodi',
                    'Bisa Upload File (Storage 10GB)',
                    'Cetak Laporan PDF'
                ]
            ],
            [
                'name' => 'Enterprise',
                'price_label' => 'Rp 7.500.000',
                'price' => 7500000,
                'description' => 'Universitas besar dengan banyak prodi',
                'features' => [
                    'Semua Fitur Paket Pro',
                    'Akun Program Studi Tidak Terbatas',
                    'Bisa Upload File (Storage 50GB)'
                ]
            ]
        ];

        return view('checkout.upgrade', compact('packages'));
    }

    public function processUpgrade(Request $request)
    {
        $request->validate([
            'package' => 'required|string',
            'years' => 'required|integer|min:1|max:5',
        ]);

        $packageName = $request->package;
        $years = (int) $request->years;
        $amount = 0;

        $user = auth()->user();

        // Prevent downgrade logic
        $hierarchy = ['Starter' => 1, 'Pro' => 2, 'Enterprise' => 3];
        $userPackage = $user->active_package;

        if ($userPackage && isset($hierarchy[$userPackage]) && isset($hierarchy[$packageName])) {
            if ($hierarchy[$packageName] < $hierarchy[$userPackage]) {
                return back()->with('error', 'Anda tidak dapat melakukan downgrade ke paket yang lebih rendah.');
            }
        }

        // Pricing logic
        $baseAmount = 0;
        if ($packageName === 'Starter') {
            $baseAmount = 1500000;
        } elseif ($packageName === 'Pro') {
            $baseAmount = 3500000;
        } elseif ($packageName === 'Enterprise') {
            $baseAmount = 7500000;
        } else {
            return back()->with('error', 'Paket tidak valid.');
        }

        $amount = $baseAmount * $years;

        // Create transaction in database explicitly linked to logged in user
        $transaction = \App\Models\Transaction::create([
            'order_id' => 'TRX-' . time() . '-' . uniqid(),
            'package_name' => $packageName,
            'amount' => $amount,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'status' => 'pending',
            'user_id' => $user->id,
            'duration_years' => $years,
            'is_registered' => true, 
        ]);

        return redirect()->route('checkout.payment', $transaction->order_id);
    }
}
