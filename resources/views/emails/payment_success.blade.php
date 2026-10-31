<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Berhasil - SILADATA</title>
</head>
<body style="font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; line-height: 1.6; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; margin: 0 auto; text-align: left;">
                    
                    <!-- Header -->
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #6d28d9; background-image: radial-gradient(circle at 15% 30%, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.08) 6px, transparent 7px), radial-gradient(circle at 85% 20%, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.06) 12px, transparent 13px), radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.05) 8px, transparent 9px), radial-gradient(circle at 20% 85%, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.06) 10px, transparent 11px), radial-gradient(circle at 50% 10%, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.04) 4px, transparent 5px), linear-gradient(135deg, #1d4ed8 0%, #7e22ce 100%); padding: 35px 40px; border-radius: 12px 12px 0 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="width: 65px; vertical-align: middle;">
                                        <img src="https://siladata.my.id/public/images/logoname.png" alt="SILADATA" style="height: 55px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); display: block;">
                                    </td>
                                    <td style="padding-left: 25px; vertical-align: middle; text-align: left;">
                                        <h2 style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #ffffff; font-size: 22px; margin: 0 0 4px 0; font-weight: 700; letter-spacing: 0.3px; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">Pembayaran Berhasil</h2>
                                        <p style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: rgba(255,255,255,0.9); font-size: 14px; margin: 0; font-weight: 500;">Sistem Layanan Dokumen Akreditasi</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 35px 40px 40px 40px;">
                            
                            
                            <p style="font-size: 16px; margin-bottom: 15px; color: #4b5563;">Halo <strong style="color: #111827;">{{ $transaction->customer_name }}</strong>,</p>
                            
                            <p style="font-size: 16px; margin-bottom: 25px; color: #4b5563;">Terima kasih, pembayaran Anda untuk <strong style="color: #111827;">Paket {{ $transaction->package_name }}</strong> telah kami terima dengan baik.</p>
                            
                            @if(!$transaction->user_id && !$transaction->is_registered)
                            <div style="background-color: #eff6ff; border-left: 4px solid #007bff; padding: 24px; margin: 35px 0; border-radius: 0 8px 8px 0;">
                                <h3 style="margin-top: 0; color: #1e3a8a; font-size: 18px; margin-bottom: 12px; font-weight: 700;">Langkah Selanjutnya</h3>
                                <p style="font-size: 15px; margin-bottom: 24px; color: #1e40af;">Silakan buat akun administrator Perguruan Tinggi (Perti) Anda untuk mulai mengelola dokumen akreditasi.</p>
                                
                                <a href="{{ route('register-perti.form', $transaction->registration_token) }}" style="display: inline-block; background-color: #007bff; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; text-align: center; box-shadow: 0 4px 6px -1px rgba(0, 123, 255, 0.3);">Buat Akun Perguruan Tinggi</a>
                            </div>
                            @else
                            <div style="background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 24px; margin: 35px 0; border-radius: 0 8px 8px 0;">
                                <p style="margin: 0; font-size: 16px; color: #166534; font-weight: 500;">Paket langganan Anda telah berhasil diperpanjang/diupgrade. Anda dapat kembali menggunakan layanan kami.</p>
                            </div>
                            @endif
                            
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 24px; margin-top: 40px;">
                                <p style="font-size: 15px; margin-bottom: 5px; color: #374151;">Terima kasih telah menggunakan <strong>SILADATA</strong>.</p>
                                <p style="font-size: 15px; color: #6b7280; margin-top: 0;">Salam hangat,<br><strong style="color: #111827;">Tim SILADATA</strong></p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 13px; color: #6b7280; line-height: 1.5;">
                                Jika Anda memerlukan bantuan atau memiliki pertanyaan, jangan ragu untuk menghubungi kami di 
                                <br>
                                <a href="mailto:siladata.official@gmail.com" style="color: #007bff; text-decoration: none; font-weight: 600;">siladata.official@gmail.com</a>
                            </p>
                            <p style="margin: 15px 0 0 0; font-size: 12px; color: #9ca3af;">
                                &copy; {{ date('Y') }} SILADATA. Hak Cipta Dilindungi.
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
