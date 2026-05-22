<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Description;
use App\Models\Unite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->user_type === 'admin') {
            return view('admin.dashboard');
        }

        if ($user->user_type === 'user') {
            return view('client.dashboard');
        }

        abort(403, 'Accès non autorisé');
    }

    public function showCreateForm()
    {
        return view('admin.article.create-article', [
            'descriptions' => Description::all(),
            'unites'       => Unite::all(),
        ]);
    }

    public function createArticles(Request $request)
    {
        $validated = $request->validate([
            'name'                              => ['required', 'string', 'max:255'],
            'total_prd_finit'                   => ['required', 'integer', 'min:1'],
            'note'                              => ['nullable', 'string', 'max:500'],
            'ingredients'                       => ['required', 'array', 'min:1'],
            'ingredients.*.description_id'      => ['required', 'integer', 'exists:descriptions,id'],
            'ingredients.*.unite_id'            => ['required', 'integer', 'exists:unites,id'],
            'ingredients.*.effectif'            => ['required', 'integer', 'min:1'],
        ], [
            'name.required'                          => "Le nom de l'article est obligatoire.",
            'total_prd_finit.required'               => "Le total produit fini est obligatoire.",
            'total_prd_finit.min'                    => "Le total doit être au moins 1.",
            'ingredients.required'                   => "Ajoutez au moins un ingrédient.",
            'ingredients.min'                        => "Ajoutez au moins un ingrédient.",
            'ingredients.*.description_id.required'  => "Sélectionnez un produit en stock.",
            'ingredients.*.description_id.exists'    => "Ce produit n'existe pas dans le stock.",
            'ingredients.*.unite_id.required' => "Sélectionnez une unité.",
            'ingredients.*.unite_id.exists' => "Cette unité n'existe pas.",
            'ingredients.*.effectif.required' => "L'effectif est obligatoire.",
            'ingredients.*.effectif.min' => "L'effectif doit être au moins 1.",
        ]);

        DB::transaction(function () use ($validated) {
            $article = Article::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'total_prd_finit' => $validated['total_prd_finit'],
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($validated['ingredients'] as $ingredientData) {
                $article->ingredients()->create([
                    'description_id' => $ingredientData['description_id'],
                    'unite_id' => $ingredientData['unite_id'],
                    'effectif' => $ingredientData['effectif'],
                ]);
            }
        });

        return redirect()->back()->with('success', 'Article et ingrédients enregistrés avec succès !');
    }
}
