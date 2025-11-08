<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekUserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //hanya admin (level 1) dan ketua (level 3) yang boleh melewati middleware ini
        if (Auth::user()->id_level != 1 && Auth::user()->id_level != 3 && Auth::user()->id_level != 6){
            abort(404);
        }


        // if (!Auth::check()){
        //     return redirect('login');
        // }

        // $user = Auth::user();
        // if ($user->level == $rules){
        //     return $next($request);
        // }

        // return redirect('login')->with('error', "Anda tidak ada akses");

        // // Mendapatkan level pengguna
        // $userLevel = Auth::user()->level;

        // // Mendapatkan konfigurasi menu dari database berdasarkan level
        // $menuConfig = $this->getMenuConfig($userLevel);

        // // Memeriksa izin akses berdasarkan konfigurasi menu
        // if ($this->hasPermission($request->path(), $menuConfig)) {
        //     return $next($request);
        // }

        // Redirect ke halaman yang sesuai jika tidak diizinkan
        return $next($request);
    }
    protected function getMenuConfig($userLevel)
    {
        // Ambil konfigurasi menu dari database berdasarkan level pengguna
        $user = Auth::user();
        $menuConfig = $user->menu_config;

        return $menuConfig ?? [];
    }

    protected function hasPermission($currentRoute, $menuConfig)
    {
        // Implementasikan logika untuk memeriksa izin akses berdasarkan konfigurasi menu
        $allowedRoutes = array_column($menuConfig, 'route');
        return in_array($currentRoute, $allowedRoutes);
    }
}
