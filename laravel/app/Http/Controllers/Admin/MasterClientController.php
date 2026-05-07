<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientModel;

class MasterClientController extends Controller
{
    public function index()
    {
        $title = 'Data Client';
        $description = 'Kelola data client perusahaan Anda.';
        $clients = ClientModel::all();
        return view('admin.master-client.index', compact('clients', 'title', 'description'));
    }

    public function create()
    {
        $title = 'Tambah Data Client';
        $description = 'Tambahkan data client baru ke dalam sistem.';
        return view('admin.master-client.create', compact('title', 'description'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_perusahaan' => 'required|string|unique:clients,id_perusahaan',
            'nama_perusahaan' => 'required|string',
            'email_perusahaan' => 'nullable|email',
            'nama_kontak_perusahaan' => 'nullable|string',
            'npwp_perusahaan' => 'nullable|string',
            'alamat_pengiriman_perusahaah' => 'nullable|string',
            'nomor_telepon_pengiriman' => 'nullable|string',
            'alamat_faktur_perusahaan' => 'nullable|string',
            'nomor_telepon_faktur' => 'nullable|string',
            'alamat_efaktur_perusahaan' => 'nullable|string',
            'nomor_rekening_perusahaan' => 'nullable|string',
        ]);
        $validated['created_by'] = 'System';
        ClientModel::create($validated);
        return redirect()->route('admin.master-client.index')->with('success', 'Data client berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $title = 'Edit Data Client';
        $description = 'Edit data client.';
        $client = ClientModel::findOrFail($id);
        return view('admin.master-client.edit', compact('client', 'title', 'description'));
    }

    public function update(Request $request, $id)
    {
        $client = ClientModel::findOrFail($id);
        $validated = $request->validate([
            'id_perusahaan' => 'required|string|unique:clients,id_perusahaan,' . $id,
            'nama_perusahaan' => 'required|string',
            'email_perusahaan' => 'nullable|email',
            'nama_kontak_perusahaan' => 'nullable|string',
            'npwp_perusahaan' => 'nullable|string',
            'alamat_pengiriman_perusahaah' => 'nullable|string',
            'nomor_telepon_pengiriman' => 'nullable|string',
            'alamat_faktur_perusahaan' => 'nullable|string',
            'nomor_telepon_faktur' => 'nullable|string',
            'alamat_efaktur_perusahaan' => 'nullable|string',
            'nomor_rekening_perusahaan' => 'nullable|string',
        ]);
        $client->update($validated);
        return redirect()->route('admin.master-client.index')->with('success', 'Data client berhasil diupdate.');
    }

    public function destroy($id)
    {
        $client = ClientModel::findOrFail($id);
        $client->delete();
        return redirect()->route('admin.master-client.index')->with('success', 'Data client berhasil dihapus.');
    }
}
