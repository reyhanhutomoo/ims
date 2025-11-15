@extends('layouts.app')        

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Riwayat Absensi</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('employee.index') }}">Dashboard Karyawan</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Riwayat Absensi
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 mx-auto">
                    <div class="card">
                        <div class="card-header">
                                <h5 class="text-center text-primary" style="text-align: center !important">Cari Absensi dengan rentang tanggal</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8 mx-auto text-center">
                                    <form action="{{ route('employee.attendance.index') }}" method="POST">
                                        @csrf
                                        <fieldset>
                                            <div class="form-group">
                                                <label for="">Rentang Tanggal</label>
                                                <input type="text" name="date_range" placeholder="Start Date" class="form-control text-center"
                                                id="date_range"
                                                >
                                                @error('date_range')
                                                <div class="ml-2 text-danger">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </fieldset>
                                        
                                            <input type="submit" name="" class="btn btn-primary" value="Submit">
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                            {{-- <div class="container">
                                <form action="{{ route('employee.attendance.index') }}" class="row" method="POST">
                                    @csrf
                                    <div class="col-sm-9 mb-2">

                                        <div class="input-group">
                                            <input type="text" name="date_range" placeholder="Start Date" class="form-control"
                                            id="date_range"
                                            >
                                        </div>
                                        @error('date_range')
                                        <div class="ml-2 text-danger">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-3 mb-2">
                                        <div class="input-group">
                                            <input type="submit" name="" class="btn btn-primary" value="Submit">
                                        </div>
                                    </div>
                                    
                                </form>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg mx-auto">
                    <div class="card card-primary">
                        <div class="card-header">
                            <div class="card-title text-center">
                                Absensi
                                @if ($filter)
                                    dari rentang
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($attendances->count())
                            <table class="table table-bordered table-hover" id="dataTable">
                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Tanggal Absensi</th>
                                        <th>Status Absensi</th>
                                        <th>Waktu Absensi</th>
                                        <th class="none">Laporan Harian</th>
                                        <th class="none">Riwayat Awal Absensi</th>
                                        <th class="none">Riwayat Akhir Absensi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($attendances as $index => $attendance)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        @if ($attendance)
                                            @if ($attendance->registered == 'Hadir')
                                            <td>{{ $attendance->created_at->format('d M Y') }}</td>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-success">Hadir</span> </h6></td>
                                            <?php if($attendance->time>=7 && $attendance->time<=9) { ?>
                                                <td><h6 class="text-center"><span class="badge badge-pill badge-success">Hadir Tepat Waktu</span></h6></td>
                                            <?php } elseif ($attendance->time>9 && $attendance->time<=15) {
                                                ?><td><h6 class="text-center"><span class="badge badge-pill badge-warning">Hadir Terlambat</span></h6></td><?php
                                            } else {
                                                ?><td><h6 class="text-center"><span class="badge badge-pill badge-danger">Absensi Di Luar Jam Kerja</span></h6></td><?php 
                                            } ?>
                                            <td>{{ $attendance->daily_report }}</td>
                                            <td>
                                                Terekam sejak {{ $attendance->created_at->format('H:i:s') }} dari {{ $attendance->entry_location}} dengan alamat IP {{ $attendance->entry_ip}} <span class="badge {{ $attendance->entry_status === 'Valid' ? 'badge-success' : 'badge-danger' }}">IP/ Lokasi Kantor 
                                                    {{ $attendance->entry_status }}
                                                </span>
                                            </td>
                                            <td>
                                                Terekam sejak {{ $attendance->updated_at->format('H:i:s') }} dari {{ $attendance->exit_location}} dengan alamat IP {{ $attendance->exit_ip}} <span class="badge {{ $attendance->exit_status === 'Valid' ? 'badge-success' : 'badge-danger' }}">IP/ Lokasi Kantor 
                                                    {{ $attendance->exit_status }}
                                                </span>
                                            </td>
                                            @elseif ($attendance->registered == 'Membutuhkan Validasi')
                                            <td>{{ $attendance->created_at->format('d M Y') }}</td>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-info">Membutuhkan Validasi</span> </h6></td>
                                            <?php if($attendance->time>=7 && $attendance->time<=9) { ?>
                                                <td><h6 class="text-center"><span class="badge badge-pill badge-success">Hadir Tepat Waktu</span></h6></td>
                                            <?php } elseif ($attendance->time>9 && $attendance->time<=15) {
                                                ?><td><h6 class="text-center"><span class="badge badge-pill badge-warning">Hadir Terlambat</span></h6></td><?php
                                            } else {
                                                ?><td><h6 class="text-center"><span class="badge badge-pill badge-danger">Absensi Di Luar Jam Kerja</span></h6></td><?php 
                                            } ?>
                                            <td>{{ $attendance->daily_report }}</td>
                                            <td>
                                                Terekam sejak {{ $attendance->created_at->format('H:i:s') }} dari {{ $attendance->entry_location}} dengan alamat IP {{ $attendance->entry_ip}} <span class="badge {{ $attendance->entry_status === 'Valid' ? 'badge-success' : 'badge-danger' }}">IP/ Lokasi Kantor 
                                                    {{ $attendance->entry_status }}
                                                </span>
                                            </td>
                                            <td>
                                                Terekam sejak {{ $attendance->updated_at->format('H:i:s') }} dari {{ $attendance->exit_location}} dengan alamat IP {{ $attendance->exit_ip}} <span class="badge {{ $attendance->exit_status === 'Valid' ? 'badge-success' : 'badge-danger' }}">IP/ Lokasi Kantor 
                                                    {{ $attendance->exit_status }}
                                                </span>
                                            </td>
                                            @elseif ($attendance->registered === null)
                                            <td>{{ $attendance->created_at->format('d M Y') }}</td>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-warning">Setengah Jam Kerja</span> </h6></td>
                                            <?php if($attendance->time>=7 && $attendance->time<=9) { ?>
                                                <td><h6 class="text-center"><span class="badge badge-pill badge-success">Hadir Tepat Waktu</span></h6></td>
                                            <?php } elseif ($attendance->time>9 && $attendance->time<=15) {
                                                ?><td><h6 class="text-center"><span class="badge badge-pill badge-warning">Hadir Terlambat</span></h6></td><?php
                                            } else {
                                                ?><td><h6 class="text-center"><span class="badge badge-pill badge-danger">Absensi Di Luar Jam Kerja</span></h6></td><?php 
                                            } ?>
                                            <td>Belum Ada Riwayat</td>
                                            <td>
                                                Terekam sejak {{ $attendance->created_at->format('H:i:s') }} dari {{ $attendance->entry_location}} dengan alamat IP {{ $attendance->entry_ip}} <span class="badge {{ $attendance->entry_status === 'Valid' ? 'badge-success' : 'badge-danger' }}">IP/ Lokasi Kantor 
                                                    {{ $attendance->entry_status }}
                                                </span>
                                            </td>
                                            <td>
                                                Belum Ada Riwayat
                                            </td>
                                            @elseif ($attendance->registered == 'Sakit')
                                            <td>{{ $attendance->created_at->format('d M Y') }}</td>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-info">Izin/Sakit</span> </h6></td>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-info">Belum Ada Riwayat</span></h6></td>
                                            <td class="text-center">Belum Ada Riwayat</td>
                                            <td class="text-center">Belum Ada Riwayat</td>
                                            <td class="text-center">Belum Ada Riwayat</td>
                                            @elseif ($attendance->registered == 'Cuti')
                                            <td>{{ $attendance->created_at->format('d M Y') }}</td>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-info">Cuti</span> </h6></td>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-info">Belum Ada Riwayat</span></h6></td>
                                            <td class="text-center">Belum Ada Riwayat</td>
                                            <td class="text-center">Belum Ada Riwayat</td>
                                            <td class="text-center">Belum Ada Riwayat</td>
                                            @else
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-info">Belum Ada Riwayat</span></h6></td>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-info">Belum Ada Riwayat</span></h6></td>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-info">Belum Ada Riwayat</span></h6></td>
                                            <td class="text-center">Belum Ada Riwayat</td>
                                            <td class="text-center">Belum Ada Riwayat</td>
                                            <td class="text-center">Belum Ada Riwayat</td>
                                            @endif
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="alert alert-info text-center" style="width:50%; margin: 0 auto">
                                <h4>Data Tidak Ada</h4>
                            </div>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('extra-js')

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            responsive:true,
            autoWidth: false,
        });
        $('#date_range').daterangepicker({
            "maxDate": new Date(),
            "locale": {
                "format": "DD-MM-YYYY",
            }
        })
    });
</script>
@endsection