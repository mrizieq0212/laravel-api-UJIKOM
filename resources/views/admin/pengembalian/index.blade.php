{{-- resources/views/admin/pengembalian/index.blade.php --}}
@extends('layouts.app')

@section('header-title', 'Kelola Pengembalian')

@section('content')

@if (session('success'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
        {{ session('error') }}
    </div>
@endif

{{-- ================= MENUNGGU PENGEMBALIAN ================= --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="px-5 py-4 border-b border-gray-100 font-semibold text-gray-800">
        Menunggu Pengembalian
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-5 py-3 font-medium">ID</th>
                    <th class="px-5 py-3 font-medium">Peminjam</th>
                    <th class="px-5 py-3 font-medium">Tgl Pinjam</th>
                    <th class="px-5 py-3 font-medium">Rencana Kembali</th>
                    <th class="px-5 py-3 font-medium">Status Waktu</th>
                    <th class="px-5 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($sedangDipinjam as $p)
                    @php
                        $telat = now()->gt($p->tgl_kembali_plan);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-700">#{{ $p->id }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $p->user?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $p->tgl_pinjam->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $p->tgl_kembali_plan->format('d/m/Y') }}</td>
                        <td class="px-5 py-3">
                            @if ($telat)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Terlambat</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Tepat Waktu</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button type="button"
                                    onclick="document.getElementById('modalProses{{ $p->id }}').classList.remove('hidden')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                                Proses Pengembalian
                            </button>
                        </td>
                    </tr>

                    {{-- Modal Proses Pengembalian --}}
                    <div id="modalProses{{ $p->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
                        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
                            <form action="{{ route('admin.pengembalian.store', $p->id) }}" method="POST">
                                @csrf
                                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                                    <h5 class="font-semibold text-gray-800">Proses Pengembalian #{{ $p->id }}</h5>
                                    <button type="button"
                                            onclick="document.getElementById('modalProses{{ $p->id }}').classList.add('hidden')"
                                            class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                                </div>
                                <div class="px-5 py-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Kembali</label>
                                        <input type="date" name="tgl_kembali"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               value="{{ now()->format('Y-m-d') }}"
                                               max="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600 mb-1">Kondisi Alat</label>
                                        <select name="kondisi_kembali" required
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Pilih Kondisi --</option>
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                            <option value="Hilang">Hilang</option>
                                        </select>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Rencana kembali: {{ $p->tgl_kembali_plan->format('d/m/Y') }}.
                                        Denda dihitung otomatis Rp5.000/hari keterlambatan.
                                    </p>
                                </div>
                                <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
                                    <button type="button"
                                            onclick="document.getElementById('modalProses{{ $p->id }}').classList.add('hidden')"
                                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition">
                                        Batal
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-8">
                            Tidak ada peminjaman yang sedang menunggu pengembalian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ================= RIWAYAT PENGEMBALIAN ================= --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="px-5 py-4 border-b border-gray-100 font-semibold text-gray-800">
        Riwayat Pengembalian
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-5 py-3 font-medium">ID Kembali</th>
                    <th class="px-5 py-3 font-medium">Peminjaman</th>
                    <th class="px-5 py-3 font-medium">Peminjam</th>
                    <th class="px-5 py-3 font-medium">Tgl Kembali</th>
                    <th class="px-5 py-3 font-medium">Kondisi</th>
                    <th class="px-5 py-3 font-medium">Denda</th>
                    <th class="px-5 py-3 font-medium">Petugas</th>
                    <th class="px-5 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($riwayat as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-700">#{{ $r->id }}</td>
                        <td class="px-5 py-3 text-gray-700">#{{ $r->peminjaman_id }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $r->peminjaman?->user?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $r->tgl_kembali->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $r->kondisi_kembali }}</td>
                        <td class="px-5 py-3">
                            @if ($r->denda > 0)
                                <span class="text-red-600 font-semibold">
                                    Rp{{ number_format($r->denda, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-green-600">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-700">{{ $r->petugas?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-right">
                            <form action="{{ route('admin.pengembalian.destroy', $r->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Batalkan data pengembalian ini?');"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 border border-red-200 hover:bg-red-50 text-xs font-medium px-3 py-1.5 rounded-lg transition">
                                    Batalkan
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-gray-400 py-8">
                            Belum ada riwayat pengembalian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $riwayat->links() }}
    </div>
</div>
@endsection