<?php

namespace App\Http\Controllers;

use App\Models\ProduksiCrimping;
use App\Models\produksi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ProduksiCrimpingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isOperatorCrimping()) {
            $produksiCrimpings = ProduksiCrimping::where('user_id', $user->id)->latest()->paginate(10);
        } else {
            $produksiCrimpings = ProduksiCrimping::latest()->paginate(10);
        }
        
        return view('produksi-crimping.index', compact('produksiCrimpings'));
    }

    public function create()
    {
        try {
            $produk = produksi::where('stasiun', 'Crimping')
                            ->where('status', 'Aktif')
                            ->select('id', 'kode_produk', 'nama_produk', 'part_number', 'target_standar')
                            ->get();
            
            if ($produk->isEmpty()) {
                produksi::create([
                    'kode_produk' => 'PRD-CRM-001',
                    'nama_produk' => 'Produk Crimping A',
                    'part_number' => 'PART-CRM-001',
                    'stasiun' => 'Crimping',
                    'target_standar' => 1000,
                    'status' => 'Aktif'
                ]);
                
                $produk = produksi::where('stasiun', 'Crimping')
                                ->where('status', 'Aktif')
                                ->select('id', 'kode_produk', 'nama_produk', 'part_number', 'target_standar')
                                ->get();
            }
            
            return view('produksi-crimping.create', compact('produk'));
            
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
                'line_crimping' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
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

            $produksiCrimping = ProduksiCrimping::create($validated);
            $produksiCrimping->logActivity('CREATE', null, $produksiCrimping->toArray());

            return redirect()->route('produksi-crimping.index')
                ->with('success', 'Data produksi crimping berhasil ditambahkan!');
                
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

    public function edit(ProduksiCrimping $produksiCrimping)
    {
        try {
            $user = auth()->user();
            if ($user->isOperatorCrimping() && $produksiCrimping->user_id != $user->id) {
                return redirect()->route('produksi-crimping.index')
                    ->with('error', 'Anda hanya bisa mengedit data sendiri!');
            }
            
            $produk = produksi::where('stasiun', 'Crimping')
                            ->where('status', 'Aktif')
                            ->select('id', 'kode_produk', 'nama_produk', 'part_number', 'target_standar')
                            ->get();
            
            return view('produksi-crimping.edit', compact('produksiCrimping', 'produk'));
            
        } catch (\Exception $e) {
            return redirect()->route('produksi-crimping.index')
                ->with('error', 'Gagal load form edit: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ProduksiCrimping $produksiCrimping)
    {
        try {
            $user = auth()->user();
            if ($user->isOperatorCrimping() && $produksiCrimping->user_id != $user->id) {
                return redirect()->route('produksi-crimping.index')
                    ->with('error', 'Anda hanya bisa mengedit data sendiri!');
            }
            
            // 🔥 TAMBAHKAN leader_name KE VALIDASI UPDATE
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'line_crimping' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
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

            $oldData = $produksiCrimping->toArray();
            $produksiCrimping->update($validated);
            $produksiCrimping->logActivity('UPDATE', $oldData, $produksiCrimping->toArray());

            return redirect()->route('produksi-crimping.index')
                ->with('success', 'Data produksi crimping berhasil diupdate!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy(ProduksiCrimping $produksiCrimping)
    {
        try {
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isManager()) {
                return redirect()->route('produksi-crimping.index')
                    ->with('error', 'Hanya Admin atau Manager yang bisa menghapus data!');
            }
            
            $oldData = $produksiCrimping->toArray();
            $produksiCrimping->logActivity('DELETE', $oldData, null);
            $produksiCrimping->delete();

            return redirect()->route('produksi-crimping.index')
                ->with('success', 'Data produksi crimping berhasil dihapus!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function show(ProduksiCrimping $produksiCrimping)
    {
        try {
            return view('produksi-crimping.show', compact('produksiCrimping'));
            
        } catch (\Exception $e) {
            return redirect()->route('produksi-crimping.index')
                ->with('error', 'Gagal load data: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            $data = ProduksiCrimping::latest()->get();
            
            if ($data->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada数据!');
            }
            
            $tanggal_mulai = $data->last()->tanggal ?? now();
            $tanggal_akhir = $data->first()->tanggal ?? now();
            
            $pdf = Pdf::loadView('exports.produksi-crimping-pdf', [
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
            return $pdf->download('laporan-crimping-' . date('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    public function history(ProduksiCrimping $produksiCrimping)
    {
        try {
            $activities = ActivityLog::where('table_name', 'produksi_crimpings')
                                     ->where('record_id', $produksiCrimping->id)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(20);
            
            return view('produksi-crimping.history', compact('produksiCrimping', 'activities'));
            
        } catch (\Exception $e) {
            return redirect()->route('produksi-crimping.index')
                ->with('error', 'Gagal load history: ' . $e->getMessage());
        }
    }

    // ========== EXPORT PDF MINGGUAN ==========
    
    public function exportWeekly()
    {
        return view('produksi-crimping.export-weekly');
    }
    
    public function downloadWeeklyPDF(Request $request)
    {
        try {
            ini_set('memory_limit', '4096M');
            set_time_limit(1200);
            
            $tahun = $request->get('tahun', date('Y'));
            $bulan = $request->get('bulan', date('m'));
            $minggu = $request->get('minggu', 1);
            
            $tanggal_mulai = $this->getStartDate($tahun, $bulan, $minggu);
            $tanggal_akhir = $this->getEndDate($tahun, $bulan, $minggu);
            
            $data = ProduksiCrimping::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
                                    ->orderBy('tanggal', 'desc')
                                    ->get();
            
            if ($data->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data!');
            }
            
            $pdf = Pdf::loadView('exports.produksi-crimping-pdf', [
                'data' => $data,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_akhir' => $tanggal_akhir,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'minggu' => $minggu,
                'totalTarget' => $data->sum('target'),
                'totalQty' => $data->sum('qty'),
                'totalReject' => $data->sum('reject'),
                'totalHasil' => $data->sum('qty') - $data->sum('reject'),
                'efisiensi' => $data->sum('target') > 0 ? round(($data->sum('qty') / $data->sum('target')) * 100, 2) : 0
            ]);
            
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download('laporan-crimping-mingguan.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download PDF: ' . $e->getMessage());
        }
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