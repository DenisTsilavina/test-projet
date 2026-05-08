<?php


namespace App\Http\Controllers;

use App\Models\Description;
use App\Models\SousCategory;
use App\Models\Stock;
use App\Models\Unite;
use http\Client\Curl\User;
use Illuminate\Http\Request;

class StockControllers extends Controller
{

    public function index()
    {
        $stocks = Stock::all();
        $descriptions = Description::all();
        $categories = SousCategory::all();
        $unites= Unite::all();

        return view('stock.index', compact('stocks','descriptions','categories','unites'));
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

    public function createUnite(Request $request)
    {
        $validated = $request->validate(
            [
                'description_id'=>'integer|nullable|exists:descriptions,description_id',
                'name'=> 'required|string|max:25',
                'symbol'=> 'required|string|max:25|unique:unites,symbol',
                'type'=> 'required|string|max:25',
                'factor' => 'required|numeric|min:0',
                'is_base' => 'nullable|boolean',
            ]
        );
        $validated['is_base'] = $request->boolean('is_base');
        $unite = Unite::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unité créée avec succès',
                'data'    => $unite,
            ], 201);
        }
        return redirect()->back()->with('success', 'Unité créée avec succès');
    }
    public function createArticle(Request $request)
    {
        $validated = $request->validate(
            [
                'name'=>'required|string|max:25',
                'total_prd_finit'=>'required|numeric|min:0',
                'note'=>'required|string|max:255',
            ]
        );
    }

}
