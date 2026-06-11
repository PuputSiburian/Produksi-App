<?php

namespace App\Http\Controllers;

use App\Models\produksi;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $stasiun = $request->get('stasiun');
        
        $query = produksi::query();
        
        if ($search) {
            $query->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%");
        }
        
        if ($stasiun && $stasiun != 'semua') {
            $query->where('stasiun', $stasiun);
        }
        
        $produk = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('produk.index', compact('produk', 'search', 'stasiun'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => 'required|unique:produk,kode_produk',
            'nama_produk' => 'required|string|max:100',
            'part_number' => 'nullable|string|max:50',
            'stasiun' => 'required|in:Cutting,Crimping,Line',
            'target_standar' => 'nullable|integer',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Aktif,Tidak Aktif'
        ]);
        
        produksi::create($request->all());
        
        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $produk = produksi::findOrFail($id);
        return view('produk.show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $produk = produksi::findOrFail($id);
        return view('produk.edit', compact('produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $produk = produksi::findOrFail($id);
        
        $request->validate([
            'kode_produk' => 'required|unique:produk,kode_produk,' . $id,
            'nama_produk' => 'required|string|max:100',
            'part_number' => 'nullable|string|max:50',
            'stasiun' => 'required|in:Cutting,Crimping,Line',
            'target_standar' => 'nullable|integer',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:Aktif,Tidak Aktif'
        ]);
        
        $produk->update($request->all());
        
        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $produk = produksi::findOrFail($id);
        $produk->delete();
        
        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}