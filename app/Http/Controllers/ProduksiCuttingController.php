<?php

namespace App\Http\Controllers;

use App\Models\ProduksiCutting;
use App\Models\produksi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ProduksiCuttingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isOperatorCutting() || $user->isOperator()) {
            $produksiCuttings = ProduksiCutting::where('user_id', $user->id)->latest()->paginate(10);
        } else {
            $produksiCuttings = ProduksiCutting::latest()->paginate(10);
        }
        
        return view('produksi-cutting.index', compact('produksiCuttings'));
    }

    public function create()
    {
        try {
            $produk = produksi::where('stasiun', 'Cutting')
                            ->where('status', 'Aktif')
                            ->select('id', 'kode_produk', 'nama_produk', 'part_number', 'target_standar')
                            ->get();
            
            if ($produk->isEmpty()) {
                // Tambahkan data produk default
                produksi::create([
                    'kode_produk' => 'PRD-CUT-001',
                    'nama_produk' => 'Produk Cutting A',
                    'part_number' => 'PART-CUT-001',
                    'stasiun' => 'Cutting',
                    'target_standar' => 1000,
                    'status' => 'Aktif'
                ]);
                
                $produk = produksi::where('stasiun', 'Cutting')
                                ->where('status', 'Aktif')
                                ->select('id', 'kode_produk', 'nama_produk', 'part_number', 'target_standar')
                                ->get();
            }
            
            return view('produksi-cutting.create', compact('produk'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal load form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            // 🔥 TAMBAHKAN leader_name KE VALIDASI
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'line_cutting' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
                'proses' => 'nullable|string|max:255',
                'produk' => 'required|string|max:255',
                'lot_produk' => 'required|string|max:255',
                'part_number' => 'required|string|max:255',
                'warna' => 'required|string|max:255',
                'target' => 'required|integer|min:1',
                'qty' => 'required|integer|min:0',
                'reject' => 'nullable|integer|min:0',
                'keterangan' => 'nullable|string',
                'leader_name' => 'required|string|max:255',  // 🔥 TAMBAHKAN
            ]);

            $validated['reject'] = $validated['reject'] ?? 0;

            if ($validated['reject'] > $validated['qty']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
            }

            $validated['user_id'] = auth()->id();

            // 🔥 SIMPAN DATA
            $produksiCutting = ProduksiCutting::create($validated);
            
            // 🔥 LOG AKTIVITAS (CREATE)
            $produksiCutting->logActivity('CREATE', null, $produksiCutting->toArray());

            return redirect()->route('produksi-cutting.index')
                ->with('success', 'Data produksi cutting berhasil ditambahkan!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->validator);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit(ProduksiCutting $produksiCutting)
    {
        try {
            $user = auth()->user();
            if ($user->isOperatorCutting() && $produksiCutting->user_id != $user->id) {
                return redirect()->route('produksi-cutting.index')
                    ->with('error', 'Anda hanya bisa mengedit data sendiri!');
            }
            
            $produk = produksi::where('stasiun', 'Cutting')
                            ->where('status', 'Aktif')
                            ->select('id', 'kode_produk', 'nama_produk', 'part_number', 'target_standar')
                            ->get();
            
            return view('produksi-cutting.edit', compact('produksiCutting', 'produk'));
            
        } catch (\Exception $e) {
            return redirect()->route('produksi-cutting.index')
                ->with('error', 'Gagal load form edit: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ProduksiCutting $produksiCutting)
    {
        try {
            $user = auth()->user();
            if ($user->isOperatorCutting() && $produksiCutting->user_id != $user->id) {
                return redirect()->route('produksi-cutting.index')
                    ->with('error', 'Anda hanya bisa mengedit data sendiri!');
            }
            
            // 🔥 TAMBAHKAN leader_name KE VALIDASI UPDATE
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'line_cutting' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
                'proses' => 'nullable|string|max:255',
                'produk' => 'required|string|max:255',
                'lot_produk' => 'required|string|max:255',
                'part_number' => 'required|string|max:255',
                'warna' => 'required|string|max:255',
                'target' => 'required|integer|min:1',
                'qty' => 'required|integer|min:0',
                'reject' => 'nullable|integer|min:0',
                'keterangan' => 'nullable|string',
                'leader_name' => 'required|string|max:255',  // 🔥 TAMBAHKAN
            ]);

            $validated['reject'] = $validated['reject'] ?? 0;

            if ($validated['reject'] > $validated['qty']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
            }

            // 🔥 UPDATE DATA
            $oldData = $produksiCutting->toArray();
            $produksiCutting->update($validated);
            
            // 🔥 LOG AKTIVITAS (UPDATE)
            $produksiCutting->logActivity('UPDATE', $oldData, $produksiCutting->toArray());

            return redirect()->route('produksi-cutting.index')
                ->with('success', 'Data produksi cutting berhasil diupdate!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy(ProduksiCutting $produksiCutting)
    {
        try {
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isManager()) {
                return redirect()->route('produksi-cutting.index')
                    ->with('error', 'Hanya Admin atau Manager yang bisa menghapus data!');
            }
            
            // 🔥 LOG AKTIVITAS (DELETE) SEBELUM DIHAPUS
            $oldData = $produksiCutting->toArray();
            $produksiCutting->logActivity('DELETE', $oldData, null);
            
            $produksiCutting->delete();

            return redirect()->route('produksi-cutting.index')
                ->with('success', 'Data produksi cutting berhasil dihapus!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function show(ProduksiCutting $produksiCutting)
    {
        try {
            return view('produksi-cutting.show', compact('produksiCutting'));
            
        } catch (\Exception $e) {
            return redirect()->route('produksi-cutting.index')
                ->with('error', 'Gagal load data: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            $data = ProduksiCutting::latest()->get();
            
            if ($data->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data!');
            }
            
            $tanggal_mulai = $data->last()->tanggal ?? now();
            $tanggal_akhir = $data->first()->tanggal ?? now();
            
            $pdf = Pdf::loadView('exports.produksi-cutting-pdf', [
                'data' => $data,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_akhir' => $tanggal_akhir,
                'tahun' => date('Y'),
                'bulan' => date('m'),
                'minggu' => 'Semua Data',
                'totalTarget' => $data->sum('target'),
                'totalQty' => $data->sum('qty'),
                'totalReject' => $data->sum('reject'),
                'totalHasil' => $data->sum('qty') - $data->sum('reject'),
                'efisiensi' => $data->sum('target') > 0 ? round(($data->sum('qty') / $data->sum('target')) * 100, 2) : 0
            ]);
            
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('laporan-cutting-' . date('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    // ========== HISTORY ==========
    public function history(ProduksiCutting $produksiCutting)
    {
        try {
            // 🔥 AMBIL RIWAYAT DARI ACTIVITY_LOGS
            $activities = ActivityLog::where('table_name', 'produksi_cuttings')
                                     ->where('record_id', $produksiCutting->id)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(20);
            
            return view('produksi-cutting.history', compact('produksiCutting', 'activities'));
            
        } catch (\Exception $e) {
            return redirect()->route('produksi-cutting.index')
                ->with('error', 'Gagal load history: ' . $e->getMessage());
        }
    }

    // ============================================================
    // ========== EXPORT PAGE (HARIAN & MINGGUAN) ==========
    // ============================================================
    
    /**
     * Halaman Export Laporan (Harian & Mingguan)
     */
    public function exportPage(Request $request)
    {
        try {
            $tahun = $request->get('tahun', date('Y'));
            $bulan = $request->get('bulan', date('m'));
            
            // Ambil data tanggal yang tersedia
            $availableDates = ProduksiCutting::select('tanggal')
                ->distinct()
                ->orderBy('tanggal', 'desc')
                ->pluck('tanggal')
                ->map(function($date) {
                    return Carbon::parse($date);
                });
            
            // Ambil tahun yang tersedia
            $availableYears = ProduksiCutting::selectRaw('YEAR(tanggal) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
            
            // 🔥 HITUNG MINGGU BERDASARKAN KALENDER (SENIN - MINGGU)
            $weeks = $this->getAvailableWeeks($tahun, $bulan);
            
            return view('produksi-cutting.export-page', compact(
                'availableDates', 
                'availableYears', 
                'weeks',
                'tahun',
                'bulan'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal load halaman export: ' . $e->getMessage());
        }
    }

    /**
     * Export Harian
     */
    public function exportHarian(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date'
            ]);
            
            $tanggal = $request->tanggal;
            
            $data = ProduksiCutting::whereDate('tanggal', $tanggal)
                ->orderBy('tanggal', 'desc')
                ->get();
            
            if ($data->isEmpty()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tidak ada data untuk tanggal ' . Carbon::parse($tanggal)->format('d/m/Y'));
            }
            
            $pdf = Pdf::loadView('exports.produksi-cutting-pdf', [
                'data' => $data,
                'tanggal_mulai' => $tanggal,
                'tanggal_akhir' => $tanggal,
                'tahun' => Carbon::parse($tanggal)->year,
                'bulan' => Carbon::parse($tanggal)->month,
                'minggu' => 'Harian - ' . Carbon::parse($tanggal)->format('d/m/Y'),
                'totalTarget' => $data->sum('target'),
                'totalQty' => $data->sum('qty'),
                'totalReject' => $data->sum('reject'),
                'totalHasil' => $data->sum('qty') - $data->sum('reject'),
                'efisiensi' => $data->sum('target') > 0 ? round(($data->sum('qty') / $data->sum('target')) * 100, 2) : 0
            ]);
            
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download('laporan_cutting_' . Carbon::parse($tanggal)->format('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    /**
     * 🔥 EXPORT MINGGUAN (SENIN - MINGGU / SENIN - JUMAT)
     */
    public function exportMingguan(Request $request)
    {
        try {
            ini_set('memory_limit', '4096M');
            set_time_limit(1200);
            
            $request->validate([
                'tahun' => 'required|integer',
                'bulan' => 'required|integer|between:1,12',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'week_label' => 'nullable|string',
                'filter_type' => 'nullable|string|in:full,workday' // full = Senin-Minggu, workday = Senin-Jumat
            ]);
            
            $tahun = $request->tahun;
            $bulan = $request->bulan;
            $tanggal_mulai = $request->start_date;
            $tanggal_akhir = $request->end_date;
            $weekLabel = $request->week_label ?? 'Mingguan';
            $filterType = $request->filter_type ?? 'full';
            
            // 🔥 FILTER DATA BERDASARKAN JENIS
            $query = ProduksiCutting::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
            
            // Jika filter = workday (Senin - Jumat), exclude Sabtu & Minggu
            if ($filterType == 'workday') {
                $query->whereRaw('DAYOFWEEK(tanggal) BETWEEN 2 AND 6'); // 2=Senin, 6=Jumat
            }
            
            $data = $query->orderBy('tanggal', 'desc')->get();
            
            if ($data->isEmpty()) {
                $periodeLabel = ($filterType == 'workday') ? 'Senin-Jumat' : 'Senin-Minggu';
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tidak ada data untuk periode ' . $periodeLabel . ' yang dipilih!');
            }
            
            // 🔥 HITUNG STATISTIK PER HARI
            $dailyStats = [];
            $currentDate = Carbon::parse($tanggal_mulai);
            $end = Carbon::parse($tanggal_akhir);
            
            while ($currentDate <= $end) {
                $dateStr = $currentDate->format('Y-m-d');
                $dayName = $currentDate->format('l');
                $isWeekend = in_array($dayName, ['Saturday', 'Sunday']);
                
                $dailyData = ProduksiCutting::whereDate('tanggal', $dateStr);
                if ($filterType == 'workday' && $isWeekend) {
                    // Skip weekend untuk workday
                    $currentDate->addDay();
                    continue;
                }
                
                $dailyStats[$dateStr] = [
                    'tanggal' => $dateStr,
                    'hari' => $currentDate->format('d/m/Y'),
                    'nama_hari' => $currentDate->translatedFormat('l'),
                    'target' => $dailyData->sum('target'),
                    'qty' => $dailyData->sum('qty'),
                    'reject' => $dailyData->sum('reject'),
                    'hasil' => $dailyData->sum('qty') - $dailyData->sum('reject'),
                    'is_weekend' => $isWeekend
                ];
                $currentDate->addDay();
            }
            
            $pdf = Pdf::loadView('exports.produksi-cutting-pdf', [
                'data' => $data,
                'dailyStats' => $dailyStats,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_akhir' => $tanggal_akhir,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'minggu' => $weekLabel . ($filterType == 'workday' ? ' (Senin-Jumat)' : ' (Full Week)'),
                'filter_type' => $filterType,
                'totalTarget' => $data->sum('target'),
                'totalQty' => $data->sum('qty'),
                'totalReject' => $data->sum('reject'),
                'totalHasil' => $data->sum('qty') - $data->sum('reject'),
                'efisiensi' => $data->sum('target') > 0 ? round(($data->sum('qty') / $data->sum('target')) * 100, 2) : 0
            ]);
            
            $pdf->setPaper('A4', 'landscape');
            
            $nama_bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            $suffix = ($filterType == 'workday') ? '_Senin-Jumat' : '_Full-Week';
            
            return $pdf->download('laporan_cutting_' . $nama_bulan[(int)$bulan] . '_' . $tahun . '_' . $weekLabel . $suffix . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal download PDF: ' . $e->getMessage());
        }
    }

    // ============================================================
    // ========== HELPER METHODS ==========
    // ============================================================
    
    /**
     * 🔥 GET AVAILABLE WEEKS (SENIN - MINGGU)
     */
    private function getAvailableWeeks($tahun, $bulan)
    {
        // Ambil semua tanggal yang tersedia di bulan tersebut
        $availableDates = ProduksiCutting::select('tanggal')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->distinct()
            ->orderBy('tanggal')
            ->pluck('tanggal')
            ->map(function($date) {
                return Carbon::parse($date);
            });
        
        if ($availableDates->isEmpty()) {
            return [];
        }
        
        // 🔥 HITUNG MINGGU BERDASARKAN KALENDER (SENIN = AWAL MINGGU)
        $weeks = [];
        $weekNumber = 1;
        $currentWeek = [];
        $weekStart = null;
        
        foreach ($availableDates as $date) {
            // Tentukan awal minggu (Senin)
            $monday = $date->copy()->startOfWeek(Carbon::MONDAY);
            $sunday = $date->copy()->endOfWeek(Carbon::SUNDAY);
            
            // Jika minggu baru dimulai
            if ($weekStart === null || $monday->format('Y-m-d') != $weekStart->format('Y-m-d')) {
                if (!empty($currentWeek)) {
                    $currentWeek['end'] = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
                    $weeks[] = $currentWeek;
                    $weekNumber++;
                }
                
                $weekStart = $monday;
                $currentWeek = [
                    'start' => $monday,
                    'end' => $sunday,
                    'week_number' => $weekNumber
                ];
            }
        }
        
        // Tambahkan minggu terakhir
        if (!empty($currentWeek)) {
            $currentWeek['end'] = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
            $weeks[] = $currentWeek;
        }
        
        return $weeks;
    }

    private function getStartDate($tahun, $bulan, $minggu)
    {
        $date = Carbon::create($tahun, $bulan, 1);
        $startOfMonth = $date->copy()->startOfMonth();
        
        $firstSunday = $startOfMonth->copy()->startOfWeek();
        if ($firstSunday->month < $bulan) {
            $firstSunday->addWeek();
        }
        $startDate = $firstSunday->copy()->addWeeks($minggu - 1);
        return $startDate->format('Y-m-d');
    }
    
    private function getEndDate($tahun, $bulan, $minggu)
    {
        $startDate = Carbon::parse($this->getStartDate($tahun, $bulan, $minggu));
        $endDate = $startDate->copy()->addDays(6);
        $lastDayOfMonth = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        if ($endDate > $lastDayOfMonth) {
            $endDate = $lastDayOfMonth;
        }
        return $endDate->format('Y-m-d');
    }
}