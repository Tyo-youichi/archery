<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Pembayaran iuran bulanan. Atlet: unggah bukti periode berjalan. Admin:
 * verifikasi/tolak. Akses atlet dikunci (EnsurePaymentVerified) sampai periode
 * berjalan berstatus 'verified'.
 */
class PaymentController extends Controller
{
    // ---------- Atlet ----------

    public function my(Request $request)
    {
        $athlete = $request->user()->athlete;
        abort_unless($athlete, 403, 'Akun ini tidak tertaut ke data atlet.');

        $now = Carbon::now();
        $current = Payment::firstOrNew(['athlete_id' => $athlete->id, 'year' => $now->year, 'month' => $now->month]);
        $history = $athlete->payments()->get();

        return view('payment.my', compact('athlete', 'current', 'history', 'now'));
    }

    public function upload(Request $request)
    {
        $athlete = $request->user()->athlete;
        abort_unless($athlete, 403);

        $data = $request->validate([
            'amount'     => 'nullable|numeric|min:0|max:100000000',
            'note'       => 'nullable|string|max:500',
            'proof_data' => ['required', 'string', 'starts_with:data:image/webp;base64,', 'max:6000000'],
        ]);

        // Validasi bukti: WEBP (magic bytes RIFF/WEBP), maks 3 MB.
        $binary = base64_decode(substr($data['proof_data'], strlen('data:image/webp;base64,')), true);
        if ($binary === false || strlen($binary) > 3 * 1024 * 1024
            || substr($binary, 0, 4) !== 'RIFF' || substr($binary, 8, 4) !== 'WEBP') {
            return back()->withErrors(['proof_data' => 'Bukti harus berupa gambar (otomatis dikonversi WEBP, maks 3 MB).'])->withInput();
        }

        $now = Carbon::now();
        $payment = Payment::firstOrNew(['athlete_id' => $athlete->id, 'year' => $now->year, 'month' => $now->month]);

        $disk = Storage::disk('payments');
        if ($payment->proof) {
            $disk->delete($payment->proof);
        }
        $file = $athlete->id . '_' . $now->format('Ym') . '_' . Str::random(6) . '.webp';
        $disk->put($file, $binary);

        $payment->fill([
            'amount'      => $data['amount'] ?? null,
            'note'        => $data['note'] ?? null,
            'proof'       => $file,
            'status'      => 'pending',   // unggah ulang → kembali menunggu verifikasi
            'verified_by' => null,
            'verified_at' => null,
        ])->save();

        return redirect()->route('payment.my')->with('ok', 'Bukti pembayaran terkirim. Menunggu verifikasi admin.');
    }

    /** Stream bukti (privat): pemilik atau staf. */
    public function proof(Payment $payment)
    {
        $u = auth()->user();
        abort_unless($u->isStaff() || optional($payment->athlete)->user_id === $u->id, 403);
        abort_unless($payment->proof && Storage::disk('payments')->exists($payment->proof), 404);

        return response(Storage::disk('payments')->get($payment->proof), 200, [
            'Content-Type'        => 'image/webp',
            'Content-Disposition' => 'inline; filename="bukti-pembayaran.webp"',
        ]);
    }

    // ---------- Admin ----------

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $payments = Payment::with('athlete')
            ->when(in_array($status, ['pending', 'verified', 'rejected'], true), fn ($q) => $q->where('status', $status))
            ->orderByDesc('year')->orderByDesc('month')->orderBy('athlete_id')
            ->paginate(30)->withQueryString();

        $counts = Payment::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');

        return view('payment.index', compact('payments', 'status', 'counts'));
    }

    public function verify(Payment $payment)
    {
        $payment->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'note'        => null,
        ]);

        return back()->with('ok', 'Pembayaran ' . $payment->athlete->name . ' (' . $payment->period_label . ') diverifikasi.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $data = $request->validate(['note' => 'nullable|string|max:500']);
        $payment->update([
            'status'      => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'note'        => $data['note'] ?: 'Bukti ditolak, mohon unggah ulang.',
        ]);

        return back()->with('ok', 'Pembayaran ' . $payment->athlete->name . ' ditolak.');
    }
}
