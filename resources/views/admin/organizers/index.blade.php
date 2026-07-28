@extends('layouts.admin')

@section('page_title', 'Persetujuan & Kelayakan Organizer')
@section('page_subtitle', 'Verifikasi dan kelola status hak akses kepanitiaan/HIMA pada platform.')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daftar Penyelenggara Event</h3>
                <p class="text-xs text-slate-500 font-medium">Total terdaftar: {{ $organizers->count() }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-[11px] font-bold uppercase tracking-wider border-b border-slate-100">
                        <th class="py-4 px-6">Nama / Organisasi</th>
                        <th class="py-4 px-6">Email Profil</th>
                        <th class="py-4 px-6">Tanggal Daftar</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-center">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                    @forelse($organizers as $organizer)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700 font-bold uppercase">
                                        {{ substr($organizer->organization_name ?? $organizer->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">
                                            {{ $organizer->organization_name ?? $organizer->name }}
                                        </p>
                                        <p class="text-xs text-slate-400">PJ: {{ $organizer->name }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-6 text-slate-600">
                                {{ $organizer->email }}
                            </td>

                            <td class="py-4 px-6 text-slate-500 text-xs">
                                {{ $organizer->created_at ? $organizer->created_at->format('d M Y, H:i') : '-' }}
                            </td>

                            <td class="py-4 px-6 text-center">
                                @if($organizer->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Disetujui
                                    </span>
                                @elseif($organizer->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Form Setujui --}}
                                    <form action="{{ route('admin.organizers.updateStatus', $organizer->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" 
                                                onclick="return confirm('Setujui akun kepanitiaan ini?')"
                                                class="px-3 py-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition shadow-sm flex items-center gap-1 {{ $organizer->status === 'approved' ? 'opacity-40 cursor-not-allowed' : '' }}"
                                                {{ $organizer->status === 'approved' ? 'disabled' : '' }}>
                                            Setujui
                                        </button>
                                    </form>

                                    {{-- Form Tolak --}}
                                    <form action="{{ route('admin.organizers.updateStatus', $organizer->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" 
                                                onclick="return confirm('Tolak akun kepanitiaan ini?')"
                                                class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 border border-slate-200 rounded-lg transition flex items-center gap-1 {{ $organizer->status === 'rejected' ? 'opacity-40 cursor-not-allowed' : '' }}"
                                                {{ $organizer->status === 'rejected' ? 'disabled' : '' }}>
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 font-medium">
                                Belum ada data pendaftaran organizer.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection