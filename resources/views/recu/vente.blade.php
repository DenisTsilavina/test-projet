{{-- resources/views/recu/vente.blade.php --}}
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de vente #{{ $vente->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
        th { background: #f5f5f5; }
        .total { font-size: 1.2rem; font-weight: bold; text-align: right; margin-top: 15px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h2>🧾 Reçu de vente</h2>
        <p><strong>N° :</strong> {{ $vente->id }}</p>
        <p><strong>Date :</strong> {{ $vente->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div>
        <p><strong>Vendeur :</strong> {{ $vente->vendeur?->name ?? 'Client direct' }}</p>
        <p><strong>Client :</strong> {{ $vente->clientAnon?->prenom }} {{ $vente->clientAnon?->nom }}</p>
        <p><strong>Téléphone :</strong> {{ $vente->clientAnon?->phone ?? '—' }}</p>
        <p><strong>Ville :</strong> {{ $vente->clientAnon?->ville ?? '—' }}</p>
    </div>
</div>

<p><strong>Mode de paiement :</strong> {{ ucfirst($vente->mode_paiement) }}</p>

<table>
    <thead>
    <tr>
        <th>Produit</th>
        <th>Sous-catégorie</th>
        <th>Quantité</th>
        <th>Unité</th>
        <th>Prix unitaire</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($vente->lignes as $ligne)
        <tr>
            <td>{{ $ligne->produit_nom }}</td>
            <td>{{ $ligne->sous_categorie }}</td>
            <td>{{ $ligne->quantite }}</td>
            <td>{{ $ligne->unite_symbol }}</td>
            <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} Ar</td>
            <td>{{ number_format($ligne->total, 0, ',', ' ') }} Ar</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="total">
    Total général : {{ number_format($vente->total_general, 0, ',', ' ') }} Ar
</div>

<div style="margin-top: 40px; text-align: center;">
    <button onclick="window.print()" style="padding: 10px 30px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
        🖨️ Imprimer
    </button>
</div>

</body>
</html>
