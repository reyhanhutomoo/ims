@extends('layouts.app')        
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Laporan Mingguan</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('employee.index') }}">Dashboard Karyawan</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Laporan Mingguan
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
                <div class="col-lg-11 col-md-10 mx-auto">
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="col-md-12 text-right mb-3">
                                <a href="" data-toggle="modal" data-target=".TambahData" class="btn btn-info" title="Tambah Karyawan">
                                <i class="fa fa-upload"></i> Upload Laporan</a>
                            </div>
                            @if ($weeklyreports->count())
                            <table class="table table-hover" id="dataTable">
                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Judul</th>
                                        <th>Tanggal Upload</th>
                                        <th>Nilai</th>
                                        <th class="none">File</th>
                                        <th class="none">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($weeklyreports as $index => $weeklyreport)
                                    <tr class="text-center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $weeklyreport->tittle }}</td>
                                        <td>{{ $weeklyreport->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <h5>
                                                <span 
                                                    @if ($weeklyreport->value === null)
                                                        class="badge badge-pill badge-info"
                                                    @elseif ($weeklyreport->value)
                                                        class="badge badge-pill badge-success"
                                                    @endif>
                                                    @if ($weeklyreport->value === null)
                                                        belum dinilai
                                                    @else
                                                        {{ ucfirst($weeklyreport->value) }}
                                                    @endif
                                                </span> 
                                            </h5>
                                        </td>
                                        <td>
                                            @if ($weeklyreport->file)
                                            <a href="{{ route('employee.weeklyreports.download', $weeklyreport->file) }}" target="_blank">{{ $weeklyreport->file }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
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
                            @for ($i = 1; $i < $weeklyreports->count()+1; $i++)
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
                                                    action="{{ route('employee.weeklyreports.delete', $weeklyreports->get($i-1)->id) }}"
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
                            @else
                            <div class="alert alert-info text-center" style="width:50%; margin: 0 auto">
                                <a href="" data-toggle="modal" data-target=".TambahData">
                                    <h4>Tambah Laporan</h4>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <div class="modal fade TambahData" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Laporan Mingguan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('employee.weeklyreports.store') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @method('POST')
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="tittle" >Judul Laporan</label>
                        <input type="text" name="tittle" class="form-control @error('tittle') is-invalid @enderror" value="{{ old('tittle') }}" placeholder="Masukan Judul">
                        @error('tittle')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="file" >Upload File (PDF or PPTX)</label>
                        <input type="file" name="file" id="file" class="custom-file @error('file') is-invalid @enderror" accept="application/pdf,application/vnd.openxmlformats-officedocument.presentationml.presentation" placeholder="Upload File">
                        @error('file')
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
    @endsection

@section('extra-js')

<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();
    $('.popover-dismiss').popover({
        trigger: 'focus'
    });
    $('#dataTable').DataTable({
        responsive:true,
        autoWidth: false,
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 1 },
            { responsivePriority: 200000000000, targets: -1 }
        ]
    });
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    });
});
</script>
@endsection