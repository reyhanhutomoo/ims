@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Review Pengajuan MOA/IA</h1>

    @include('messages.alerts')
    
    <!-- Dashboard Statistics -->
    <div class="row mb-4">
        <!-- MOA Count Box -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total MOA
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $moaCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- IA Count Box -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total IA
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $iaCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Pengajuan</h6>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filterType">Filter Jenis Dokumen:</label>
                    <select id="filterType" class="form-control">
                        <option value="">Semua</option>
                        <option value="MOA">MOA</option>
                        <option value="IA">IA</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatus">Filter Status:</label>
                    <select id="filterStatus" class="form-control">
                        <option value="">Semua</option>
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterCampus">Filter Universitas:</label>
                    <select id="filterCampus" class="form-control">
                        <option value="">Semua</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->nama }}">{{ $campus->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="searchBox">Cari:</label>
                    <input type="text" id="searchBox" class="form-control" placeholder="Cari nama atau judul...">
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Mahasiswa</th>
                            <th>Universitas</th>
                            <th>Judul / Instansi</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>File TTD</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($moas as $index => $moa)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $moa->created_at->format('d-m-Y') }}</td>
                            <td>{{ $moa->user->nama }}</td>
                            <td>{{ $moa->user->employee->campus->nama ?? '-' }}</td>
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
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.moa.edit', $moa) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Review
                                </a>
                                
                                <form action="{{ route('admin.moa.destroy', $moa) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@section('extra-js')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#dataTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    });

    // Custom search box
    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Filter by document type
    $('#filterType').on('change', function() {
        var filterValue = this.value;
        if (filterValue === '') {
            table.column(5).search('').draw(); // Column 5 is Jenis
        } else {
            table.column(5).search('^' + filterValue + '$', true, false).draw();
        }
    });

    // Filter by status
    $('#filterStatus').on('change', function() {
        var filterValue = this.value;
        if (filterValue === '') {
            table.column(6).search('').draw(); // Column 6 is Status
        } else {
            // Map status values to display text
            var statusMap = {
                'pending': 'Pending',
                'reviewed': 'Reviewed',
                'approved': 'Approved',
                'rejected': 'Rejected'
            };
            table.column(6).search(statusMap[filterValue], false, false).draw();
        }
    });

    // Filter by campus/university
    $('#filterCampus').on('change', function() {
        var filterValue = this.value;
        console.log('Campus filter changed to:', filterValue);
        if (filterValue === '') {
            table.column(3).search('').draw(); // Column 3 is Universitas
        } else {
            // Use exact match with regex
            table.column(3).search('^' + $.fn.dataTable.util.escapeRegex(filterValue) + '$', true, false).draw();
        }
    });
});
</script>
@endsection
@endsection