@extends('layouts.client')

@section('title', 'Votre Information')

@section('content')

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
