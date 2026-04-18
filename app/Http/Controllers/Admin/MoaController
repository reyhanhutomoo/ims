<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Moa; // Panggil Model Moa
use App\User; // Panggil Model User
use Illuminate\Support\Facades\Storage; // Untuk handle file
use Carbon\Carbon; // Untuk format label bulan

class MoaController extends Controller
{
    /**
     * Menampilkan semua list pengajuan dari semua mahasiswa.
     */
    public function index()
    {
        // Ambil semua data MOA, beserta data relasi 'user' dan 'employee' dengan 'campus'
        $moas = Moa::with(['user.employee.campus'])->latest()->get();

        // Hitung jumlah MOA dan IA (kolom Bahasa Indonesia: jenis_dokumen)
        $moaCount = Moa::where('jenis_dokumen', 'MOA')->count();
        $iaCount = Moa::where('jenis_dokumen', 'IA')->count();

        // Ambil semua data kampus untuk dropdown filter (kolom Bahasa Indonesia: nama)
        $campuses = \App\Campus::orderBy('nama')->get();

        return view('admin.moa.index', compact('moas', 'moaCount', 'iaCount', 'campuses'));
    }

    /**
     * Dashboard grafik MoA/IA per bulan.
     */
    public function dashboard(Request $request)
    {
        $year = (int) ($request->get('year') ?? now()->year);

        // Aggregasi per bulan untuk MOA dan IA di tahun tertentu
        $moaCounts = Moa::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->where('jenis_dokumen', 'MOA')
            ->groupBy('month')
            ->pluck('count', 'month');

        $iaCounts = Moa::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->where('jenis_dokumen', 'IA')
            ->groupBy('month')
            ->pluck('count', 'month');

        // Siapkan label (Jan-Des) dan dataset 12 bulan dengan nilai 0 jika tidak ada data
        $labels = [];
        $moaSeries = [];
        $iaSeries = [];
        $totalSeries = [];

        Carbon::setLocale('id');
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create(null, $m, 1)->translatedFormat('M');
            $moa = (int) ($moaCounts[$m] ?? 0);
            $ia = (int) ($iaCounts[$m] ?? 0);
            $moaSeries[] = $moa;
            $iaSeries[] = $ia;
            $totalSeries[] = $moa + $ia;
        }

        return view('admin.moa.dashboard', compact('labels', 'moaSeries', 'iaSeries', 'totalSeries', 'year'));
    }

    /**
     * Menampilkan form untuk mereview/mengubah status pengajuan.
     * Kita pakai 'edit' karena 'show' biasanya hanya untuk read-only.
     */
    public function edit(Moa $moa)
    {
        return view('admin.moa.edit', compact('moa'));
    }

    /**
     * Menampilkan detail satu pengajuan (read-only untuk admin).
     */
    public function show(Moa $moa)
    {
        return view('admin.moa.show', compact('moa'));
    }


    /**
     * Update status pengajuan (Approve/Reject).
     */
    public function update(Request $request, Moa $moa)
    {
        // 1. Validasi input dari admin
        $request->validate([
            'status' => 'required|in:pending,reviewed,approved,rejected',
            'admin_notes' => 'nullable|string',
            'signed_file' => 'nullable|file|mimes:pdf|max:5120', // File TTD (opsional)
        ]);

        // 2. Siapkan data untuk di-update
        $dataToUpdate = [
            'status' => $request->status,
            'catatan_admin' => $request->admin_notes,
        ];

        // 3. Cek jika admin mengunggah file balasan (file yg sudah TTD)
        if ($request->hasFile('signed_file')) {
            $file = $request->file('signed_file');
            
            // Validasi file
            if (!$file->isValid()) {
                return redirect()->back()->with('error', 'File tidak valid atau corrupt.')->withInput();
            }
            
            try {
                // Log info file
                \Log::info('Attempting to upload file', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ]);
                
                // Generate nama file unik dengan sanitasi
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
                $fileName = time() . '_' . $safeName . '.' . $extension;
                
                \Log::info('Generated filename: ' . $fileName);
                
                // Pastikan direktori ada
                $directory = storage_path('app/public/moa_signed');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                    \Log::info('Created directory: ' . $directory);
                }
                
                // Simpan file menggunakan move
                $destinationPath = $directory . '/' . $fileName;
                $file->move($directory, $fileName);
                
                \Log::info('File moved successfully to: ' . $destinationPath);
                
                // Path relatif untuk database
                $relativePath = 'moa_signed/' . $fileName;
                
                // Hapus file lama jika ada
                if ($moa->path_berkas_ttd) {
                    $oldFilePath = storage_path('app/public/' . $moa->path_berkas_ttd);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                        \Log::info('Old file deleted: ' . $oldFilePath);
                    }
                }
                
                $dataToUpdate['path_berkas_ttd'] = $relativePath;
                
            } catch (\Exception $e) {
                \Log::error('File upload error', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->with('error', 'Gagal mengunggah file: ' . $e->getMessage())->withInput();
            }
        }

        // 4. Update data di database
        $moa->update($dataToUpdate);

        // 5. Redirect kembali ke list dengan pesan yang sesuai
        if ($request->hasFile('signed_file')) {
            $message = '✅ Status pengajuan berhasil diperbarui!<br>' .
                      '<strong>File TTD:</strong> Berhasil diunggah<br>' .
                      '<strong>Tracking:</strong> ' . $moa->nomor_pelacakan . '<br>' .
                      '<strong>Status:</strong> <span class="badge badge-' .
                      ($request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'info')) .
                      '">' . ucfirst($request->status) . '</span>';
        } else {
            $message = '✅ Status pengajuan berhasil diperbarui!<br>' .
                      '<strong>Tracking:</strong> ' . $moa->nomor_pelacakan . '<br>' .
                      '<strong>Status:</strong> <span class="badge badge-' .
                      ($request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'info')) .
                      '">' . ucfirst($request->status) . '</span>';
        }
        return redirect()->route('admin.moa.index')->with('success', $message);
    }

    /**
     * Menghapus data pengajuan.
     */
    public function destroy(Moa $moa)
    {
        // Hapus file draf dari storage
        if (!empty($moa->path_berkas) && Storage::disk('public')->exists($moa->path_berkas)) {
            Storage::disk('public')->delete($moa->path_berkas);
        }

        // Hapus file TTD dari storage (jika ada)
        if (!empty($moa->path_berkas_ttd) && Storage::disk('public')->exists($moa->path_berkas_ttd)) {
            Storage::disk('public')->delete($moa->path_berkas_ttd);
        }

        // Hapus data dari database
        $moa->delete();

        return redirect()->route('admin.moa.index')->with('success', 'Data pengajuan berhasil dihapus.');
    }
}
