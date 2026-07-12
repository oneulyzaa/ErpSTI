<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;

class MasterAssetController extends Controller
{
    function index()
    {
        $title = 'Data Material';
        $description = 'Kelola data material perusahaan Anda dengan mudah dan efisien.';
        $materials = Material::all();
        return view('admin.master-asset.index', compact('materials', 'title', 'description'));
    }

    function create()
    {
        $title = 'Tambah Data Material';
        $description = 'Tambahkan data material baru ke dalam sistem.';
        return view('admin.master-asset.create', compact('title', 'description'));
    }

    function edit($id)
    {
        $title = 'Perbarui Data Material';
        $description = 'Perbarui data material yang sudah ada.';
        $material = Material::findOrFail($id);
        return view('admin.master-asset.edit', compact('material', 'title', 'description'));
    }

    function show($id)
    {
        $material = Material::findOrFail($id);
        return view('admin.master-asset.show', compact('material'));
    }

    function store(Request $request)
    {
        $validated = $request->validate([
            'nama_material'   => 'required|string|max:255',
            'harga_material'  => 'required|string',
            'satuan'          => 'required|string|max:50',
            'stok'            => 'required|integer',
            'supplier'        => 'nullable|string|max:255',
            'status_material' => 'required|string|max:50',
        ]);

        // Hapus format titik pada harga (contoh: 3.000.000 → 3000000)
        $validated['harga_material'] = str_replace('.', '', $validated['harga_material']);

        Material::create($validated);

        return redirect()->route('admin.master-assets.index')->with('success', 'Data berhasil ditambahkan.');
    }

    function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_material'   => 'required|string|max:255',
            'harga_material'  => 'required|string',
            'satuan'          => 'required|string|max:50',
            'stok'            => 'required|integer',
            'supplier'        => 'nullable|string|max:255',
            'status_material' => 'required|string|max:50',
        ]);

        // Hapus format titik pada harga
        $validated['harga_material'] = str_replace('.', '', $validated['harga_material']);

        $material = Material::findOrFail($id);
        $material->update($validated);

        return redirect()->route('admin.master-assets.index')->with('success', 'Data berhasil diperbarui.');
    }

    function destroy($id)
    {
        $material = Material::findOrFail($id);
        $material->delete();
        return redirect()->route('admin.master-assets.index')->with('success', 'Data berhasil dihapus.');
    }
}