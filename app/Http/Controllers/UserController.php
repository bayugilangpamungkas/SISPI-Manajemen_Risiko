<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Level;
use App\Models\UnitKerja;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        //get users
        $users = User::with('unitKerja')->latest()->paginate(5);
        // $users = Level::all();

        //render view with users
        return view('users.userView', compact('users'))->with([
            'user' => Auth::user(),
            'active' => 10,
        ]);
    }

    public function create()
    {
        $levels = Level::all();
        $unit_kerjas = UnitKerja::all();
        return view('users.tambahUser')->with([
            'active' => 10,
            'user' => Auth::user(),
            'levels' => $levels,
            'unit_kerjas' => $unit_kerjas,
        ]);
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */

    public function store(Request $request)
    {
        // dd($request);
        //validate form
        $this->validate($request, [
            'name'     => 'required|min:3',
            'username' => 'required',
            'email' => 'required|min:4|email|unique:users',
            'nip'     => 'required|min:1',
            'password' => 'required',
            'confirmation' => 'required|same:password',
            'id_unit_kerja' => 'required',
            'id_level'     => 'required',
        ]);

        //create user
        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'nip'    => $request->nip,
            'password' => bcrypt($request->password),
            'id_unit_kerja' => $request->id_unit_kerja,
            'id_level'     => $request->id_level,
        ]);

        event(new Registered($user));
        Auth::login($user);

        //redirect to index
        return redirect('/email/verify');
    }

    public function storeFromAdmin(Request $request)
    {
        // dd($request);
        //validate form
        $this->validate($request, [
            'name'     => 'required|min:3',
            'username' => 'required',
            'email' => 'required|min:4|email|unique:users',
            'nip'     => 'required|min:1',
            'password' => 'required',
            'confirmation' => 'required|same:password',
            'id_unit_kerja' => 'required',
            'id_level'     => 'required',
        ]);

        //create user
        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'email_verified_at' => now(),
            'nip'    => $request->nip,
            'password' => bcrypt($request->password),
            'id_unit_kerja' => $request->id_unit_kerja,
            'id_level'     => $request->id_level,
        ]);

        //redirect to index
        return redirect(route('users.index'))->with('success', 'User berhasil ditambahkan!');
    }

    //tampil data
    public function tampilDataUser($id)
    {
        $users = User::find($id);
        $levels = Level::all();
        $unit_kerjas = UnitKerja::all();
        //dd($users);
        return view('users.tampilEditUser', compact('users', 'levels', 'unit_kerjas'))->with([
            'user' => Auth::user(),
            'active' => 10,
        ]);
    }

    //Edit Data
    public function updateDataUser(Request $request, $id)
    {
        // Validasi input
        $this->validate($request, [
            'name' => 'required|min:3',
            'username' => 'required',
            'email' => 'required|min:4|email|unique:users,email,' . $id,
            'nip' => 'required|min:1',
            'id_unit_kerja' => 'required',
            'id_level' => 'required',
        ]);

        // Mencari user berdasarkan ID
        $user = User::find($id);

        // Update data user kecuali password
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->nip = $request->nip;
        $user->id_unit_kerja = $request->id_unit_kerja;
        $user->id_level = $request->id_level;

        // Jika password diisi, hash password baru
        if ($request->filled('password')) {
            $this->validate($request, [
                'password' => 'required|confirmed',
            ]);
            $user->password = bcrypt($request->password);
        }

        // Simpan perubahan
        $user->save();

        return redirect()->route('users.index')->with('success', 'Data Berhasil Diupdate!');
    }

    //Update Profil
    public function updateProfile(Request $request, $id)
    {
        $this->validate($request, [
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tanda_tangan' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'password' => 'nullable|min:4|confirmed',
        ]);

        $user = User::find($id);

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = ('profile_pictures/');
            $image->move($destinationPath, $name);
            $user->profile_picture = $name;
        }

        if ($request->hasFile('tanda_tangan')) {
            $image = $request->file('tanda_tangan');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = ('tanda_tangans/');
            $image->move($destinationPath, $name);
            $user->tanda_tangan = $name;
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('profileDataUser', $id)->with('success', 'Profile updated successfully');
    }

    //Hapus Data User
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
    //Searching
    public function show(Request $request)
    {
        if ($request->has('search')) {
            $users = User::where('name', 'nip', 'LIKE', '%' . $request->search . '%')->get();
        } else {
            $users = User::all();
        }

        return view('users.userView', ['users' => $users, 'active' => 10]);
    }

    public function search(Request $request)
    {
        $active = 10;
        $search = $request->input('search');
        $users = User::where('name', 'like', '%' . $search . '%')
            ->orWhere('nip', 'like', '%' . $search . '%')
            ->paginate(10);
        return view('users.userView', compact('active', 'users'));
    }

    //profil user
    public function profileDataUser()
    {
        $active = 10;
        $user = User::with('unitKerja')->where('id', Auth::user()->id)->first();
        if (!$user) {
            abort(404); // Jika user tidak ditemukan, tampilkan halaman 404
        }
        $levels = Level::all();
        // dd($user);/
        return view('profile.profileView', compact('active', 'user', 'levels'));
    }
    public function approveUser($id)
    {
        $user = User::find($id);
        $user->is_approved = true;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil disetujui!');
    }

    public function disapproveUser($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil ditolak!');
    }
}
