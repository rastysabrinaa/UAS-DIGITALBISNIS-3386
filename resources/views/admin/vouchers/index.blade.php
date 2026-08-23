@extends('layouts.admin')
@section('title', 'Kelola Voucher')
@section('page_title', 'Kelola Voucher')
@section('page_subtitle', 'Berikan diskon untuk menarik lebih banyak pembeli (Dynamic Pricing).')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Tambah Voucher -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-[2rem] border border-slate-100 p-8 shadow-sm">
            <h3 class="font-black text-xl mb-6">Buat Voucher Baru</h3>
            <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kode Voucher</label>
                    <input type="text" name="code" placeholder="Cth: DISKON50" required class="w-full px-5 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 outline-none uppercase font-bold text-slate-800">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Persentase Diskon (%)</label>
                    <input type="number" name="discount_percent" min="1" max="100" placeholder="50" required class="w-full px-5 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Berlaku Untuk</label>
                    <select name="event_id" class="w-full px-5 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 outline-none">
                        <option value="">Semua Event Saya</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Batas Waktu (Opsional)</label>
                    <input type="datetime-local" name="valid_until" class="w-full px-5 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 outline-none text-sm">
                </div>
                <button type="submit" class="w-full py-4 mt-4 bg-indigo-600 text-white rounded-xl font-black shadow-lg shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition">
                    + Simpan Voucher
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Voucher -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Diskon</th>
                            <th class="px-6 py-4">Event Terkait</th>
                            <th class="px-6 py-4">Kedaluwarsa</th>
                            <th class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-t">
                        @forelse($vouchers as $v)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-5 font-black text-indigo-600 uppercase">{{ $v->code }}</td>
                            <td class="px-6 py-5 font-bold">{{ $v->discount_percent }}%</td>
                            <td class="px-6 py-5 text-sm text-slate-500">
                                {{ $v->event ? $v->event->title : 'Semua Event' }}
                            </td>
                            <td class="px-6 py-5 text-sm {{ $v->valid_until && \Carbon\Carbon::parse($v->valid_until)->isPast() ? 'text-red-500 font-bold' : 'text-slate-500' }}">
                                {{ $v->valid_until ? \Carbon\Carbon::parse($v->valid_until)->format('d M Y, H:i') : 'Tanpa Batas' }}
                            </td>
                            <td class="px-6 py-5">
                                <form action="{{ route('admin.vouchers.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Hapus voucher ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 font-medium">Belum ada voucher yang dibuat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-slate-50/50 border-t">
                {{ $vouchers->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
