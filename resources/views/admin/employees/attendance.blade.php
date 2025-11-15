@extends('layouts.app')        

@section('content')
    <!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Absensi</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard Admin</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Absensi
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
            <div class="col-md-4 mx-auto">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="text-center">Tanggal Absensi</h5>
                    </div>
                    <form action="{{ route('admin.employees.attendance') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="input-group mx-auto" style="width:70%">
                            <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            <input type="text" name="date" id="date" class="form-control text-center" >
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-flat btn-primary" type="submit">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-11 mx-auto">
                @include('messages.alerts')
                <div class="card card-primary">
                    <div class="card-header">
                        <div class="card-title text-center">
                            @if ($date)
                            Absensi Karyawan berdasarkan rentang tanggal {{ $date }}                                
                            @else
                            Absensi Karyawan Hari ini
                            @endif
                        </div>
                        <div class="col-md-12 text-right">
                            <a href="" data-toggle="modal" data-target=".modalExport" class="btn btn-whatsapp" title="Export Data">
                            <i class="fa fa-cloud-download"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($employees->count())
                        <table class="table table-bordered table-hover" id="dataTable">
                            <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Status Absensi</th>
                                    <th class="none">Riwayat Awal Absensi</th>
                                    <th>Waktu Absensi</th>
                                    <th class="none">Riwayat Akhir Absensi</th>
                                    <th class="none">Laporan Harian</th>
                                    <th>Divisi</th>
                                    <th class="none">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $index => $employee)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $employee->name }}</td>
                                    @if($employee->attendanceToday)
                                    <td>
                                        <h6 class="text-center">
                                            @if ($employee->attendanceToday->registered === null)
                                                <span class="badge badge-pill badge-warning">Setengah Jam Kerja</span>
                                            @elseif ($employee->attendanceToday->registered === 'Cuti')
                                            <span class="badge badge-pill badge-info">Cuti</span>
                                            @elseif ($employee->attendanceToday->registered === 'Sakit')
                                            <span class="badge badge-pill badge-info">Izin/Sakit</span>
                                            @elseif ($employee->attendanceToday->registered === 'Membutuhkan Validasi')
                                            <span class="badge badge-pill badge-info">Membutuhkan Validasi</span>
                                            @elseif ($employee->attendanceToday->registered === 'Hadir')
                                            <span class="badge badge-pill badge-success">Hadir</span>
                                            @else
                                            <span class="badge badge-pill badge-info">-</span>
                                            @endif
                                        </h6>
                                    </td>
                                        <td>
                                            Terekam sejak {{ $employee->attendanceToday->created_at->format('H:i:s') }} dari {{ $employee->attendanceToday->entry_location}} dengan alamat IP {{ $employee->attendanceToday->entry_ip}} <span class="badge {{ $employee->attendanceToday->entry_status === 'Valid' ? 'badge-success' : 'badge-danger' }}">IP/ Lokasi Kantor 
                                                {{ $employee->attendanceToday->entry_status }}
                                            </span>
                                        </td>
                                        <?php if($employee->attendanceToday->time>=7 && $employee->attendanceToday->time<=9) { ?>
                                            <td><h6 class="text-center"><span class="badge badge-pill badge-success">Hadir Tepat Waktu</span></h6></td>
                                        <?php } elseif ($employee->attendanceToday->time>9 && $employee->attendanceToday->time<=15) {
                                            ?><td><h6 class="text-center"><span class="badge badge-pill badge-warning">Hadir Terlambat</span></h6></td><?php
                                        } else {
                                            ?><td><h6 class="text-center"><span class="badge badge-pill badge-danger">Absensi Di Luar Jam Kerja</span></h6></td><?php 
                                        } ?>
                                        <td>
                                            Terekam sejak {{ $employee->attendanceToday->updated_at->format('H:i:s') }} dari {{ $employee->attendanceToday->exit_location}} dengan alamat IP {{ $employee->attendanceToday->exit_ip}} <span class="badge {{ $employee->attendanceToday->exit_status === 'Valid' ? 'badge-success' : 'badge-danger' }}">IP/ Lokasi Kantor 
                                                {{ $employee->attendanceToday->exit_status }}
                                            </span>
                                        </td>
                                        <td>{{ $employee->attendanceToday->daily_report }}</td>
                                    @else
                                        <td><h6 class="text-center"><span class="badge badge-pill badge-danger">Belum Ada Riwayat</span></h6></td>
                                        <td><h6 class="text-center"><span class="badge badge-pill badge-danger">Belum Ada Riwayat</span></h6></td>
                                        <td><h6 class="text-center"><span class="badge badge-pill badge-danger">Belum Ada Riwayat</span></h6></td>
                                        <td><h6 class="text-center"><span class="badge badge-pill badge-danger">Belum Ada Riwayat</span></h6></td>
                                        <td>-</td>
                                    @endif
                                    <td class="text-center">{{ $employee->division_name }}</td>

                                    {{-- <td>
                                    <?php 
                                    $conn = mysqli_connect("localhost","root","","ims");
                                    $loc2=mysqli_query($conn,"SELECT * FROM attendances"); 
                                    while($loc=mysqli_fetch_array($loc2)) {
                                    if(!empty($loc['entry_location'])) { 
                                        echo $loc['entry_location']; 
                                    } else { echo " - ";} }?></td> --}}
                                    <td>
                                        @if($employee->attendanceToday)
                                        <button 
                                        class="btn btn-flat btn-warning"
                                        data-toggle="modal" data-target=".EditVal{{ $employee->attendanceToday->id }}"
                                        >Edit Validasi</button>
                                        <button 
                                        class="btn btn-flat btn-danger"
                                        data-toggle="modal"
                                        data-target="#deleteModalCenter{{ $employee->attendanceToday->id }}"
                                        >Hapus Riwayat</button>
                                        @else 
                                        Aksi Tidak Tersedia
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @for ($i = 1; $i < $employees->count()+1; $i++)
                                <!-- Modal -->
                                @if($employees->get($i-1)->attendanceToday)
                                <div class="modal fade" id="deleteModalCenter{{ $employees->get($i-1)->attendanceToday->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalCenterTitle1{{ $employees->get($i-1)->attendanceToday->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card card-danger">
                                                <div class="card-header">
                                                    <h5 style="text-align: center !important">Yakin ingin dihapus?</h5>
                                                </div>
                                                <div class="card-body text-center d-flex" style="justify-content: center">
                                                    
                                                    
                                                    <form 
                                                    action="{{ route('admin.employees.attendance.delete', $employees->get($i-1)->attendanceToday->id) }}"
                                                    method="POST"
                                                    >
                                                    <button type="button" class="btn flat btn-secondary ml-1" data-dismiss="modal">Tidak</button>
                                                    @csrf
                                                    @method('DELETE')
                                                        <button type="submit" class="btn flat btn-danger ml-1">Ya</button>
                                                    </form>
                                                </div>
                                                <div class="card-footer text-center">
                                                    <small>Aksi tidak tersedia</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.modal -->
                                @endif
                            @endfor
                        @else
                        <div class="alert alert-info text-center" style="width:50%; margin: 0 auto">
                            <h4>Belum Ada Riwayat</h4>
                        </div>
                        @endif
                        
                    </div>
                </div>
                <!-- general form elements -->
                
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
    <!-- /.content -->

    <div class="modal fade modalExport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Export Data</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
            <div class="modal-body">
                <form name="pdfForm" action="{{ route('admin.attendance.download') }}" target="_blank" method="GET">
                    <div class="card-body">
                        <div class="input-group mx-auto" style="width:100%">
                            <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            <input type="date" name="date" id="date" class="form-control text-center" >
                            <small class="text-muted text-center">Kosongkan jika ingin mendownload semua data.</small>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-flat btn-primary" type="submit">Export</button>
                    </div>
                </form>            
            </div>
        </div>
    </div>
