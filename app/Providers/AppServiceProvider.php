<?php

namespace App\Providers;

use App\Models\Stock;
use App\Models\Vente;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('stocks', Stock::all());
        View::composer('admin.partials._metrics', function ($view) {

            // 1. Récupérer les ventes récentes pour les calculs
            $venteRecentes = Vente::with('categorie', 'description')
                ->latest()
                ->take(10) // ou le nombre que tu veux
                ->get();

            // 2. Calculer le revenu total (logique métier)
            $totalRevenue = Vente::all()->sum(function($vente) {
                $prixAchat = $vente->categorie->prix_achat ?? 0;
                return ($vente->prix - $prixAchat) * $vente->effectif;
            });

            // 3. Envoyer les variables à la vue
            $view->with([
                'totalVente'    => Vente::count(),
                'totalRevenue'  => $totalRevenue,
                'venteRecentes' => $venteRecentes,
            ]);
        });
    }
}
