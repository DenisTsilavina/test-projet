<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Notifications\CommandeStatusNotification;
use Illuminate\Http\Request;

class CommandeAdminController extends Controller
{
    public function index()
    {
        $commandes = Commande::with('client.user')
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Commande::count(),
            'pending' => Commande::pending()->count(),
            'approved' => Commande::approved()->count(),
            'cancelled' => Commande::cancelled()->count(),
            'paid' => Commande::paid()->count(),
            'unpaid' => Commande::unpaid()->count(),
            'advance' => Commande::advance()->count(),
        ];

        return view('admin.commandes.index', compact('commandes', 'stats'));
    }

    public function updateStatus(Request $request, Commande $commande)
    {
        $request->validate([
            'status'       => 'required|in:pending,approved,cancelled',
            'montant_paye' => 'nullable|numeric|min:0',
        ]);

        $commande->status = $request->status;

        if ($request->filled('montant_paye')) {
            $commande->montant_paye = $request->montant_paye;
        }

        $commande->save();

        $commande->client->user->notify(new CommandeStatusNotification($commande));

        return back()->with('success', 'Commande mise à jour et client notifié.');
    }

}
