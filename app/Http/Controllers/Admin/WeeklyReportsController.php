<?php

namespace App\Http\Controllers\Admin;

use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WeeklyReports;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use RealRashid\SweetAlert\Facades\Alert;

class WeeklyReportsController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        $weeklyreports = WeeklyReports::all();
        return view('admin.employees.weeklyreports', compact('employees', 'weeklyreports'));
    }
    
    public function download($fileName)
    {
        $filePath = storage_path('app/public/pdf_reports/' . $fileName);

        if (file_exists($filePath)) {
            return response()->file($filePath);
        } else {
            abort(404, 'File not found');
        }
    }

    public function downloadWeeklyReports(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = WeeklyReports::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay(),
                Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay()
            ]);
        }

        $weeklyreports = $query->get();
        if ($weeklyreports->isEmpty()) {
            Alert::info('Info', 'Tidak ada laporan mingguan untuk rentang tanggal yang diberikan.');
            return back();
        }
        
        $tempDir = storage_path('app/temp_reports');

        if (!file_exists($tempDir)) {
            mkdir($tempDir);
        }

        foreach ($weeklyreports as $report) {
            $filePath = storage_path('app/public/pdf_reports/' . $report->file);

            // Pastikan file yang di-zip memiliki ekstensi .pdf
            $fileName = pathinfo($report->file, PATHINFO_FILENAME) . '.pdf';

            copy($filePath, $tempDir . '/' . $fileName);
        }

        $zipFileName = 'weekly_reports_' . $startDate . '_sampai_' . $endDate . '.zip';
        $zipFilePath = storage_path('app/public/') . $zipFileName;

        $zip = new ZipArchive;

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = scandir($tempDir);

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $zip->addFile($tempDir . '/' . $file, $file);
                }
            }

            $zip->close();
        }

        $this->deleteDirectory($tempDir);

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

private function deleteDirectory($dir)
    {
        if (file_exists($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));

            foreach ($files as $file) {
                $filePath = $dir . '/' . $file;

                if (is_dir($filePath)) {
                    $this->deleteDirectory($filePath);
                } else {
                    unlink($filePath);
                }
            }

            rmdir($dir);
        }
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'value' => 'required',
        ],[
            'value.required' => 'Nilai wajib diisi!',
        ]);
        $weeklyreport = WeeklyReports::find($id);
        $weeklyreport->value = $request->value;
        $weeklyreport->save();

        Alert::success('success', 'Berhasil memberikan nilai.');
        return redirect()->route('admin.employees.weeklyreports');
    }

    public function destroy($id)
    {
        $weeklyReport = WeeklyReports::findOrFail($id);
        if ($weeklyReport->file) {
            Storage::delete('public/pdf_reports/' . $weeklyReport->file);
        }
        $weeklyReport->delete();
        Alert::success('Success', 'Laporan mingguan berhasil dihapus!');

        return redirect()->route('admin.employees.weeklyreports');
    }
}