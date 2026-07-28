<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    // Halaman daftar kelayakan organizer
    public function indexOrganizers()
    {
        $organizers = User::where('role', 'organizer')->latest()->get();
        return view('admin.organizers.index', compact('organizers'));
    }

    // Aksi Persetujuan / Verifikasi Kelayakan
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);
        
        $organizer = User::where('role', 'organizer')->findOrFail($id);
        $organizer->update([
            'status' => $request->status
        ]);

        $namaOrganisasi = $organizer->organization_name ?? $organizer->name;

        return back()->with('success', 'Status kelayakan ' . $namaOrganisasi . ' berhasil diperbarui menjadi ' . ucfirst($request->status) . '!');
    }
}