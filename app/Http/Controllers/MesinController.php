<?php

namespace App\Http\Controllers;

use App\Models\Mesin;
use Illuminate\Http\Request;

class MesinController extends Controller
{
    public function index()
    {
        $mesins = Mesin::latest()->paginate(10);
        $statistik = [
            'total' => Mesin::count(),
            'beroperasi' => Mesin::where('status', 'Beroperasi')->count(),
            'perbaikan' => Mesin::where('status', 'Perbaikan')->count(),
            'rusak' => Mesin::where('status', 'Rusak')->count(),
            'maintenance' => Mesin::where('status', 'Maintenance')->count(),
            'idle' => Mesin::where('status', 'Idle')->count(),
            'gangguan_aktif' => Mesin::whereIn('status', ['Perbaikan', 'Rusak'])->count(),
        ];
        
        return view('mesin.index', compact('mesins', 'statistik'));
    }

    public function create()
    {
        return view('mesin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mesin' => 'required|unique:mesins|max:50',
            'nama_mesin' => 'required|max:100',
            'jenis_mesin' => 'nullable|max:50',
            'lokasi' => 'nullable|max:100',
            'status' => 'required|in:Beroperasi,Perbaikan,Rusak,Maintenance,Idle',
            'gangguan' => 'nullable|string',
            'tanggal_gangguan' => 'nullable|date',
            'teknisi' => 'nullable|max:100',
            'durasi_gangguan' => 'nullable|integer|min:0',
            'prioritas' => 'nullable|in:Rendah,Sedang,Tinggi,Darurat',
            'keterangan' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        Mesin::create($validated);

        return redirect()->route('mesin.index')
            ->with('success', 'Data mesin berhasil ditambahkan');
    }

    public function edit(Mesin $mesin)
    {
        return view('mesin.edit', compact('mesin'));
    }

    public function update(Request $request, Mesin $mesin)
    {
        $validated = $request->validate([
            'kode_mesin' => 'required|unique:mesins,kode_mesin,' . $mesin->id . '|max:50',
            'nama_mesin' => 'required|max:100',
            'jenis_mesin' => 'nullable|max:50',
            'lokasi' => 'nullable|max:100',
            'status' => 'required|in:Beroperasi,Perbaikan,Rusak,Maintenance,Idle',
            'gangguan' => 'nullable|string',
            'tanggal_gangguan' => 'nullable|date',
            'teknisi' => 'nullable|max:100',
            'durasi_gangguan' => 'nullable|integer|min:0',
            'prioritas' => 'nullable|in:Rendah,Sedang,Tinggi,Darurat',
            'keterangan' => 'nullable|string',
        ]);

        $mesin->update($validated);

        return redirect()->route('mesin.index')
            ->with('success', 'Data mesin berhasil diupdate');
    }

    public function destroy(Mesin $mesin)
    {
        $mesin->delete();
        return redirect()->route('mesin.index')
            ->with('success', 'Data mesin berhasil dihapus');
    }

    public function show(Mesin $mesin)
    {
        return view('mesin.show', compact('mesin'));
    }
}