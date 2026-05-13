@extends('layouts.admin')
@section('title', 'Kelola Parner - Admin')
@section('page_title', 'Kelola Partner')
@section('page_subtitle', 'Kelola keseluruhan data partner disini')

@section('content')

<div class="mb-4 text-right">
    <a href="{{ route('admin.partners.create') }}" class="inline-block px-6 py-3
bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100
hover:bg-indigo-700 active:scale-95 transition">
        + Tambah Partner Baru
    </a>
</div>

<table class="w-full text-left border-collapse">
    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
        <tr>
            <th class="px-8 py-4 w-16">No</th>
            <th class="px-8 py-4">Nama Partner</th>
            <th class="px-8 py-4">Logo URL</th>
        </tr>
    </thead>

    <tbody class="divide-y border-t">
    @foreach($partners as $partner)
    <tr class="hover:bg-slate-50/50 transition">
        <td class="px-8 py-6 font-bold text-slate-400"> {{ $partner->id }} </td>
        <td class="px-8 py-6"> {{ $partner->name }} </td>
        <td class="px-8 py-6"> {{ $partner->logo_url }} </td>
    </tr>
    @endforeach
    </tbody>
</table>

@endsection