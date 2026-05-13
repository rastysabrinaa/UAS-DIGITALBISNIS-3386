<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index() {
        $partners = \App\Models\Partners::paginate(10);
        
        return view('admin.partners.index', compact('partners'));

    }
}
