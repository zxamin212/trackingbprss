<?php

namespace App\Http\Controllers;

use App\Models\BerkasKredit;
use App\Models\Kantor;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Berkas
    public function berkasIndex()
    {
        return view('admin.berkas.index');
    }

    public function berkasShow($id)
    {
        return view('admin.berkas.show');
    }

    // Kantor
    public function kantorIndex()
    {
        return view('admin.kantor.index');
    }

    public function kantorStore(Request $request) {}
    public function kantorUpdate(Request $request, $id) {}
    public function kantorDestroy($id) {}

    // User
    public function userIndex()
    {
        return view('admin.user.index');
    }

    public function userStore(Request $request) {}
    public function userUpdate(Request $request, $id) {}
    public function userDestroy($id) {}

    // Laporan
    public function laporanIndex()
    {
        return view('admin.laporan.index');
    }

    public function exportProses(Request $request) {}
}