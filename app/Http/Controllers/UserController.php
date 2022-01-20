<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\User;


class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function profile(){
        return view('user.profile');
    }

    public function password(){
        return view('user.password');
    }

    public function getImage($filename){
		$file = Storage::disk('users')->get($filename);
		return new Response($file, 200);
	}


    public function update(Request $request){

		// Conseguir usuario identificado
		$user = Auth::user();
		$id = $user->id;

		// Validación del formulario
		$validate = $this->validate($request, [
            'name' => 'required|string|max:255',
			'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id
        ]);

		// Recoger datos del formulario
		$name = $request->input('name');
		$surname = $request->input('surname');
		$email = $request->input('email');

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

		return redirect()->route('user.profile')
						 ->with(['message'=>'Usuario actualizado correctamente']);
	}



    public function updatepassword(Request $request){
        // Conseguir usuario identificado
		$user = Auth::user();
		$id = $user->id;
        $passwordDB = $user->password;


        // Validación del formulario
		$validate = $this->validate($request, [
            'password' => 'required|string|min:8',
			'newpassword' => 'required|string|min:8|confirmed',
        ]);

        //Recogemos los datos del formulario
        $password = $request->input('password');
        $newpassword = $request->input('newpassword');

        //comprobamos que la $password actual introducida en el formulario es igual a $passwordDB
        if(Hash::check($password, $passwordDB)){
            //Seteamos el valor de la contraseña una vez hasheada
            $user->password=Hash::make($newpassword);
            // Ejecutar consulta y cambios en la base de datos
            $user->update();


            //Enviamos email al correo del usuario con la nueva contraseña
            $to_name = $user->name.' '.$user->surname;
            $to_email = $user->email;
            $data = array("name"=>$to_name, "body"=>"Te informamos que tu contraseña para la aplicación gastos cambió a ".$request->input('newpassword').". Muchas gracias.");
            Mail::send('email.email', $data, function ($message) use ($to_name, $to_email){
                $message->from('citas010lugo@gmail.com', 'App gastos');
                $message->to($to_email, $to_name);
                $message->subject('Gastos: Cambio de password');
            });

            return redirect()->route('user.password')
                            ->with(['message'=>'Contraseña de usuario actualizada correctamente!']);
        }else{
            return redirect()->route('user.password')
                            ->with(['message'=>'Error, La contraseña actual no coincide con la almacenada en la base de datos']);
        }

    }

    public function activateUsers(){
        $users = User::all()->sortBy('id');
        return view('admin.activateusers',['users' => $users]);
    }

    public function activeUser($userId){
        $user = User::find($userId);
        $user->active = 'Y';
        $user->update();
        return redirect()->route('admin.activateusers')
                        ->with(['message'=>'Éxito!, El usuario ha sido activado']);
    }

    public function desactiveUser($userId){
        $user = User::find($userId);
        $user->active = 'N';
        $user->update();
        return redirect()->route('admin.activateusers')
                        ->with(['message'=>'El usuario ha sido desactivado...']);
    }




}
