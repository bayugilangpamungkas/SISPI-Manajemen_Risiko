<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Head_menu;
use App\Models\Level;
use App\Models\Level_menu;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = Menu::all();
        $menus_heads = Menu::where('id_head_menu', null)->get();
        $menus_head = $menus_heads->shift();
        $menus_head = $menus_heads->all();
        $head_menus = Head_menu::all();
        $levels = Level::all()->except(1);
        $List_menus = Level_menu::all();
        return view('admin.panel', [
            'active' => 9,
            'menus' => $menus,
            'menus_head' => $menus_head,
            'head_menus' => $head_menus,
            'levels' => $levels,
            'List_menus' => $List_menus,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $menu = new Menu;

        $menu->name = $request->get('name');
        $menu->link = $request->get('link');
        $menu->icon = $request->get('icon');
        // if ($request->get('admin')) {

        //     $menu->ketua = "1";
        // }
        // if ($request->get('ketua')) {

        //     $menu->anggota = "1";
        // }
        // if ($request->get('anggota')) {

        //     $menu->anggota = "1";
        // }
        // if ($request->get('auditee')) {

        //     $menu->anggota = "1";
        // }
        // dd($menu);
        $menu->save();


        $lastMenu = Menu::latest()->first();
        $level = Level::count();
        // dd($level);

        for ($x = 1; $x <= $level; $x++) {
            if ($x == 1) {
                $levelmenu = new Level_menu();
                $levelmenu->id_level = 1;
                $levelmenu->id_menu = $lastMenu->id;
                $levelmenu->save();
            } elseif ($x > 1) {

                if ($request->get('level' . $x) !== null) {
                    $levelmenu = new Level_menu();
                    $levelmenu->id_level = $request->get('level' . $x);
                    $levelmenu->id_menu = $lastMenu->id;
                    $levelmenu->save();
                }
            }
        };
        return redirect('/admin/panel/')->with('success', 'Panel berhasil ditambah');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $menu = Menu::where('id', $id)->first();
        if ($request->get('name')) {

            $menu->name = $request->get('name');
            $menu->save();
        }
        if ($request->get('link')) {

            $menu->link = $request->get('link');
            $menu->save();
        }
        if ($request->get('icon')) {

            $menu->icon = $request->get('icon');
            $menu->save();
        }
        if ($request->get('level')) {
            $List_menus = Level_menu::all();
            $id_level = $request->get('level');
            if ($List_menus->where('id_level', $id_level)->where('id_menu', $id)->first() == null) {

                $Level_menu = new Level_menu();
                $Level_menu->id_level = $id_level;
                $Level_menu->id_menu = $id;
                $Level_menu->save();
            } else {

                $level_menu_del = $List_menus->where('id_level', $id_level)->where('id_menu', $id)->first();
                $level_menu_del->delete();
            }
        }
        // if ($request->get('admin')) {
        //     if($request->get('admin') == "false"){
        //         $menu->admin = "0";
        //     }else{
        //         $menu->admin = $request->get('admin');
        //     }
        //     $menu->save();
        // }
        // if ($request->get('ketua')) {
        //     if($request->get('ketua') == "false"){
        //         $menu->ketua = "0";
        //     }else{
        //         $menu->ketua = $request->get('ketua');
        //     }
        //     $menu->save();
        // }
        // if ($request->get('anggota')) {
        //     if($request->get('anggota') == "false"){
        //         $menu->anggota = "0";
        //     }else{
        //         $menu->anggota = $request->get('anggota');
        //     }
        //     $menu->save();
        // }
        // if ($request->get('auditee')) {
        //     if($request->get('auditee') == "false"){
        //         $menu->auditee = "0";
        //     }else{
        //         $menu->auditee = $request->get('auditee');
        //     }
        //     $menu->save();
        // }

        return redirect('/admin/panel/')->with('success', 'Panel berhasil diedit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        $menu->Level_menu()->delete();
        $menu->delete();

        return redirect('/admin/panel')->with('success', 'Panel berhasil dihapus');
    }

    public function storeHead(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => 'required|unique:head_menus,name',
            'icon' => 'required',
        ]);
        $headMenu = new Head_Menu;
        $headMenu->name = $request->get('name');
        $headMenu->icon = $request->get('icon');
        $headMenu->save();

        $recent_head = Head_Menu::latest('created_at')->first();

        for ($x = 0; $x < count($request->get('id_menu')); $x++) {
            $menu = Menu::where('id', $request->get('id_menu')[$x])->first();
            if($menu->id_head_menu == null) {
                $menu->id_head_menu = $recent_head->id;
                $menu->save();

            } else {
                $errors[] = 'Menu ' . $menu->name . ' Tidak dapat ditambahkan pada head menu 2 kali';
            }
        }

        if (!empty($errors)) {
            return redirect('/admin/panel/')->withErrors($errors);
        }

        return redirect('/admin/panel/')->with('success', 'Head Menu berhasil ditambah');

    }

    public function editHead(Request $request, $id){
        // dd($request);
        $request->validate([
            'name' => 'required',
            'icon' => 'required',
        ]);
        $head_menu = Head_Menu::where('id', $id)->first();
        $head_menu->name = $request->get('name');
        $head_menu->icon = $request->get('icon');
        $head_menu->save();
        return redirect('/admin/panel/')->with('success', 'Head Menu berhasil diubah');
    }
    public function removeHead($id){

        $head_menu = Head_Menu::where('id', $id)->first();
        foreach($head_menu->Menu as $menu){
            $menu->id_head_menu = null;
            $menu->save();
        }
        $head_menu->delete();
        return redirect('/admin/panel/')->with('success', 'Head Menu berhasil dihapus   ');
    }

    public function addMenu(Request $request, $id){
        $request->validate([
            'id_menu' => 'required',
        ]);
        $menu = Menu::where('id', $request->get('id_menu'))->first();
        $menu->id_head_menu = $id;
        $menu->save();
        return redirect('/admin/panel/')->with('success', 'Menu berhasil ditambah');
    }

    public function removeMenu($id){
        $menu = Menu::where('id', $id)->first();
        $menu->id_head_menu = null;
        $menu->save();
        return redirect('/admin/panel/')->with('success', 'Menu berhasil ditambah');
    }
}
