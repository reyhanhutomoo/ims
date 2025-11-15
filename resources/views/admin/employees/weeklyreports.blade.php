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
                        <a href="{{ route('admin.index') }}">Dashboard Admin</a>
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
                        <div class="col-md-12 text-right mb-2">
                            <a href="" data-toggle="modal" data-target=".modalExport" class="btn btn-whatsapp" title="Export Data">
                            <i class="fa fa-cloud-download"></i></a>
                        </div>
                        @if ($weeklyreports->count())
                        <table class="table table-hover" id="dataTable">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Judul</th>
                                    <th>Tanggal Upload</th>
                                    <th>Nilai</th>
                                    <th class="none">Aksi</th>
                                    <th class="none">File</th>
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
                                        <a href="{{ route('admin.employees.weeklyreports.download', $weeklyreport->file) }}" target="_blank">{{ $weeklyreport->file }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <a href="" class="btn btn-warning" data-toggle="modal" data-target=".editWeeklyreport{{ $weeklyreport->id }}" title="Edit Weeklyreport">Beri Nilai</a>
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
                                                    action="{{ route('admin.employees.weeklyreports.delete', $weeklyreports->get($i-1)->id) }}"
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
                                <h4>Tidak Ada Data</h4>
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

<div class="modal fade modalExport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Export Data</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="pdfForm" action="{{ route('admin.employees.weeklyreports.filter') }}" method="GET">
                    <div class="card-body">
                        <div class="col">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-flat btn-primary" type="submit">Export</button>
                    </div>
                </form>            
            </div>
        </div>
    </div>
</div>

@foreach ($weeklyreports as $weeklyreport)
<div class="modal fade editWeeklyreport{{ $weeklyreport->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nilai Laporan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('admin.employees.weeklyreports.update', ['id' => $weeklyreport->id]) }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label class="required-label faded-label" for="value">Nilai</label>
                        <input type="text" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ $weeklyreport->value }}" placeholder="Masukkan Nilai">
                        @error('value')
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