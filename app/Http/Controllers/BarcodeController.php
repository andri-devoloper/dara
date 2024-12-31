<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function showForm()
    {
        return view('barcode');
    }

    public function generateBarcode(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string',
            'id' => 'required|string',
            'keterangan' => 'required|string',
        ]);

        return view('barcode', [
            'nama' => $validatedData['nama'],
            'id' => $validatedData['id'],
            'keterangan' => $validatedData['keterangan'],
            'barcode' => true, // Flag untuk menampilkan hasil barcode
        ]);
    }
}
