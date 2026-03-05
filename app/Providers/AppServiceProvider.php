<?php

namespace App\Providers;

use App\Models\Level_menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Menu;
use App\Models\Head_menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {

            View::share('user', auth()->user());

            if (auth()->check()) {
                $level_menus = Level_menu::where('id_level', auth()->user()->id_level)->get();
                $first = Menu::first();
                $menus = Menu::get();

                // Ambil semua id_menu yang boleh diakses oleh level user ini
                $allowed_menu_ids = $level_menus->pluck('id_menu')->toArray();

                $panel_menus = [];
                foreach ($menus as $menu) {
                    // Cek apakah menu ini ada dalam daftar yang diizinkan
                    if (in_array($menu->id, $allowed_menu_ids)) {
                        // Hanya tambahkan parent menus (bukan submenu) dan bukan head_menu
                        if ($menu->id_head_menu == null && $menu->parent_id == null) {
                            $panel_menus[] = $menu;
                        }
                    }
                }

                $head_menus = Head_menu::get();

                View::share([
                    'first_menu'  => $first,
                    'panel_menus' => $panel_menus,
                    'head_menus'  => $head_menus,
                    'level_menus' => $level_menus,
                ]);
            }
        });
    }
}
