@extends('layouts.app')        

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Ajukan Cuti</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('employee.index') }}">Dashboard Karyawan</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Ajukan Cuti
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
                <div class="col-md-6 mx-auto">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        @include('messages.alerts')
                        <form action="{{ route('employee.leaves.store', $employee->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="">Alasan</label>
                                    <!-- <input type="text" name="reason" value="{{ old('reason') }}" class="form-control"> -->
                                    <select class="form-control" name="reason">
                                        <option value="Sakit" selected>Izin/Sakit</option>
                                        <option value="Cuti">Cuti</option>
                                    </select>
                                    @error('reason')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">File Bukti</label>
                                    <input type="file" name="evidence" class="custom-file @error('file') is-invalid @enderror" accept="application/pdf">
                                    @error('evidence')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea name="description" class="form-control" >{{ old('description') }}</textarea>
                                    @error('description')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Lebih dari Sehari?</label>
                                    <select class="form-control" name="multiple-days" onchange="showDate()">
                                        <option value="yes" selected>Ya</option>
                                        <option value="no">Tidak</option>
                                    </select>
                                </div>
                                <div class="form-group hide-input" id="half-day">
                                    <label>Setengah Jam Kerja</label>
                                    <select class="form-control" name="half-day">
                                        <option value="no">Tidak</option>
                                        <option value="yes">Ya</option>
                                    </select>
                                </div>
                                <div class="form-group" id="range-group">
                                    <label for="">Rentang Tanggal: </label>
                                    <input type="text" name="date_range" id="date_range" class="form-control">
                                    @error('date_range')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group hide-input" id="date-group">
                                    <label for="">Seleksi Data </label>
                                    <input type="text" name="date" id="date" class="form-control">
                                </div>
                                <div class="text-danger">
                                    <small>*Izin Sakit hanya bisa diajukan maksimal H+3 sejak tanggal ketidakhadiran.</small>
                                    <br>
                                    <small>*Izin Cuti harus diajukan minimal 1 hari sebelum tanggal cuti dan tanggal cuti harus lebih dari hari ini.</small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary" type="submit">Ajukan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

@endsection

@section('extra-js')

<script>
    $(document).ready(function() {
        $('#date_range').daterangepicker({
            "locale": {
                "format": "DD-MM-YYYY",
            }
        });
        $('#date').daterangepicker({
            "singleDatePicker": true,
            "locale": {
                "format": "DD-MM-YYYY",
            }
        });

    });
    function showDate() {
        $('#range-group').toggleClass('hide-input');
        $('#date-group').toggleClass('hide-input');
        $('#half-day').toggleClass('hide-input');
    }
</script>
@endsection