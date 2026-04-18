@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Review Pengajuan: {{ $moa->judul }}</h1>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Pengajuan</h6>
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
                            <th>Draf Dokumen</th>
                            <td>
                                <a href="{{ Storage::url($moa->path_berkas) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-download"></i> Download Draf
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Tindakan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.moa.update', $moa) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="status">Ubah Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ $moa->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="reviewed" {{ $moa->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                <option value="approved" {{ $moa->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $moa->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="admin_notes">Catatan (Jika ditolak/revisi)</label>
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror" id="admin_notes" name="admin_notes" rows="4">{{ old('admin_notes', $moa->catatan_admin) }}</textarea>
                            @error('admin_notes')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="signed_file">Upload File Final (TTD)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('signed_file') is-invalid @enderror" id="signed_file" name="signed_file" accept=".pdf">
                                <label class="custom-file-label" for="signed_file">Pilih file PDF...</label>
                            </div>
                            <small class="form-text text-muted">Opsional. Upload file PDF yang sudah ditandatangani (Max: 5MB).</small>
                            @if($moa->path_berkas_ttd)
                                <div class="alert alert-info mt-2 mb-0">
                                    <i class="fas fa-file-pdf"></i> File saat ini: <strong>{{ basename($moa->path_berkas_ttd) }}</strong>
                                    <br><small>Upload file baru akan mengganti file ini.</small>
                                </div>
                            @endif
                            <div id="file-selected-info" class="alert alert-success mt-2 mb-0" style="display:none;">
                                <i class="fas fa-check-circle"></i> File dipilih: <strong id="selected-file-name"></strong>
                                <br><small>Ukuran: <span id="selected-file-size"></span></small>
                            </div>
                            @error('signed_file')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <hr>
                        
                        <button type="submit" class="btn btn-primary">Update Status</button>
                        <a href="{{ route('admin.moa.index') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script untuk menampilkan nama file di input bootstrap
    $('#signed_file').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
        
        // Tampilkan info file yang dipilih
        if (this.files && this.files[0]) {
            let file = this.files[0];
            let fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            
            $('#selected-file-name').text(file.name);
            $('#selected-file-size').text(fileSize + ' MB');
            $('#file-selected-info').show();
            
            // Validasi ukuran file (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 5MB.');
                $(this).val('');
                $(this).next('.custom-file-label').html('Pilih file PDF...');
                $('#file-selected-info').hide();
            }
            
            // Validasi tipe file
            if (file.type !== 'application/pdf') {
                alert('File harus berupa PDF!');
                $(this).val('');
                $(this).next('.custom-file-label').html('Pilih file PDF...');
                $('#file-selected-info').hide();
            }
        }
    });
    
    // Konfirmasi sebelum submit jika ada file
    $('form').on('submit', function(e) {
        let fileInput = $('#signed_file')[0];
        if (fileInput.files && fileInput.files[0]) {
            if (!confirm('Anda yakin ingin mengupload file ini?')) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>
@endpush