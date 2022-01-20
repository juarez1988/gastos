@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">




            <div class="card">
                <div class="card-header">{{ __('Buscar') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('searchmovements') }}" enctype="multipart/form-data">
                        @csrf


                        <div class="form-group row">
                            <label for="fechaDesde" class="col-md-4 col-form-label text-md-right">{{ __('Fecha desde') }}</label>
                            <div class="col-md-6">
                                <input id="fechaDesde" type="date" placeholder="YYYY/MM/DD" class="form-control" name="fechaDesde" value=""/>
                                @error('fechaDesde')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="fechaHasta" class="col-md-4 col-form-label text-md-right">{{ __('Fecha hasta') }}</label>
                            <div class="col-md-6">
                                <input id="fechaHasta" type="date" placeholder="YYYY/MM/DD" class="form-control" name="fechaHasta" value=""/>
                                @error('fechaHasta')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="category_id" class="col-md-4 col-form-label text-md-right">{{ __('Categoría') }}</label>

                            <div class="col-md-6">
                                <select id="category_id" class="form-control @error('category_id') is-invalid @enderror" name="category_id" >
                                    <option value=""></option>
                                    @foreach ($categories as $category)
                                        <option value="@if (isset($category)) {{ $category->id }} @endif">{{ $category->description }}</option>
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
                                <select id="account_id" class="form-control @error('account_id') is-invalid @enderror" name="account_id" >
                                    <option value=""></option>
                                    @foreach ($accounts as $account)
                                        <option value="@if (isset($account)) {{ $account->id }} @endif">{{ $account->description }} - {{ $account->user->name }}</option>
                                    @endforeach
                                </select>

                                @error('account_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">{{ __('Buscar') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>







            <br/>
            @include('includes.message')
            <br/>

            <div class="card">
                <div class="card-header">
                    {{ __('Movements') }}
                    <a href="{{ route('createmovement')}}" class="btn btn-success float-right">{{ __('Nuevo movimiento') }}</a>
                </div>

                <div class="card-body table-responsive">

                    <!--Tabla de exportación a excel-->
                    <button onclick="exportTableToExcel('tableMovements', 'tableMovements')" class="btn btn-success">Excel</button>
                    <table id="tableMovements" style="display: none">
                        <thead>
                          <tr>
                            <th>Fecha</th>
                            <th>Cantidad</th>
                            <th>Categoria</th>
                            <th>Tipo Categoria</th>
                            <th>Usuario cuenta</th>
                            <th>Cuenta</th>
                            <th>Descripcion</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($movements as $movement)
                                <tr>
                                    <td>{{$movement->fecha}}</td>
                                    <td>{{$movement->amount}}</td>
                                    <td>{{$movement->category->description}}</td>
                                    <td>{{$movement->category->type}}</td>
                                    <td>{{$movement->account->user->name}} {{$movement->account->user->surname}}</td>
                                    <td>{{$movement->account->description}}</td>
                                    <td>{{$movement->description}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!--Fin tabla de exportación a excel-->


                    <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Categoria</th>
                            <th scope="col">Tipo Categoria</th>
                            <th scope="col">Usuario cuenta</th>
                            <th scope="col">Cuenta</th>
                            <th scope="col">Descripcion</th>
                            <th scope="col">Documento</th>
                            <th scope="col">Editar</th>
                            <th scope="col">Eliminar</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
                                $total_ingresos = 0;
                                $total_descuentos = 0;
                            ?>

                          @foreach($movements as $movement)
                          <tr>
                            <td>{{$movement->fecha}}</td>
                            <td>{{$movement->amount}}</td>
                            <?php
                                if($movement->category->type == 'entry'){
                                    $total_ingresos = $total_ingresos + $movement->amount;
                                }else {
                                    $total_descuentos = $total_descuentos + $movement->amount;
                                }
                            ?>
                            <td>{{$movement->category->description}}</td>
                            <td>
                                @if ($movement->category->type == 'entry')
                                    <p style="color:green;font-weight: bold;">{{ __('ingreso') }}</p>
                                @else
                                    <p style="color:red;font-weight: bold;">{{ __('descuento') }}</p>
                                @endif
                            </td>
                            <td>{{$movement->account->user->name}} {{$movement->account->user->surname}}</td>
                            <td>{{$movement->account->description}}</td>
                            <td>{{$movement->description}}</td>
                            <td>
                                @if (isset($movement->image))
                                    <a href="{{ route('movement.ticket',['filename'=>$movement->image]) }}" target="_blank"><img src="{{ asset('ticket.png') }}" width="50px"/></a>
                                @endif
                            </td>
                            @if (Auth::user()->role == 'admin' || Auth::user()->id == $movement->account->user->id)
                                <td><a href="{{ route('editmovement',['movementId'=>$movement->id])}}" class="btn btn-primary">{{ __('Editar') }}</a></td>
                                <td><a href="#" class="btn btn-danger" data-toggle="modal" data-target="#Modal{{$movement->id}}">{{ __('Eliminar') }}</a></td>
                            @else
                                <td></td>
                                <td></td>
                            @endif

                          </tr>
                          <!--Ventana modal para la eliminación-->
                            <div class="modal fade" id="Modal{{$movement->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Eliminar movimiento {{$movement->description}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">Si pulsa en eliminar el movimiento {{$movement->description}} será eliminado permanentemente del sistema...</div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
                                            <a href="{{ route('deletemovement',['movementId'=>$movement->id])}}" class="btn btn-danger">Eliminar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          @endforeach
                        </tbody>
                      </table>
                </div>
            </div>
            <br>
            {{$movements->links()}}
            <h2>Total ingresos: {{$total_ingresos}}</h2>
            <h2>Total descuentos: {{$total_descuentos}}</h2>
            <h1>TOTAL: {{$total_ingresos - $total_descuentos}}</h1>


            @if (isset($filtro) && $filtro == true && isset($datosGrafica))
                <?php
                    $categoriasGrafica = '';
                    $cantidadesGrafica = '';
                ?>
                @foreach ($datosGrafica as $datoGrafica)
                    <?php
                        $categoriasGrafica .=  "'".$datoGrafica->description."', ";
                        $cantidadesGrafica .=  $datoGrafica->amount.', ';
                    ?>
                @endforeach
                <canvas id="myChart" width="400" height="200"></canvas>
                <script>
                    const ctx = document.getElementById('myChart').getContext('2d');
                    const myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: [<?php echo $categoriasGrafica;?>],
                            datasets: [{
                                label: 'Movimientos',
                                data: [<?php echo $cantidadesGrafica;?>],
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            @endif





        </div>
    </div>
</div>
@endsection
