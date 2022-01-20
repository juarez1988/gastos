@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @include('includes.message')

            <div class="card">
                <div class="card-header">{{ __('Cambiar contraseña') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('user.updatepassword') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Contraseña actual') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="text" class="form-control @error('password') is-invalid @enderror" name="password" value="" required autofocus>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="newpassword" class="col-md-4 col-form-label text-md-right">{{ __('Nueva contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="newpassword" type="text" class="form-control @error('newpassword') is-invalid @enderror" name="newpassword" value="" required autofocus>

                                @error('newpassword')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="newpassword-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirmar nueva contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="newpassword-confirm" type="text" class="form-control" name="newpassword_confirmation" value="" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Cambiar') }}
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
