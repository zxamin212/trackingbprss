<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use Illuminate\Http\Request;

class AdminLegalController extends Controller
{
    public function dashboard()
    {
        return view('legal.dashboard');
    }

    public function berkasIndex()
    {
        return view('legal.berkas.index');
    }

    public function berkasShow($id)
    {
        return view('legal.berkas.show');
    }

    public function updateAkad(Request $request, $id)
    {
        //
    }

    public function belumLengkap(Request $request, $id)
    {
        //
    }

    public function pencairan(Request $request, $id)
    {
        //
    }

    public function batalkan(Request $request, $id)
    {
        //
    }
}