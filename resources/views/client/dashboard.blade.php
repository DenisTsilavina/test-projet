@extends('layouts.client')

@section('title', 'Votre Information')

@section('content')
    <div class="container py-4">

        {{-- ================= STOCKS ================= --}}
        <h4 class="mb-3">Articles disponibles</h4>

        <div class="row">
            @forelse($descriptions as $description)
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">

                        {{-- HEADER --}}
                        <div class="card-header bg-primary text-white fw-semibold d-flex justify-content-between">
                            <span>{{ $description->description }}</span>

                            <span class="badge bg-light text-dark">
                            {{ $description->sousCategories->count() }} choix
                        </span>
                        </div>

                        <div class="card-body">

                            {{-- STOCK --}}
                            <p class="small text-muted mb-2">
                                Stock : <strong>{{ $description->effectif }}</strong>
                            </p>

                            {{-- CATÉGORIES --}}
                            @forelse($description->sousCategories as $sous)

                                <button type="button"
                                        class="btn btn-outline-secondary w-100 mb-2 add-to-sale"

                                        data-stock="0"
                                        data-description="{{ $description->id }}"
                                        data-categorie="{{ $sous->id }}"
                                        data-name="{{ $description->description }}"
                                        data-sous-nom="{{ $sous->stock_categorie }}"
                                        data-prix="{{ (float) $sous->prix_vente }}"
                                        data-stock-dispo="{{ (int) $description->effectif }}"
                                        data-stock-name="Stock">

                                    <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">
                                        {{ $sous->stock_categorie }}
                                    </span>

                                        <span class="badge bg-success">
                                        {{ number_format($sous->prix_vente, 0, ',', ' ') }} Ar
                                    </span>
                                    </div>

                                </button>

                            @empty
                                <p class="text-muted small">Aucune catégorie disponible.</p>
                            @endforelse

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        Aucun article disponible
                    </div>
                </div>
            @endforelse
        </div>

        {{-- ================= CLIENT ================= --}}
        <div class="card mt-4 border-primary shadow-sm">
            <div class="card-header bg-primary text-white fw-semibold">
                <i class="bi bi-person-fill me-1"></i> Informations client
            </div>

            <form action="{{ route('client.createNewClient') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="row g-2">

                        {{-- USER --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Utilisateur</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ auth()->user()->name }}"
                                   disabled>
                        </div>

                        {{-- ADRESSE --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" name="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address') }}"
                                   placeholder="Adresse du client">

                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- VILLE --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ville</label>
                            <input type="text" name="ville"
                                   class="form-control @error('ville') is-invalid @enderror"
                                   value="{{ old('ville') }}"
                                   placeholder="Ville du client">

                            @error('ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TELEPHONE --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}"
                                   placeholder="034 XX XXX XX">

                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary mt-3 w-100">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
