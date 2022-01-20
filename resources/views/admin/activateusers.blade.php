@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @include('includes.message')

            <div class="card">
                <div class="card-header">{{ __('activeUsers') }}</div>

                <div class="card-body table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Avatar</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Apellidos</th>
                            <th scope="col">Email</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Estado</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($users as $user)
                          <tr>
                            <td>
                                @if ($user->image)
                                <img src="{{ route('user.avatar',['filename'=>$user->image])}}" class="avatar-table"/>
                                @endif

                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->surname }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                @if ($user->active == 'Y')
                                    <a href="{{ route('admin.desactiveuser',['userId'=>$user->id])}}" class="btn btn-success">{{ __('active') }}</a>
                                @elseif ($user->active == 'N')
                                    <a href="{{ route('admin.activeuser',['userId'=>$user->id])}}" class="btn btn-danger">{{ __('desactive') }}</a>
                                @endif
                            </td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
