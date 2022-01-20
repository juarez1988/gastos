@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @include('includes.message')

            <div class="card">
                <div class="card-header">
                    {{ __('Accounts') }}
                    <a href="{{ route('admin.createaccount')}}" class="btn btn-success float-right">{{ __('Nueva cuenta') }}</a>
                </div>

                <div class="card-body table-responsive">


                    <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Imagen</th>
                            <th scope="col">Usuario</th>
                            <th scope="col">Descripción cuenta</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Eliminar</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($accounts as $account)
                          <tr>
                            <td>
                                @if ($account->user->image)
                                <img src="{{ route('user.avatar',['filename'=>$account->user->image])}}" class="avatar-table"/>
                                @endif

                            </td>
                            <td>{{ $account->user->name }} {{ $account->user->surname }}</td>
                            <td>{{ $account->description }}</td>
                            <td>
                                @if ($account->active == 'Y')
                                    <a href="{{ route('admin.desactiveaccount',['accountId'=>$account->id])}}" class="btn btn-success">{{ __('active') }}</a>
                                @elseif ($account->active == 'N')
                                    <a href="{{ route('admin.activeaccount',['accountId'=>$account->id])}}" class="btn btn-danger">{{ __('desactive') }}</a>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.editaccount',['accountId'=>$account->id])}}" class="btn btn-primary">{{ __('Editar') }}</a>
                            </td>
                            <td>
                                <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#Modal{{$account->id}}">{{ __('Eliminar') }}</a>
                            </td>
                          </tr>
                          <!--Ventana modal para la eliminación-->
                            <div class="modal fade" id="Modal{{$account->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Eliminar cuenta {{$account->description}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">Si pulsa en eliminar la cuenta {{$account->description}} será eliminada permanentemente del sistema...</div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                                            <a href="{{ route('admin.deleteaccount',['accountId'=>$account->id])}}" class="btn btn-danger">Eliminar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          @endforeach
                        </tbody>
                      </table>





































                </div>
            </div>
        </div>
    </div>
</div>
@endsection
