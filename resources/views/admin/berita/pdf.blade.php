<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Berita</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 4px;
        }

        .generated-at {
            text-align: center;
            color: #555;
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <h1>Data Berita</h1>
    <p class="generated-at">Diekspor pada {{ now()->format('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 20%">Judul</th>
                <th style="width: 14%">Kategori</th>
                <th>Isi Berita</th>
                <th style="width: 12%">Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($beritas as $berita)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $berita->judul }}</td>
                    <td>{{ $berita->kategori->nama_kategori }}</td>
                    <td>{{ $berita->isi }}</td>
                    <td>{{ $berita->created_at->format('d-m-Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center">Belum ada data berita.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
