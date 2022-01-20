<?php


namespace App\Http\Controllers;


use App\Account;
use App\Category;
use App\Movement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;


class MovementController extends Controller
{


    public function index(){
        //$movements = Movement::all()->sortByDesc('Fecha');
        $movements = Movement::orderBy('fecha', 'desc')->paginate(10);
        $categories = Category::where('active','Y')->orderBy('description','ASC')->get();
        $accounts = Account::where('active','Y')->orderBy('description','ASC')->get();
        return view('movement.movements',['movements' => $movements, 'categories' => $categories, 'accounts' => $accounts]);
    }


    public function search(Request $request){
        //REALIZAR BÚSQUEDA
        $fechaDesde = $request->input('fechaDesde');
        $fechaHasta = $request->input('fechaHasta');
        $category_id = $request->input('category_id');
        $account_id = $request->input('account_id');

        if(!empty($category_id) || (!empty($fechaDesde) && !empty($fechaHasta)) || !empty($account_id)){
            $movements = Movement::when(request('category_id') && request('category_id')!= '', function($query){
                return $query->where('category_id', '=', request('category_id'));
            })
            ->when(request('account_id') && request('account_id')!= '', function($query){
                return $query->where('account_id', '=', request('account_id'));
            })
            ->when(request('fechaDesde') && request('fechaDesde')!='' && request('fechaHasta') && request('fechaHasta')!='', function($query){
                return $query->where('fecha','>=', request('fechaDesde'))
                             ->where('fecha','<=', request('fechaHasta'));
            })
            ->orderBy('fecha', 'desc')
            ->paginate();

            $consultaDatosGráfica = "select categories.description,sum(amount) as amount from movements,categories where movements.category_id = categories.id ";
            if($category_id && $category_id != ''){
                $consultaDatosGráfica .= "AND category_id = '".$category_id."' ";
            }
            if($account_id && $account_id != ''){
                $consultaDatosGráfica .= "AND account_id = '".$account_id."' ";
            }
            if($fechaDesde && $fechaDesde != '' && $fechaHasta && $fechaHasta != ''){
                $consultaDatosGráfica .= "AND fecha >= '".$fechaDesde."' AND fecha <= '".$fechaHasta."' ";
            }
            $consultaDatosGráfica .= "GROUP BY categories.description";
            $datosGrafica = DB::select($consultaDatosGráfica);

            $categories = Category::where('active','Y')->orderBy('description','ASC')->get();
            $accounts = Account::where('active','Y')->orderBy('description','ASC')->get();
            return view('movement.movements',['movements' => $movements, 'categories' => $categories, 'accounts' => $accounts, 'filtro' => true, 'datosGrafica' => $datosGrafica]);
        }else{
            $movements = Movement::orderBy('fecha', 'desc')->paginate(10);
            $categories = Category::where('active','Y')->orderBy('description','ASC')->get();
            $accounts = Account::where('active','Y')->orderBy('description','ASC')->get();
            return view('movement.movements',['movements' => $movements, 'categories' => $categories, 'accounts' => $accounts]);
        }
        //FIN REALIZAR BÚSQUEDA
    }


    public function createMovement(){
        $categories = Category::where('active','Y')->orderBy('description','ASC')->get();
        $accounts = Account::where('active','Y')->orderBy('description','ASC')->get();
        return view('movement.createmovement',['categories' => $categories, 'accounts' => $accounts]);
    }


    public function editMovement($movementId){
        $movement = Movement::find($movementId);
        if(Auth::user()->role == 'admin' || Auth::user()->id == $movement->account->user->id){
            $categories = Category::where('active','Y')->orderBy('description','ASC')->get();
            $accounts = Account::where('active','Y')->orderBy('description','ASC')->get();
            return view('movement.createmovement',['movement' => $movement, 'categories' => $categories, 'accounts' => $accounts]);
        }else{
            return redirect()->route('movements')->with(['message'=>'Error! No tiene permisos para editar este movimiento, solo el admin o el propietario del mismo puede hacerlo...']);
        }
    }


    public function getTicket($filename){
        return response()->download(public_path('storage\\'.$filename));
	}


    public function createdMovement(Request $request){
        //Validación del formulario
        $validate = $this->validate($request, [
            'amount' => 'required|numeric',
			'description' => 'required|string|min:3',
            'fecha' => 'required|date',
            'category_id' => 'exists:App\Category,id',
            'account_id' => 'exists:App\Account,id'
        ]);
        //Recogemos los datos del formulario
        $amount = $request->input('amount');
        $description = $request->input('description');
        $fecha = $request->input('fecha');
        $category_id = $request->input('category_id');
        $account_id = $request->input('account_id');
        //Creamos el objeto Category a guardar
        $movement = new Movement();
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
        $movement->save();
        //Redireccionamos
        return redirect()->route('movements')->with(['message'=>'El movimiento ha sido creado!!!']);
    }



    public function editedMovement(Request $request){
        //Validación del formulario
        $validate = $this->validate($request, [
            'amount' => 'required|numeric',
			'description' => 'required|string|min:3',
            'fecha' => 'required|date',
            'category_id' => 'exists:App\Category,id',
            'account_id' => 'exists:App\Account,id'
        ]);
        //Recogemos los datos del formulario
        $amount = $request->input('amount');
        $description = $request->input('description');
        $fecha = $request->input('fecha');
        $category_id = $request->input('category_id');
        $account_id = $request->input('account_id');
        //Creamos el objeto Movement a guardar
        $movement = Movement::find($request->input('movementId'));
        if($movement == null){
            return redirect()->route('movements')->with(['message'=>'El movimiento no se ha modificado dado que no existe...']);
        }else{
            //Comprobamos que se admin o sea el usuario de la cuenta del movimiento
            if(Auth::user()->role == 'admin' || Auth::user()->id == $movement->account->user->id){
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
                //Redireccionamos
                return redirect()->route('movements')->with(['message'=>'El movimiento ha sido modificado!!!']);
            }else{
                return redirect()->route('movements')->with(['message'=>'Error! No tiene permisos para editar este movimiento, solo el admin o el propietario del mismo puede hacerlo...']);
            }
        }
    }



    public function deleteMovement($movementId){
        $movement = Movement::find($movementId);
        if($movement == null){
            return redirect()->route('movements')->with(['message'=>'El movimiento no se ha eliminado dado que no existe...']);
        }else{
            if(Auth::user()->role == 'admin' || Auth::user()->id == $movement->account->user->id){
                $movement->delete();
                return redirect()->route('movements')->with(['message'=>'El movimiento ha sido eliminada satisfactoriamente!!']);
            }else{
                return redirect()->route('movements')->with(['message'=>'Error! No tiene permisos para eliminar este movimiento, solo el admin o el propietario del mismo puede hacerlo...']);
            }
        }
    }


}
