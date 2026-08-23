<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partners;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request) {
        $query = \App\Models\Partners::query();
        
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }
        
        $partners = $query->paginate(10);
        
        return view('admin.partners.index', compact('partners'));

    }

    public function create() {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'required|string',
        ]);

        \App\Models\Partners::create($data);

        \Illuminate\Support\Facades\Cache::forget('partners');

        return redirect()->route('admin.partners.index')->with('success', 'Data Partner berhasil ditambahkan.');
    }

    public function edit(\App\Models\Partners $partner) {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, \App\Models\Partners $partner) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'required|string',
        ]);

        $partner->update($data);

        \Illuminate\Support\Facades\Cache::forget('partners');

        return redirect()->route('admin.partners.index')->with('success', 'Data Partner berhasil diperbarui.');
    }

    public function destroy(\App\Models\Partners $partner) {
        $partner->delete();

        \Illuminate\Support\Facades\Cache::forget('partners');

        return redirect()->route('admin.partners.index')->with('success', 'Data Partner berhasil dihapus.');
    }
}
