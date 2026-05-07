<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AsetModel;

class MasterClientController extends Controller
{
    function index(){
        return view('admin.master-aset.index');
    }
    function create(){
        return view('admin.master-aset.create');
    }
    function edit($id){
        return view('admin.master-aset.edit', compact('id'));
    }
    function show($id){
        return view('admin.master-aset.show', compact('id'));
    }
    function store(Request $request){
        // Validasi data
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'kondisi' => 'required|string|max:255',
            'tanggal_perolehan' => 'required|date',
            'nilai_perolehan' => 'required|numeric',
        ]);

        // Simpan data ke database (ganti dengan model yang sesuai)
        // Contoh: Aset::create($validated);

        return redirect()->route('admin.master-aset.index')->with('success', 'Aset berhasil ditambahkan.');
    }
}
