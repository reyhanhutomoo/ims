<?php

namespace App\Http\Controllers\Admin;

use App\Division;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class DivisionController extends Controller
{
    public function index(){
        $divisions = Division::all();

        return view('admin.division.index', compact('divisions'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
        ],[
            'name.required' => 'Nama Divisi wajib diisi!',
        ]);
        Division::create([
            'name' => $request->name,
        ]);
        
        Alert::success('Success', 'Data berhasil ditambahkan!');
        return redirect()->route('admin.division.index');
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => 'required',
        ], [
            'name.required' => 'Nama Divisi wajib diisi!',
        ]);

        $divisions = Division::find($id);
        $divisions->name = $request->name;
        $divisions->save();

        Alert::success('Success', 'Data berhasil diubah!');
        return redirect()->route('admin.division.index');
    }

    public function destroy($id) {
        $division = Division::findOrFail($id);
        $employeesUsingDivision = Employee::where('division_id', $id)->exists();

        if ($employeesUsingDivision) {
            Alert::error('Gagal', 'Divisi ini sedang digunakan oleh karyawan.');
            return redirect()->back();
        }
        DB::table('division')->where('id', '=', $id)->delete();
        $division->delete();

        Alert::success('Success', 'Data berhasil dihapus!');
        return redirect()->route('admin.division.index');
    }
}