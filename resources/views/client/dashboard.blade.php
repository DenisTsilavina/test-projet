@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Tableau de bord Client</h4>
                    </div>
                    <div class="card-body">
                        <h5>Bienvenue, {{ Auth::user()->name }} !</h5>
                        <p class="text-muted">Vous êtes connecté en tant que client.</p>

                        <hr>

                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6>Mes Achats</h6>
                                    <span class="h4">0</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6>Profil</h6>
                                    <a href="#" class="btn btn-sm btn-outline-primary">Modifier</a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6>Support</h6>
                                    <a href="#" class="btn btn-sm btn-outline-secondary">Contact</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
