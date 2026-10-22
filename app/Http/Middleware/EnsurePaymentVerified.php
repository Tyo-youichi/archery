<?php

namespace App\Http\Middleware;

use App\Models\Payment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Kunci akses atlet yang BELUM punya pembayaran terverifikasi untuk periode
 * (bulan+tahun) berjalan: dialihkan ke halaman unggah bukti. Staf (admin/coach)
 * tidak dikunci. Route unggah bukti & logout tetap boleh diakses.
 */
class EnsurePaymentVerified
{
    /** Route yang tetap boleh walau pembayaran belum terverifikasi. */
    private const ALLOW = ['payment.my', 'payment.upload', 'payment.proof', 'logout'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya atlet yang di-gate.
        if (! $user || $user->role !== 'athlete') {
            return $next($request);
        }

        $name = $request->route()?->getName();
        if ($name && in_array($name, self::ALLOW, true)) {
            return $next($request);
        }

        $athlete = $user->athlete;
        if (! $athlete) {
            return $next($request);   // akun atlet tanpa data atlet — jangan kunci
        }

        $now = Carbon::now();
        $verified = Payment::where('athlete_id', $athlete->id)
            ->where('year', $now->year)->where('month', $now->month)
            ->where('status', 'verified')->exists();

        if (! $verified) {
            return redirect()->route('payment.my')->with(
                'warn',
                'Akses dikunci. Unggah bukti pembayaran periode ' . $now->translatedFormat('F Y') . ' lalu tunggu verifikasi admin.'
            );
        }

        return $next($request);
    }
}
