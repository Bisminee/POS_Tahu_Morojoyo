<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Identitas;
use App\Models\Menu;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function home()
    {
    $identitas = Identitas::first();
    $menus     = Menu::where('is_active', 1)->with('harga')->get();
    return view('guest.home', compact('identitas', 'menus'));
    }

    public function menu()
    {
        $identitas = Identitas::first();
        $menus     = Menu::where('is_active', 1)->with('harga')->get();
        return view('guest.menu', compact('identitas', 'menus'));
    }

    public function about()
    {
        $identitas = Identitas::first();
        $cabangs   = Cabang::all();
        return view('guest.about', compact('identitas', 'cabangs'));
    }

    public function contact()
    {
        $identitas = Identitas::first();
        return view('guest.contact', compact('identitas'));
    }
}