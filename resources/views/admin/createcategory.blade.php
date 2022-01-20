@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @include('includes.message')
            <div class="card">
                @if (isset($category))
                    <div class="card-header">{{ __('Editar categoría') }}</div>
                @else
                    <div class="card-header">{{ __('Crear categoría') }}</div>
                @endif
                <div class="card-body">
                    @if (isset($category))
                        <form method="POST" action="{{ route('admin.editedcategory') }}" enctype="multipart/form-data">
                    @else
                        <form method="POST" action="{{ route('admin.createdcategory') }}" enctype="multipart/form-data">
                    @endif
                        @csrf
                        @if (isset($category))
                            <input id="categoryId" type="hidden" name="categoryId" value="{{ $category->id }}" required />
                        @endif
                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                            <div class="col-md-6">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="@if (isset($category)) {{ $category->description }} @endif" required autocomplete="description" autofocus>

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('Tipo') }}</label>

                            <div class="col-md-6">
                                <select id="type" class="form-control @error('type') is-invalid @enderror" name="type" required autofocus>
                                    <option value="discount" @if (isset($category) && $category->type == 'discount') selected @endif>{{ __('descuento') }}</option>
                                    <option value="entry" @if (isset($category) && $category->type == 'entry') selected @endif>{{ __('ingreso') }}</option>
                                </select>

                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    @if (isset($category))
                                        {{ __('Editar') }}
                                    @else
                                        {{ __('Crear') }}
                                    @endif
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
