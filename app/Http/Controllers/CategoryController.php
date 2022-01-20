<?php

namespace App\Http\Controllers;

use App\Category;
use App\Movement;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all()->sortBy('description');
        return view('admin.categories',['categories' => $categories]);
    }

    public function activeCategory($categoryId){
        $category = Category::find($categoryId);
        $category->active = 'Y';
        $category->update();
        return redirect()->route('admin.categories')->with(['message'=>'Éxito!, La categoría ha sido activada']);
    }

    public function desactiveCategory($categoryId){
        $category = Category::find($categoryId);
        $category->active = 'N';
        $category->update();
        return redirect()->route('admin.categories')->with(['message'=>'La categoría ha sido desactivada...']);
    }

    public function createCategory(){
        return view('admin.createcategory');
    }

    public function createdCategory(Request $request){
        //Validación del formulario
        $validate = $this->validate($request, [
            'type' => 'required|string',
			'description' => 'required|string|min:3',
        ]);

        //Recogemos los datos del formulario
        $description = $request->input('description');
        $type = $request->input('type');

        //Creamos el objeto Category a guardar
        $category = new Category();
        $category->type = $type;
        $category->description = $description;
        $category->save();

        //Redireccionamos
        return redirect()->route('admin.categories')->with(['message'=>'La categoría ha sido creada!!!']);
    }

    public function editCategory($categoryId){
        $category = Category::find($categoryId);
        return view('admin.createcategory',['category' => $category]);
    }

    public function editedCategory(Request $request){
        //Validación del formulario
        $validate = $this->validate($request, [
            'type' => 'required|string',
            'description' => 'required|string|min:3',
        ]);

        //Recogemos los datos del formulario
        $description = $request->input('description');
        $type = $request->input('type');

        //Creamos el objeto Category a guardar
        $category = Category::find($request->input('categoryId'));
        if($category == null){
            return redirect()->route('admin.categories')->with(['message'=>'La categoría no se ha modificado dado que no existe...']);
        }else{
            $category->type = $type;
            $category->description = $description;
            $category->update();
            //Redireccionamos
            return redirect()->route('admin.categories')->with(['message'=>'La categoría ha sido modificada!!!']);
        }
    }

    public function deleteCategory($categoryId){
        //Buscamos que no haya movimientos asociados a esta categoría,
        //si los hay no eliminamos y redireccionamos indicándolo.
        //Si no hay movimeintos eliminamos la categoría y redireccionamos dando el aviso de eliminación.
        $movements = Movement::where('category_id', $categoryId)->get();
        if(count($movements) == 0){
            $category = Category::find($categoryId);
            if($category == null){
                return redirect()->route('admin.categories')->with(['message'=>'La categoría no se ha eliminado dado que no existe...']);
            }else{
                $category->delete();
                return redirect()->route('admin.categories')->with(['message'=>'La categoría ha sido eliminada satisfactoriamente!!']);
            }
        }else{
            return redirect()->route('admin.categories')->with(['message'=>'La categoría no se ha podido eliminar dado que tiene movimientos asociados...']);
        }
    }

}
