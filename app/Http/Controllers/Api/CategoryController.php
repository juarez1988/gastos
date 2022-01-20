<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\HelperCheckToken;
use App\Category;
use App\Movement;

class CategoryController extends Controller
{
    public function index(){
        //$categories = Category::all()->sortBy('description');
        $categories = Category::where('active','Y')->orderBy('description')->get();
        return response()->json([
            'data' => $categories,
            'status' => 'success',
            'code' => 200,
            'message' => 'Listado de categorías'
            ]
        );
    }

    public function getAllCategories(){
        $categories = Category::where('id','>','0')->orderBy('description')->get();

        return response()->json([
            'data' => $categories,
            'status' => 'success',
            'code' => 200,
            'message' => 'Listado de categorías'
            ]
        );

    }

    public function getCategory($categoryId){
        $category = Category::find($categoryId);
        if($category == null){
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'La categoría no existe...'
            ]);
        }else{
            return response()->json([
                'data' => $category,
                'status' => 'success',
                'code' => 200,
                'message' => 'Éxito, categoría encontrada'
            ]);
        }
    }

    public function activeCategory($categoryId){
        $category = Category::find($categoryId);
        $category->active = 'Y';
        $category->update();
        return response()->json([
            'data' => $category,
            'status' => 'success',
            'code' => 200,
            'message' => 'Éxito, la categoría ha sido activada'
        ]);
    }

    public function desactiveCategory($categoryId){
        $category = Category::find($categoryId);
        $category->active = 'N';
        $category->update();
        return response()->json([
            'data' => $category,
            'status' => 'success',
            'code' => 200,
            'message' => 'Éxito, la categoría ha sido desactivada'
        ]);
    }






    public function createdCategory(Request $request){
        //Validación del formulario
        $v = Validator::make($request->all(), [
            'type' => 'required|string|in:discount,entry',
			'description' => 'required|string|min:3|unique:categories,description'
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
            $type = $request->get('type');

            //Creamos el objeto category a guardar
            $category = new Category();
            $category->type = $type;
            $category->description = $description;
            $category->save();

            return response()->json([
                'data' => $category,
                'status' => 'success',
                'code' => 200,
                'message' => 'La categoría ha sido creada correctamente!!'
            ]);
        }
    }


    public function editedCategory(Request $request){
        //Validación del formulario
        $v = Validator::make($request->all(), [
            'categoryId' => 'required',
            'type' => 'required|string',
			'description' => 'required|string|min:3|unique:categories,description,'.$request->get('categoryId')
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
            $type = $request->get('type');
            //Creamos el objeto category a guardar
            $category = Category::find($request->get('categoryId'));
            if($category == null){
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'La categoría no se ha modificado dado que no existe...'
                ]);
            }else{
                $category->type = $type;
                $category->description = $description;
                $category->update();
                //Redireccionamos
                return response()->json([
                    'data' => $category,
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La categoría ha sido modificada correctamente!!'
                ]);
            }
        }
    }

    public function deleteCategory($categoryId){
        //Buscamos que no haya movimientos asociados a esta categoría,
        //si los hay no eliminamos y devolvemos respuesta indicándolo.
        //Si no hay movimeintos eliminamos la categoría y devolvemos respuesta.
        $movements = Movement::where('category_id', $categoryId)->get();
        if(count($movements) == 0){
            $category = Category::find($categoryId);
            if($category == null){
                return response()->json([
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'La categoría no se ha eliminado dado que no existe...'
                ]);
            }else{
                $category->delete();
                return response()->json([
                    'data' => $category,
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'La categoría ha sido eliminada satisfactoriamente!!'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'La categoría no se ha podido eliminar dado que tiene movimientos asociados...'
            ]);
        }
    }




}
