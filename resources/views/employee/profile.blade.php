@extends('layouts.app')        

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Profile</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="#">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Profile
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
                    <div class="card-header">
                        <h5 class="text-center mt-2">My Profile</h5>
                    </div>
                    <div class="card-body">
                        @include('messages.alerts')
                        <div class="row mb-3">
                            <div class="col text-center mx-auto">
                                <img src="/storage/{{ $employee->photo }}" class="rounded-circle img-fluid" alt=""
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
                    <div class="card-footer text-center">
                        <a href="" data-toggle="modal" data-target=".editEmployee{{ $employee->id }}" title="Edit Profile" class="btn btn-flat btn-primary">Upload Foto</a>
                    </div>
                    <div class="card-footer text-center">
                        <a href="" data-toggle="modal" data-target=".ubahPassword" title="Ubah Password" class="btn btn-flat btn-warning">Ubah Password</a>
                    </div>
                    {{-- <div class="card-footer text-center">
                        <a href="{{ route('employee.profile-edit', $employee->id) }}" class="btn btn-flat btn-primary">Edit Profile</a>
                    </div> --}}
                </div>
            </div>
        </div>
        
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->

<div class="modal fade editEmployee{{ $employee->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Peserta Magang</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('employee.profile.updatePhoto', $employee->id) }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="photo">Foto Profile</label>
                        <input type="file" name="photo" class="custom-file @error('photo') is-invalid @enderror" accept="image/png, image/jpeg, image/jpg">
                        @error('photo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade ubahPassword" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Password</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('employee.profile.update-password') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="input-group mb-3">
                        <input type="password" name="old_password" class="form-control" placeholder="Current Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    @if (session('error'))
                            <div class="text-danger">
                                Wrong Password
                            </div>
                    @endif
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="New Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
