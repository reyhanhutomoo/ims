@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="h3 mb-4 text-gray-800">Status Pengajuan MOA/IA</h1>

            @include('messages.alerts') <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <a href="{{ route('employee.moa.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Buat Pengajuan Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Judul / Instansi</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th>File Final</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($moas as $index => $moa)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $moa->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $moa->judul }}</td>
                                    <td><span class="badge badge-info">{{ $moa->jenis_dokumen }}</span></td>
                                    <td>
                                        @if($moa->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($moa->status == 'reviewed')
                                            <span class="badge badge-info">Reviewed</span>
                                        @elseif($moa->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($moa->status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($moa->path_berkas_ttd)
                                            <a href="{{ Storage::url($moa->path_berkas_ttd) }}" target="_blank" class="btn btn-success btn-sm">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('employee.moa.show', $moa) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection