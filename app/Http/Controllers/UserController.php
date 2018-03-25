<?php

namespace App\Http\Controllers;
use App\Item;
use App\User;
use App\Comment;
use Illuminate\Http\Request;
use App\Exceptions\AppCustomHttpException;

class UserController extends Controller
{
    public function renderDashboard(Request $request){

        $items = Item::getItems($request);
        $user = User::find(User::getUserID($request));
        $isAdmin = User::isUserAdmin($request);
        $page = 1;
        if(isset($request['page']))
            $page = $request['page'];
        return view('dashboard',['items' => collect($items), 'user' => $user, 'page' => $page, 'isAdmin' => $isAdmin]);
    }

    public function renderUserItems(Request $request){
        $items =  Item::getItems($request, $with_userID = true);
        // return $items;
        return view('userItems',['items' => $items]);
    }

    public function deleteItem(Request $request, $id){
        try {
            $res = Item::deleteItem($request, $id);
            return response()->json(["message" => "deleted successfully"], 200);        
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), $e->getCode()]);
        }
    }

    public function renderCreateView(Request $request){
        return view('createItem');
    }

    public function createItem(Request $request){
        try {
            $res = Item::validateRequest($request);
            $res = Item::createItem($request);
            return response()->json(["message" => "created successfully"], 200);        
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function renderEditView(Request $request, $id){
        $item = Item::find($id);
        $item->image_path =  (string)$item->user_id.'/'.(string)$item->id.'.jpeg';
        return view('editItem',["item" => $item]);
    }

    public function editItem(Request $request, $id){
        try {
            $res = Item::validateRequest($request);
            $res = Item::editItem($request, $id);
            return response()->json(["message" => "updated successfully"], 200);        
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }        
    }

    public function deleteComment(Request $request, $id){
        try {
            $res = Comment::deleteComment($request, $id);
            return response()->json(["message" => "deleted successfully"], 200);        
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), $e->getCode()]);
        }
    }

    public function createComment(Request $request, $id){
        try {
            $res = Comment::validateRequest($request);
            $res = Comment::createComment($request, $id);
            $res->user = User::find(User::getUserID($request));
            return response()->json(["message" => "created successfully", "data" => $res], 200);        
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }       
    }

}
