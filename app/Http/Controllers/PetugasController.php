<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    // Menyetujui Peminjaman (Mengubah status & mengurangi stok alat)
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menolak Peminjaman (Menghapus pengajuan agar siswa bisa mengajukan ulang)
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Pastikan statusnya memang masih diajukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan daftar peminjaman yang sedang berjalan (perlu dipantau/dikembalikan)
    public function indexPengembalian(Request $request)
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'dipinjam')
            ->latest()
            ->get();

        // Tandai mana yang telat (lewat dari tanggal rencana kembali)
        $peminjamans->each(function ($item) {
            $item->is_telat = \Carbon\Carbon::parse($item->tgl_kembali_plan)->isPast();
        });

        return view('petugas.pengembalian.index', compact('peminjamans'));
    }

    // Memproses pengembalian alat (mengubah status & mengembalikan stok)
    public function prosesPengembalian(Request $request, $id)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string|max:255',
            'denda' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);

            // Catat data pengembalian
            Pengembalian::create([
                'peminjaman_id'      => $peminjaman->id,
                'kondisi_alat'       => $request->kondisi_kembali,
                'denda'              => $request->denda ?? 0,
                'tgl_kembali_aktual' => now(),
            ]);

            // Update status peminjaman jadi selesai
            $peminjaman->update(['status' => 'selesai']);

            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan halaman laporan peminjaman dengan filter
    public function laporan(Request $request)
    {
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $status = $request->input('status');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->when($tanggalAwal, function ($query) use ($tanggalAwal) {
                return $query->whereDate('tgl_pinjam', '>=', $tanggalAwal);
            })
            ->when($tanggalAkhir, function ($query) use ($tanggalAkhir) {
                return $query->whereDate('tgl_pinjam', '<=', $tanggalAkhir);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest('tgl_pinjam')
            ->get();

        return view('petugas.laporan.index', compact('peminjamans', 'tanggalAwal', 'tanggalAkhir', 'status'));
    }

    // Menampilkan versi cetak (halaman polos untuk di-print lewat browser)
    public function cetakLaporan(Request $request)
    {
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $status = $request->input('status');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->when($tanggalAwal, function ($query) use ($tanggalAwal) {
                return $query->whereDate('tgl_pinjam', '>=', $tanggalAwal);
            })
            ->when($tanggalAkhir, function ($query) use ($tanggalAkhir) {
                return $query->whereDate('tgl_pinjam', '<=', $tanggalAkhir);
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest('tgl_pinjam')
            ->get();

        return view('petugas.laporan.cetak', compact('peminjamans', 'tanggalAwal', 'tanggalAkhir', 'status'));
    }
}