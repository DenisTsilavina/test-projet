<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\SousCategory;
use App\Models\Description;
use App\Models\Stock;

class ClientController extends Controller
{
    public function index()
    {
         $client = Client::all();
         return view('client.dashboard', compact('client'));
    }

    public function achat( ){
        $stocks= Stock::all();
        $descriptions = Description::where('effectif', '>', 0)->get();
        $categories  = SousCategory::all();
        return view('client.achat', compact('stocks', 'descriptions', 'categories'));
    }

    public function createNewClient(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required',
            'ville' => 'required',
            'phone' => 'required',
        ]);
        Client::create([
            'user_id'=> auth()->id(),
            'address' => $request->address,
            'ville' => $request->ville,
            'phone' => $request->phone,
        ]);
        return redirect()->route('dashboard')->with('success', 'tu est authorise');
    }

}
