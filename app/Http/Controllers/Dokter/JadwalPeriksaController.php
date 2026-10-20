<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPeriksaController extends Controller
{
    private const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index()
    {
        $jadwals = JadwalPeriksa::where('id_dokter', Auth::id())->orderBy('id')->get();

        return view('dokter.jadwal-periksa.index', compact('jadwals'));
    }

    public function create()
    {
        $haris = self::HARI;

        return view('dokter.jadwal-periksa.create', compact('haris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => 'required|in:'.implode(',', self::HARI),
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        JadwalPeriksa::create([
            'id_dokter' => Auth::id(),
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return redirect()->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil di tambahkan')
            ->with('type', 'success');
    }

    public function edit(string $id)
    {
        $jadwal = $this->findJadwalDokter($id);
        $haris = self::HARI;

        return view('dokter.jadwal-periksa.edit', compact('jadwal', 'haris'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'hari' => 'required|in:'.implode(',', self::HARI),
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal = $this->findJadwalDokter($id);
        $jadwal->update($validated);

        return redirect()->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil di update')
            ->with('type', 'success');
    }

    public function destroy(string $id)
    {
        $jadwal = $this->findJadwalDokter($id);
        $jadwal->delete();

        return redirect()->route('jadwal-periksa.index')
            ->with('success', 'Jadwal periksa berhasil dihapus')
            ->with('type', 'success');
    }

    private function findJadwalDokter(string $id): JadwalPeriksa
    {
        return JadwalPeriksa::where('id_dokter', Auth::id())->findOrFail($id);
    }
}
