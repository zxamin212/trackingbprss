<?php

namespace App\Http\Controllers;

class DireksiController extends Controller
{
    public function dashboard()
    {
        return view('direksi.dashboard');
    }

    public function berkasIndex()
    {
        return view('direksi.berkas.index');
    }

    public function berkasShow($id)
    {
        return view('direksi.berkas.show');
    }

    public function laporanIndex()
    {
        return view('direksi.laporan.index');
    }
}