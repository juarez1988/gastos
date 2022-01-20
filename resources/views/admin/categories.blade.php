@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @include('includes.message')

            <div class="card">
                <div class="card-header">
                    {{ __('Categories') }}
                    <a href="{{ route('admin.createcategory')}}" class="btn btn-success float-right">{{ __('Nueva categoría') }}</a>
                </div>

                <div class="card-body table-responsive">

                    <!--Tabla de exportación a excel-->
                    <button onclick="exportTableToExcel('tableCategories', 'tableCategories')" class="btn btn-success">Excel</button>
                    <table id="tableCategories" style="display: none">
                        <thead>
                          <tr>
                            <th>type</th>
                            <th>category</th>
                            <th>active</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{$category->type}}</td>
                                    <td>{{$category->description}}</td>
                                    <td>{{$category->active}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!--Fin tabla de exportación a excel-->


                    <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Tipo</th>
                            <th scope="col">Descripción categoría</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Eliminar</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($categories as $category)
                          <tr>
                            <td>
                                @if ($category->type == 'entry')
                                <p style="color:green;font-weight: bold;">{{ __('ingreso') }}</p>
                                @else
                                <p style="color:red;font-weight: bold;">{{ __('descuento') }}</p>
                                @endif
                            </td>
                            <td>{{ $category->description }}</td>
                            <td>
                                @if ($category->active == 'Y')
                                    <a href="{{ route('admin.desactivecategory',['categoryId'=>$category->id])}}" class="btn btn-success">{{ __('active') }}</a>
                                @elseif ($category->active == 'N')
                                    <a href="{{ route('admin.activecategory',['categoryId'=>$category->id])}}" class="btn btn-danger">{{ __('desactive') }}</a>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.editcategory',['categoryId'=>$category->id])}}" class="btn btn-primary">{{ __('Editar') }}</a>
                            </td>
                            <td>
                                <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#Modal{{$category->id}}">{{ __('Eliminar') }}</a>
                            </td>
                          </tr>
                          <!--Ventana modal para la eliminación-->
                            <div class="modal fade" id="Modal{{$category->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Eliminar categoría {{$category->description}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">Si pulsa en eliminar la categoría {{$category->description}} será eliminada permanentemente del sistema...</div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                                            <a href="{{ route('admin.deletecategory',['categoryId'=>$category->id])}}" class="btn btn-danger">Eliminar</a>
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
