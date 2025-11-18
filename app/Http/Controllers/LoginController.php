<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Level;
use App\Models\TemplateDokumen;
use App\Models\UnitKerja;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function index()
    {
        if (Auth::user()) {
            // if ($user->level == '1') {
            //     return redirect()->intended('beranda');
            // } elseif ($user->level == '2') {
            //     return redirect()->intended('kasir');
            // }
            return redirect()->intended('dashboard');
        }
        return view('login.view_login');
    }

    public function manualbook()
    {
        $templatePath = ('all_template/');
        $files = File::files($templatePath);
        $peraturan = TemplateDokumen::where('id_jenis', 3)->get();

        return view('login.manual-book', compact('files', 'peraturan'));
    }

    public function proses(Request $request)
    {
        $request->validate(
            [
                'username' => 'required',
                'password' => 'required',
            ],
            [
                'username.required' => 'Username tidak boleh kosong',
            ]
        );

        // Determine if the user is logging in with email or username
            if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
                // User is logging in with email and must have level 1
                $user = User::where('email', $request->username)->where('id_level', 1)->first();
            } else {
                // User is logging in with username and must have level 6
                $areSuperAdmin = User::where('username', $request->username)->where('id_level', 1)->first();
                if ($areSuperAdmin) {
                    $user = User::where('username', $request->username)->where('id_level', 6)->first();
                } else {
                    $user = User::where('username', $request->username)->first();
                }
            }

        // Check if the user exists and if the password is correct
        if ($user && Hash::check($request->password, $user->password)) {
            // Log the user in manually
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        // If authentication fails
        return back()->withErrors([
            'username' => 'Maaf username atau password anda salah',
        ])->withInput($request->only('username'));
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/welcome');
    }
    public function create()
    {
        $levels = Level::all();
        $unit_kerjas = UnitKerja::all();
        return view('login.register')->with([
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
}
