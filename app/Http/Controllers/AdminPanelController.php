<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\User;

class AdminPanelController extends Controller
{
    public function index()
    {
        $adminUsers = User::where('level', 1)->get();
        // Ambil semua menu
        $menus = Menu::all();
        // Tampilkan formulir untuk mengonfigurasi menu
        return view('admin.panel', [
            'users' => $adminUsers, // Ambil semua pengguna untuk dipilih
            'menus' => $menus,
        ]);
    }

    public function __construct()
    {
        $this->middleware(['auth', 'cekUserLogin:1']);
    }

    public function saveMenuConfig(Request $request, $userId)
    {
        $request->validate([
            'menu_config' => 'required|json',
            'menu_visibility' => 'array',
        ]);

        $user = User::find($userId);

        $user->update([
            'menu_config' => $request->input('menu_config'),
        ]);

        // Jika ada menu_visibility, simpan ke tabel pivot atau metode lain yang sesuai
        if ($request->has('menu_visibility')) {
            $user->menus()->sync($request->input('menu_visibility'));
        }

        return redirect()->back()->with('success', 'Konfigurasi menu berhasil disimpan');
    }
}
