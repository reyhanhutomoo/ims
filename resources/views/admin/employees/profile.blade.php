@extends('layouts.app')        

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Detail Profile</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Detail Profile
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
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card card-primary">
                    <div class="card-body">
                        @include('messages.alerts')
                        <div class="row mb-3">
                            <div class="col text-center mx-auto">
                                <img src="/storage/employee_photos/{{ $employee->photo }}" class="rounded-circle img-fluid" alt=""
                                style="box-shadow: 2px 4px rgba(0,0,0,0.1)"
                                >
                            </div>
                        </div>
                        <table class="table profile-table table-hover">
                            <tr>
                                <td>Nama</td>
                                <td>{{ $employee->name }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ $employee->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Umur</td>
                                <td>{{ $employee->age }}</td>
                            </tr>
                            <tr>
                                <td>Asal Kampus</td>
                                <td>{{ $employee->campus->name }}</td>
                            </tr>
                            <tr>
                                <td>Divisi</td>
                                <td>{{ $employee->division->name }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Mulai Magang</td>
                                <td>{{ \Carbon\Carbon::parse($employee->start_date)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Selesai Magang</td>
                                <td>{{ \Carbon\Carbon::parse($employee->end_date)->format('d F Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer text-center" style="height: 2rem">
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <!-- /.container-fluid -->
</section>
@endsection
