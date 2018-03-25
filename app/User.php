<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Exceptions\AppCustomHttpException;
use Exception;
use Illuminate\Http\Request;
use App\AuthorizationLevel;
use App\Item;
use App\Comment;

class User extends Authenticatable

{
    use Notifiable;

    protected $table = "users";

    public function authorizationLevel(){
        return $this->belongsTo('App\AuthorizationLevel');
    }

    public function items(){
        return $this->hasMany('App\Item');
    }

    public function comments(){
        return $this->hasMany('App\Comment');
    }

    static public function getUserID($request){
        return 1;
       // $user = User::where('username',$request->header('X-NITT-APP-USERNAME'))->first();
       // return $user->id;
    }

    static public function isUserAdmin($request){
        return true;
        // return $request->header('X-NITT-APP-IS-ADMIN') == 'true';
    }

    static public function create($rollno){
        $user = new User();
        $user->username = $rollno;
        $user->name = "N/A";
        $user->save();
    }
}

