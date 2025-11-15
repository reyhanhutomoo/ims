@extends('layouts.app')        
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Peserta Magang</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard Admin</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Peserta Magang
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        @include('messages.alerts')
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="col-md-12 text-right mb-3">
                            <a href="" data-toggle="modal" data-target=".TambahData" class="btn btn-info" title="Tambah Peserta Magang">
                            <i class="fa fa-plus"></i> Tambah</a>
                        </div>
                        <table class="table table-bordered table-hover" id="dataTable">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Umur</th>
                                    <th>Asal Kampus</th>
                                    <th>Divisi</th>
                                    <th>Mulai Magang</th>
                                    <th>Akhir Magang</th>
                                    <th class="none">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $index => $employee)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $employee->name }}</td>
                                    <td class="text-center">{{ $employee->age }}</td>
                                    <td class="text-center">{{ $employee->campus->name }}</td>
                                    <td class="text-center">{{ $employee->division->name }}</td>
                                    <td class="text-center">{{ $employee->start_date }}</td>
                                    <td class="text-center">{{ $employee->end_date }}</td>
                                    <td>
                                        <a href="{{ route('admin.employees.profile', $employee->id) }}" class="btn btn-info">Lihat Profil</a>
                                        <a href="" class="btn btn-warning" data-toggle="modal" data-target=".editEmployee{{ $employee->id }}" title="Edit Employee">Edit</a>
                                        <button 
                                        class="btn btn-danger"
                                        data-toggle="modal" 
                                        data-target="#deleteModalCenter{{ $index + 1 }}"
                                        >Hapus</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                            @for ($i = 1; $i < $employees->count()+1; $i++)
                                <div class="modal fade" id="deleteModalCenter{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalCenterTitle1{{ $i }}" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card card-danger">
                                                <div class="card-header">
                                                    <h5 style="text-align: center !important">Yakin ingin dihapus?</h5>
                                                </div>
                                                <div class="card-body text-center d-flex" style="justify-content: center">
                                                    
                                                    <button type="button" class="btn flat btn-secondary" data-dismiss="modal">Tidak</button>
                                                    
                                                    <form 
                                                    action="{{ route('admin.employees.delete', $employees->get($i-1)->id) }}"
                                                    method="POST"
                                                    >
                                                    @csrf
                                                    @method('DELETE')
                                                        <button type="submit" class="btn flat btn-danger ml-1">Ya</button>
                                                    </form>
                                                </div>
                                                <div class="card-footer text-center">
                                                    <small>Pastikan data yang anda pilih benar</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade TambahData" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Peserta Magang</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @method('POST')
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="name" >Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukan name">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="age" >Umur</label>
                        <input type="number" name="age" class="form-control @error('age') is-invalid @enderror" value="{{ old('age') }}" placeholder="Masukan Umur">
                        @error('age')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="email" >Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Masukan Email">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="start_date">Tanggal Mulai Magang</label>
                        <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" placeholder="Masukkan Tanggal Mulai Magang">
                        @error('start_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="end_date">Tanggal Selesai Magang</label>
                        <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" placeholder="Masukkan Tanggal Selesai Magang">
                        @error('end_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="division_id">Division</label>
                        <select name="division_id" class="form-control">
                            @foreach ($division as $id => $name)
                                <option value="{{ $name->id }}">{{ $name->name }}</option>
                            @endforeach
                        </select>
                        @error('division_idx')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="campus_id">Asal Kampus</label>
                        <select name="campus_id" class="form-control">
                            @foreach ($campus as $id => $name)
                                <option value="{{ $name->id }}">{{ $name->name }}</option>
                            @endforeach
                        </select>
                        @error('campus_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="password" >Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}" placeholder="Masukan password baru">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="password_confirmation" >Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" value="{{ old('password_confirmation') }}" placeholder="Masukan password">
                        @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($employees as $employee)
<div class="modal fade editEmployee{{ $employee->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Peserta Magang</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('admin.employees.update', ['id' => $employee->id]) }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="name">Nama</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $employee->name }}" placeholder="Masukkan Nama">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="age">Umur</label>
                        <input type="number" name="age" class="form-control @error('age') is-invalid @enderror" value="{{ $employee->age }}" placeholder="Masukkan Umur">
                        @error('age')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="email">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $employee->user->email }}" placeholder="Masukkan Email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="start_date">Tanggal Mulai Magang</label>
                        <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ $employee->start_date }}" placeholder="Masukkan Periode Magang">
                        @error('start_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="end_date">Tanggal Selesai Magang</label>
                        <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ $employee->end_date }}" placeholder="Masukkan Periode Magang">
                        @error('end_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="division_id">Divisi Magang</label>
                        <select name="division_id" class="form-control">
                            @foreach ($division as $id => $name)
                                <option value="{{ $name->id }}" {{ $name->id == $employee->division_id ? 'selected' : '' }}>{{ $name->name }}</option>
                            @endforeach
                        </select>
                        @error('division')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="campus_id">Asal Kampus</label>
                        <select name="campus_id" class="form-control">
                            @foreach ($campus as $id => $name)
                            <option value="{{ $name->id }}" {{ $name->id == $employee->campus_id ? 'selected' : '' }}>{{ $name->name }}</option>
                            @endforeach
                        </select>
                        @error('campus_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password baru">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
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
@endforeach
@endsection

@section('extra-js')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            responsive:true,
            autoWidth: false,
        });
    });
</script>
<script>
    $().ready(function() {
        if('{{ old('dob') }}') {
            const dob = moment('{{ old('dob') }}', 'DD-MM-YYYY');
            const intern_period = moment('{{ old('intern_period') }}', 'DD-MM-YYYY');
            console.log('{{ old('dob') }}')
            $('#dob').daterangepicker({
                "startDate": dob,
                "singleDatePicker": true,
                "showDropdowns": true,
                "locale": {
                    "format": "DD-MM-YYYY"
                }
            });
            $('#intern_period').daterangepicker({
                "startDate": intern_period,
                "singleDatePicker": true,
                "showDropdowns": true,
                "locale": {
                    "format": "DD-MM-YYYY"
                }
            });
        } else {
            $('#dob').daterangepicker({
                "singleDatePicker": true,
                "showDropdowns": true,
                "locale": {
                    "format": "DD-MM-YYYY"
                }
            });
            $('#intern_period').daterangepicker({
                "singleDatePicker": true,
                "showDropdowns": true,
                "locale": {
                    "format": "DD-MM-YYYY"
                }
            });
        }
        
    });
</script>
<script>
    // Inisialisasi popover
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
</script>
@endsection
