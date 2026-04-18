<?php

namespace App\Http\Controllers;

use App\Moa;
use App\Campus;
use Illuminate\Http\Request;

class PublicMoaController extends Controller
{
    /**
     * Display a listing of all MoA/IA for public viewers (universities)
     * with search and filter capabilities
     */
    public function index(Request $request)
    {
        $query = Moa::with(['user.employee.campus']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('jenis_dokumen', 'like', "%{$search}%")
                  ->orWhere('nomor_pelacakan', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('nama', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by document type
        if ($request->has('document_type') && $request->document_type != '') {
            $query->where('jenis_dokumen', $request->document_type);
        }

        // Filter by status (map English values to Indonesian ENUM before querying)
        if ($request->has('status') && $request->status != '') {
            $statusParam = $request->status;
            $statusDb = \App\Moa::mapStatusToDb($statusParam);
            $query->where('status', $statusDb ?? $statusParam);
        }

        // Filter by campus/university ID
        if ($request->has('campus_id') && $request->campus_id != '') {
            $query->whereHas('user.employee', function($q) use ($request) {
                $q->where('kampus_id', $request->campus_id);
            });
        }

        // Order by latest
        $moas = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get all campuses/universities for dropdown filter
        $campuses = Campus::orderBy('nama')->get();

        return view('public.moa.index', compact('moas', 'campuses'));
    }

    /**
     * Display the specified MoA/IA details
     */
    public function show($id)
    {
        $moa = Moa::with(['user.employee.campus'])->findOrFail($id);
        return view('public.moa.show', compact('moa'));
    }
}