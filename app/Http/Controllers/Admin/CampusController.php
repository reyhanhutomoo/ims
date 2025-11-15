<?php

namespace App\Http\Controllers\Admin;

use App\Campus;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class CampusController extends Controller
{
    public function index(){
        $campuses = Campus::all();

        return view('admin.campus.index', compact('campuses'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
        ],[
            'name.required' => 'Nama Divisi wajib diisi!',
        ]);
        Campus::create([
            'name' => $request->name,
        ]);
        
        Alert::success('Success', 'Data berhasil ditambahkan!');
        return redirect()->route('admin.campus.index');
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => 'required',
        ], [
            'name.required' => 'Nama Divisi wajib diisi!',
        ]);

        $campuses = Campus::find($id);
        $campuses->name = $request->name;
        $campuses->save();

        Alert::success('Success', 'Data berhasil diubah!');
        return redirect()->route('admin.campus.index');
    }

    public function destroy($id) {
        $campus = Campus::findOrFail($id);
        $employeesUsingCampus = Employee::where('campus_id', $id)->exists();
        
        if ($employeesUsingCampus) {
            Alert::error('Gagal', 'Kampus ini sedang digunakan oleh karyawan.');
            return redirect()->back();
        }
        DB::table('campus')->where('id', '=', $id)->delete();
        $campus->delete();

        Alert::success('Success', 'Data berhasil dihapus!');
        return redirect()->route('admin.campus.index');
    }
}