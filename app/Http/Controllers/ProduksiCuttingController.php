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
            
            return view('produksi-cutting.create', compact('produk'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal load form: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'line_cutting' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
                'proses' => 'nullable|string|max:255',
                'produk' => 'required|string|max:255',
                'lot_produk' => 'nullable|string|max:255',
                'part_number' => 'required|string|max:255',
                'warna' => 'required|string|max:255',
                'target' => 'required|integer|min:1',
                'qty' => 'required|integer|min:0',
                'reject' => 'nullable|integer|min:0',
                'keterangan' => 'nullable|string',
            ]);

            $validated['reject'] = $validated['reject'] ?? 0;

            if ($validated['reject'] > $validated['qty']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
            }

            $validated['user_id'] = auth()->id();

            $produksiCutting = ProduksiCutting::create($validated);

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
            
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'line_cutting' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
                'proses' => 'nullable|string|max:255',
                'produk' => 'required|string|max:255',
                'lot_produk' => 'nullable|string|max:255',
                'part_number' => 'required|string|max:255',
                'warna' => 'required|string|max:255',
                'target' => 'required|integer|min:1',
                'qty' => 'required|integer|min:0',
                'reject' => 'nullable|integer|min:0',
                'keterangan' => 'nullable|string',
            ]);

            $validated['reject'] = $validated['reject'] ?? 0;

            if ($validated['reject'] > $validated['qty']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
            }

            $produksiCutting->update($validated);

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

    // ========== EXPORT PDF MINGGUAN ==========
    
    public function exportWeekly(Request $request)
    {
        try {
            $tahun = $request->get('tahun', date('Y'));
            $bulan = $request->get('bulan', date('m'));
            $minggu = $request->get('minggu', 1);
            
            $tanggal_mulai = $this->getStartDate($tahun, $bulan, $minggu);
            $tanggal_akhir = $this->getEndDate($tahun, $bulan, $minggu);
            
            $data = ProduksiCutting::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
                                   ->orderBy('tanggal', 'desc')
                                   ->get();
            
            return view('exports.produksi-cutting-pdf', compact('data', 'tanggal_mulai', 'tanggal_akhir', 'tahun', 'bulan', 'minggu'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal load export: ' . $e->getMessage());
        }
    }
    
    public function downloadWeeklyPDF(Request $request)
    {
        try {
            ini_set('memory_limit', '4096M');
            set_time_limit(1200);
            
            $request->validate([
                'tahun' => 'required|integer|min:2020|max:2030',
                'bulan' => 'required|integer|between:1,12',
                'minggu' => 'required|integer|between:1,5'
            ]);
            
            $tahun = $request->tahun;
            $bulan = $request->bulan;
            $minggu = $request->minggu;
            
            $tanggal_mulai = $this->getStartDate($tahun, $bulan, $minggu);
            $tanggal_akhir = $this->getEndDate($tahun, $bulan, $minggu);
            
            $data = ProduksiCutting::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
                                   ->orderBy('tanggal', 'desc')
                                   ->get();
            
            if ($data->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data!');
            }
            
            $pdf = Pdf::loadView('exports.produksi-cutting-pdf', [
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
            
            return $pdf->download('laporan-cutting-mingguan.pdf');
            
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