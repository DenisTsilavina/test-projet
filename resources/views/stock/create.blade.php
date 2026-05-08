@extends('layouts.admin.admin-layout')

@section('title', 'Nouveau Stock')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- En-tête avec bouton d'action rapide -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-0">Gestion des Stocks</h4>
                        <p class="text-muted small mb-0">Enregistrement d'un nouveau mouvement ou inventaire</p>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreerUnite">
                        <i class="bi bi-plus-circle me-2"></i>Nouvelle Unité
                    </button>
                </div>

                <!-- Formulaire Principal -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="bi bi-box-seam me-2"></i>Détails du stock
                        </h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('stock.store') }}">
                            @csrf

                            <div class="row">
                                <!-- Nom du stock -->
                                <div class="col-md-12 mb-3">
                                    <label for="name_stock" class="form-label fw-semibold">Désignation / Nom du stock</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-pencil"></i></span>
                                        <input type="text" class="form-control @error('name_stock') is-invalid @enderror"
                                               name="name_stock" id="name_stock"
                                               placeholder="ex: Arrivage Fruits de saison" required>
                                    </div>
                                    @error('name_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Responsable -->
                                <div class="col-md-6 mb-3">
                                    <label for="persn_stock" class="form-label fw-semibold">Responsable</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control bg-light" id="persn_stock"
                                               value="{{ auth()->user()->name }}" readonly>
                                    </div>
                                </div>

                                <!-- Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="date_stock" class="form-label fw-semibold">Date d'enregistrement</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-calendar-event"></i></span>
                                        <input type="date" class="form-control" name="date_stock" id="date_stock"
                                               value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('stock.index') }}" class="btn btn-light px-4">Annuler</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check-lg me-2"></i>Enregistrer le stock
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
