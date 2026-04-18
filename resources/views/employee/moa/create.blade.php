@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="h3 mb-4 text-gray-800">Form Pengajuan MOA/IA</h1>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Pengajuan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('employee.moa.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="title">Judul / Nama Instansi</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: MOA - Nama - Universitas Indonesia - Kerjasama Penelitian">
                            <small class="form-text text-muted">
                                <strong>Contoh Judul:</strong> Tipe Pengajuan - Nama - Universitas - Judul Pengajuan
                            </small>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="document_type">Jenis Dokumen</label>
                            <select class="form-control @error('document_type') is-invalid @enderror" id="document_type" name="document_type" required>
                                <option value="">-- Pilih Jenis Dokumen --</option>
                                <option value="MOA" {{ old('document_type') == 'MOA' ? 'selected' : '' }}>MOA (Memorandum of Agreement)</option>
                                <option value="IA" {{ old('document_type') == 'IA' ? 'selected' : '' }}>IA (Implementation Arrangement)</option>
                            </select>
                            @error('document_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="document_file">Upload Draf Dokumen</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('document_file') is-invalid @enderror" id="document_file" name="document_file" required>
                                <label class="custom-file-label" for="document_file">Pilih file (PDF, DOC, DOCX)...</label>
                            </div>
                            <small class="form-text text-muted">Format: PDF, DOC, DOCX. Maksimal 5MB.</small>
                            @error('document_file')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                        <a href="{{ route('employee.moa.index') }}" class="btn btn-secondary">Batal</a>
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
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush