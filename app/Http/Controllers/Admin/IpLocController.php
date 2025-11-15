<?php

namespace App\Http\Controllers\Admin;

use App\StatusAtten;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class IpLocController extends Controller
{
    public function index(){
        $iploc = StatusAtten::all();

        return view('admin.iploc.index', compact('iploc'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'ip' => 'required',
            'location' => 'required',
        ],[
            'ip.required' => 'IP wajib diisi!',
            'location.required' => 'Lokasi wajib diisi!',
        ]);
        StatusAtten::create([
            'ip' => $request->ip,
            'location' => $request->location,
        ]);
        
        Alert::success('Success', 'Data berhasil ditambahkan!');
        return redirect()->route('admin.iploc.index');
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'ip' => 'required',
            'location' => 'required',
        ], [
            'ip.required' => 'IP wajib diisi!',
            'location.required' => 'Lokasi wajib diisi!',
        ]);

        $iploc = StatusAtten::find($id);
        $iploc->ip = $request->ip;
        $iploc->location = $request->location;
        $iploc->save();

        Alert::success('Success', 'Data berhasil diubah!');
        return redirect()->route('admin.iploc.index');
    }

    public function destroy($id) {
        $iploc = StatusAtten::findOrFail($id);
        DB::table('statusatten')->where('id', '=', $id)->delete();
        $iploc->delete();
        Alert::success('Success', 'Data berhasil di hapus!');
        return redirect()->route('admin.iploc.index');
    }
}