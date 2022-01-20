<?php

namespace App\Http\Controllers\Api;

use App\Helpers\HelperCheckToken;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserController extends Controller{

    public function getImage($filename){
		$file = Storage::disk('users')->get($filename);
		return new Response($file, 200);
	}

    public function login(Request $request){
        $email = $request->get('email','');
        $password = $request->get('password','');
        $user = User::where(['email' => $email, 'active' => 'Y'])->first();
        if(!$user){
            return response () -> json ([
                'message' => 'No existe en la base de datos un usuario activo con ese email...',
                'code' => 404,
                'status' => 'error'
            ]);
        }

        if(Hash::check($password, $user->password)){
            unset($user['password']);
            // Token de inicio de sesión exitoso
            $checkToken = new HelperCheckToken();
            $token = $checkToken->encodeToken($user);
            //cache('user-'.$user['id'],$user);
            return response()->json([
                'data' => $token,
                'status' => 'success',
                'code' => 200,
                'message' => 'Token devuelto correctamente'
                ]
            );
        }else{
            return response () -> json ([
                'message' => 'La password proporcionada para ese usuario no es correcta...',
                'code' => 404,
                'status' => 'error'
            ]);
        }
    }


    public function getDecodeToken(Request $request){
        $checkToken = new HelperCheckToken();
        $decodeToken = $checkToken->decodeToken($request->header('token'));
        if(is_object($decodeToken)){
            return response()->json([
                'data' => $decodeToken,
                'status' => 'success',
                'code' => 200,
                'message' => 'Token decodificado devuelto'
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'El token no es correcto...'
            ]);
        }
    }

    public function register(Request $request){
        //Recojo los datos
        $name = $request->get('name','');
        $surname = $request->get('surname','');
        $email = $request->get('email','');
        $password = $request->get('password','');

        //Validaciones de los datos
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
			'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ]);

        //Compruebo si hay fallos y sino registro el usuario
        if($v->fails()){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido registrar: '.$v->errors()
            ]);
        }else{
            //REGISTRO DE USUARIO!
            $user = User::create([
                'role' => 'user',
                'name' => $name,
                'surname' => $surname,
                'email' => $email,
                'password' => Hash::make($password),
                'active' => 'N'
            ]);
            return response()->json([
                'data' => $user,
                'status' => 'success',
                'code' => 200,
                'message' => 'Usuario registrado correctamente!!'
            ]);
        }
    }


    public function activateUsers(){
        $users = User::where('id','>','0')->orderBy('id')->get();
        return response()->json([
            'data' => $users,
            'status' => 'success',
            'code' => 200,
            'message' => 'Listado ded usuarios'
        ]);
    }

    public function listActiveUsers(){
        $users = User::where(['active' => 'Y'])->orderBy('id')->get();
        return response()->json([
            'data' => $users,
            'status' => 'success',
            'code' => 200,
            'message' => 'Listado ded usuarios'
        ]);
    }

    public function activeUser($userId){
        $user = User::find($userId);
        $user->active = 'Y';
        $user->update();
        return response()->json([
            'data' => $user,
            'status' => 'success',
            'code' => 200,
            'message' => 'Éxito, el usuario ha sido activado'
        ]);
    }

    public function desactiveUser($userId){
        $user = User::find($userId);
        $user->active = 'N';
        $user->update();
        return response()->json([
            'data' => $user,
            'status' => 'success',
            'code' => 200,
            'message' => 'Éxisto el usuario ha sido desactivado'
        ]);
    }

    public function updatepassword(Request $request){
        //Obtenemos los datos del usuario autenticado
        $checkToken = new HelperCheckToken();
        $decodeToken = $checkToken->decodeToken($request->header('token'));
        // Conseguir usuario identificado
		$user = User::find($decodeToken->id)->first();
        $passwordDB = $user->password;
        // Validación del formulario
        $v = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
			'newpassword' => 'required|string|min:8|confirmed',
        ]);

        if($v->fails()){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Error en las contraseñas facilitadas: '.$v->errors()
            ]);
        }else{
            //Recogemos los datos del formulario
            $password = $request->get('password','');
            $newpassword = $request->get('newpassword','');

            //comprobamos que la $password actual introducida en el formulario es igual a $passwordDB
            if(Hash::check($password, $passwordDB)){
                //Seteamos el valor de la contraseña una vez hasheada
                $user->password=Hash::make($newpassword);
                // Ejecutar consulta y cambios en la base de datos
                $user->update();

                //Enviamos email al correo del usuario con la nueva contraseña
                $to_name = $user->name.' '.$user->surname;
                $to_email = $user->email;
                $data = array("name"=>$to_name, "body"=>"Te informamos que tu contraseña para la aplicación gastos cambió a ".$newpassword.". Muchas gracias.");
                Mail::send('email.email', $data, function ($message) use ($to_name, $to_email){
                    $message->from('citas010lugo@gmail.com', 'App gastos');
                    $message->to($to_email, $to_name);
                    $message->subject('Gastos: Cambio de password');
                });

                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Éxito, la contraseña del usuario ha sido actualizada!'
                ]);
            }else{
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'La contraseña facilitada no coincide con la almacenada en la base de datos...'
                ]);
            }
        }
    }

    public function update(Request $request){
		//Obtenemos los datos del usuario autenticado
        $checkToken = new HelperCheckToken();
        $decodeToken = $checkToken->decodeToken($request->header('token'));
        // Conseguir usuario identificado
        $user = User::find($decodeToken->id)->first();
		$id = $user->id;

		// Validación del formulario
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
			'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id
        ]);

        if($v->fails()){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'No se ha pasado la validación de los datos: '.$v->errors()
            ]);
        }else{
            // Recoger datos del formulario
            $name = $request->get('name');
            $surname = $request->get('surname');
            $email = $request->get('email');

            // Asignar nuevos valores al objeto del usuario
            $user->name = $name;
            $user->surname = $surname;
            $user->email = $email;

            // Subir la imagen
            $image_path = $request->file('image_path');
            if($image_path){
                // Poner nombre unico
                $image_path_name = time().$image_path->getClientOriginalName();

                // Guardar en la carpeta storage (storage/app/users)
                Storage::disk('users')->put($image_path_name, File::get($image_path));

                // Seteo el nombre de la imagen en el objeto
                $user->image = $image_path_name;
            }

            // Ejecutar consulta y cambios en la base de datos
		    $user->update();
            return response()->json([
                'data' => $user,
                'status' => 'success',
                'code' => 200,
                'message' => 'Éxisto el usuario ha sido actualizado'
            ]);
        }
	}

}
