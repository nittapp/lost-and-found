<?php
namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Comment;
use App\Exceptions\AppCustomHttpException;
use Exception;
use Storage;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class Item extends Model
{
    protected $table = 'items';

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function comments(){
        return $this->hasMany('App\Comment');
    }

    static public function validateRequest(Request $request){
          
        if($request->method() == 'POST'){
            
            $validator = Validator::make($request->all(), [
                  'title' => 'required|string|max:255',
                  'description' => 'required|string|max:1023',
            ]);
        }
        else
            $validator = Validator::make($request->all(), [
                      'title' => 'string|nullable|max:255',
                      'description' => 'nullable|string|max:1023',
            ]);
       if ($validator->fails())
             throw new AppCustomHttpException($validator->errors()->first(), 422);        
       
    }

    static public function getItems(Request $request, $with_userID = false){
         
        $userID = User::getUserID($request);
        if(! $userID)
            throw new AppCustomHttpException("user not logged in", 401);

        if($with_userID)
            $items = Item::select('id','user_id','title','description','created_at')
                         ->where('user_id','=',$userID)
                         ->orderBy('created_at','desc')
                         ->paginate(20);
        else
            $items = Item::select('id','user_id','title','description','created_at')
                          ->orderBy('created_at','desc')
                          ->paginate(20);

        foreach ($items as $item) {
            $item->image_path =  (string)$item->user_id.'/'.(string)$item->id.'.jpeg';
            if(! Storage::disk('local')->exists($item->image_path))
                $item->image_path = 'null';
            $item->user = $item->user()->select('id','username','name')->first();
            $item->comments = $item->comments()->orderBy('created_at','asc')->get();
            $item->time = $item->created_at->diffForHumans();
            foreach ($item->comments as $comment) {
                $comment->username = (User::find($comment->user_id))->username;
                $comment->time = $comment->created_at->diffForHumans();
            }
        }

        return $items->values()->all();
    }
   
    static public function deleteItem(Request $request, $id){
               
         $userID = User::getUserID($request);
     
         if(! $userID )
              throw new AppCustomHttpException("user not logged in", 401);
         
        $item = Item::find($id);
        if(empty($item))
            throw new AppCustomHttpException("item not found", 404);

         $itemUserID = $item->user_id;
         if(! User::isUserAdmin($request) || $itemUserID != $userID )
              throw new AppCustomHttpException("Access denied", 403);
                  
         $item->delete();
         $file_url =  (string)$userID.'/'.(string)$id.'.jpeg';
         Storage::disk('local')->delete($file_url);        
    } 
    
    static public function createItem(Request $request){
  
        $userID = User::getUserID($request);

        if( $request->hasFile('image') ){
            if(! in_array($request->image->extension(), array('jpg','jpeg','png')))
                throw new AppCustomHttpException("Only jpeg images allowed",422);
            if($request->image->getSize() > 500000)
                throw new AppCustomHttpException("File too large",422);
        }

        $item = new Item;
        $item->title = $request['title']; 
        $item->description = $request['description']; 
        $user = User::find($userID);
        $response = $user->items()->save($item);
        $item_id = $response->id; 
        if( $request->hasFile('image') )
            $path = $request->image->storeAs((string)$userID, (string)$item_id.'.jpeg', 'local');    
    }

    static public function editItem(Request $request, $id){

        $userID = User::getUserID($request);
        $item = Item::find($id);

        if(! $userID)
             throw new AppCustomHttpException("user not logged in", 401);
        if(empty($item))
            throw new AppCustomHttpException("Item not found",404);
        
        if($item->user_id != User::getUserID($request))
            throw new AppCustomHttpException("Action not allowed",403);

        if( $request->hasFile('image') ){
            if(! in_array($request->image->extension(), array('jpg','jpeg','png')))
                throw new AppCustomHttpException("Only jpeg images allowed",422);
            if($request->image->getSize() > 500000)
                throw new AppCustomHttpException("File too large",422);
        }
        
        $item->title = $request['title']; 
        $item->description = $request['description']; 
        $item->save();
        
        if( $request->hasFile('image') )
            $path = $request->image->storeAs((string)$userID, (string)$id.'.jpeg', 'local');   

    }

}