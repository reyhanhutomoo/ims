<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Moa; // Panggil Model Moa
use Illuminate\Support\Facades\Auth; // Untuk mengambil data user yang login
use Illuminate\Support\Facades\Storage; // Untuk handle file upload

class MoaController extends Controller
{
    /**
     * Menampilkan list pengajuan milik mahasiswa yang sedang login.
     */
    public function index()
    {
        // Ambil semua data 'moas' yang user_id-nya sama dengan ID user yang login
        $moas = Moa::where('pengguna_id', Auth::id())->latest()->get();

        // Kirim data ke view
        return view('employee.moa.index', compact('moas'));
    }

    /**
     * Menampilkan form untuk membuat pengajuan baru.
     */
    public function create()
    {
        return view('employee.moa.create');
    }

    /**
     * Menyimpan pengajuan baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi data
        $request->validate([
            'title' => 'required|string|max:255',
            'document_type' => 'required|in:MOA,IA', // Pastikan nilainya MOA atau IA
            'document_file' => 'required|file|mimes:pdf,doc,docx|max:5120', // Maks 5MB
        ], [
            'title.required' => 'Judul atau Nama Instansi wajib diisi.',
            'document_type.required' => 'Jenis dokumen wajib dipilih.',
            'document_file.required' => 'File draf dokumen wajib diunggah.',
            'document_file.mimes' => 'File harus berupa PDF, DOC, atau DOCX.',
            'document_file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        // 2. Simpan file
        // File akan disimpan di storage/app/public/moa_drafts/namafile.pdf
        $file = $request->file('document_file');
        $filePath = $file->store('moa_drafts', 'public');
        $fileName = $file->getClientOriginalName();

        // 3. Generate tracking number
        $documentType = $request->document_type;
        $date = date('Ymd');
        
        // Hitung jumlah dokumen dengan tipe yang sama pada hari ini
        $count = Moa::where('jenis_dokumen', $documentType)
                    ->whereDate('created_at', today())
                    ->count() + 1;
        
        $trackingNumber = $documentType . '-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        // 4. Simpan data ke database
        Moa::create([
            'pengguna_id' => Auth::id(),
            'nomor_pelacakan' => $trackingNumber,
            'judul' => $request->title,
            'jenis_dokumen' => $request->document_type,
            'path_berkas' => $filePath,
            'status' => 'pending', // Status awal
        ]);

        // 5. Redirect kembali dengan pesan sukses
        $successMessage = '✅ Pengajuan berhasil dikirim!<br>' .
                         '<strong>Nomor Tracking:</strong> <span class="badge badge-info">' . $trackingNumber . '</span><br>' .
                         '<strong>File:</strong> ' . $fileName . '<br>' .
                         '<small>Simpan nomor tracking untuk melacak status pengajuan Anda.</small>';
        
        return redirect()->route('employee.moa.index')->with('success', $successMessage);
    }

    /**
     * Menampilkan detail satu pengajuan.
     */
    public function show(Moa $moa)
    {
        // Cek apakah mahasiswa ini pemilik pengajuan tsb
        if ($moa->pengguna_id != Auth::id()) {
            abort(403); // Jika bukan, tolak akses
        }

        return view('employee.moa.show', compact('moa'));
    }
}