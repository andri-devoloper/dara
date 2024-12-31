<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perputakan;
use Carbon\Carbon;

class PerputakanController extends Controller
{
    public function showForm()
    {
        $perputakanList = Perputakan::all();
        return view('perputakan.form', compact('perputakanList'));
    }

    public function submitForm(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|numeric',
            'kelas' => 'required|string|max:50',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $status_masuk = Carbon::now();

        $perputakan = Perputakan::create([
            'nama' => $validated['nama'],
            'nis' => $validated['nis'],
            'kelas' => $validated['kelas'],
            'keterangan' => $validated['keterangan'],
            'status_masuk' => $status_masuk,
        ]);

        // Simpan data ke database atau tampilkan sebagai respon
        // Misalnya, kita hanya menampilkan data yang sudah diterima:
        return back()->with('success', 'Data berhasil dikirim!')->with('data', $validated);
    }

    public function updateStatusKeluar($id)
    {
        $perputakan = Perputakan::findOrFail($id);

        // Waktu keluar
        $status_keluar = Carbon::now();

        // Hitung durasi dalam menit
        $durasi = $status_keluar->diffInMinutes($perputakan->status_masuk);

        // Update data
        $perputakan->update([
            'status_keluar' => $status_keluar,
            'durasi' => $durasi,
        ]);

        return back()->with('success', 'Status keluar berhasil diupdate! Durasi: ' . $durasi . ' menit');
    }
    // public function checkNis($nis)
    // {
    //     // Cari data perputakan berdasarkan NIS
    //     $perputakan = Perputakan::where('nis', $nis)->first();

    //     if ($perputakan) {
    //         // Jika NIS ditemukan, kembalikan data
    //         return response()->json([
    //             'exists' => true,
    //             'nama' => $perputakan->nama,
    //             'nis' => $perputakan->nis,
    //             'kelas' => $perputakan->kelas,
    //             'keterangan' => $perputakan->keterangan,
    //         ]);
    //     } else {
    //         // Jika NIS tidak ditemukan
    //         return response()->json([
    //             'exists' => false
    //         ]);
    //     }
    // }

    public function store(Request $request)
    {
        // Validasi dan simpan data perputakan
        $request->validate([
            'nama' => 'required',
            'nis' => 'required|unique:perputakan',
            'kelas' => 'required',
            'keterangan' => 'required'
        ]);

        // Menyimpan data dengan status_masuk terisi
        $perputakan = Perputakan::create([
            'nama' => $request->input('nama'),
            'nis' => $request->input('nis'),
            'kelas' => $request->input('kelas'),
            'keterangan' => $request->input('keterangan'),
            'status_masuk' => now(), // Set waktu sekarang sebagai status masuk
            'status_keluar' => null,  // Set status keluar masih kosong
            'durasi' => null,         // Set durasi sebagai null
        ]);

        return redirect()->back()->with('success', 'Data berhasil disimpan!');
    }


    public function checkNis(Request $request)
    {
        // Ambil NIS dari permintaan
        $nis = $request->input('nis');

         // Ambil data dari database berdasarkan NIS
        $data = Perputakan::where('nis', $nis)->first();

        // Jika data ditemukan, kembalikan data; jika tidak, kembalikan pesan kosong
        if ($data) {
            return response()->json([
                'exists' => true,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'exists' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    public function handleEntry(Request $request)
    {
        // Ambil NIS dari permintaan
        $nis = $request->input('nis');

        // Cek apakah NIS ada di database
        $existingRecord = Perputakan::where('nis', $nis)->first();

        if ($existingRecord) {
            // Buat duplikat data dengan status masuk baru
            Perputakan::create([
                'nama' => $existingRecord->nama,
                'nis' => $existingRecord->nis,
                'kelas' => $existingRecord->kelas,
                'keterangan' => $existingRecord->keterangan,
                'status_masuk' => now(),
                'status_keluar' => null,
                'durasi' => null,
            ]);

            return response()->json(['success' => true, 'message' => 'Data baru berhasil dibuat dan masuk']);
            // 'message' => 'Data baru berhasil dibuat dan masuk'
        } else {
            // Arahkan ke form untuk data baru
            return response()->json(['success' => false, 'redirect' => route('showForm')]);
        }
    }


    public function handleExit(Request $request)
    {
        // Ambil NIS dari permintaan
        $nis = $request->input('nis');

        // Cek apakah NIS dan status masuk ada
        $record = Perputakan::where('nis', $nis)
            ->whereNotNull('status_masuk')
            ->whereNull('status_keluar')
            ->first();

        if ($record) {
            // Hitung durasi (dalam menit)
            $timeIn = $record->status_masuk;
            $timeOut = now();
            $duration = $timeOut->diffInMinutes($timeIn);

            // Perbarui status keluar dan durasi
            $record->update([
                'status_keluar' => $timeOut,
                'durasi' => $duration,
            ]);

            return response()->json(['success' => true]);
            // 'message' => 'Berhasil keluar', 'durasi' => $duration
        } else {
            // Tidak ada catatan masuk, kemungkinan data tidak ditemukan atau belum melakukan proses masuk
            return response()->json(['success' => false]);
            //, 'message' => 'Tidak ada catatan masuk ditemukan'
        }
    }


    public function checkStatus(Request $request)
    {
        $nis = $request->input('nis');

        // Cari data dengan status masuk yang belum keluar
        $record = Perputakan::where('nis', $nis)
            ->whereNotNull('status_masuk')
            ->whereNull('status_keluar')
            ->first();

        if ($record) {
            // Jika ada catatan status masuk yang belum keluar
            return response()->json(['success' => true, 'status' => 'masuk']);
        } else {
            // Jika tidak ada catatan status masuk
            return response()->json(['success' => true, 'status' => 'keluar']);
        }
    }

}
