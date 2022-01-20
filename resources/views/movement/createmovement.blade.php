@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @include('includes.message')
            <div class="card">
                <div class="card-header">{{ __('Crear movimiento') }}</div>
                <div class="card-body">
                    @if (isset($movement))
                        <form method="POST" action="{{ route('editedmovement') }}" enctype="multipart/form-data">
                    @else
                        <form method="POST" action="{{ route('createdmovement') }}" enctype="multipart/form-data">
                    @endif
                        @csrf
                        @if (isset($movement))
                            <input id="movementId" type="hidden" name="movementId" value="{{ $movement->id }}" required />
                        @endif

                        <div class="form-group row">
                            <label for="amount" class="col-md-4 col-form-label text-md-right">{{ __('Cantidad') }}</label>
                            <div class="col-md-6">
                                <input id="amount" type="number" step="any" class="form-control @error('amount') is-invalid @enderror" name="amount" value="@if (isset($movement)){{$movement->amount}}@endif" required autocomplete="amount" autofocus>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>
                            <div class="col-md-6">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="@if (isset($movement)) {{ $movement->description }} @endif" required autocomplete="description" autofocus>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="fecha" class="col-md-4 col-form-label text-md-right">{{ __('Fecha') }}</label>
                            <div class="col-md-6">
                                <input id="fecha" type="date" placeholder="YYYY/MM/DD" class="form-control @error('fecha') is-invalid @enderror" name="fecha" value="@if (isset($movement)){{ $movement->fecha }}@endif" required autocomplete="fecha" autofocus>

                                @error('fecha')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="category_id" class="col-md-4 col-form-label text-md-right">{{ __('Categoría') }}</label>

                            <div class="col-md-6">
                                <select id="category_id" class="form-control @error('category_id') is-invalid @enderror" name="category_id" required autofocus>
                                    <option value=""></option>
                                    @foreach ($categories as $category)
                                        <option value="@if (isset($category)) {{ $category->id }} @endif" @if (isset($movement) && $category->id == $movement->category_id) selected @endif>{{ $category->description }}</option>
                                    @endforeach
                                </select>

                                @error('category_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="account_id" class="col-md-4 col-form-label text-md-right">{{ __('Cuenta') }}</label>

                            <div class="col-md-6">
                                <select id="account_id" class="form-control @error('account_id') is-invalid @enderror" name="account_id" required autofocus>
                                    <option value=""></option>
                                    @foreach ($accounts as $account)
                                        @if (isset($account) && (Auth::user()->role == 'admin' || Auth::user()->id == $account->user->id))
                                            <option value="@if (isset($account)) {{ $account->id }} @endif" @if (isset($movement) && $account->id == $movement->account_id) selected @endif>{{ $account->description }} - {{ $account->user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>

                                @error('account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>





                        <div class="form-group row">


                            <label for="image_path" class="col-md-4 col-form-label text-md-right">{{ __('Ticket') }}</label>

                            <div class="col-md-6">


                                @if(isset($movement) && $movement->image != '' && $movement->image != null)
		                            <a href="{{ route('movement.ticket',['filename'=>$movement->image]) }}" target="_blank">
                                        Documento
                                        <img src="{{ asset('ticket.png') }}" width="50px"/>
                                    </a>
                                @endif



                                <div class="image-upload">
                                    <label for="image_path">
                                        Subir nuevo documento asociado <img src="{{ asset('camara.png') }}" alt ="Click aquí para subir foto" title ="Click aquí para subir foto" >
                                    </label>
                                    <input id="image_path" type="file" class="form-control{{ $errors->has('image_path') ? ' is-invalid' : '' }}" name="image_path">
                                </div>

                                @if ($errors->has('image_path'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('image_path') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>







                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    @if (isset($movement))
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
