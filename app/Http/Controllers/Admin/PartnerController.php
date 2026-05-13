<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partners;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index() {
        $partners = \App\Models\Partners::paginate(10);
        
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

        return redirect()->route('admin.partners.index')->with('success', 'Data Partner berhasil ditambahkan.');
    }
}
