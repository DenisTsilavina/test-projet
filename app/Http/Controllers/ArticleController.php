<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['user', 'ingredients.description', 'ingredients.unite'])->get();
        return response()->json($articles);
    }

    /**
     * Enregistrer un nouvel article et ses ingrédients.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'total_prd_finit'  => 'required|integer|min:1',
            'note' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.description_id' => 'required|exists:descriptions,id',
            'ingredients.*.unite_id' => 'required|exists:unites,id',
            'ingredients.*.effectif' => 'required|numeric|min:0',
            'ingredients.*.prix' => 'required|numeric|min:0',
        ]);

        // Utilisation d'une transaction pour s'assurer que tout s'enregistre correctement
        $article = DB::transaction(function () use ($validated) {
            $article = Article::create([
                'user_id' => $validated['user_id'],
                'name' => $validated['name'],
                'total_prd_finit'  => $validated['total_prd_finit'],
                'note' => $validated['note'] ?? null,
            ]);

            $article->ingredients()->createMany($validated['ingredients']);

            return $article;
        });

        return response()->json([
            'message' => 'article créé avec succès',
            'data' => $article->load('ingredients')
        ], 201);
    }

    /**
     * Afficher un article spécifique.
     */
    public function show(Article $article)
    {
        return response()->json($article->load(['user', 'ingredients.description', 'ingredients.unite']));
    }

    /**
     * Mettre à jour un article et synchroniser ses ingrédients.
     */
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'total_prd_finit'  => 'sometimes|required|integer|min:1',
            'note' => 'nullable|string',
            'ingredients' => 'sometimes|required|array|min:1',
            'ingredients.*.description_id' => 'required_with:ingredients|exists:descriptions,id',
            'ingredients.*.unite_id' => 'required_with:ingredients|exists:unites,id',
            'ingredients.*.effectif' => 'required_with:ingredients|numeric|min:0',
            'ingredients.*.prix' => 'required_with:ingredients|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $article) {
            $article->update($validated);

            // Si des ingrédients sont fournis, on remplace les anciens par les nouveaux
            if (isset($validated['ingredients'])) {
                $article->ingredients()->delete();
                $article->ingredients()->createMany($validated['ingredients']);
            }
        });

        return response()->json([
            'message' => 'article mis à jour avec succès',
            'data'    => $article->load('ingredients')
        ]);
    }

    /**
     * Supprimer un article (les ingrédients seront supprimés via cascade).
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json([
            'message' => 'article supprimé avec succès'
        ]);
    }

}
