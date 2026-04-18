@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Pengajuan</h1>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengajuan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <td>{{ $moa->user->nama }} ({{ $moa->user->email }})</td>
                        </tr>
                        <tr>
                            <th>Judul / Instansi</th>
                            <td>{{ $moa->judul }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Dokumen</th>
                            <td>{{ $moa->jenis_dokumen }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <td>{{ $moa->created_at->format('d F Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
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
                        </tr>
                        <tr>
                            <th>Draf Dokumen</th>
                            <td>
                                <a href="{{ Storage::url($moa->path_berkas) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Download Draf
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>File Final (TTD)</th>
                            <td>
                                @if($moa->path_berkas_ttd)
                                    <a href="{{ Storage::url($moa->path_berkas_ttd) }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i> Download File Final
                                    </a>
                                @else
                                    <span class="text-muted">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Catatan Admin</h6>
                </div>
                <div class="card-body">
                    @if($moa->catatan_admin)
                        <p>{{ $moa->catatan_admin }}</p>
                    @else
                        <p class="text-muted">Belum ada catatan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.moa.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke List
    </a>
    <a href="{{ route('admin.moa.edit', $moa) }}" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit / Review
    </a>
</div>
@endsection