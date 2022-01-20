@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @include('includes.message')
            <div class="card">
                <div class="card-header">{{ __('Crear cuenta') }}</div>
                <div class="card-body">
                    @if (isset($account))
                        <form method="POST" action="{{ route('admin.editedaccount') }}" enctype="multipart/form-data">
                    @else
                        <form method="POST" action="{{ route('admin.createdaccount') }}" enctype="multipart/form-data">
                    @endif
                        @csrf
                        @if (isset($account))
                            <input id="accountId" type="hidden" name="accountId" value="{{ $account->id }}" required />
                        @endif
                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Description') }}</label>

                            <div class="col-md-6">
                                <input id="description" type="text" class="form-control @error('description') is-invalid @enderror" name="description" value="@if (isset($account)) {{ $account->description }} @endif" required autocomplete="description" autofocus>

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="user_id" class="col-md-4 col-form-label text-md-right">{{ __('Usuario') }}</label>

                            <div class="col-md-6">
                                <select id="user_id" class="form-control @error('user_id') is-invalid @enderror" name="user_id" required autofocus>
                                    @foreach ($users as $user)
                                        <option value="@if (isset($account)) {{ $user->id }} @endif" @if (isset($account) && $user->id == $account->user_id) selected @endif>{{ $user->name }} {{ $user->surname }} - {{ $user->active }}</option>
                                    @endforeach
                                </select>

                                @error('user_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    @if (isset($account))
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
