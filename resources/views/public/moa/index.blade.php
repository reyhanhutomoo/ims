<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Viewer - Status MoA/IA</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
    <link href="{{ asset('/') }}css/style.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #89CFF0 0%, #4A90E2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .viewer-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            margin: 20px auto;
            max-width: 1400px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4A90E2;
        }
        .header-section h1 {
            color: #4A90E2;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header-section p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .table-container {
            overflow-x: auto;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn-back {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        @media (max-width: 768px) {
            .viewer-container {
                padding: 15px;
            }
            .btn-back {
                position: relative;
                top: 0;
                left: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <a href="{{ route('welcome') }}" class="btn btn-light btn-back">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <div class="container">
        <div class="viewer-container">
            <div class="header-section">
                <img src="{{ asset('/') }}images/divhub.png" style="width: 80px; height: 90px; margin-bottom: 15px;" alt="logo">
                <h1><i class="fas fa-file-contract"></i> Status Pengajuan MoA/IA</h1>
                <p>Viewer untuk Universitas - Lihat semua status pengajuan MoA dan IA</p>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="{{ route('public.moa.index') }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="search"><i class="fas fa-search"></i> Cari</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   placeholder="Cari nama, tracking number..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="campus_id"><i class="fas fa-university"></i> Universitas</label>
                            <select class="form-control select2" id="campus_id" name="campus_id">
                                <option value="">Semua Universitas</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                        {{ $campus->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="document_type"><i class="fas fa-file-alt"></i> Jenis</label>
                            <select class="form-control" id="document_type" name="document_type">
                                <option value="">Semua</option>
                                <option value="MOA" {{ request('document_type') == 'MOA' ? 'selected' : '' }}>MOA</option>
                                <option value="IA" {{ request('document_type') == 'IA' ? 'selected' : '' }}>IA</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="status"><i class="fas fa-info-circle"></i> Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Semua</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                    @if(request()->hasAny(['search', 'document_type', 'status', 'campus_id']))
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('public.moa.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                    @endif
                </form>
            </div>

            <!-- Results Info -->
            <div class="mb-3">
                <p class="text-muted">
                    <i class="fas fa-list"></i> Menampilkan <strong>{{ $moas->count() }}</strong> dari <strong>{{ $moas->total() }}</strong> pengajuan
                </p>
            </div>

            <!-- Table Section -->
            <div class="table-container">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Tanggal</th>
                            <th width="15%">Nama Mahasiswa</th>
                            <th width="15%">Universitas</th>
                            <th width="20%">Judul</th>
                            <th width="10%">Jenis</th>
                            <th width="10%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($moas as $index => $moa)
                        <tr>
                            <td>{{ $moas->firstItem() + $index }}</td>
                            <td>{{ $moa->created_at->format('d/m/Y') }}</td>
                            <td>{{ $moa->user->nama }}</td>
                            <td>{{ $moa->user->employee->campus->nama ?? 'N/A' }}</td>
                            <td>{{ $moa->judul }}</td>
                            <td>
                                <span class="badge badge-info">
                                    <i class="fas fa-file"></i> {{ $moa->jenis_dokumen }}
                                </span>
                            </td>
                            <td>
                                @if($moa->status == 'pending')
                                    <span class="badge badge-warning status-badge">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @elseif($moa->status == 'reviewed')
                                    <span class="badge badge-info status-badge">
                                        <i class="fas fa-eye"></i> Reviewed
                                    </span>
                                @elseif($moa->status == 'approved')
                                    <span class="badge badge-success status-badge">
                                        <i class="fas fa-check-circle"></i> Approved
                                    </span>
                                @elseif($moa->status == 'rejected')
                                    <span class="badge badge-danger status-badge">
                                        <i class="fas fa-times-circle"></i> Rejected
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('public.moa.show', $moa->id) }}" class="btn btn-sm btn-primary" title="Lihat Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data pengajuan yang ditemukan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $moas->appends(request()->query())->links() }}
            </div>

            <!-- Footer Info -->
            <div class="text-center mt-4 pt-3 border-top">
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle"></i> Halaman ini dapat diakses tanpa login untuk memudahkan universitas melihat status pengajuan
                </p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for university dropdown with search
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Pilih atau cari universitas...',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
</body>
</html>