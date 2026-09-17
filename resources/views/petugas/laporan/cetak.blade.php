<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Peminjaman</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        p.sub { font-size: 12px; color: #555; margin-top: 0; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f3f3f3; text-transform: uppercase; font-size: 11px; }
        ul { margin: 0; padding-left: 16px; }
        .status { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .status-diajukan { background: #fef3c7; color: #92400e; }
        .status-dipinjam { background: #dbeafe; color: #1e40af; }
        .status-selesai { background: #d1fae5; color: #065f46; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Print / Simpan sebagai PDF</button>
    </div>

    <h1>Laporan Peminjaman Alat</h1>
    <p class="sub">
        Dicetak: {{ now()->translatedFormat('l, d F Y, H:i') }}
        @if($tanggalAwal || $tanggalAkhir)
            &mdash; Periode: {{ $tanggalAwal ?? '...' }} s/d {{ $tanggalAkhir ?? '...' }}
        @endif
        @if($status)
            &mdash; Status: {{ ucfirst($status) }}
        @endif
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peminjam</th>
                <th>Alat yang Dipinjam</th>
                <th>Tgl Pinjam</th>
                <th>Rencana Kembali</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjamans as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->user->name ?? 'User Dihapus' }}</td>
                    <td>
                        <ul>
                            @foreach($item->detailPinjams as $detail)
                                <li>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $detail->jumlah }} pcs)</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $item->tgl_pinjam }}</td>
                    <td>{{ $item->tgl_kembali_plan }}</td>
                    <td>
                        @php
                            $label = $item->status == 'selesai' ? 'Dikembalikan' : ucfirst($item->status);
                        @endphp
                        <span class="status status-{{ $item->status }}">{{ $label }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Tidak ada data untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>