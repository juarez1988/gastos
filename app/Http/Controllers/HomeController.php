<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /*
        $users = User::all();
        foreach($users as $user){
            echo $user->name."</br>";
            echo $user->surname."</br>";
            echo $user->role."</br>";
            foreach($user->accounts as $account){
                echo $account->description."</br>";
            }
            echo "<hr/>";
        }
        die();
        */
        return view('home');
    }
}
