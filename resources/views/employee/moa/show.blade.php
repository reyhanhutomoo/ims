@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Pengajuan</h1>

    <!-- Alert Nomor Tracking -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-barcode"></i> Nomor Tracking:</strong>
        <span class="badge badge-light" style="font-size: 1.1em; padding: 8px 12px;">{{ $moa->nomor_pelacakan }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <hr>
        <small class="mb-0">Simpan nomor tracking ini untuk melacak status pengajuan Anda.</small>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengajuan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Nomor Tracking</th>
                            <td>
                                <strong class="text-primary">{{ $moa->nomor_pelacakan }}</strong>
                                <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyTracking()" title="Salin nomor tracking">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
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
                        <p class="text-muted">Belum ada catatan dari admin.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fab fa-whatsapp"></i> Hubungi Admin
                    </h6>
                </div>
                <div class="card-body text-center">
                    <p class="mb-3">Ada pertanyaan tentang pengajuan Anda?</p>
                    <a href="https://wa.me/62895348003920" target="_blank" class="btn btn-success btn-block">
                        <i class="fab fa-whatsapp"></i> Chat via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('employee.moa.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali ke List
    </a>
</div>
@endsection

@push('scripts')
<script>
function copyTracking() {
    const trackingNumber = "{{ $moa->nomor_pelacakan }}";
    
    // Use modern Clipboard API
    if (navigator.clipboard && window.isSecureContext) {
        // Modern approach using Clipboard API
        navigator.clipboard.writeText(trackingNumber).then(function() {
            // Show success notification with SweetAlert2
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                html: '<strong>Nomor tracking berhasil disalin!</strong><br><br>' +
                      '<code style="background: #f0f0f0; padding: 8px 12px; border-radius: 4px; font-size: 1.1em;">' +
                      trackingNumber + '</code>',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end'
            });
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyalin nomor tracking',
                showConfirmButton: true
            });
        });
    } else {
        // Fallback for older browsers
        const tempInput = document.createElement('input');
        tempInput.value = trackingNumber;
        tempInput.style.position = 'fixed';
        tempInput.style.opacity = '0';
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices
        
        try {
            const successful = document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            if (successful) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    html: '<strong>Nomor tracking berhasil disalin!</strong><br><br>' +
                          '<code style="background: #f0f0f0; padding: 8px 12px; border-radius: 4px; font-size: 1.1em;">' +
                          trackingNumber + '</code>',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menyalin nomor tracking',
                    showConfirmButton: true
                });
            }
        } catch (err) {
            document.body.removeChild(tempInput);
            console.error('Failed to copy: ', err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Gagal menyalin nomor tracking',
                showConfirmButton: true
            });
        }
    }
}
</script>
@endpush