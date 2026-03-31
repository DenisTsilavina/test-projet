@extends('layouts.app')
@section('content')
    <div class="container"  tabindex="-1">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class=" border-green-400">
                    <h5 style=" color: #ef4444;text-align: center">Ajouter nouvelle stock</h5>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('stock.store') }}">
                        @csrf

                        <div class="">

                            <div class="mb-3">
                                <label for="name_stock" class="form-label">Nom de la stock</label>
                                <input type="text" class="form-control" name="name_stock" id="name_stock" rows="3" required></input>
                            </div>

                            <div class="mb-3">
                                <label for="persn_stock" class="form-label">Responsable</label>
                                <input type="text"
                                       class="form-control bg-light"
                                       id="persn_stock"
                                       value="{{ auth()->user()->name }}"
                                       readonly>
                            </div>
                            <div class="mb-3">
                                <label for="date_stock" class="form-label">date</label>
                                <input type="date" class="form-control" name="date_stock" id="date_stock" value="{{ now()->format('Y-m-d') }}" >

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
