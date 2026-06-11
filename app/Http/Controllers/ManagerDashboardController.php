<?php

namespace App\Http\Controllers;

use App\Models\ProduksiCutting;
use App\Models\ProduksiCrimping;
use App\Models\ProduksiLine;
use App\Models\Mesin;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        // ============ STATISTIK PRODUKSI ============
        $totalCutting = ProduksiCutting::count();
        $totalCrimping = ProduksiCrimping::count();
        $totalLine = ProduksiLine::count();
        
        // Total QTY
        $totalQtyCutting = ProduksiCutting::sum('qty');
        $totalQtyCrimping = ProduksiCrimping::sum('qty');
        $totalQtyLine = ProduksiLine::sum('qty');
        
        // Total Target
        $totalTargetCutting = ProduksiCutting::sum('target');
        $totalTargetCrimping = ProduksiCrimping::sum('target');
        $totalTargetLine = ProduksiLine::sum('target');
        
        // ============ TOTAL REJECT (TAMBAHKAN INI) ============
        $totalRejectCutting = ProduksiCutting::sum('reject');
        $totalRejectCrimping = ProduksiCrimping::sum('reject');
        $totalRejectLine = ProduksiLine::sum('reject');
        
        // Persentase pencapaian
        $persenCutting = $totalTargetCutting > 0 ? round(($totalQtyCutting / $totalTargetCutting) * 100, 1) : 0;
        $persenCrimping = $totalTargetCrimping > 0 ? round(($totalQtyCrimping / $totalTargetCrimping) * 100, 1) : 0;
        $persenLine = $totalTargetLine > 0 ? round(($totalQtyLine / $totalTargetLine) * 100, 1) : 0;
        
        // Reject Rate
        $totalQtyAll = $totalQtyCutting + $totalQtyCrimping + $totalQtyLine;
        $totalRejectAll = $totalRejectCutting + $totalRejectCrimping + $totalRejectLine;
        $rejectRate = $totalQtyAll > 0 ? round(($totalRejectAll / $totalQtyAll) * 100, 2) : 0;
        
        // ============ PRODUKSI HARI INI ============
        $hariIni = Carbon::today();
        
        $produksiHariIni = [
            'cutting' => ProduksiCutting::whereDate('tanggal', $hariIni)->count(),
            'crimping' => ProduksiCrimping::whereDate('tanggal', $hariIni)->count(),
            'line' => ProduksiLine::whereDate('tanggal', $hariIni)->count(),
            'total' => 0,
        ];
        $produksiHariIni['total'] = $produksiHariIni['cutting'] + $produksiHariIni['crimping'] + $produksiHariIni['line'];
        
        // ============ STATISTIK MESIN ============
        $mesinStats = [
            'total' => Mesin::count(),
            'beroperasi' => Mesin::where('status', 'Beroperasi')->count(),
            'perbaikan' => Mesin::where('status', 'Perbaikan')->count(),
            'rusak' => Mesin::where('status', 'Rusak')->count(),
            'maintenance' => Mesin::where('status', 'Maintenance')->count(),
            'idle' => Mesin::where('status', 'Idle')->count(),
        ];
        
        // Mesin bermasalah
        $mesinBermasalah = Mesin::whereIn('status', ['Perbaikan', 'Rusak'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        
        // ============ AKTIVITAS TERBARU ============
        $recentActivities = collect();
        
        foreach(ProduksiCutting::latest('tanggal')->take(5)->get() as $item) {
            $recentActivities->push((object)[
                'tanggal' => $item->tanggal,
                'stasiun' => 'CUTTING',
                'operator' => $item->nama_operator,
                'produk' => $item->produk,
                'qty' => $item->qty,
                'reject' => $item->reject ?? 0,
                'status' => ($item->reject ?? 0) > 0 ? '⚠️ Dengan Reject' : '✅ Selesai',
            ]);
        }
        
        foreach(ProduksiCrimping::latest('tanggal')->take(5)->get() as $item) {
            $recentActivities->push((object)[
                'tanggal' => $item->tanggal,
                'stasiun' => 'CRIMPING',
                'operator' => $item->nama_operator,
                'produk' => $item->produk,
                'qty' => $item->qty,
                'reject' => $item->reject ?? 0,
                'status' => ($item->reject ?? 0) > 0 ? '⚠️ Dengan Reject' : '✅ Selesai',
            ]);
        }
        
        foreach(ProduksiLine::latest('tanggal')->take(5)->get() as $item) {
            $recentActivities->push((object)[
                'tanggal' => $item->tanggal,
                'stasiun' => $item->proses ?? 'LINE',
                'operator' => $item->nama_operator,
                'produk' => $item->produk,
                'qty' => $item->qty,
                'reject' => $item->reject ?? 0,
                'status' => ($item->reject ?? 0) > 0 ? '⚠️ Dengan Reject' : '✅ Selesai',
            ]);
        }
        
        $recentActivities = $recentActivities->sortByDesc('tanggal')->take(10);
        
        // ============ DATA UNTUK CHART ============
        $chartData = [
            'labels' => ['Cutting', 'Crimping', 'Line'],
            'target' => [$totalTargetCutting, $totalTargetCrimping, $totalTargetLine],
            'actual' => [$totalQtyCutting, $totalQtyCrimping, $totalQtyLine],
            'reject' => [$totalRejectCutting, $totalRejectCrimping, $totalRejectLine],
        ];
        
        return view('manager-dashboard', compact(
            'totalCutting', 'totalCrimping', 'totalLine',
            'totalQtyCutting', 'totalQtyCrimping', 'totalQtyLine',
            'totalTargetCutting', 'totalTargetCrimping', 'totalTargetLine',
            'totalRejectCutting', 'totalRejectCrimping', 'totalRejectLine',
            'persenCutting', 'persenCrimping', 'persenLine',
            'rejectRate', 'produksiHariIni',
            'mesinStats', 'mesinBermasalah',
            'recentActivities', 'chartData'
        ));
    }

    // ============ EXPORT PDF MANAGER ============
    public function exportPdf()
    {
        // Data Cutting
        $totalCutting = ProduksiCutting::count();
        $totalQtyCutting = ProduksiCutting::sum('qty');
        $totalTargetCutting = ProduksiCutting::sum('target');
        $totalRejectCutting = ProduksiCutting::sum('reject');
        $dataCutting = ProduksiCutting::latest()->take(50)->get();
        
        // Data Crimping
        $totalCrimping = ProduksiCrimping::count();
        $totalQtyCrimping = ProduksiCrimping::sum('qty');
        $totalTargetCrimping = ProduksiCrimping::sum('target');
        $totalRejectCrimping = ProduksiCrimping::sum('reject');
        $dataCrimping = ProduksiCrimping::latest()->take(50)->get();
        
        // Data Line
        $totalLine = ProduksiLine::count();
        $totalQtyLine = ProduksiLine::sum('qty');
        $totalTargetLine = ProduksiLine::sum('target');
        $totalRejectLine = ProduksiLine::sum('reject');
        $dataLine = ProduksiLine::latest()->take(50)->get();
        
        $pdf = Pdf::loadView('exports.manager-laporan-pdf', compact(
            'totalCutting', 'totalCrimping', 'totalLine',
            'totalQtyCutting', 'totalQtyCrimping', 'totalQtyLine',
            'totalTargetCutting', 'totalTargetCrimping', 'totalTargetLine',
            'totalRejectCutting', 'totalRejectCrimping', 'totalRejectLine',
            'dataCutting', 'dataCrimping', 'dataLine'
        ));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('laporan_manager_' . date('Y-m-d') . '.pdf');
    }
}