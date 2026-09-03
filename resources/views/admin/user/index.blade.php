@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', 'Manajemen Pengguna Sistem')

@section('content')

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        <!-- Header -->
        <div class="p-5 border-b border-gray-200 bg-gray-50">

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">

                <h3 class="text-lg font-bold text-gray-800">
                    Daftar Pengguna Sistem
                </h3>

                <div class="flex items-center gap-3 w-full md:w-auto">

                    <!-- Form Search -->
                    <form action="{{ route('admin.user.index') }}" method="GET" class="flex w-full md:w-80">

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama, email, role..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                        <button
                            type="submit"
                            class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                            Cari
                        </button>

                        @if(request('search'))
                            <a
                                href="{{ route('admin.user.index') }}"
                                class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition"
                                title="Reset Pencarian">
                                Reset
                            </a>
                        @endif

                    </form>

                    <!-- Tombol Tambah User -->
                    <a href="{{ route('admin.user.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                        + Tambah User
                    </a>

                </div>

            </div>

        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Nama</th>
                        <th class="py-3 px-4 border-b">Email</th>
                        <th class="py-3 px-4 border-b">Role</th>
                        <th class="py-3 px-4 border-b">No. HP</th>
                        <th class="py-3 px-4 border-b text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">

                    @forelse($users as $user)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="py-3 px-4 border-b font-medium">
                                {{ $user->name }}
                            </td>

                            <td class="py-3 px-4 border-b">
                                {{ $user->email }}
                            </td>

                            <td class="py-3 px-4 border-b">

                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @if($user->role == 'admin')
                                        bg-purple-100 text-purple-700
                                    @elseif($user->role == 'petugas')
                                        bg-blue-100 text-blue-700
                                    @else
                                        bg-green-100 text-green-700
                                    @endif">

                                    {{ ucfirst($user->role) }}

                                </span>

                            </td>

                            <td class="py-3 px-4 border-b">
                                {{ $user->no_hp ?? '-' }}
                            </td>

                            <td class="py-3 px-4 border-b">

                                <div class="flex gap-2">

                                    <a href="{{ route('admin.user.edit', $user->id) }}"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.user.destroy', $user->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus user ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="py-6 text-center text-gray-500">
                                Tidak ada data pengguna.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $users->links() }}
        </div>

    </div>

@endsection