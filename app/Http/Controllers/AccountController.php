<?php

namespace App\Http\Controllers;

use App\Account;
use App\Movement;
use App\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(){
        $accounts = Account::all()->sortBy('id');
        return view('admin.accounts',['accounts' => $accounts]);
    }

    public function activeAccount($accountId){
        $account = Account::find($accountId);
        $account->active = 'Y';
        $account->update();
        return redirect()->route('admin.accounts')->with(['message'=>'Éxito!, La cuenta ha sido activada']);
    }

    public function desactiveAccount($accountId){
        $account = Account::find($accountId);
        $account->active = 'N';
        $account->update();
        return redirect()->route('admin.accounts')->with(['message'=>'La cuenta ha sido desactivada...']);
    }

    public function createAccount(){
        $users = User::where('active','Y')->get();
        return view('admin.createaccount',['users' => $users]);
    }

    public function createdAccount(Request $request){
        //Validación del formulario
        $validate = $this->validate($request, [
            'user_id' => 'required|string',
			'description' => 'required|string|min:3',
        ]);

        //Recogemos los datos del formulario
        $description = $request->input('description');
        $user_id = $request->input('user_id');

        //Creamos el objeto account a guardar
        $account = new Account();
        $account->user_id = $user_id;
        $account->description = $description;
        $account->save();

        //Redireccionamos
        return redirect()->route('admin.accounts')->with(['message'=>'La cuenta ha sido creada!!!']);
    }

    public function editAccount($accountId){
        $users = User::where('active', 'Y')->get();
        $account = Account::find($accountId);
        return view('admin.createaccount',['users' => $users, 'account' => $account]);
    }

    public function editedAccount(Request $request){
        //Validación del formulario
        $validate = $this->validate($request, [
            'user_id' => 'required|string',
            'description' => 'required|string|min:3',
        ]);

        //Recogemos los datos del formulario
        $description = $request->input('description');
        $user_id = $request->input('user_id');

        //Creamos el objeto account a guardar
        $account = Account::find($request->input('accountId'));
        if($account == null){
            return redirect()->route('admin.accounts')->with(['message'=>'La cuenta no se ha modificado dado que no existe...']);
        }else{
            $account->user_id = $user_id;
            $account->description = $description;
            $account->update();
            //Redireccionamos
            return redirect()->route('admin.accounts')->with(['message'=>'La cuenta ha sido modificada!!!']);
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
                return redirect()->route('admin.accounts')->with(['message'=>'La cuenta no se ha eliminado dado que no existe...']);
            }else{
                $account->delete();
                return redirect()->route('admin.accounts')->with(['message'=>'La cuenta ha sido eliminada satisfactoriamente!!']);
            }

        }else{
            return redirect()->route('admin.accounts')->with(['message'=>'La cuenta no se ha podido eliminar dado que tiene movimientos asociados...']);
        }
    }
}
