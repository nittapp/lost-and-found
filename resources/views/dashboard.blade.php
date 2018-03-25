@extends('layouts.base')

@section('content')
    
    @foreach($items as $item)
            <div class="card card-dark" style="margin-top: 2%">
                <div class="view overlay">
                    @if($item->image_path != "null")
                    <img src="/images/{{$item->image_path}}" style="max-height: 500px" class="img-fluid">
                    @endif
                    <a>
                        <div class="mask rgba-white-slight"></div>
                    </a>
                </div>
                <div class="card-body elegant-color white-text">
                    <h4 class="card-title">{{$item->title}}</h4>
                    <h5 class="card-title">{{$item->user->name}}</h5>
                    <h5 class="card-title">{{$item->user->username}}</h5>
                    <small>{{$item->time}}</small>
                    <hr class="hr-light">
                    <p class="font-small mb-4 semi">{{str_limit($item->description, 100)}}</p>
                    <p class="font-small mb-4 full" style="display: none">{{$item->description}}</p>
                    <a class="white-text d-flex justify-content-end expand"><h5>More</h5><span><i class="fa fa-chevron-right pl-2"></i></span></a>
                    @if($isAdmin)
                        <a item={{$item->id}} class="white-text d-flex justify-content-end delete">
                            <h5 style="color: #12CBC4">Delete</h5>
                            <span><i class="fa fa-trash pl-2" style="color: #12CBC4"></i></span>
                        </a>
                        <a href="/item/{{$item->id}}" class="white-text d-flex justify-content-end">
                            <h5 style="color: #12CBC4">Edit</h5>
                            <span><i class="fa fa-pencil-square-o pl-2" style="color: #12CBC4" aria-hidden="true"></i></span>
                        </a>
                    @endif
                    <div class="comments" style="display: none">
                        <div class="list-group">
                          <a style="color:#2C3A47;" class="list-group-item list-group-item-action flex-column align-items-start list-group-item-info">
                            <div class="d-flex w-100 justify-content-between">
                              <h5 class="mb-1">Comments</h5>
                            </div>
                          </a>                          
                          <a style="color:#2C3A47;" class="list-group-item list-group-item-action flex-column align-items-start"">
                            <div class="d-flex w-100 justify-content-between">
                                <input type="text" id="title" name="title" class="form-control">
                                <small class="text-muted pl-2">Add comment</small>
                                  <span class="create-comment" item={{$item->id}}>
                                    <i class="fa fa-3x fa-plus-square-o pl-2"  style="color: #1B9CFC aria-hidden="true"></i>
                                  </span>
                            </div>
                          </a>
                          @foreach($item->comments as $comment)
                          <a style="color:#2C3A47;" class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                              <h5 class="mb-1">{{$comment->comment}}</h5><br/>
                              <small class="text-muted pl-2">{{$comment->username}}</small>
                              <small class="text-muted pl-2">{{$comment->time}}</small>
                              @if($comment->user_id == $user->id || $isAdmin)
                              <span class="delete-comment" comment={{$comment->id}}>
                                <i class="fa fa-trash pl-2" style="color: #1B9CFC"></i>
                              </span>
                              @endif
                            </div>
                          </a>
                          @endforeach
                        </div>
                    </div>
                </div>
            </div>
    @endforeach
    <br/>
    <div class="col-12" style="text-align: center;">
        <a href="/?page={{$page-1}}">
            <button class="btn btn-primary"><i class="fa fa-mail-reply mr-1"></i> Prev Page</button>
        </a>
        <a href="/?page={{$page+1}}">
            <button class="btn btn-primary"><i class="fa fa-mail-forward mr-1"></i> Next Page</button>
        </a>
    </div>
    <br/>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function(){
        console.log("hello!");
        $(".expand").click(function(){            
            if($(this).find('h5')[0].innerHTML == "More")
                $(this).find('h5')[0].innerHTML = "Hide";
            else
                $(this).find('h5')[0].innerHTML = "More";

            $($(this.parentElement).find('.semi')).toggle();
            $($(this.parentElement).find('.full')).toggle();
            $($(this.parentElement).find('.comments')).toggle();
        });

        $(document).on('click','.delete-comment',function(){
            var el = this;
            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'DELETE',
                url: '/comment/'+$(this).attr('comment'),
                success: function(data){
                    $(el.parentElement.parentElement).remove();
                    toastr.success(data['message']);
                },
                error: function(data){
                    toastr.error(data.responseJSON['message']);
                }
            });
        });

        $(".create-comment").click(function(){
            var comment  = $($(this.parentElement).find('input')[0]).val();
            var el = this;
            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'POST',
                url: '/comment/'+$(this).attr('item'),
                data: {"comment":comment},
                success: function(data){
                    toastr.success(data['message']);
                    var comment ='<a style="color:#2C3A47;" class="list-group-item list-group-item-action flex-column align-items-start">\
                            <div class="d-flex w-100 justify-content-between">\
                              <h5 class="mb-1">'+data.data["comment"]+'</h5><br/>\
                              <small class="text-muted pl-2">'+data.data["user"]["username"]+'</small>\
                              <small class="text-muted pl-2">Just now</small>\
                              <span class="delete-comment" comment='+data.data["id"]+'>\
                                <i class="fa fa-trash pl-2" style="color: #1B9CFC"></i>\
                              </span>\
                            </div>\
                          </a>';
                   $(el.parentElement.parentElement.parentElement).append(comment);
                },
                error: function(data){
                    toastr.error(data.responseJSON['message']);
                }
            });            
        });

        $(".delete").click(function(){
            var txt;
            var r = confirm("Are you sure you want to delete your post? This action cannot be reversed.");
            if (r == true) {
              console.log($(this).attr('item'));
              $.ajaxSetup({
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 }
              });
              $.ajax({
                type: 'DELETE',
                url: '/item/'+$(this).attr('item'),
                success: function(data){
                    console.log("deleted successfully");
                    window.location.reload(true);
                },
                error: function(err){
                    console.log(err);
                    toastr.error(err.responseJSON['message']);
                }
              });
            } 
        });

    });
</script>
@endsection