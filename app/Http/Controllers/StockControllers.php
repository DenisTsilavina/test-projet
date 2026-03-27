<?php


namespace App\Http\Controllers;

use App\Models\Description;
use App\Models\SousCategory;
use App\Models\Stock;
use http\Client\Curl\User;
use Illuminate\Http\Request;

class StockControllers extends Controller
{

    public function index()
    {
        $stocks = Stock::all();
        $descriptions = Description::all();
        $categories = SousCategory::all();

        return view('stock.index', compact('stocks','descriptions','categories'));
    }


    public function modifierStock()
    {
        $stocks = Stock::all();
        return view('stock.modifierStock', compact('stocks'));
    }
    public function create()
    {
        return view('stock.create');
    }
    public function createStock(Request $request){
       $validated = $request->validate(
            [
                'name_stock' => 'required|string|max:255|unique:stocks,name_stock',
                'date_stock' => 'required|date',
            ]);
       //recuperer la date aujourd'huit
       $validated['date_stock'] = $request->date_stock ?? now();
        $validated['persn_stock']=auth()->user()->name;
        $validated['user_id']=auth()->user()->id;

       Stock::create($validated);
       return redirect()->route('stock.index')
           ->with('success', 'la Stockest bien inserer');
    }
}
