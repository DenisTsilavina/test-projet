@extends('layouts.admin.admin-layout')
@section('title', 'Admin Dashboard')
@section('content')
    {{-- On passe en container-fluid pour la pleine largeur --}}
    <div class="container-fluid px-4">
        {{-- En-tête de page --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="h4 fw-bold text-dark mb-0">
                <i class="bi bi-speedometer2 me-2"></i>{{ __('Admin Dashboard') }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active">Vue d'ensemble</li>
                </ol>
            </nav>
        </div>
        {{-- Messages d'état --}}
        @if (session('status'))
            <div class="alert alert-success border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
            </div>
        @endif
        {{--
            Appel du Partial des Métriques
            Grâce au container-fluid, ce bloc va s'étendre sur toute la largeur
        --}}
        <section class="mb-4">
            @include('admin.partials._metrics')
        </section>
        {{-- Section supplémentaire (Tableau ou graphiques) --}}
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-3">Activités récentes</h5>
                        <p class="text-muted small">
                            Bienvenue, {{ auth()->user()->name }}. Voici l'état actuel de votre application.
                        </p>
                        <hr class="text-faded">
                        {{-- Tu peux inclure ton tableau de ventes ici si nécessaire --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
