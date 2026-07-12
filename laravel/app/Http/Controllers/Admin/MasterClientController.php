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
            'id_customer'       => 'required|string|max:5|unique:customers,id_customer',
            'nama_perusahaan'   => 'required|string',
            'nama_kontak'       => 'required|string|max:100',
            'email_perusahaan'  => 'nullable|email|max:100',
            'npwp_perusahaan'   => 'nullable|string|max:50',
            'alamat_perusahaan' => 'required|string',
            'alamat_faktur'     => 'nullable|string',
            'alamat_efaktur'    => 'nullable|string',
            'telepon_faktur'    => 'nullable|string|max:20',
            'telepon_efaktur'   => 'nullable|string|max:20',
            'rekening_perusahaan' => 'nullable|string|max:50',
        ]);

        ClientModel::create($validated);

        return redirect()->route('admin.master-clients.index')
            ->with('success', 'Data client berhasil ditambahkan.');
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
            'id_customer'       => 'required|string|max:5|unique:customers,id_customer,' . $id . ',id_customer',
            'nama_perusahaan'   => 'required|string',
            'nama_kontak'       => 'required|string|max:100',
            'email_perusahaan'  => 'nullable|email|max:100',
            'npwp_perusahaan'   => 'nullable|string|max:50',
            'alamat_perusahaan' => 'required|string',
            'alamat_faktur'     => 'nullable|string',
            'alamat_efaktur'    => 'nullable|string',
            'telepon_faktur'    => 'nullable|string|max:20',
            'telepon_efaktur'   => 'nullable|string|max:20',
            'rekening_perusahaan' => 'nullable|string|max:50',
        ]);

        $client->update($validated);

        return redirect()->route('admin.master-clients.index')
            ->with('success', 'Data client berhasil diupdate.');
    }

    public function destroy($id)
    {
        $client = ClientModel::findOrFail($id);
        $client->delete();

        return redirect()->route('admin.master-clients.index')
            ->with('success', 'Data client berhasil dihapus.');
    }
}