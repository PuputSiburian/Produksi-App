<?php

namespace App\Http\Controllers;

use App\Models\ProduksiLine;
use App\Models\produksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $this->authorizeCreate();
        
        $produk = produksi::where('stasiun', 'Line')
                          ->where('status', 'Aktif')
                          ->get();
        
        return view('produksi-line.create', compact('produk'));
    }

    public function store(Request $request)
    {
        $this->authorizeCreate();
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_line' => 'required|string',
            'nama_operator' => 'required|string',
            'proses' => 'nullable|string',
            'produk' => 'required|string',
            'lot_produk' => 'nullable|string',
            'part_number' => 'required|string',
            'warna' => 'nullable|string',
            'target' => 'required|integer|min:1',
            'qty' => 'required|integer|min:0',
            'reject' => 'nullable|integer|min:0',
            'downtime' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $reject = $request->reject ?? 0;
        $qty = $request->qty ?? 0;
        
        if ($reject > $qty) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
        }

        $validated['user_id'] = auth()->id();
        $produksiLine = ProduksiLine::create($validated);
        
        // Catat log CREATE
        $produksiLine->logActivity('CREATE', null, $produksiLine->toArray());

        return redirect()->route('produksi-line.index')
            ->with('success', 'Data produksi line berhasil ditambahkan');
    }

    public function edit(ProduksiLine $produksiLine)
    {
        $this->authorizeEdit($produksiLine);
        
        $produk = produksi::where('stasiun', 'Line')
                          ->where('status', 'Aktif')
                          ->get();
        
        return view('produksi-line.edit', compact('produksiLine', 'produk'));
    }

    public function update(Request $request, ProduksiLine $produksiLine)
    {
        $this->authorizeEdit($produksiLine);
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama_line' => 'required|string',
            'nama_operator' => 'required|string',
            'proses' => 'nullable|string',
            'produk' => 'required|string',
            'lot_produk' => 'nullable|string',
            'part_number' => 'required|string',
            'warna' => 'nullable|string',
            'target' => 'required|integer|min:1',
            'qty' => 'required|integer|min:0',
            'reject' => 'nullable|integer|min:0',
            'downtime' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $reject = $request->reject ?? 0;
        $qty = $request->qty ?? 0;
        
        if ($reject > $qty) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jumlah Reject tidak boleh lebih besar dari Jumlah Produksi (QTY)!');
        }

        $oldData = $produksiLine->toArray();
        $produksiLine->update($validated);
        $newData = $produksiLine->fresh()->toArray();
        
        // Catat log UPDATE
        $produksiLine->logActivity('UPDATE', $oldData, $newData);

        return redirect()->route('produksi-line.index')
            ->with('success', 'Data produksi line berhasil diupdate');
    }

    public function destroy(ProduksiLine $produksiLine)
    {
        $this->authorizeDelete($produksiLine);
        
        $oldData = $produksiLine->toArray();
        
        // Catat log DELETE
        $produksiLine->logActivity('DELETE', $oldData, null);
        
        $produksiLine->delete();
        
        return redirect()->route('produksi-line.index')
            ->with('success', 'Data produksi line berhasil dihapus');
    }

    public function show(ProduksiLine $produksiLine)
    {
        return view('produksi-line.show', compact('produksiLine'));
    }

    public function exportPdf()
    {
        $this->authorizeExport();
        
        $data = ProduksiLine::latest()->get();
        $pdf = Pdf::loadView('exports.produksi-line-pdf', compact('data'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('laporan-line-' . date('Y-m-d') . '.pdf');
    }

    public function history(ProduksiLine $produksiLine)
    {
        $activities = $produksiLine->activities()->paginate(20);
        return view('produksi-line.history', compact('produksiLine', 'activities'));
    }

    public function exportWeekly(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        $minggu = $request->get('minggu', 1);
        
        $tanggal_mulai = $this->getStartDate($tahun, $bulan, $minggu);
        $tanggal_akhir = $this->getEndDate($tahun, $bulan, $minggu);
        
        $data = ProduksiLine::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
            ->orderBy('tanggal', 'desc')
            ->get();
        
        return view('exports.produksi-line-pdf', compact('data', 'tanggal_mulai', 'tanggal_akhir', 'tahun', 'bulan', 'minggu'));
    }

    public function downloadWeeklyPDF(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        $minggu = $request->get('minggu', 1);
        
        $tanggal_mulai = $this->getStartDate($tahun, $bulan, $minggu);
        $tanggal_akhir = $this->getEndDate($tahun, $bulan, $minggu);
        
        $data = ProduksiLine::whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir])
            ->orderBy('tanggal', 'desc')
            ->get();
        
        $pdf = Pdf::loadView('exports.produksi-line-pdf', compact('data', 'tanggal_mulai', 'tanggal_akhir', 'tahun', 'bulan', 'minggu'));
        $pdf->setPaper('A4', 'landscape');
        
        $nama_bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        return $pdf->download('laporan_line_minggu_' . $minggu . '_' . $nama_bulan[(int)$bulan] . '_' . $tahun . '.pdf');
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