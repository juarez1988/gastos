<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\HelperCheckToken;
use App\Account;
use App\Movement;
use App\User;

class AccountController extends Controller
{

    public function index(Request $request){
        //Obtenemos los datos del usuario logueado a través del token
        $checkToken = new HelperCheckToken();
        $decodeToken = $checkToken->decodeToken($request->header('token'));
        //Si el usuario logueado tiene rol admin hacemos la consulta para sacar todas las cuentas activas (de todos los usuaios de la aplicación),
        //si no es admin entonces sacamos las cuentas activas del usuario logueado.
        if($decodeToken->role == 'admin'){
            $accounts = Account::where('active','Y')->with('user')->get();
        }else{
            $accounts = Account::where(['active' => 'Y', 'user_id' => $decodeToken->id])->with('user')->get();
        }
        return response()->json([
            'data' => $accounts,
            'status' => 'success',
            'code' => 200,
            'message' => 'Cuenta devuelta correctamente'
            ]
        );
    }

    public function getAllAccounts(){
        $accounts = Account::where('id','>','0')->with('user')->get();
        return response()->json([
            'data' => $accounts,
            'status' => 'success',
            'code' => 200,
            'message' => 'Token devuelto correctamente'
            ]
        );
    }

    public function getAccount($accountId){
        $account = Account::find($accountId);
        if($account == null){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'La cuenta no existe...'
            ]);
        }else{
            return response()->json([
                'data' => $account,
                'status' => 'success',
                'code' => 200,
                'message' => 'Éxito, cuenta encontrada'
            ]);
        }
    }

    public function activeAccount($accountId){
        $account = Account::find($accountId);
        $account->active = 'Y';
        $account->update();
        return response()->json([
            'data' => $account,
            'status' => 'success',
            'code' => 200,
            'message' => 'Éxito, la cuenta ha sido activada'
        ]);
    }

    public function desactiveAccount($accountId){
        $account = Account::find($accountId);
        $account->active = 'N';
        $account->update();
        return response()->json([
            'data' => $account,
            'status' => 'success',
            'code' => 200,
            'message' => 'Éxito, la cuenta ha sido desactivada'
        ]);
    }

    public function createdAccount(Request $request){
        //Validación del formulario
        $v = Validator::make($request->all(), [
            'user_id' => 'required|string|exists:users,id',
			'description' => 'required|string|min:3',
        ]);

        if($v->fails()){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Error en los datos de los campos facilitados: '.$v->errors()
            ]);
        }else{
            //Recogemos los datos del formulario
            $description = $request->get('description');
            $user_id = $request->get('user_id');

            //Creamos el objeto account a guardar
            $account = new Account();
            $account->user_id = $user_id;
            $account->description = $description;
            $account->save();

            return response()->json([
                'data' => $account,
                'status' => 'success',
                'code' => 200,
                'message' => 'La cuenta ha sido creada correctamente!!'
            ]);
        }
    }


    public function editedAccount(Request $request){
        //Validación del formulario
        $v = Validator::make($request->all(), [
            'accountId' => 'required',
            'user_id' => 'required|string|exists:users,id',
			'description' => 'required|string|min:3',
        ]);
        if($v->fails()){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Error!! en los datos de los campos facilitados: '.$v->errors()
            ]);
        }else{
            //Recogemos los datos del formulario
            $description = $request->get('description');
            $user_id = $request->get('user_id');
            //Creamos el objeto account a guardar
            $account = Account::find($request->get('accountId'));
            if($account == null){
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'La cuenta no se ha modificado dado que no existe...'
                ]);
            }else{
                $account->user_id = $user_id;
                $account->description = $description;
                $account->update();
                //Redireccionamos
                return response()->json([
                    'data' => $account,
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La cuenta ha sido modificada correctamente!!'
                ]);
            }
        }
    }

    public function deleteAccount($accountId){
        //Buscamos que no haya movimientos asociados a esta cuenta,
        //si los hay no eliminamos y redireccionamos indicándolo.
        //Si no hay movimeintos eliminamos la cuenta y redireccionamos dando el aviso de eliminación.
        $movements = Movement::where('account_id', $accountId)->get();
        if(count($movements) == 0){
            $account = Account::find($accountId);
            if($account == null){
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'La cuenta no se ha eliminado dado que no existe...'
                ]);
            }else{
                $account->delete();
                return response()->json([
                    'data' => $account,
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La cuenta ha sido eliminada satisfactoriamente!!'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'La cuenta no se ha podido eliminar dado que tiene movimientos asociados...'
            ]);
        }
    }

}
