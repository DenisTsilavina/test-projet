    @extends('layouts.admin');

    @section('content')
        <div class="container mt-4">
            <div class="card shadow">
                <div class="card gap-2">
                    <div class="card-header d-flex justify-content-between align-items-center bg-gray-50 text-black">
                        <h4 class="mb-0 text-left flex-grow-1">Description et Catégorie : Nouvelle</h4>
                        <a href="{{ route('stock.index') }}" class="btn btn-outline-warning">
                            Retour
                        </a>
                    </div>
                </div>

                <div class="card-body gap-6">
                    @if ($errors->any())
                       <div class="alert alert-danger">
                           <ul>
                               @foreach ($errors->all() as $error)
                                   <li>{{ $error }}</li>
                               @endforeach
                           </ul>
                       </div>
                   @endif

                   <form action="{{ route('description.store',$stock->id) }}" method="POST">
                       @csrf

                       <div class="mb-3">
                           <label>id</label>
                           <input name="stock_id" value="{{ $stock->id }}">
                       </div>


                       <div class="mb-3">
                           <label for="description">Description</label>
                           <input type="text" name="description" id="description" class="form-control" required>
                       </div>

                       <div class="mb-3">
                           <label for="region">Region d'origine</label>
                           <input type="text" name="region" id="region" class="form-control" required>
                       </div>

                       <div class="mb-3">
                           <label for="stock_categorie">Sous catégorie</label>
                           <input type="text" name="stock_categorie" id="stock_categorie" class="form-control">
                       </div>

                       <div class="mb-3">
                           <label for="prix_achat">Prix achat</label>
                           <input type="number" name="prix_achat" id="prix_achat" class="form-control">
                       </div>

                       <div class="mb-3">
                           <label for="prix_vente">Prix vente</label>
                           <input type="number" name="prix_vente" id="prix_vente" class="form-control">
                       </div>

                       <button type="submit" class="btn btn-success">Enregistrer</button>
                   </form>
                </div>
           </div>
       </div>
    @endsection
