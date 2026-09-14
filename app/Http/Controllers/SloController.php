<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use Illuminate\Http\Request;

class SloController extends Controller
{
    public function dashboard()
    {
        return view('slo.dashboard');
    }

    // Berkas yang perlu di-survey/dianalisa
    public function berkasIndex()
    {
        return view('slo.berkas.index');
    }

    public function berkasShow($id)
    {
        return view('slo.berkas.show');
    }

    // Update hasil survey
    public function updateSurvey(Request $request, $id)
    {
        //
    }

    // Update keputusan komite (lanjut / ditolak)
    public function updateKomite(Request $request, $id)
    {
        //
    }

    // Batalkan berkas
    public function batalkan(Request $request, $id)
    {
        //
    }
}