@endsection

@foreach ($employees as $employee)
<div class="modal fade EditVal{{ optional($employee->attendanceToday)->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Validasi</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                @if ($employee->attendanceToday)
                <form action="{{ route('admin.employees.attendance.updateval', ['id' => $employee->attendanceToday->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="entry_status">Kevalidasian IP/Lokasi Masuk</label>
                        <select name="entry_status" class="form-control @error('entry_status') is-invalid @enderror">
                            <option value="Valid" {{ old('entry_status', $employee->attendanceToday->entry_status) == 'Valid' ? 'selected' : '' }}>Valid</option>
                            <option value="Invalid" {{ old('entry_status', $employee->attendanceToday->entry_status) == 'Invalid' ? 'selected' : '' }}>Invalid</option>
                        </select>
                        @error('entry_status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="exit_status">Kevalidasian IP/Lokasi Keluar</label>
                        <select name="exit_status" class="form-control @error('exit_status') is-invalid @enderror">
                            <option selected disabled value="" style="font-style: italic;">Pilih Salah Satu</option>
                            <option value="Valid" {{ old('exit_status', $employee->attendanceToday->exit_status) == 'Valid' ? 'selected' : '' }}>Valid</option>
                            <option value="Invalid" {{ old('exit_status', $employee->attendanceToday->exit_status) == 'Invalid' ? 'selected' : '' }}>Invalid</option>
                        </select>
                        @error('exit_status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="registered">Status Absensi</label>
                        <select name="registered" class="form-control @error('registered') is-invalid @enderror">
                            <option value="Hadir" {{ old('registered', $employee->attendanceToday->registered) == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="Cuti" {{ old('registered', $employee->attendanceToday->registered) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Sakit" {{ old('registered', $employee->attendanceToday->registered) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="Membutuhkan Validasi" {{ old('registered', $employee->attendanceToday->registered) == 'Membutuhkan Validasi' ? 'selected' : '' }}>Membutuhkan Validasi</option>
                            <option value="" {{ old('registered', $employee->attendanceToday->registered) == '' ? 'selected' : '' }}>Setengah Jam Kerja</option>
                        </select>
                        @error('registered')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach


@section('extra-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('#pdfForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const url = this.getAttribute('action') + '?' + new URLSearchParams(new FormData(this)).toString();
    
            // Open the PDF in a new tab
            window.open(url, '_blank');
        });
    });
    </script>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            responsive:true,
            autoWidth: false,
        });
        $('#date').daterangepicker({
            "singleDatePicker": true,
            "showDropdowns": true,
            "locale": {
                "format": "DD-MM-YYYY"
            }
        });
    });
</script>
@endsection