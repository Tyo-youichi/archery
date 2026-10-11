<?php

namespace App\Http\Controllers;

use App\Models\Metric;
use App\Support\Metrics;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CRUD metrik + benchmark (admin). Perubahan di sini langsung memengaruhi form
 * input tes, perhitungan profil, dan Head to Head. Nilai tes lama tidak berubah
 * karena BM-nya di-snapshot saat input (kolom JSON results).
 */
class MetricController extends Controller
{
    private const DOMAINS = ['physical' => 'Performa Fisik', 'technique' => 'Performa Teknik'];

    public function index()
    {
        $metrics = Metric::orderBy('domain')->orderBy('sort')->orderBy('id')->get()->groupBy('domain');

        return view('metrics.index', ['metrics' => $metrics, 'domains' => self::DOMAINS]);
    }

    public function create()
    {
        $metric = new Metric([
            'domain' => 'physical', 'is_active' => true, 'is_promoted' => false,
            'step' => '0.01', 'sort' => (int) Metric::max('sort') + 1,
        ]);

        return view('metrics.form', ['metric' => $metric, 'domains' => self::DOMAINS]);
    }

    public function store(Request $request)
    {
        Metric::create($this->validated($request));
        Metrics::flush();

        return redirect()->route('metrics.index')->with('ok', 'Metrik ditambahkan.');
    }

    public function edit(Metric $metric)
    {
        return view('metrics.form', ['metric' => $metric, 'domains' => self::DOMAINS]);
    }

    public function update(Request $request, Metric $metric)
    {
        // domain & key tidak diubah (key dipakai sebagai kunci di JSON results).
        $metric->update($this->validated($request, $metric, lockKey: true));
        Metrics::flush();

        return redirect()->route('metrics.index')->with('ok', 'Metrik diperbarui.');
    }

    public function destroy(Metric $metric)
    {
        $metric->delete();
        Metrics::flush();

        return redirect()->route('metrics.index')
            ->with('ok', "Metrik '{$metric->label}' dihapus. Data tes lama yang memuat key ini tetap tersimpan.");
    }

    private function validated(Request $request, ?Metric $metric = null, bool $lockKey = false): array
    {
        $domain = $lockKey ? $metric->domain : $request->input('domain');

        $rules = [
            'group_key'   => ['required', 'string', 'max:40', 'regex:/^[a-z0-9_]+$/'],
            'group_label' => ['required', 'string', 'max:80'],
            'label'       => ['required', 'string', 'max:150'],
            'unit'        => ['nullable', 'string', 'max:30'],
            'step'        => ['nullable', 'string', 'max:10'],
            'bm_l'        => ['nullable', 'numeric', 'min:0'],
            'bm_p'        => ['nullable', 'numeric', 'min:0'],
            'sort'        => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];

        if (! $lockKey) {
            $rules['domain'] = ['required', Rule::in(array_keys(self::DOMAINS))];
            $rules['key'] = [
                'required', 'string', 'max:40', 'regex:/^[a-z0-9_]+$/',
                Rule::unique('metrics', 'key')->where(fn ($q) => $q->where('domain', $domain)),
            ];
        }

        $data = $request->validate($rules, [], [
            'group_key' => 'kode grup', 'group_label' => 'nama grup', 'bm_l' => 'BM putra', 'bm_p' => 'BM putri',
        ]);

        $data['step'] = $data['step'] ?: '0.01';
        $data['sort'] = $data['sort'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        $data['is_promoted'] = $request->boolean('is_promoted');

        return $data;
    }
}
