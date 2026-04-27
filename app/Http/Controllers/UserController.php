<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notifikasi;
use App\Models\Timkerja;
use App\Services\UserActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private UserActivityService $userActivityService
    ) {
    }

    public function index()
    {
        $users = User::with(['timkerja.subjek', 'creator', 'editor'])
            ->orderBy('nama')
            ->get();
        $timkerja = Timkerja::all();
        $accountRecoveryNotifications = Notifikasi::with('user')
            ->where('tipe', 'account_recovery')
            ->where('status_tindak_lanjut', false)
            ->latest()
            ->get();

        return view('pages.admin.user.index', compact('users', 'timkerja', 'accountRecoveryNotifications'));
    }

    public function create()
    {
        $timkerja = Timkerja::orderBy('nama_timkerja')->get();

        return view('pages.admin.user.tambah_user', compact('timkerja'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'username' => 'required|unique:tb_user,username',
            'nip' => 'required|unique:tb_user,nip',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:admin,operator,viewer',
            'id_timkerja' => 'nullable|required_unless:role,admin|exists:tb_timkerja,id_timkerja',
        ]);

        $validated['role'] = $validated['role'] ?? 'viewer';

        if ($validated['role'] === 'admin') {
            $validated['id_timkerja'] = null;
        }

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'nip' => $validated['nip'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'id_timkerja' => $validated['id_timkerja'] ?? null,
            'created_date' => now(),
            'created_by' => Auth::id(),
        ]);

        $this->userActivityService->log(
            $request->user(),
            'Tambah user',
            'Menambahkan user ' . $user->nama . ' dengan role ' . strtoupper((string) $user->role) . '.',
            $request
        );

        return back()->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'nip' => 'required|unique:tb_user,nip,' . $id . ',id_user',
            'role' => 'required|in:admin,operator,viewer',
            'id_timkerja' => 'nullable|required_unless:role,admin|exists:tb_timkerja,id_timkerja',
        ]);

        if ($validated['role'] === 'admin') {
            $validated['id_timkerja'] = null;
        }

        $user = User::findOrFail($id);

        $data = [
            'nama' => $validated['nama'],
            'nip' => $validated['nip'],
            'role' => $validated['role'],
            'id_timkerja' => $validated['id_timkerja'] ?? null,
            'modified_date' => now(),
            'modified_by' => Auth::id(),
        ];

        $user->update($data);

        $this->userActivityService->log(
            $request->user(),
            'Ubah user',
            'Memperbarui data user ' . $user->nama . ' (' . strtoupper((string) $user->role) . ').',
            $request
        );

        return redirect()->route('admin.user.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $deletedUserName = $user->nama;
        $deletedRole = $user->role;
        $user->delete();

        $this->userActivityService->log(
            $request->user(),
            'Hapus user',
            'Menghapus user ' . $deletedUserName . ' dengan role ' . strtoupper((string) $deletedRole) . '.',
            $request
        );

        return back()->with('success', 'User berhasil dihapus');
    }

    public function show($id)
    {
        return redirect()->route('admin.user.index');
    }

    public function edit($id)
    {
        return redirect()->route('admin.user.index');
    }

    public function resetCredentials(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $notification = Notifikasi::whereKey((int) $request->input('notification_id'))
            ->where('tipe', 'account_recovery')
            ->where('status_tindak_lanjut', false)
            ->firstOrFail();
        $metadata = $notification->metadata ?? [];
        $requestType = $metadata['jenis_permohonan'] ?? null;

        if ($requestType === 'username') {
            $validated = $request->validate([
                'new_username' => 'required|string|max:255|unique:tb_user,username,' . $id . ',id_user',
            ]);

            $user->update([
                'username' => $validated['new_username'],
                'modified_date' => now(),
                'modified_by' => Auth::id(),
            ]);

            $actionDescription = 'Mengubah username user ' . $user->nama . ' menjadi ' . $validated['new_username'] . ' berdasarkan notifikasi bantuan akun.';
            $successMessage = 'Username berhasil diubah dan notifikasi permintaan sudah diselesaikan.';
        } elseif ($requestType === 'password') {
            $validated = $request->validate([
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($validated['new_password']),
                'modified_date' => now(),
                'modified_by' => Auth::id(),
            ]);

            $actionDescription = 'Mengubah password user ' . $user->nama . ' berdasarkan notifikasi bantuan akun.';
            $successMessage = 'Password berhasil diubah dan notifikasi permintaan sudah diselesaikan.';
        } else {
            abort(422, 'Jenis permintaan akun tidak valid.');
        }

        $notification->update([
            'status_baca' => 'sudah',
            'status_tindak_lanjut' => true,
            'dibaca_pada' => now(),
            'ditindaklanjuti_pada' => now(),
            'ditindaklanjuti_oleh' => Auth::id(),
        ]);

        $this->userActivityService->log(
            $request->user(),
            'Tindak lanjut bantuan akun',
            $actionDescription,
            $request
        );

        return back()->with('success', $successMessage);
    }

    public function dismissRecoveryNotification(Request $request, int $notificationId)
    {
        $notification = Notifikasi::whereKey($notificationId)
            ->where('tipe', 'account_recovery')
            ->where('status_tindak_lanjut', false)
            ->firstOrFail();

        $notification->delete();

        $this->userActivityService->log(
            $request->user(),
            'Hapus notifikasi bantuan akun',
            'Menghapus notifikasi bantuan akun yang tidak valid.',
            $request
        );

        return back()->with('success', 'Notifikasi bantuan akun yang tidak valid sudah dihapus.');
    }
}
