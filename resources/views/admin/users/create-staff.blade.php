{{-- resources/views/admin/users/create-staff.blade.php --}}
@extends('layouts.admin')

@section('title', 'Nouveau membre du personnel')
@section('page-title', 'Créer un membre du personnel')

@section('content')
    <div class="max-w-xl mx-auto">

        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <h2 class="text-xl font-bold text-slate-900">Créer un membre du personnel</h2>
            <a href="{{ route('admin.users.list') }}" class="text-sm text-slate-500 hover:text-indigo-600">
                &larr; Retour à la liste
            </a>
        </div>

        @if (session('success'))
            <div class="flex items-center gap-3 p-4 mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
                <i class="ti ti-circle-check text-emerald-500 text-lg shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="flex items-start gap-3 p-4 mb-6 border rounded-xl bg-rose-50 border-rose-200 text-rose-800 shadow-sm">
                <i class="text-lg ti ti-alert-circle text-rose-600 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold mb-1">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc list-inside text-xs space-y-1 text-rose-700">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-2 p-3 mb-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
            <i class="ti ti-shield-lock text-amber-500"></i>
            Ce compte aura accès à l'administration de la boutique. Réservé au personnel de confiance.
        </div>

        <form action="{{ route('admin.users.store-staff') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Nom complet <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name"
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              @error('name') border-rose-500 bg-rose-50/30 @else border-slate-200 @enderror text-slate-900"
                       value="{{ old('name') }}" required>
                @error('name')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Email <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email"
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              @error('email') border-rose-500 bg-rose-50/30 @else border-slate-200 @enderror text-slate-900"
                       value="{{ old('email') }}" required>
                @error('email')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- CHANGEMENT : liste réduite (jamais CLIENT), via $rolesStaff
                 filtré côté contrôleur. --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Rôle <span class="text-rose-500">*</span>
                </label>
                <select name="role"
                        class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                               @error('role') border-rose-500 bg-rose-50/30 @else border-slate-200 @enderror text-slate-900"
                        required>
                    <option value="">-- Choisir --</option>
                    @foreach ($rolesStaff as $role)
                        <option value="{{ $role->value }}" {{ old('role') == $role->value ? 'selected' : '' }}>
                            {{ $role->label() }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Mot de passe <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password"
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              @error('password') border-rose-500 bg-rose-50/30 @else border-slate-200 @enderror text-slate-900"
                       required>
                @error('password')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Confirmer le mot de passe <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password_confirmation"
                       class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900"
                       required>
            </div>

            <div class="flex items-center gap-3 pt-6 border-t border-slate-100">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                    <i class="ti ti-device-floppy"></i> Créer le compte
                </button>
                <a href="{{ route('admin.users.list') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-sm rounded-xl shadow-sm transition-colors">
                    Annuler
                </a>
            </div>
        </form>
    </div>
@endsection
