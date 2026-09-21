@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

@section('content')
    <div class="space-y-6">

        <!-- En-tête -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Gestion des administrateurs & utilisateurs</h1>
                <p class="mt-1 text-sm text-slate-500">Consultez, créez et gérez les rôles et accès de tous les utilisateurs enregistrés.</p>
            </div>
            <div>
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl shadow-sm hover:bg-indigo-500 transition-all duration-200">
                    <i class="text-lg ti ti-plus"></i>
                    Nouvel utilisateur
                </a>
            </div>
        </div>

        <!-- Tableau -->
        <div class="overflow-hidden bg-white border border-slate-200 rounded-xl shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-600">
                    <thead class="text-xs font-semibold uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Utilisateur</th>
                        <th scope="col" class="px-6 py-4">Adresse E-mail</th>
                        <th scope="col" class="px-6 py-4">Rôle</th>
                        <th scope="col" class="px-6 py-4">Date d'inscription</th>
                        <th scope="col" class="px-6 py-4 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- 1. Utilisateur -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-9 h-9 font-bold text-white uppercase rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 shrink-0">
                                        {{ Str::upper(Str::substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                        @if($user->id === auth()->id())
                                            <span class="text-[10px] font-medium text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-200">Vous</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Adresse E-mail -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                {{ $user->email }}
                            </td>

                            <!-- 3. Rôle (Badge placé dans sa propre cellule td) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleEnum = $user->role instanceof \App\Enums\UserRole
                                        ? $user->role
                                        : \App\Enums\UserRole::tryFrom((int)$user->role);
                                @endphp

                                @if($roleEnum === \App\Enums\UserRole::SUPER_ADMIN)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-purple-700 bg-purple-50 rounded-lg border border-purple-200">
                                        <i class="ti ti-shield-lock text-purple-600"></i>
                                        Super Admin
                                    </span>
                                @elseif($roleEnum === \App\Enums\UserRole::ADMINS)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 rounded-lg border border-indigo-200">
                                        <i class="ti ti-shield text-indigo-600"></i>
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-100 rounded-lg border border-slate-200">
                                        <i class="ti ti-user text-slate-500"></i>
                                        Client
                                    </span>
                                @endif
                            </td>

                            <!-- 4. Date d'inscription -->
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-xs">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y à H:i') : '—' }}
                            </td>

                            <!-- 5. Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors"
                                       title="Modifier l'utilisateur">
                                        <i class="text-lg ti ti-edit"></i>
                                    </a>

                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                                    title="Supprimer l'utilisateur">
                                                <i class="text-lg ti ti-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="text-4xl text-slate-300 ti ti-users"></i>
                                    <p class="font-medium">Aucun utilisateur trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
