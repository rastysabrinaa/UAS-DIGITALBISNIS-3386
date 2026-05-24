@extends('layouts.admin')
@section('title', 'Kelola Parner - Admin')
@section('page_title', 'Kelola Partner')
@section('page_subtitle', 'Kelola keseluruhan data partner disini')

@section('content')

<div class="flex justify-between items-center mb-4">
    <form action="{{ route('admin.partners.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari partner..." class="px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button type="submit" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-300 transition">Cari</button>
    </form>
    <a href="{{ route('admin.partners.create') }}" class="inline-block px-6 py-3
bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100
hover:bg-indigo-700 active:scale-95 transition">
        + Tambah Partner Baru
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
            <tr>
                <th class="px-8 py-4 w-16">No</th>
                <th class="px-8 py-4">Nama Partner</th>
                <th class="px-8 py-4">Logo URL</th>
                <th class="px-8 py-4">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y border-t">
        @foreach($partners as $partner)
        <tr class="hover:bg-slate-50/50 transition">
            <td class="px-8 py-6 font-bold text-slate-400"> {{ $partner->id }} </td>
            <td class="px-8 py-6"> {{ $partner->name }} </td>
            <td class="px-8 py-6"> {{ $partner->logo_url }} </td>
            <td class="px-8 py-6 flex gap-2">
                <a href="{{ route('admin.partners.edit', $partner->id) }}" class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-bold hover:bg-yellow-200 transition">Edit</a>
                <form action="{{ route('admin.partners.destroy', $partner->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus partner ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-sm font-bold hover:bg-red-200 transition">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $partners->links() }}
</div>

@endsection