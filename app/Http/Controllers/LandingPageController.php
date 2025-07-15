<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestasi; // Model yang relevan dengan sistem Anda
use App\Models\BeritaUKT; // Contoh model untuk berita terkait UKT

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil data yang perlu ditampilkan di landing page
        $prestasiTerbaru = Prestasi::latest()->take(3)->get();
        $beritaUKT = BeritaUKT::where('status', 'published')->latest()->take(5)->get();
        
        return view('landing.index', compact('prestasiTerbaru', 'beritaUKT'));
    }
}