<?php

namespace App\Http\Controllers;

use App\Models\ProduksiLine;
use App\Models\produksi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ProduksiLineController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isOperatorLine()) {
            $produksiLines = ProduksiLine::where('user_id', $user->id)->latest()->paginate(10);
        } else {
            $produksiLines = ProduksiLine::latest()->paginate(10);
        }
        
        return view('produksi-line.index', compact('produksiLines'));
    }

    public function create()
    {
        try {
            $produk = produksi::where('stasiun', 'Line')
                              ->where('status', 'Aktif')
                              ->select('id', 'kode_produk', 'nama_produk', 'part_number', 'target_standar')
                              ->get();
            
            if ($produk->isEmpty()) {
                // Tambahkan data produk default
                produksi::create([
                    'kode_produk' => 'PRD-LINE-001',
                    'nama_produk' => 'Produk Line A',
                    'part_number' => 'PART-LINE-001',
                    'stasiun' => 'Line',
                    'target_standar' => 1000,
                    'status' => 'Aktif'
                ]);
                
                $produk = produksi::where('stasiun', 'Line')
                                  ->where('status', 'Aktif')
                                  ->select('id', 'kode_produk', 'nama_produk', 'part_number', 'target_standar')
                                  ->get();
            }
            
            return view('produksi-line.create', compact('produk'));
            
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
                'nama_line' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
                'proses' => 'nullable|string|max:255',
                'produk' => 'required|string|max:255',
                'lot_produk' => 'required|string|max:255',
                'part_number' => 'required|string|max:255',
                'warna' => 'nullable|string|max:255',
                'target' => 'required|integer|min:1',
                'qty' => 'required|integer|min:0',
                'reject' => 'nullable|integer|min:0',
                'downtime' => 'nullable|integer|min:0',
                'keterangan' => 'nullable|string',
                'leader_name' => 'required|string|max:255',  // 🔥 TAMBAHKAN
            ]);

            $validated['reject'] = $validated['reject'] ?? 0;
            $validated['downtime'] = $validated['downtime'] ?? 0;

            if ($validated['reject'] > $validated['qty']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
            }

            $validated['user_id'] = auth()->id();

            // 🔥 SIMPAN DATA
            $produksiLine = ProduksiLine::create($validated);
            
            // 🔥 LOG AKTIVITAS (CREATE)
            $produksiLine->logActivity('CREATE', null, $produksiLine->toArray());

            return redirect()->route('produksi-line.index')
                ->with('success', 'Data produksi line berhasil ditambahkan!');
                
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

    public function edit(ProduksiLine $produksiLine)
    {
        try {
            $user = auth()->user();
            if ($user->isOperatorLine() && $produksiLine->user_id != $user->id) {
                return redirect()->route('produksi-line.index')
                    ->with('error', 'Anda hanya bisa mengedit data sendiri!');
            }
            
            $produk = produksi::where('stasiun', 'Line')
                              ->where('status', 'Aktif')
                              ->select('id', 'kode_produk', 'nama_produk', 'part_number', 'target_standar')
                              ->get();
            
            return view('produksi-line.edit', compact('produksiLine', 'produk'));
            
        } catch (\Exception $e) {
            return redirect()->route('produksi-line.index')
                ->with('error', 'Gagal load form edit: ' . $e->getMessage());
        }
    }

    public function update(Request $request, ProduksiLine $produksiLine)
    {
        try {
            $user = auth()->user();
            if ($user->isOperatorLine() && $produksiLine->user_id != $user->id) {
                return redirect()->route('produksi-line.index')
                    ->with('error', 'Anda hanya bisa mengedit data sendiri!');
            }
            
            // 🔥 TAMBAHKAN leader_name KE VALIDASI UPDATE
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'nama_line' => 'required|string|max:255',
                'nama_operator' => 'required|string|max:255',
                'proses' => 'nullable|string|max:255',
                'produk' => 'required|string|max:255',
                'lot_produk' => 'required|string|max:255',
                'part_number' => 'required|string|max:255',
                'warna' => 'nullable|string|max:255',
                'target' => 'required|integer|min:1',
                'qty' => 'required|integer|min:0',
                'reject' => 'nullable|integer|min:0',
                'downtime' => 'nullable|integer|min:0',
                'keterangan' => 'nullable|string',
                'leader_name' => 'required|string|max:255',  // 🔥 TAMBAHKAN
            ]);

            $validated['reject'] = $validated['reject'] ?? 0;
            $validated['downtime'] = $validated['downtime'] ?? 0;

            if ($validated['reject'] > $validated['qty']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
            }

            // 🔥 UPDATE DATA
            $oldData = $produksiLine->toArray();
            $produksiLine->update($validated);
            
            // 🔥 LOG AKTIVITAS (UPDATE)
            $produksiLine->logActivity('UPDATE', $oldData, $produksiLine->toArray());

            return redirect()->route('produksi-line.index')
                ->with('success', 'Data produksi line berhasil diupdate!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    public function destroy(ProduksiLine $produksiLine)
    {
        try {
            $user = auth()->user();
            if (!$user->isAdmin() && !$user->isManager()) {
                return redirect()->route('produksi-line.index')
                    ->with('error', 'Hanya Admin atau Manager yang bisa menghapus data!');
            }
            
            // 🔥 LOG AKTIVITAS (DELETE) SEBELUM DIHAPUS
            $oldData = $produksiLine->toArray();
            $produksiLine->logActivity('DELETE', $oldData, null);
            
            $produksiLine->delete();

            return redirect()->route('produksi-line.index')
                ->with('success', 'Data produksi line berhasil dihapus!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function show(ProduksiLine $produksiLine)
    {
        try {
            return view('produksi-line.show', compact('produksiLine'));
            
        } catch (\Exception $e) {
            return redirect()->route('produksi-line.index')
                ->with('error', 'Gagal load data: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        try {
            $data = ProduksiLine::latest()->get();
            
            if ($data->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data!');
            }
            
            $tanggal_mulai = $data->last()->tanggal ?? now();
            $tanggal_akhir = $data->first()->tanggal ?? now();
            
            $pdf = Pdf::loadView('exports.produksi-line-pdf', [
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
            return $pdf->download('laporan-line-' . date('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }

    // 🔥 HISTORY - DIPERBAIKI
    public function history(ProduksiLine $produksiLine)
    {
        try {
            // 🔥 PERBAIKI: NAMA TABEL = 'produksi_line' (TANPA 's')
            $activities = ActivityLog::where('table_name', 'produksi_line')  // ✅ BENAR
                                     ->where('record_id', $produksiLine->id)
                                     ->orderBy('created_at', 'desc')
                                     ->paginate(20);
            
            return view('produksi-line.history', compact('produksiLine', 'activities'));
            
        } catch (\Exception $e) {
            return redirect()->route('produksi-line.index')
                ->with('error', 'Gagal load history: ' . $e->getMessage());
        }
    }

    // ========== EXPORT MINGGUAN ==========
    
    public function exportWeekly(Request $request)
    {
        try {
            $tahun = $request->get('tahun', date('Y'));
            $bulan = $request->get('bulan', date('m'));
            $minggu = $request->get('minggu', 1);
            
            $tanggal_mulai = $this->getStartDate($tahun, $bulan, $minggu);
            $tanggal_akhir = $this->getEndDate($tahun, $bulan, $minggu);
            
            $data = ProduksiLine::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
                ->orderBy('tanggal', 'desc')
                ->get();
            
            return view('exports.produksi-line-pdf', compact('data', 'tanggal_mulai', 'tanggal_akhir', 'tahun', 'bulan', 'minggu'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal load export: ' . $e->getMessage());
        }
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
            
            $data = ProduksiLine::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
                ->orderBy('tanggal', 'desc')
                ->get();
            
            if ($data->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada data!');
            }
            
            $pdf = Pdf::loadView('exports.produksi-line-pdf', [
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
            
            $nama_bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                           'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            return $pdf->download('laporan_line_minggu_' . $minggu . '_' . $nama_bulan[(int)$bulan] . '_' . $tahun . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download PDF: ' . $e->getMessage());
        }
    }

    private function getStartDate($tahun, $bulan, $minggu)
    {
        $firstDay = date("Y-m-01", strtotime("$tahun-$bulan-01"));
        $startDate = date("Y-m-d", strtotime("$firstDay +" . (($minggu - 1) * 7) . " days"));
        return $startDate;
    }

    private function getEndDate($tahun, $bulan, $minggu)
    {
        $startDate = $this->getStartDate($tahun, $bulan, $minggu);
        $endDate = date("Y-m-d", strtotime("$startDate +6 days"));
        
        $lastDayOfMonth = date("Y-m-t", strtotime("$tahun-$bulan-01"));
        if ($endDate > $lastDayOfMonth) {
            $endDate = $lastDayOfMonth;
        }
        return $endDate;
    }
}