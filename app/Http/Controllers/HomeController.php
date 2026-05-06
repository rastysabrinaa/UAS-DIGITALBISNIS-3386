<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Event;

class HomeController extends Controller
{
    public function index(Request $request) {
        $categories = Category::all();

        $query = Event::with('category')->where('date','>=',now())->orderBy('date','asc');

        if ($request->has('category') && $request->category != "") {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $events = $query->get();

        return view('welcome', compact('events', 'categories'));
    }
}
