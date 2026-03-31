@extends('layouts.app')
@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header gap-1">
                <h1 class="text-center" style="font-family: 'Times New Roman', Times, serif;">
                    Les stocks: disponible actuellement
                </h1>
            </div>

            <div class="card shadow">
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th class="col-2">Stock (ID)</th>
                                <th class="col-2">Responsable</th>
                                <th class="col-2">Description</th>
                                <th class="col-2">Sous-Catégorie</th>
                                <th class="col-2">Effectif</th>
                                <th class="col-2">Prix Vente</th>
                                <th class="col-2">action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                        @forelse($stocks as $stock)
                            @php $descriptions = $stock->descriptions; @endphp

                            @if($descriptions->isEmpty())
                                <tr>
                                    <td><strong>{{ $stock->name_stock }}</strong> (#{{ $stock->id }})</td>
                                    <td>{{ $stock->persn_stock }}</td>
                                    <td colspan="4" style="color: #e53e3e; font-style: italic;">
                                        Aucune description enregistrée pour ce stock
                                    </td>

                                    <td class="d-flex gap-1">
                                        <a href="{{ route('description.create', $stock->id) }}"
                                           class="btn btn-sm btn-success">
                                            ➕
                                        </a>
                                    </td>
                                </tr>

                            @else
                                @foreach($descriptions as $desc)
                                    @php $subCats = $desc->sousCategories; @endphp

                                    @if($subCats->isEmpty())
                                        <tr style="background-color: #fffaf0;">
                                            <td><strong>{{ $stock->name_stock }}</strong></td>
                                            <td>{{ $stock->persn_stock }}</td>
                                            <td style="color: var(--accent-dark);">{{ $desc->description }}</td>
                                            <td colspan="2" style="color: #dd6b20; font-style: italic;">
                                                c'est vide
                                            </td>
                                            <td><strong>{{ $desc->effectif }}</strong></td>

                                            <td class="d-flex gap-1">
                                                <a href="{{ route('description.create', $stock->id) }}"
                                                   class="btn btn-sm btn-success">➕</a>

                                                <a href="{{ route('description.edit', $desc->id) }}"
                                                   class="btn btn-sm btn-warning">✏</a>

                                                <form action="{{ route('description.destroy', $desc->id) }}"
                                                      method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">🗑</button>
                                                </form>
                                            </td>
                                        </tr>

                                    @else
                                        @foreach($subCats as $subCat)
                                            <tr>
                                                <td><strong>{{ $stock->name_stock }}</strong></td>
                                                <td>{{ $stock->persn_stock }}</td>
                                                <td>{{ $desc->description }}</td>
                                                <td class="revenue-positive">{{ $subCat->stock_categorie }}</td>
                                                <td><strong>{{ $desc->effectif }}</strong></td>
                                                <td>{{ number_format($subCat->prix_vente, 0, ',', ' ') }} Ar</td>

                                                <td class="d-flex gap-1">
                                                    <a href="{{ route('description.create', $stock->id) }}"
                                                       class="btn btn-sm btn-success">➕</a>

                                                    <a href=""
                                                       class="btn btn-sm btn-warning">✏</a>

                                                    <form action=""
                                                          method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">🗑</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach


                            @endif

                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    La base de données des stocks est vide.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
