@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Modifier le plat') }}</span>
                    <div>
                        <a href="{{ route('admin.restaurants.show', $dish->restaurant_id) }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> {{ __('Retour') }}
                        </a>
                        <x-delete-form
                            :route="route('admin.dishes.destroy', $dish)"
                            message="Êtes-vous sûr de vouloir supprimer ce plat ? Cette action est irréversible."
                        />
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.dishes.update', $dish) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name">{{ __('Nom') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $dish->name) }}" required autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $dish->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="price">{{ __('Prix (€)') }}</label>
                            <input id="price" type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $dish->price) }}" required>
                            @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="category_id">{{ __('Catégorie') }}</label>
                            <select id="category_id" class="form-control @error('category_id') is-invalid @enderror" name="category_id">
                                <option value="">{{ __('Aucune catégorie') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $dish->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $dish->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ __('Actif') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Mettre à jour') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
