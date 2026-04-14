<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\SousCategory;
use App\Models\Description;
use App\Models\Stock;

class ClientController extends Controller
{
    public function index()
    {
         $stocks= Stock::all();
         $descriptions = Description::where('effectif', '>', 0)->get();
         $categories  = SousCategory::all();
         return view('client.achat', compact('stocks', 'descriptions', 'categories'));
    }

    public function createNewClient(Request $request)
    {
        $validated = $request->validate([
            'adress' => 'required',
            'ville' => 'required',
            'phone' => 'required',
        ]);
        Client::create([
            'user_id'=> auth()->id(),
            'adress' => $request->adress,
            'ville' => $request->ville,
            'phone' => $request->phone,
        ]);
        return redirect()->route('client.dashboard')->with('success', 'tu est authorise');
    }

}
