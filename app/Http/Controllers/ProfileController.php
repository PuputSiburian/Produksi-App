<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ProduksiCutting;
use App\Models\ProduksiCrimping;
use App\Models\ProduksiLine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // 🔥 PERBAIKI: Admin dan Manager melihat SEMUA data
        if ($user->isAdmin() || $user->isManager()) {
            $totalCutting = ProduksiCutting::count();
            $totalCrimping = ProduksiCrimping::count();
            $totalLine = ProduksiLine::count();
        } else {
            // Operator hanya melihat datanya sendiri
            $totalCutting = ProduksiCutting::where('user_id', $user->id)->count();
            $totalCrimping = ProduksiCrimping::where('user_id', $user->id)->count();
            $totalLine = ProduksiLine::where('user_id', $user->id)->count();
        }
        
        return view('profile.edit', [
            'user' => $user,
            'totalCutting' => $totalCutting,
            'totalCrimping' => $totalCrimping,
            'totalLine' => $totalLine,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'jabatan' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update data user
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->jabatan = $validated['jabatan'] ?? $user->jabatan;
        $user->divisi = $validated['divisi'] ?? $user->divisi;

        // Handle upload foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::exists('public/' . $user->foto)) {
                Storage::delete('public/' . $user->foto);
            }
            
            // Upload foto baru
            $path = $request->file('foto')->store('profile-photos', 'public');
            $user->foto = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile berhasil diupdate!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}