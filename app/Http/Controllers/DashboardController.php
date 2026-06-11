<?php

namespace App\Http\Controllers;

use App\Models\ProduksiCutting;
use App\Models\ProduksiCrimping;
use App\Models\ProduksiLine;
use App\Models\Mesin;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ============ STATISTIK PRODUKSI ============
        $cutting = ProduksiCutting::count();
        $crimping = ProduksiCrimping::count();
        $line = ProduksiLine::count();
        
        $hari_ini = ProduksiCutting::whereDate('tanggal', Carbon::today())->count() +
                    ProduksiCrimping::whereDate('tanggal', Carbon::today())->count() +
                    ProduksiLine::whereDate('tanggal', Carbon::today())->count();

        // ============ STATISTIK REJECT ============
        // ProduksiCutting menggunakan 'qty'
        $cuttingQty = ProduksiCutting::sum('qty');
        $cuttingReject = ProduksiCutting::sum('reject');
        
        // ProduksiCrimping menggunakan 'qty' (BUKAN 'actual')
        $crimpingQty = ProduksiCrimping::sum('qty');  // <-- DIUBAH
        $crimpingReject = ProduksiCrimping::sum('reject');
        
        // ProduksiLine menggunakan 'qty'
        $lineQty = ProduksiLine::sum('qty');
        $lineReject = ProduksiLine::sum('reject');
        
        $totalQty = $cuttingQty + $crimpingQty + $lineQty;
        $totalReject = $cuttingReject + $crimpingReject + $lineReject;
        
        // Hitung reject rate
        $rejectRate = $totalQty > 0 ? round(($totalReject / $totalQty) * 100, 2) : 0;
        
        // Hitung persentase kontribusi reject per stasiun
        $rejectStats = [
            'cutting' => [
                'total' => $cuttingReject,
                'persen' => $totalReject > 0 ? round(($cuttingReject / $totalReject) * 100, 1) : 0,
            ],
            'crimping' => [
                'total' => $crimpingReject,
                'persen' => $totalReject > 0 ? round(($crimpingReject / $totalReject) * 100, 1) : 0,
            ],
            'line' => [
                'total' => $lineReject,
                'persen' => $totalReject > 0 ? round(($lineReject / $totalReject) * 100, 1) : 0,
            ],
            'total_semua' => $totalReject,
        ];

        // ============ STATISTIK MESIN ============
        $totalMesin = Mesin::count();
        $mesinStats = [
            'total' => $totalMesin,
            'beroperasi' => Mesin::where('status', 'Beroperasi')->count(),
            'perbaikan' => Mesin::where('status', 'Perbaikan')->count(),
            'rusak' => Mesin::where('status', 'Rusak')->count(),
            'maintenance' => Mesin::where('status', 'Maintenance')->count(),
            'idle' => Mesin::where('status', 'Idle')->count(),
            'gangguan_aktif' => Mesin::whereIn('status', ['Perbaikan', 'Rusak'])->count(),
        ];

        // Mesin dengan gangguan terbaru
        $mesinBermasalah = Mesin::whereIn('status', ['Perbaikan', 'Rusak'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // ============ AKTIVITAS TERBARU ============
        $recentActivities = collect();
        
        // Data Cutting - pakai 'qty'
        foreach(ProduksiCutting::latest('tanggal')->take(3)->get() as $item) {
            $recentActivities->push((object)[
                'waktu' => Carbon::parse($item->tanggal),
                'stasiun' => 'CUTTING',
                'operator' => $item->nama_operator,
                'produk' => $item->produk,
                'part_number' => $item->part_number,
                'target' => $item->target,
                'actual' => $item->qty,
                'reject' => $item->reject ?? 0,
                'status' => ($item->reject ?? 0) > 0 ? 'Dengan Reject' : 'Selesai',
                'badge_warna' => ($item->reject ?? 0) > 0 ? 'warning' : 'success',
                'type' => 'cutting'
            ]);
        }
        
        // Data Crimping - pakai 'qty' (BUKAN 'actual')
        foreach(ProduksiCrimping::latest('tanggal')->take(3)->get() as $item) {
            $recentActivities->push((object)[
                'waktu' => Carbon::parse($item->tanggal),
                'stasiun' => 'CRIMPING',
                'operator' => $item->nama_operator,
                'produk' => $item->produk,
                'part_number' => $item->part_number,
                'target' => $item->target,
                'actual' => $item->qty,  // <-- DIUBAH (sekarang pakai qty)
                'reject' => $item->reject ?? 0,
                'status' => ($item->reject ?? 0) > 0 ? 'Dengan Reject' : 'Selesai',
                'badge_warna' => ($item->reject ?? 0) > 0 ? 'warning' : 'danger',
                'type' => 'crimping'
            ]);
        }
        
        // Data Line - pakai 'qty'
        foreach(ProduksiLine::latest('tanggal')->take(3)->get() as $item) {
            $recentActivities->push((object)[
                'waktu' => Carbon::parse($item->tanggal),
                'stasiun' => $item->proses ?? 'LINE',
                'operator' => $item->nama_operator ?? $item->shift,
                'produk' => $item->produk,
                'part_number' => $item->part_number,
                'target' => $item->target,
                'actual' => $item->qty,
                'reject' => $item->reject ?? 0,
                'status' => ($item->reject ?? 0) > 0 ? 'Dengan Reject' : 'Selesai',
                'badge_warna' => ($item->reject ?? 0) > 0 ? 'warning' : 'primary',
                'type' => 'line'
            ]);
        }
        
        $recentActivities = $recentActivities->sortByDesc('waktu')->take(10)->values();

        // Chart Data
        $chartData = [
            'cutting' => $cuttingQty,
            'crimping' => $crimpingQty,
            'line' => $lineQty,
        ];

        // Data untuk ditampilkan di view
        $dataSample = [
            'cutting' => ProduksiCutting::latest()->take(5)->get(),
            'crimping' => ProduksiCrimping::latest()->take(5)->get(),
            'line' => ProduksiLine::latest()->take(5)->get(),
        ];

        return view('dashboard', compact(
            'cutting', 'crimping', 'line', 'hari_ini',
            'rejectStats', 'rejectRate',
            'mesinStats', 'mesinBermasalah', 'totalMesin',
            'recentActivities', 'chartData', 'dataSample'
        ));
    }
}