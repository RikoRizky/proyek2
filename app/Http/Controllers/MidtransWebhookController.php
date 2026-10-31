<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            $notification = new \Midtrans\Notification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $order_id = $notification->order_id;
            $fraud = $notification->fraud_status;

            $trx = \App\Models\Transaction::where('order_id', $order_id)->first();
            if (!$trx) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $trx->update(['status' => 'pending']);
                    } else {
                        if ($trx->status !== 'success') {
                            $trx->update(['status' => 'success']);
                            $this->processSubscription($trx);
                            try {
                                \Illuminate\Support\Facades\Mail::to($trx->customer_email)->send(new \App\Mail\PaymentSuccessMail($trx));
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Failed to send payment success email via webhook: ' . $e->getMessage());
                            }
                        }
                    }
                }
            } else if ($transaction == 'settlement') {
                if ($trx->status !== 'success') {
                    $trx->update(['status' => 'success']);
                    $this->processSubscription($trx);
                    try {
                        \Illuminate\Support\Facades\Mail::to($trx->customer_email)->send(new \App\Mail\PaymentSuccessMail($trx));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send payment success email via webhook: ' . $e->getMessage());
                    }
                }
            } else if ($transaction == 'pending') {
                $trx->update(['status' => 'pending']);
            } else if ($transaction == 'deny') {
                $trx->update(['status' => 'failed']);
            } else if ($transaction == 'expire') {
                $trx->update(['status' => 'failed']);
            } else if ($transaction == 'cancel') {
                $trx->update(['status' => 'failed']);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    private function processSubscription($trx)
    {
        if ($trx->user_id) {
            $user = \App\Models\User::find($trx->user_id);
            if ($user) {
                $currentValidUntil = $user->package_valid_until;
                $newValidUntil = now()->addYears($trx->duration_years ?? 1);
                
                // If they already have an active package that hasn't expired, extend it
                if ($currentValidUntil && $currentValidUntil->isFuture()) {
                    $newValidUntil = $currentValidUntil->addYears($trx->duration_years ?? 1);
                }

                $user->update([
                    'active_package' => $trx->package_name,
                    'package_valid_until' => $newValidUntil,
                ]);
                
                // Mark transaction as registered so the token is consumed
                $trx->update(['is_registered' => true]);
            }
        }
    }
}
