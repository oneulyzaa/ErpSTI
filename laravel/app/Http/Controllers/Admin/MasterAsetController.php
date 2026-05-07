<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AsetModel;

class MasterAssetController extends Controller
{
    function index(){
        $assets = AsetModel::all(); // Ambil semua data aset dari database
        return view('admin.master-aset.index', compact('assets')); // Kirim data aset
    }
    function create(){
        return view('admin.master-aset.create');
    }
    function edit($id){
        $asset = AsetModel::findOrFail($id);
        return view('admin.master-aset.edit', compact('asset'));
    }
    function show($id){
        $asset = AsetModel::findOrFail($id);
        return view('admin.master-aset.show', compact('asset'));
    }
    function store(Request $request){
        
        // Validasi data
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'satuan' => 'required|string|max:10',
            'stok' => 'required|integer',
            'supplier_from' => 'nullable|string|max:255',
        ]);

        // Simpan data ke database (ganti dengan model yang sesuai)
        $model = new AsetModel();
        $model->nama_aset = $request->nama_aset;
        $model->harga = $request->harga;
        $model->satuan = $request->satuan;
        $model->stok = $request->stok;
        $model->supplier_from = $request->supplier_from ?? null;
        $model->save();

        return redirect()->route('admin.master-aset.index')->with('success', 'Data berhasil ditambahkan.');
    }
}
