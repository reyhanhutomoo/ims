<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    
</head>
<body>
    <h1 style="text-align: center;">Laporan Absen {{ $date ? $date : "Semua Riwayat" }}</h1>
    <table>
        <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Nama</th>
                <th>Divisi</th>
                <th>IP Masuk</th>
                <th>Waktu Masuk</th>
                <th>Lokasi Masuk</th>
                <th>Validasi Masuk</th>
                <th>IP Keluar</th>
                <th>Waktu Keluar</th>
                <th>Lokasi Keluar</th>
                <th>Validasi Masuk</th>
                <th>Status Absensi</th>
                <th>Keterangan Absensi</th>
                <th>Laporan Harian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $attendance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $attendance->employee->nama }}</td>
                <td>{{ $attendance->employee->division->nama }}</td>
                <td>{{ $attendance->ip_masuk }}</td>
                <td>{{ $attendance->waktu_masuk }}</td>
                <td>{{ $attendance->lokasi_masuk }}</td>
                <td>{{ $attendance->status_masuk }}</td>
                <td>{{ $attendance->ip_keluar }}</td>
                <td>{{ $attendance->waktu_keluar }}</td>
                <td>{{ $attendance->lokasi_keluar }}</td>
                <td>{{ $attendance->status_keluar }}</td>
                <td><span style="font-family: Arial, Helvetica, sans-serif">{{ $attendance->registered }}</span></td>
                    <?php if($attendance->time>=7 && $attendance->time<=9) { ?>
                        <td><span style="font-family: Arial, Helvetica, sans-serif">Hadir Tepat Waktu</span></td>
                    <?php } elseif ($attendance->time>9 && $attendance->time<=15) {
                        ?><td><span style="font-family: Arial, Helvetica, sans-serif">Hadir Terlambat</span></td><?php
                    } else {
                        ?><td><span style="font-family: Arial, Helvetica, sans-serif">Absensi Tidak Valid</span></td><?php 
                    } ?>
                    <td>{{ $attendance->laporan_harian }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
