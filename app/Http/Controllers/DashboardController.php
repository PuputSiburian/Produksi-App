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
        $user = auth()->user();
        
        // ============================================================
        // 🔥 CEK ROLE USER
        // ============================================================
        $isAdminOrManager = ($user->isAdmin() || $user->isManager());
        $isOperator = $user->isOperator();
        
        // ============================================================
        // 🔥 STATISTIK PRODUKSI (Filter berdasarkan role)
        // ============================================================
        if ($isAdminOrManager) {
            // Admin & Manager: Lihat SEMUA data
            $cutting = ProduksiCutting::count();
            $crimping = ProduksiCrimping::count();
            $line = ProduksiLine::count();
            
            $hari_ini = ProduksiCutting::whereDate('tanggal', Carbon::today())->count() +
                        ProduksiCrimping::whereDate('tanggal', Carbon::today())->count() +
                        ProduksiLine::whereDate('tanggal', Carbon::today())->count();
            
            // Total QTY & Reject (SEMUA)
            $cuttingQty = ProduksiCutting::sum('qty');
            $cuttingReject = ProduksiCutting::sum('reject');
            $crimpingQty = ProduksiCrimping::sum('qty');
            $crimpingReject = ProduksiCrimping::sum('reject');
            $lineQty = ProduksiLine::sum('qty');
            $lineReject = ProduksiLine::sum('reject');
            
            // Data Sample (SEMUA)
            $dataSample = [
                'cutting' => ProduksiCutting::latest()->take(5)->get(),
                'crimping' => ProduksiCrimping::latest()->take(5)->get(),
                'line' => ProduksiLine::latest()->take(5)->get(),
            ];
            
        } else if ($isOperator) {
            // Operator: Hanya lihat datanya sendiri
            $cutting = ProduksiCutting::where('user_id', $user->id)->count();
            $crimping = ProduksiCrimping::where('user_id', $user->id)->count();
            $line = ProduksiLine::where('user_id', $user->id)->count();
            
            $hari_ini = ProduksiCutting::where('user_id', $user->id)->whereDate('tanggal', Carbon::today())->count() +
                        ProduksiCrimping::where('user_id', $user->id)->whereDate('tanggal', Carbon::today())->count() +
                        ProduksiLine::where('user_id', $user->id)->whereDate('tanggal', Carbon::today())->count();
            
            // Total QTY & Reject (DATA SENDIRI)
            $cuttingQty = ProduksiCutting::where('user_id', $user->id)->sum('qty');
            $cuttingReject = ProduksiCutting::where('user_id', $user->id)->sum('reject');
            $crimpingQty = ProduksiCrimping::where('user_id', $user->id)->sum('qty');
            $crimpingReject = ProduksiCrimping::where('user_id', $user->id)->sum('reject');
            $lineQty = ProduksiLine::where('user_id', $user->id)->sum('qty');
            $lineReject = ProduksiLine::where('user_id', $user->id)->sum('reject');
            
            // Data Sample (DATA SENDIRI)
            $dataSample = [
                'cutting' => ProduksiCutting::where('user_id', $user->id)->latest()->take(5)->get(),
                'crimping' => ProduksiCrimping::where('user_id', $user->id)->latest()->take(5)->get(),
                'line' => ProduksiLine::where('user_id', $user->id)->latest()->take(5)->get(),
            ];
            
        } else {
            // Default: kosong
            $cutting = 0;
            $crimping = 0;
            $line = 0;
            $hari_ini = 0;
            $cuttingQty = 0;
            $cuttingReject = 0;
            $crimpingQty = 0;
            $crimpingReject = 0;
            $lineQty = 0;
            $lineReject = 0;
            $dataSample = [
                'cutting' => collect(),
                'crimping' => collect(),
                'line' => collect(),
            ];
        }
        
        // ============================================================
        // 🔥 STATISTIK REJECT (Berlaku untuk semua role)
        // ============================================================
        $totalQty = $cuttingQty + $crimpingQty + $lineQty;
        $totalReject = $cuttingReject + $crimpingReject + $lineReject;
        
        $rejectRate = $totalQty > 0 ? round(($totalReject / $totalQty) * 100, 2) : 0;
        
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

        // ============================================================
        // 🔥 STATISTIK MESIN (SEMUA USER BISA LIHAT)
        // ============================================================
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

        $mesinBermasalah = Mesin::whereIn('status', ['Perbaikan', 'Rusak'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // ============================================================
        // 🔥 AKTIVITAS TERBARU (Filter berdasarkan role)
        // ============================================================
        $recentActivities = collect();
        
        if ($isAdminOrManager) {
            // Admin & Manager: Lihat SEMUA aktivitas
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
            
            foreach(ProduksiCrimping::latest('tanggal')->take(3)->get() as $item) {
                $recentActivities->push((object)[
                    'waktu' => Carbon::parse($item->tanggal),
                    'stasiun' => 'CRIMPING',
                    'operator' => $item->nama_operator,
                    'produk' => $item->produk,
                    'part_number' => $item->part_number,
                    'target' => $item->target,
                    'actual' => $item->qty,
                    'reject' => $item->reject ?? 0,
                    'status' => ($item->reject ?? 0) > 0 ? 'Dengan Reject' : 'Selesai',
                    'badge_warna' => ($item->reject ?? 0) > 0 ? 'warning' : 'danger',
                    'type' => 'crimping'
                ]);
            }
            
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
            
        } else if ($isOperator) {
            // Operator: Hanya lihat aktivitas sendiri
            foreach(ProduksiCutting::where('user_id', $user->id)->latest('tanggal')->take(3)->get() as $item) {
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
            
            foreach(ProduksiCrimping::where('user_id', $user->id)->latest('tanggal')->take(3)->get() as $item) {
                $recentActivities->push((object)[
                    'waktu' => Carbon::parse($item->tanggal),
                    'stasiun' => 'CRIMPING',
                    'operator' => $item->nama_operator,
                    'produk' => $item->produk,
                    'part_number' => $item->part_number,
                    'target' => $item->target,
                    'actual' => $item->qty,
                    'reject' => $item->reject ?? 0,
                    'status' => ($item->reject ?? 0) > 0 ? 'Dengan Reject' : 'Selesai',
                    'badge_warna' => ($item->reject ?? 0) > 0 ? 'warning' : 'danger',
                    'type' => 'crimping'
                ]);
            }
            
            foreach(ProduksiLine::where('user_id', $user->id)->latest('tanggal')->take(3)->get() as $item) {
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
        }
        
        $recentActivities = $recentActivities->sortByDesc('waktu')->take(10)->values();

        // ============================================================
        // 🔥 CHART DATA
        // ============================================================
        $chartData = [
            'cutting' => $cuttingQty,
            'crimping' => $crimpingQty,
            'line' => $lineQty,
        ];

        return view('dashboard', compact(
            'cutting', 'crimping', 'line', 'hari_ini',
            'rejectStats', 'rejectRate',
            'mesinStats', 'mesinBermasalah', 'totalMesin',
            'recentActivities', 'chartData', 'dataSample'
        ));
    }
}