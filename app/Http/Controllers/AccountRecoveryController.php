<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountRecoveryController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:150',
            'nip' => 'required|string|max:50',
            'jenis_permohonan' => 'required|in:username,password',
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'accountRecovery')
                ->withInput($request->only('nama', 'nip', 'jenis_permohonan', 'catatan'));
        }

        $validated = $validator->validated();

        $user = User::query()
            ->where('nip', $validated['nip'])
            ->whereRaw('LOWER(TRIM(nama)) = ?', [mb_strtolower(trim($validated['nama']))])
            ->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'account_recovery' => 'Nama dan NIP tidak cocok dengan data akun. Permintaan tidak dikirim.',
                ], 'accountRecovery')
                ->withInput($request->only('nama', 'nip', 'jenis_permohonan', 'catatan'));
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

        return back()->with('account_recovery_status', 'Permintaan berhasil dikirim. Admin akan menerima notifikasi.');
    }
}
