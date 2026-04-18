<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan - {{ $moa->judul }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="{{ asset('/') }}css/style.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .detail-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            margin: 20px auto;
            max-width: 1000px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        .header-section h1 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            width: 200px;
            flex-shrink: 0;
        }
        .info-value {
            color: #212529;
            flex-grow: 1;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }
        .notes-section {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .detail-container {
                padding: 15px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="detail-container">
            <div class="header-section">
                <img src="{{ asset('/') }}images/divhub.png" style="width: 80px; height: 90px; margin-bottom: 15px;" alt="logo">
                <h1><i class="fas fa-file-contract"></i> Detail Pengajuan</h1>
                <p class="text-muted">Informasi lengkap pengajuan MoA/IA</p>
            </div>

            <!-- Main Information Card -->
            <div class="info-card">
                <h5 class="mb-3"><i class="fas fa-info-circle text-primary"></i> Informasi Pengajuan</h5>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-user"></i> Nama Mahasiswa
                    </div>
                    <div class="info-value">
                        {{ $moa->user->nama }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-envelope"></i> Email
                    </div>
                    <div class="info-value">
                        {{ $moa->user->email }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-building"></i> Judul / Universitas
                    </div>
                    <div class="info-value">
                        <strong>{{ $moa->judul }}</strong>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-file-alt"></i> Jenis Dokumen
                    </div>
                    <div class="info-value">
                        <span class="badge badge-info">{{ $moa->jenis_dokumen }}</span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-calendar"></i> Tanggal Pengajuan
                    </div>
                    <div class="info-value">
                        {{ $moa->created_at->format('d F Y, H:i') }} WIB
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-info-circle"></i> Status
                    </div>
                    <div class="info-value">
                        @if($moa->status == 'pending')
                            <span class="badge badge-warning status-badge">
                                <i class="fas fa-clock"></i> Pending - Menunggu Review
                            </span>
                        @elseif($moa->status == 'reviewed')
                            <span class="badge badge-info status-badge">
                                <i class="fas fa-eye"></i> Reviewed - Sedang Diproses
                            </span>
                        @elseif($moa->status == 'approved')
                            <span class="badge badge-success status-badge">
                                <i class="fas fa-check-circle"></i> Approved - Disetujui
                            </span>
                        @elseif($moa->status == 'rejected')
                            <span class="badge badge-danger status-badge">
                                <i class="fas fa-times-circle"></i> Rejected - Ditolak
                            </span>
                        @endif
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-file-pdf"></i> Draf Dokumen
                    </div>
                    <div class="info-value">
                        <a href="{{ Storage::url($moa->path_berkas) }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-download"></i> Download Draf
                        </a>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-file-signature"></i> File Final (TTD)
                    </div>
                    <div class="info-value">
                        @if($moa->path_berkas_ttd)
                            <a href="{{ Storage::url($moa->path_berkas_ttd) }}" target="_blank" class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Download File Final
                            </a>
                        @else
                            <span class="text-muted">
                                <i class="fas fa-hourglass-half"></i> Belum tersedia
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin Notes Section -->
            @if($moa->catatan_admin)
            <div class="notes-section">
                <h6 class="mb-2">
                    <i class="fas fa-sticky-note"></i> Catatan dari Admin
                </h6>
                <p class="mb-0">{{ $moa->catatan_admin }}</p>
            </div>
            @endif

            <!-- Status Timeline -->
            <div class="info-card mt-4">
                <h5 class="mb-3"><i class="fas fa-history text-primary"></i> Timeline Status</h5>
                <div class="timeline">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <span>Pengajuan dibuat: {{ $moa->created_at->format('d F Y, H:i') }}</span>
                    </div>
                    @if($moa->updated_at != $moa->created_at)
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-sync text-info mr-2"></i>
                        <span>Terakhir diupdate: {{ $moa->updated_at->format('d F Y, H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('public.moa.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>

            <!-- Footer Info -->
            <div class="text-center mt-4 pt-3 border-top">
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle"></i> Untuk informasi lebih lanjut, silakan hubungi admin
                </p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>