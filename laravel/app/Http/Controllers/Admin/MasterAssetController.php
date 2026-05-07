<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AsetModel;

class MasterAssetController extends Controller
{
    function index(){
        $title = 'Data Asset';
        $description = 'Kelola data aset perusahaan Anda dengan mudah dan efisien.';
        $assets = AsetModel::all(); // Ambil semua data aset dari database
        return view('admin.master-asset.index', compact('assets','title','description')); // Kirim data aset
    }
    function create(){
        $title = 'Tambah Data Asset';
        $description = 'Tambahkan data aset baru ke dalam sistem.';
        return view('admin.master-asset.create', compact('title', 'description'));
    }
    function edit($id){
        $title = 'Perbarui Data Asset';
        $description = 'Perbarui data aset yang sudah ada.';
        $asset = AsetModel::findOrFail($id);
        return view('admin.master-asset.edit', compact('asset', 'title', 'description'));
    }
    function show($id){
        $asset = AsetModel::findOrFail($id);
        return view('admin.master-asset.show', compact('asset'));
    }
    function store(Request $request){
        
        // Validasi data
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'harga' => 'required|string',
            'satuan' => 'required|string|max:10',
            'stok' => 'required|integer',
            'supplier_from' => 'nullable|string|max:255',
            'status' => 'required|integer',
        ]);
        // pada harga, semisal inputnya 3.000.000, maka kita harus menghapus titiknya agar bisa disimpan sebagai angka di database
        $validated['harga'] = str_replace('.', '', $validated['harga']);
        $harga = (int) $validated['harga'];

        // Simpan data ke database (ganti dengan model yang sesuai)
        $model = new AsetModel();
        $model->nama_aset = $request->nama_aset;
        $model->harga = $harga;
        $model->satuan = $request->satuan;
        $model->stok = $request->stok;
        $model->supplier_from = $request->supplier_from ?? null;
        $model->is_active = $request->status;
        $model->save();

        return redirect()->route('admin.master-assets.index')->with('success', 'Data berhasil ditambahkan.');
    }
    function update(Request $request, $id){
        // Validasi data
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'harga' => 'required|string',
            'satuan' => 'required|string|max:10',
            'stok' => 'required|integer',
            'supplier_from' => 'nullable|string|max:255',
            'status' => 'required|integer',
        ]);
        // pada harga, semisal inputnya 3.000.000, maka kita harus menghapus titiknya agar bisa disimpan sebagai angka di database
        $validated['harga'] = str_replace('.', '', $validated['harga']);
        $harga = (int) $validated['harga'];

        // Update data di database (ganti dengan model yang sesuai)
        $model = AsetModel::findOrFail($id);
        $model->nama_aset = $request->nama_aset;
        $model->harga = $harga;
        $model->satuan = $request->satuan;
        $model->stok = $request->stok;
        $model->supplier_from = $request->supplier_from ?? null;
        $model->is_active = $request->status;
        $model->save();

        return redirect()->route('admin.master-assets.index')->with('success', 'Data berhasil diperbarui.');
    }
    function destroy($id){
        $model = AsetModel::findOrFail($id);
        $model->delete();
        return redirect()->route('admin.master-assets.index')->with('success', 'Data berhasil dihapus.');
    }
}
