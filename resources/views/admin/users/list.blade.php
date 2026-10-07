{{-- resources/views/admin/users/list.blade.php --}}
@extends('layouts.admin')

@section('title', 'Utilisateurs')
@section('page-title', 'Utilisateurs')

@section('content')
    <div class="space-y-6">

        @if (session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
                <i class="ti ti-circle-check text-emerald-500 text-lg shrink-0"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <i class="ti ti-alert-circle text-rose-500 text-lg shrink-0"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Utilisateurs</h2>
                <p class="text-sm text-slate-400 mt-0.5">{{ $users->total() }} utilisateur(s) au total</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                    <i class="ti ti-plus"></i> Nouveau client
                </a>
                <a href="{{ route('admin.users.create-staff') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold text-sm rounded-xl shadow-sm transition-colors border border-amber-200">
                    <i class="ti ti-shield-plus"></i> Nouveau membre du personnel
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                <tr class="text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                    <th class="px-5 py-3">Nom</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Rôle</th>
                    <th class="px-5 py-3">Inscrit le</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                @forelse ($users as $user)
                    @php
                        $badgeClass = match($user->role) {
                            \App\Enums\UserRole::SUPER_ADMIN => 'bg-amber-50 text-amber-700 border-amber-200',
                            \App\Enums\UserRole::ADMINS      => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            \App\Enums\UserRole::CLIENT      => 'bg-slate-100 text-slate-600 border-slate-200',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $user->email }}</td>
                        <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold border {{ $badgeClass }}">
                                    {{ $user->role->label() }}
                                </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs">
                            {{ $user->created_at?->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                    <i class="ti ti-pencil"></i>
                                </a>
                                @if ($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                        @csrf @method('DELETE')
                                        <button class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                            Aucun utilisateur pour le moment.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $users->links() }}
        </div>
    </div>
@endsection
