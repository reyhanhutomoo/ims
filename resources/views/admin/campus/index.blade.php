@extends('layouts.app')        
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Campus</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard Admin</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Campus
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
                            <a href="" data-toggle="modal" data-target=".TambahData" class="btn btn-info" title="Tambah Karyawan">
                            <i class="fa fa-plus"></i> Tambah</a>
                        </div>
                        <table class="table table-bordered table-hover" id="dataTable">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Asal Kampus</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($campuses as $index => $campus)
                                <tr class="text-center">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $campus->name }}</td>
                                    <td>
                                        <a href="" class="btn btn-warning" data-toggle="modal" data-target=".editCampus{{ $campus->id }}" title="Edit Kampus">Edit</a>
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
                            @for ($i = 1; $i < $campuses->count()+1; $i++)
                                <!-- Modal -->
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
                                                    action="{{ route('admin.campus.delete', $campuses->get($i-1)->id) }}"
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
                <h5 class="modal-title">Tambah Kampus</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ route('admin.campus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="form-group mb-3">
                    <label class="required-label faded-label" for="name" >Nama Kampus</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukan Kampus">
                    @error('name')
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

@foreach ($campuses as $campus)
<div class="modal fade editCampus{{ $campus->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kampus</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <!-- Isi modal body -->
            <div class="modal-body">
                <!-- Form edit/update -->
                <form action="{{ route('admin.campus.update', ['id' => $campus->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="name">Nama Kampus</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $campus->name }}" placeholder="Masukkan Kampus">
                        @error('name')
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
@endsection
