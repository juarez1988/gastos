<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Helpers\HelperCheckToken;
use App\Movement;

class MovementController extends Controller
{


    public function index(){
        //$movements = Movement::where('id','>','0')->orderBy('fecha','DESC')->get();
        $movements = Movement::where('id','>','0')->with('account','account.user')->orderBy('fecha','DESC')->get();
        return response()->json([
            'data' => $movements,
            'status' => 'success',
            'code' => 200,
            'message' => 'Listado de movimientos'
            ]
        );
    }


    public function getTicket($filename){
        return response()->download(public_path('storage\\'.$filename));
	}


    public function searchMovements(Request $request){

        $fechaDesde = $request->get('fechaDesde');
        $fechaHasta = $request->get('fechaHasta');
        $category_id = $request->get('category_id');
        $account_id = $request->get('account_id');

        if(!empty($category_id) || (!empty($fechaDesde) && !empty($fechaHasta)) || !empty($account_id)){

            $movements = Movement::where('id','>','0');
            if($category_id && $category_id != ''){
                $movements=$movements->where('category_id', $category_id);
            }
            if($account_id && $account_id != ''){
                $movements=$movements->where('account_id', $account_id);
            }
            if($fechaDesde && $fechaDesde != '' && $fechaHasta && $fechaHasta != ''){
                $movements=$movements->where('fecha', '>=' , $fechaDesde);
                $movements=$movements->where('fecha', '<=' , $fechaHasta);
            }
            $movements=$movements->with('account','account.user')->orderBy('fecha','DESC')->get();

        }else{

            $movements = Movement::where('id','>','0')->with('account','account.user')->orderBy('fecha','DESC')->get();

        }

        return response()->json([
            'data' => $movements,
            'status' => 'success',
            'code' => 200,
            'message' => 'Listado de movimientos'
            ]
        );

    }


    public function getMovement(Request $request, $movementId){
        //Mostramos los datos del movimiento si la cuenta del movimiento pertenece al usuario logueado o si tiene rol admin
        //Obtenemos los datos del usuario logueado a través del token
        $checkToken = new HelperCheckToken();
        $decodeToken = $checkToken->decodeToken($request->header('token'));
        $movement = Movement::find($movementId);

        if($movement == null){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'El movimiento no existe...'
            ]);
        }else{
            if($decodeToken->role == 'admin' || $decodeToken->id == $movement->account->user_id){
                return response()->json([
                    'data' => $movement,
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Éxito, movimiento encontrado'
                ]);
            }else{
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No tienes permiso para ver este movimiento...'
                ]);
            }
        }
    }


    public function createdMovement(Request $request){
        //Validación del formulario
        $v = Validator::make($request->all(), [
            'amount' => 'required|numeric',
			'description' => 'required|string|min:3',
            'fecha' => 'required|date',
            'category_id' => 'exists:App\Category,id',
            'account_id' => 'exists:App\Account,id'
        ]);

        if($v->fails()){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Error en los datos de los campos facilitados: '.$v->errors()
            ]);
        }else{
            //Recogemos los datos del formulario
            $amount = $request->get('amount');
            $description = $request->get('description');
            $fecha = $request->get('fecha');
            $category_id = $request->get('category_id');
            $account_id = $request->get('account_id');

            //Creamos el objeto category a guardar
            $movement = new Movement();
            $movement->amount = $amount;
            $movement->description = $description;
            $movement->fecha = $fecha;
            $movement->category_id = $category_id;
            $movement->account_id = $account_id;
            // Subir la imagen si la hay
            $image_path = $request->file('image_path');
            if($image_path){
                // Poner nombre unico
                $image_path_name = time().$image_path->getClientOriginalName();
                // Guardar en la carpeta storage (storage/app/users)
                Storage::disk('public')->put($image_path_name, File::get($image_path));
                // Seteo el nombre de la imagen en el objeto
                $movement->image = $image_path_name;
            }
            $movement->save();
            return response()->json([
                'data' => $movement,
                'status' => 'success',
                'code' => 200,
                'message' => 'El movimiento ha sido creado correctamente!!'
            ]);
        }
    }


    public function editedMovement(Request $request){
        //Validación
        $v = Validator::make($request->all(), [
            'amount' => 'required|numeric',
			'description' => 'required|string|min:3',
            'fecha' => 'required|date',
            'category_id' => 'exists:App\Category,id',
            'account_id' => 'exists:App\Account,id'
        ]);

        if($v->fails()){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Error en los datos de los campos facilitados: '.$v->errors()
            ]);
        }else{
            //Recogemos los datos del formulario
            $amount = $request->get('amount');
            $description = $request->get('description');
            $fecha = $request->get('fecha');
            $category_id = $request->get('category_id');
            $account_id = $request->get('account_id');
            //Creamos el objeto Movement a guardar
            $movement = Movement::find($request->get('movementId'));
            if($movement == null){
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Movimiento no modificado, el movimiento no existe...'
                ]);
            }else{
                //Comprobamos que se admin o sea el usuario de la cuenta del movimiento
                $checkToken = new HelperCheckToken();
                $decodeToken = $checkToken->decodeToken($request->header('token'));
                if($decodeToken->role == 'admin' || $decodeToken->id == $movement->account->user_id){
                    $movement->amount = $amount;
                    $movement->description = $description;
                    $movement->fecha = $fecha;
                    $movement->category_id = $category_id;
                    $movement->account_id = $account_id;
                    // Subir la imagen
                    $image_path = $request->file('image_path');
                    if($image_path){
                        // Poner nombre unico
                        $image_path_name = time().$image_path->getClientOriginalName();
                        // Guardar en la carpeta storage (storage/app/users)
                        Storage::disk('public')->put($image_path_name, File::get($image_path));
                        // Seteo el nombre de la imagen en el objeto
                        $movement->image = $image_path_name;
                    }
                    $movement->update();
                    //Respuesta
                    return response()->json([
                        'data' => $movement,
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El movimiento ha sido modificado correctamente!!'
                    ]);
                }else{
                    return response()->json([
                        'status' => 'error',
                        'code' => 404,
                        'message' => 'No tienes permiso para editar este movimiento...'
                    ]);
                }
            }
        }
    }


    public function deleteMovement(Request $request, $movementId){
        $movement = Movement::find($movementId);
        if($movement == null){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Movimiento no eliminado, el movimiento no existe...'
            ]);
        }else{
            $checkToken = new HelperCheckToken();
            $decodeToken = $checkToken->decodeToken($request->header('token'));
            if($decodeToken->role == 'admin' || $decodeToken->id == $movement->account->user_id){
                $movement->delete();
                return response()->json([
                    'data' => $movement,
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El movimiento ha sido eliminado correctamente!!'
                ]);
            }else{
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'No tienes permiso para eliminar este movimiento...'
                ]);
            }
        }
    }


}
