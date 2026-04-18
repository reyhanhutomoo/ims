<?php

namespace App\Http\Controllers\Admin;

use App\Holiday;
use App\Http\Controllers\Controller;
use App\Rules\DateRange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'holidays' => Holiday::all()
        ];

        return view('admin.holidays.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.holidays.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->input('multiple-days') == "no") {
            $this->validate($request, [
                'name' => 'required',
            ]);
            Holiday::create([
                'nama' => $request->name,
                'tanggal_mulai' => Carbon::create($request->date)
            ]);
            
        } else {
            $this->validate($request, [
                'name' => 'required',
                'date_range' => new DateRange
            ]);
            [$start, $end] = explode(' - ', $request->date_range);
            Holiday::create([
                'nama' => $request->name,
                'tanggal_mulai' => $start,
                'tanggal_selesai' => $end
            ]);
        }
        $request->session()->flash('success', 'Hari Libur berhasil ditambah');
        return redirect()->route('admin.holidays.index');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $holiday = Holiday::findOrFail($id);

        return view('admin.holidays.edit')->with('holiday', $holiday);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $holiday = Holiday::findOrFail($id);
        if($request->input('multiple-days') == "no") {
            $this->validate($request, [
                'name' => 'required',
            ]);
            $holiday->nama = $request->name;
            $holiday->tanggal_mulai = Carbon::create($request->date);
            $holiday->tanggal_selesai = null;
            
        } else {
            $this->validate($request, [
                'name' => 'required',
                'date_range' => new DateRange
            ]);
            [$start, $end] = explode(' - ', $request->date_range);
            $holiday->nama = $request->name;
            $holiday->tanggal_mulai = $start;
            $holiday->tanggal_selesai = $end;
        }
        $holiday->save();
        $request->session()->flash('success', 'Update Hari Libur berhasil');
        return redirect()->route('admin.holidays.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();
        request()->session()->flash('success', 'Hari Libur berhasil dihapus!');
        return back();
    }
}
