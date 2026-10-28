@extends('layouts.admin')

@section('title', 'Nouvelle vente')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        {{-- En-tête --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Nouvelle vente</h1>
                <p class="text-sm text-slate-500">La disponibilité est vérifiée en direct. Si le magasin manque d'un article, chargez-le depuis la réserve en un clic.</p>
            </div>
            <a href="{{ route('admin.vente.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900">
                <i class="ti ti-arrow-left text-base" aria-hidden="true"></i> Retour
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 text-sm border rounded-xl bg-rose-50 border-rose-200 text-rose-800" role="alert">
                <p class="mb-1 font-semibold">Veuillez corriger les erreurs suivantes :</p>
                <ul class="space-y-0.5 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.vente.store') }}" method="POST" id="vente-form" class="space-y-6">
            @csrf

            {{-- Lignes de vente --}}
            <div class="p-6 space-y-4 bg-white border shadow-sm rounded-2xl border-slate-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-900">Produits vendus</h2>
                    <button type="button" id="ajouter-ligne"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition rounded-lg bg-indigo-50 hover:bg-indigo-100">
                        <i class="ti ti-plus" aria-hidden="true"></i> Ajouter un produit
                    </button>
                </div>

                <div class="hidden gap-3 px-3 text-xs font-semibold tracking-wider uppercase md:grid md:grid-cols-[1fr_140px_110px_130px_40px] text-slate-500" aria-hidden="true">
                    <span>Produit</span><span>Prix (Ar)</span><span>Quantité</span><span class="text-right">Sous-total</span><span></span>
                </div>

                <div id="lignes" class="space-y-3"></div>
            </div>

            {{-- Chargement du magasin + total + validation --}}
            <div class="p-6 space-y-4 bg-white border shadow-sm rounded-2xl border-slate-200">

                {{-- Bandeau : apparaît seulement s'il y a des articles à charger --}}
                <div id="panneau-charger" class="hidden p-4 border rounded-xl bg-amber-50 border-amber-200">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-amber-900">
                            <p class="font-semibold">
                                <i class="ti ti-alert-triangle" aria-hidden="true"></i>
                                Le magasin ne contient pas assez pour cette vente.
                            </p>
                            <p class="mt-0.5 text-xs">À charger depuis la réserve : <span id="liste-charger" class="font-semibold"></span></p>
                        </div>
                        <button type="button" id="btn-charger"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white transition rounded-lg shadow-sm bg-amber-600 hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:opacity-60">
                            <i class="ti ti-arrows-transfer-down" aria-hidden="true"></i>
                            <span id="btn-charger-texte">Charger le magasin</span>
                        </button>
                    </div>
                </div>

                {{-- Résultat du chargement --}}
                <div id="retour-charger" class="hidden p-3 text-sm border rounded-lg" role="status" aria-live="polite"></div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold tracking-wider uppercase text-slate-500">Total</p>
                        <p id="total-general" class="text-2xl font-bold tabular-nums text-slate-900">0 Ar</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.vente.index') }}" class="px-4 py-2 text-sm font-semibold transition rounded-lg text-slate-600 hover:bg-slate-100">Annuler</a>
                        <button type="submit" id="btn-valider"
                                class="px-5 py-2 text-sm font-semibold text-white transition bg-indigo-600 rounded-lg shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            Enregistrer la vente
                        </button>
                    </div>
                </div>
                <p id="aide-valider" class="hidden text-xs text-rose-600"></p>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const catalogue = @json($catalogue);
            const stocksList = @json($stocksData);
            const anciennes = @json(old('ventes', []));
            const urlCharger = @json(route('admin.vente.charger'));

            const EPS = 0.0001;
            const produits = {};
            const stocks = {};
            catalogue.forEach(function (p) { produits[p.id] = p; });
            stocksList.forEach(function (s) { stocks[s.id] = s; });

            const lignesEl = document.getElementById('lignes');
            const panneau = document.getElementById('panneau-charger');
            const listeCharger = document.getElementById('liste-charger');
            const btnCharger = document.getElementById('btn-charger');
            const btnChargerTexte = document.getElementById('btn-charger-texte');
            const retour = document.getElementById('retour-charger');
            const totalEl = document.getElementById('total-general');
            const btnValider = document.getElementById('btn-valider');
            const aide = document.getElementById('aide-valider');
            let index = 0;

            const RANG = { ok: 0, charger: 1, insuffisant: 2 };
            const STATUTS = {
                ok:          { classe: 'text-emerald-600', icone: 'ti-circle-check',   texte: 'Disponible au magasin' },
                charger:     { classe: 'text-amber-600',   icone: 'ti-alert-triangle', texte: 'À charger depuis la réserve' },
                insuffisant: { classe: 'text-rose-600',    icone: 'ti-circle-x',       texte: 'Stock insuffisant' }
            };

            function fmt(n) {
                return (Math.round(n * 100) / 100).toLocaleString('fr-FR');
            }

            function esc(value) {
                return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
                });
            }

            // État d'un stock face à un besoin : ok (magasin suffit), charger (réserve nécessaire), insuffisant
            function etatStock(id, besoin) {
                const s = stocks[id];
                if (!s) return 'insuffisant';
                if (besoin <= s.magasin + EPS) return 'ok';
                if (besoin <= s.magasin + s.reserve + EPS) return 'charger';
                return 'insuffisant';
            }

            function addRow(data) {
                data = data || {};
                const i = index++;

                let options = '<option value="">Choisir un produit...</option>';
                catalogue.forEach(function (p) {
                    const sel = String(p.id) === String(data.produit_id) ? ' selected' : '';
                    options += '<option value="' + esc(p.id) + '"' + sel + '>' + esc(p.nom) + '</option>';
                });

                const html = `
                <div data-ligne class="grid grid-cols-2 gap-3 p-3 border md:grid-cols-[1fr_140px_110px_130px_40px] bg-slate-50 border-slate-200 rounded-xl md:items-start">
                    <div class="col-span-2 space-y-1.5 md:col-span-1">
                        <select name="ventes[${i}][produit_id]" required aria-label="Produit"
                                class="w-full text-sm rounded-lg champ-produit border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">${options}</select>
                        <p class="statut"></p>
                    </div>
                    <input type="number" name="ventes[${i}][prix]" min="0" step="1" required placeholder="Prix (Ar)" aria-label="Prix"
                           value="${esc(data.prix)}"
                           class="w-full text-sm rounded-lg champ-prix border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <input type="number" name="ventes[${i}][effectif]" min="1" step="1" required placeholder="Quantité" aria-label="Quantité"
                           value="${esc(data.effectif || 1)}"
                           class="w-full text-sm rounded-lg champ-qte border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="py-2 text-sm font-semibold text-right sous-total tabular-nums text-slate-900">0 Ar</p>
                    <button type="button" class="p-2 transition rounded-lg retirer text-rose-500 hover:bg-rose-50" aria-label="Retirer la ligne">
                        <i class="text-base ti ti-trash" aria-hidden="true"></i>
                    </button>
                </div>`;

                lignesEl.insertAdjacentHTML('beforeend', html);
            }

            function lignes() {
                return Array.from(lignesEl.querySelectorAll('[data-ligne]')).map(function (row) {
                    const qte = parseInt(row.querySelector('.champ-qte').value, 10);
                    const prix = parseFloat(row.querySelector('.champ-prix').value);
                    return {
                        row: row,
                        produit: produits[row.querySelector('.champ-produit').value],
                        qte: qte > 0 ? qte : 0,
                        prix: prix >= 0 ? prix : 0
                    };
                });
            }

            function afficherStatut(el, etat) {
                const s = STATUTS[etat];
                el.className = 'statut text-xs font-medium flex items-center gap-1 ' + s.classe;
                el.innerHTML = '<i class="ti ' + s.icone + '" aria-hidden="true"></i> ' + s.texte;
            }

            function render() {
                const l = lignes();

                // Besoins cumulés par stock sur tout le panier
                const besoins = {};
                l.forEach(function (x) {
                    if (x.produit && x.qte > 0) {
                        x.produit.besoins.forEach(function (b) {
                            besoins[b.stock_id] = (besoins[b.stock_id] || 0) + b.qte * x.qte;
                        });
                    }
                });

                const etats = {};
                Object.keys(besoins).forEach(function (id) { etats[id] = etatStock(id, besoins[id]); });

                let total = 0, nbInsuffisantes = 0, nbValides = 0;

                l.forEach(function (x) {
                    const statutEl = x.row.querySelector('.statut');
                    x.row.querySelector('.sous-total').textContent = fmt(x.prix * x.qte) + ' Ar';

                    if (!x.produit || x.qte <= 0) {
                        statutEl.textContent = '';
                        return;
                    }

                    total += x.prix * x.qte;
                    nbValides++;

                    let etat = x.produit.besoins.length === 0 ? 'insuffisant' : 'ok';
                    x.produit.besoins.forEach(function (b) {
                        if (RANG[etats[b.stock_id]] > RANG[etat]) etat = etats[b.stock_id];
                    });

                    if (etat === 'insuffisant') nbInsuffisantes++;
                    afficherStatut(statutEl, etat);
                });

                totalEl.textContent = fmt(total) + ' Ar';

                // Articles à charger depuis la réserve
                const aCharger = Object.keys(etats)
                    .filter(function (id) { return etats[id] === 'charger'; })
                    .map(function (id) { return stocks[id].nom + ' (+' + fmt(besoins[id] - stocks[id].magasin) + ')'; });

                panneau.classList.toggle('hidden', aCharger.length === 0);
                listeCharger.textContent = aCharger.join(', ');

                // Validation possible seulement si tout est couvert
                const bloque = nbValides === 0 || nbInsuffisantes > 0;
                btnValider.disabled = bloque;
                aide.classList.toggle('hidden', !bloque || nbValides === 0);
                aide.textContent = nbInsuffisantes > 0
                    ? 'Impossible d\'enregistrer : le stock total (magasin + réserve) est insuffisant pour une ou plusieurs lignes.'
                    : '';
            }

            function afficherRetour(message, ok) {
                retour.className = 'p-3 text-sm border rounded-lg ' + (ok
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                    : 'bg-rose-50 border-rose-200 text-rose-800');
                retour.textContent = message;
            }

            // ---- Événements ----
            document.getElementById('ajouter-ligne').addEventListener('click', function () {
                addRow();
                render();
            });

            lignesEl.addEventListener('change', function (e) {
                if (e.target.classList.contains('champ-produit')) {
                    const p = produits[e.target.value];
                    if (p) e.target.closest('[data-ligne]').querySelector('.champ-prix').value = p.prix;
                    render();
                }
            });

            lignesEl.addEventListener('input', function (e) {
                if (e.target.matches('.champ-prix, .champ-qte')) render();
            });

            lignesEl.addEventListener('click', function (e) {
                if (e.target.closest('.retirer')) {
                    e.target.closest('[data-ligne]').remove();
                    if (lignesEl.children.length === 0) addRow();
                    render();
                }
            });

            // ---- UN CLIC : charger le magasin depuis la réserve ----
            btnCharger.addEventListener('click', async function () {
                const payload = {
                    lignes: lignes()
                        .filter(function (x) { return x.produit && x.qte > 0; })
                        .map(function (x) { return { produit_id: x.produit.id, effectif: x.qte }; })
                };

                btnCharger.disabled = true;
                btnChargerTexte.textContent = 'Chargement...';

                try {
                    const res = await fetch(urlCharger, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json().catch(function () { return {}; });

                    if (!res.ok) {
                        afficherRetour(data.message || 'Le chargement du magasin a échoué.', false);
                    } else {
                        // Met à jour les quantités locales puis recalcule l'affichage
                        (data.stocks || []).forEach(function (s) {
                            if (stocks[s.id]) {
                                stocks[s.id].magasin = s.magasin;
                                stocks[s.id].reserve = s.reserve;
                            }
                        });
                        afficherRetour(data.message, true);
                    }
                } catch (err) {
                    afficherRetour('Erreur réseau : le chargement du magasin a échoué.', false);
                } finally {
                    btnCharger.disabled = false;
                    btnChargerTexte.textContent = 'Charger le magasin';
                    retour.classList.remove('hidden');
                    render();
                }
            });

            // ---- Initialisation (restaure la saisie après une erreur de validation) ----
            Object.values(anciennes || {}).forEach(function (ligne) { addRow(ligne); });
            if (lignesEl.children.length === 0) addRow();
            render();
        });
    </script>
@endsection
