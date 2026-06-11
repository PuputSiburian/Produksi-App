<?php

namespace App\Http\Controllers;

use App\Models\ProduksiCutting;
use App\Models\produksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProduksiCuttingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Operator hanya melihat datanya sendiri, Manager & Admin lihat semua
        if ($user->isOperatorCutting()) {
            $produksiCuttings = ProduksiCutting::where('user_id', $user->id)->latest()->paginate(10);
        } else {
            $produksiCuttings = ProduksiCutting::latest()->paginate(10);
        }
        
        return view('produksi-cutting.index', compact('produksiCuttings'));
    }

    public function create()
    {
        $this->authorizeCreate();
        
        // Ambil data produk untuk stasiun Cutting
        $produk = produksi::where('stasiun', 'Cutting')
                          ->where('status', 'Aktif')
                          ->get();
        
        return view('produksi-cutting.create', compact('produk'));
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'line_cutting' => 'required|string',
            'nama_operator' => 'required|string',
            'proses' => 'nullable|string',
            'produk' => 'required|string',
            'lot_produk' => 'nullable|string',
            'part_number' => 'required|string',
            'warna' => 'required|string',
            'target' => 'required|integer|min:1',
            'qty' => 'required|integer|min:0',
            'reject' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // VALIDASI TAMBAHAN: Reject tidak boleh lebih dari QTY
        $reject = $request->reject ?? 0;
        $qty = $request->qty ?? 0;
        
        if ($reject > $qty) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
        }

        $validated['user_id'] = auth()->id();
        
        // CREATE DATA
        $produksiCutting = ProduksiCutting::create($validated);
        
        // CATAT LOG CREATE
        $produksiCutting->logActivity('CREATE', null, $produksiCutting->toArray());

        return redirect()->route('produksi-cutting.index')
            ->with('success', 'Data produksi cutting berhasil ditambahkan');
    }

    public function edit(ProduksiCutting $produksiCutting)
    {
        $this->authorizeEdit($produksiCutting);
        
        // Ambil data produk untuk stasiun Cutting
        $produk = produksi::where('stasiun', 'Cutting')
                          ->where('status', 'Aktif')
                          ->get();
        
        return view('produksi-cutting.edit', compact('produksiCutting', 'produk'));
    }

    public function update(Request $request, ProduksiCutting $produksiCutting)
    {
        $this->authorizeEdit($produksiCutting);
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'line_cutting' => 'required|string',
            'nama_operator' => 'required|string',
            'proses' => 'nullable|string',
            'produk' => 'required|string',
            'lot_produk' => 'nullable|string',
            'part_number' => 'required|string',
            'warna' => 'required|string',
            'target' => 'required|integer|min:1',
            'qty' => 'required|integer|min:0',
            'reject' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // VALIDASI TAMBAHAN: Reject tidak boleh lebih dari QTY
        $reject = $request->reject ?? 0;
        $qty = $request->qty ?? 0;
        
        if ($reject > $qty) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
        }

        // SIMPAN DATA LAMA SEBELUM UPDATE
        $oldData = $produksiCutting->toArray();
        
        // UPDATE DATA
        $produksiCutting->update($validated);
        
        // SIMPAN DATA BARU SETELAH UPDATE
        $newData = $produksiCutting->fresh()->toArray();
        
        // CATAT LOG UPDATE
        $produksiCutting->logActivity('UPDATE', $oldData, $newData);

        return redirect()->route('produksi-cutting.index')
            ->with('success', 'Data produksi cutting berhasil diupdate');
    }

    public function destroy(ProduksiCutting $produksiCutting)
    {
        $this->authorizeDelete($produksiCutting);
        
        // SIMPAN DATA LAMA SEBELUM DIHAPUS
        $oldData = $produksiCutting->toArray();
        
        // CATAT LOG DELETE
        $produksiCutting->logActivity('DELETE', $oldData, null);
        
        // HAPUS DATA
        $produksiCutting->delete();
        
        return redirect()->route('produksi-cutting.index')
            ->with('success', 'Data produksi cutting berhasil dihapus');
    }

    public function show(ProduksiCutting $produksiCutting)
    {
        return view('produksi-cutting.show', compact('produksiCutting'));
    }

    public function exportPdf()
    {
        $this->authorizeExport();
        
        $data = ProduksiCutting::latest()->get();
        $pdf = Pdf::loadView('exports.produksi-cutting-pdf', compact('data'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('laporan-cutting-' . date('Y-m-d') . '.pdf');
    }

    public function history(ProduksiCutting $produksiCutting)
    {
        $activities = $produksiCutting->activities()->paginate(20);
        return view('produksi-cutting.history', compact('produksiCutting', 'activities'));
    }

    // ========== EXPORT PDF MINGGUAN (WEEKLY REPORT) ==========
    
    /**
     * Export PDF per Minggu - Halaman Filter (GET)
     */
    public function exportWeekly(Request $request)
    {
        $this->authorizeExport();
        
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        $minggu = $request->get('minggu', 1);
        
        // Hitung tanggal mulai dan akhir minggu
        $tanggal_mulai = $this->getStartDate($tahun, $bulan, $minggu);
        $tanggal_akhir = $this->getEndDate($tahun, $bulan, $minggu);
        
        // Ambil data sesuai periode
        $data = ProduksiCutting::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
                               ->orderBy('tanggal', 'desc')
                               ->get();
        
        return view('exports.produksi-cutting-pdf', compact('data', 'tanggal_mulai', 'tanggal_akhir', 'tahun', 'bulan', 'minggu'));
    }
    
    /**
     * Download PDF per Minggu (POST)
     */
    public function downloadWeeklyPDF(Request $request)
    {
        $this->authorizeExport();
        
        $request->validate([
            'tahun' => 'required|integer',
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
        
        $pdf = Pdf::loadView('exports.produksi-cutting-pdf', compact('data', 'tanggal_mulai', 'tanggal_akhir', 'tahun', 'bulan', 'minggu'));
        $pdf->setPaper('A4', 'landscape');
        
        $nama_bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        session()->flash('success', "Laporan mingguan Cutting berhasil di-generate! Periode: " . 
                         \Carbon\Carbon::parse($tanggal_mulai)->format('d/m/Y') . " s/d " . 
                         \Carbon\Carbon::parse($tanggal_akhir)->format('d/m/Y'));
        
        return $pdf->download('laporan_cutting_minggu_' . $minggu . '_' . $nama_bulan[(int)$bulan] . '_' . $tahun . '.pdf');
    }
    
    /**
     * Hitung tanggal mulai minggu
     */
    private function getStartDate($tahun, $bulan, $minggu)
    {
        $firstDay = date("Y-m-01", strtotime("$tahun-$bulan-01"));
        $startDate = date("Y-m-d", strtotime("$firstDay +" . (($minggu - 1) * 7) . " days"));
        return $startDate;
    }
    
    /**
     * Hitung tanggal akhir minggu
     */
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

    // ========== END EXPORT PDF MINGGUAN ==========

    // Authorization methods
    private function authorizeCreate()
    {
        if (!auth()->user()->canCreateData()) {
            abort(403, 'Anda tidak memiliki izin untuk menambah data');
        }
    }

    private function authorizeEdit($data)
    {
        $user = auth()->user();
        if (!$user->canEditData()) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit data');
        }
        if ($user->isOperator() && $data->user_id != $user->id) {
            abort(403, 'Anda hanya bisa mengedit data sendiri');
        }
    }

    private function authorizeDelete($data)
    {
        $user = auth()->user();
        if (!$user->canDeleteData()) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus data');
        }
    }

    private function authorizeExport()
    {
        if (!auth()->user()->canExport()) {
            abort(403, 'Anda tidak memiliki izin untuk export data');
        }
    }
}