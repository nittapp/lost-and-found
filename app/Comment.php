<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\AppCustomHttpException;

use App\Comment;
use Validator;

use Illuminate\Http\Request;

class Comment extends Model
{
    protected $table = "comments";

    public function item(){
        return $this->belongsTo('App\Item');
    }

    public function user(){
        return  $this->belongsTo('App\User');
    }
     
    static public function validateRequest(Request $request){
        if($request->method() == 'POST')
            $validator = Validator::make($request->all(), [
                        'comment' => 'required|string',
                        ]);
        else
            $validator = Validator::make($request->all(), [
                        'comment' => 'required|string',
                        ]);
        if ($validator->fails())
            throw new AppCustomHttpException($validator->errors()->first(), 422);
    }

    static public function getComments(Request $request, $itemID){

        $userID = User::getUserID($request);
        if(! $userID)
             throw new AppCustomHttpException("user not logged in", 401);
        
        $item = Item::find($itemID);
        if(empty($item))
            throw new AppCustomHttpException("Item not found", 404);

        if(empty(Comment::where('item_id',$itemID)->first()))
            throw new AppCustomHttpException("comments not found", 404);

        if($item->user()->value('id') != User::getUserID($request) && !$item->is_public)
            throw new AppCustomHttpException("action not allowed", 403);

        $comments = Comment::where('item_id',$itemID)->orderBy('created_at','desc')->get();

        return $comments->values()->all();

    }

    static public function createComment(Request $request, $itemID){
        $userID = User::getUserID($request);
        if(! $userID)
             throw new AppCustomHttpException("user not logged in", 401);

        $item = Item::find($itemID);
        if(empty($item))
            throw new AppCustomHttpException("Item not found", 404);

        if($item->user()->value('id') != User::getUserID($request) &&
           ! User::isUserAdmin($request) && !$item->is_public)
            throw new AppCustomHttpException("action not allowed", 403);

        $comment = new Comment;
        $comment->user_id = User::getUserID($request);
        $comment->comment = $request['comment'];

        $item = Item::find($itemID);
        $response = $item->comments()->save($comment);
        
        return $comment;
    }

    static public function editComment(Request $request, $commentID, $commentText){

        $userID = User::getUserID($request);
        if(! $userID)
             throw new AppCustomHttpException("user not logged in", 401);

        $comment = Comment::find($commentID);
        if(empty($comment))
            throw new AppCustomHttpException("Comment not found", 404);

        if($comment->user_id != User::getUserID($request) && ! User::isUserAdmin($request))
            throw new AppCustomHttpException("action not allowed", 403);
         
        $comment->comment = $commentText;
        $comment->save();
    }

    /**
    * This is a DELETE route for deleting complaint comments
    * In order to delete comments we will be taking in one parameter
    *@param [int] $itemID
    **/

    static public function deleteComment(Request $request, $commentID){
        $userID = User::getUserID($request);
     
        if(! $userID )
             throw new AppCustomHttpException("user not logged in ", 401);
         
        $comment = Comment::find($commentID);
        if(empty($comment))
            throw new AppCustomHttpException("Comment not found", 404);

        if($comment->user_id != $userID && ! User::isUserAdmin($request))
            throw new AppCustomHttpException("Action not allowed", 403);

        $comment->delete();
    }

}