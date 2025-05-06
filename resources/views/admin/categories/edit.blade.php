@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Modifier la catégorie') }}</span>
                    <div>
                        <a href="{{ route('admin.restaurants.show', $category->restaurant_id) }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> {{ __('Retour') }}
                        </a>
                        <x-delete-form
                            :route="route('admin.categories.destroy', $category)"
                            message="Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible."
                        />
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name">{{ __('Nom') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $category->name) }}" required autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
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
