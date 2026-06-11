<?php

namespace App\Http\Controllers;

use App\Models\ProduksiCrimping;
use App\Models\produksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProduksiCrimpingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isOperator()) {
            $produksiCrimpings = ProduksiCrimping::where('user_id', $user->id)->latest()->paginate(10);
        } else {
            $produksiCrimpings = ProduksiCrimping::latest()->paginate(10);
        }
        
        return view('produksi-crimping.index', compact('produksiCrimpings'));
    }

    // CREATE - Menampilkan form tambah data (DENGAN DROPDOWN PRODUK)
    public function create()
    {
        // Ambil data produk untuk stasiun Crimping
        $produk = produksi::where('stasiun', 'Crimping')
                          ->where('status', 'Aktif')
                          ->get();
        
        return view('produksi-crimping.create', compact('produk'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'line_crimping' => 'required|string',
            'nama_operator' => 'required|string',
            'produk' => 'required|string',
            'part_number' => 'required|string',
            'lot_produk' => 'nullable|string',
            'warna' => 'nullable|string',
            'target' => 'required|integer|min:1',
            'qty' => 'required|integer|min:0',
            'reject' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Set nilai default 0 untuk reject jika null
        $validated['reject'] = $validated['reject'] ?? 0;

        // VALIDASI: Reject tidak boleh lebih dari QTY
        $reject = $validated['reject'];
        $qty = $validated['qty'];
        
        if ($reject > $qty) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
        }

        $validated['user_id'] = auth()->id();
        
        // CREATE DATA
        $produksiCrimping = ProduksiCrimping::create($validated);
        
        // CATAT LOG CREATE
        $produksiCrimping->logActivity('CREATE', null, $produksiCrimping->toArray());

        return redirect()->route('produksi-crimping.index')
            ->with('success', 'Data produksi crimping berhasil ditambahkan');
    }

    // EDIT - Menampilkan form edit data (DENGAN DROPDOWN PRODUK)
    public function edit(ProduksiCrimping $produksiCrimping)
    {
        // Ambil data produk untuk stasiun Crimping
        $produk = produksi::where('stasiun', 'Crimping')
                          ->where('status', 'Aktif')
                          ->get();
        
        return view('produksi-crimping.edit', compact('produksiCrimping', 'produk'));
    }

    public function update(Request $request, ProduksiCrimping $produksiCrimping)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'line_crimping' => 'required|string',
            'nama_operator' => 'required|string',
            'produk' => 'required|string',
            'part_number' => 'required|string',
            'lot_produk' => 'nullable|string',
            'warna' => 'nullable|string',
            'target' => 'required|integer|min:1',
            'qty' => 'required|integer|min:0',
            'reject' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Set nilai default 0 untuk reject jika null
        $validated['reject'] = $validated['reject'] ?? 0;

        // VALIDASI: Reject tidak boleh lebih dari QTY
        $reject = $validated['reject'];
        $qty = $validated['qty'];
        
        if ($reject > $qty) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
        }

        // SIMPAN DATA LAMA SEBELUM UPDATE
        $oldData = $produksiCrimping->toArray();
        
        // UPDATE DATA
        $produksiCrimping->update($validated);
        
        // SIMPAN DATA BARU SETELAH UPDATE
        $newData = $produksiCrimping->fresh()->toArray();
        
        // CATAT LOG UPDATE
        $produksiCrimping->logActivity('UPDATE', $oldData, $newData);

        return redirect()->route('produksi-crimping.index')
            ->with('success', 'Data produksi crimping berhasil diupdate');
    }

    public function destroy(ProduksiCrimping $produksiCrimping)
    {
        // SIMPAN DATA LAMA SEBELUM DIHAPUS
        $oldData = $produksiCrimping->toArray();
        
        // CATAT LOG DELETE
        $produksiCrimping->logActivity('DELETE', $oldData, null);
        
        // HAPUS DATA
        $produksiCrimping->delete();
        
        return redirect()->route('produksi-crimping.index')
            ->with('success', 'Data produksi crimping berhasil dihapus');
    }

    public function show(ProduksiCrimping $produksiCrimping)
    {
        return view('produksi-crimping.show', compact('produksiCrimping'));
    }

    public function exportPdf()
    {
        $data = ProduksiCrimping::latest()->get();
        $pdf = Pdf::loadView('exports.produksi-crimping-pdf', compact('data'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('laporan-crimping-' . date('Y-m-d') . '.pdf');
    }
    
    public function history(ProduksiCrimping $produksiCrimping)
    {
        $activities = $produksiCrimping->activities()->paginate(20);
        return view('produksi-crimping.history', compact('produksiCrimping', 'activities'));
    }

    // ========== EXPORT PDF MINGGUAN (WEEKLY REPORT) ==========
    
    /**
     * Export PDF per Minggu - Halaman Filter (GET)
     */
    public function exportWeekly(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        $minggu = $request->get('minggu', 1);
        
        // Hitung tanggal mulai dan akhir minggu
        $tanggal_mulai = $this->getStartDate($tahun, $bulan, $minggu);
        $tanggal_akhir = $this->getEndDate($tahun, $bulan, $minggu);
        
        // Ambil data sesuai periode
        $data = ProduksiCrimping::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
                                ->orderBy('tanggal', 'desc')
                                ->get();
        
        return view('exports.produksi-crimping-pdf', compact('data', 'tanggal_mulai', 'tanggal_akhir', 'tahun', 'bulan', 'minggu'));
    }
    
    /**
     * Download PDF per Minggu (POST)
     */
    public function downloadWeeklyPDF(Request $request)
    {
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
        
        $data = ProduksiCrimping::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
                                ->orderBy('tanggal', 'desc')
                                ->get();
        
        $pdf = Pdf::loadView('exports.produksi-crimping-pdf', compact('data', 'tanggal_mulai', 'tanggal_akhir', 'tahun', 'bulan', 'minggu'));
        $pdf->setPaper('A4', 'landscape');
        
        $nama_bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        session()->flash('success', "Laporan mingguan Crimping berhasil di-generate! Periode: " . 
                         \Carbon\Carbon::parse($tanggal_mulai)->format('d/m/Y') . " s/d " . 
                         \Carbon\Carbon::parse($tanggal_akhir)->format('d/m/Y'));
        
        return $pdf->download('laporan_crimping_minggu_' . $minggu . '_' . $nama_bulan[(int)$bulan] . '_' . $tahun . '.pdf');
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
}