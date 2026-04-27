<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;

class AccountRecoveryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'required|string|max:50',
            'jenis_permohonan' => 'required|in:username,password',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = User::query()
            ->where('nip', $validated['nip'])
            ->whereRaw('LOWER(TRIM(nama)) = ?', [mb_strtolower(trim($validated['nama']))])
            ->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'account_recovery' => 'Nama dan NIP tidak cocok dengan data akun. Permintaan tidak dikirim.',
                ])
                ->withInput();
        }

        Notifikasi::create([
            'id_user' => $user->id_user,
            'id_sop' => null,
            'judul' => 'Permintaan bantuan akun',
            'tipe' => 'account_recovery',
            'pesan' => sprintf(
                '%s mengajukan permintaan lupa %s.',
                $validated['nama'],
                $validated['jenis_permohonan'] === 'username' ? 'username' : 'password'
            ),
            'status_baca' => 'belum',
            'status_tindak_lanjut' => false,
            'metadata' => [
                'nama' => $validated['nama'],
                'nip' => $validated['nip'],
                'jenis_permohonan' => $validated['jenis_permohonan'],
                'catatan' => $validated['catatan'] ?? null,
                'resolved_user_id' => $user->id_user,
            ],
        ]);

        return back()->with('status', 'Permintaan berhasil dikirim. Admin akan menerima notifikasi.');
    }
}
