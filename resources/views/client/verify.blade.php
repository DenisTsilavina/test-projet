@extends('layouts.client')
@section('title', 'Vérification du compte')
@section('content')

    <div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="card border-0 shadow-lg p-4" style="width: 100%; max-width: 420px; border-radius: 16px;">

            <div class="text-center mb-4">
                <i class="bi bi-envelope-check text-primary" style="font-size: 3rem;"></i>
                <h4 class="fw-bold mt-2">Vérifiez votre email</h4>
                <p class="text-muted small">
                    Code envoyé à <strong>{{ session('pending_client_email') }}</strong>
                </p>
            </div>

            @if(session('success'))
                <div class="alert alert-success py-2">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('client.verify') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Code de vérification</label>
                    <input type="text"
                           name="verification_code"
                           class="form-control form-control-lg text-center @error('verification_code') is-invalid @enderror"
                           placeholder="000000"
                           maxlength="6"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           value="{{ old('verification_code') }}"
                           autofocus
                           required>
                    @error('verification_code')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check-circle me-1"></i>Valider mon compte
                </button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">
                    Session expirée ?
                    <a href="{{ route('client.achat') }}">Recommencer l'inscription</a>
                </small>
            </div>

        </div>
    </div>

@endsection
