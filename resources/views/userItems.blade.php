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
                <hr class="hr-light">
                <p class="font-small mb-4 semi">{{str_limit($item->description, 150)}}</p>
                <p class="font-small mb-4 full" style="display: none">{{$item->description}}</p>
                <a class="white-text d-flex justify-content-end expand">
                    <h5>Read more</h5>
                    <span><i class="fa fa-chevron-right pl-2"></i></span>
                </a>
                <a item={{$item->id}} class="white-text d-flex justify-content-end delete">
                    <h5 style="color: #12CBC4">Delete</h5>
                    <span><i class="fa fa-trash pl-2" style="color: #12CBC4"></i></span>
                </a>
                <a href="/item/{{$item->id}}" class="white-text d-flex justify-content-end">
                    <h5 style="color: #12CBC4">Edit</h5>
                    <span><i class="fa fa-pencil-square-o pl-2" style="color: #12CBC4" aria-hidden="true"></i></span>
                </a>
            </div>
        </div>
    @endforeach
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function(){
        console.log("hello!");
        $(".expand").click(function(){            
            if($(this).find('h5')[0].innerHTML == "Read more")
                $(this).find('h5')[0].innerHTML = "Read less";
            else
                $(this).find('h5')[0].innerHTML = "Read more";

            $($(this.parentElement).find('.semi')).toggle();
            $($(this.parentElement).find('.full')).toggle();
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
                    console.log(err["message"]);
                }
              });
            } 
        });

    });
</script>
@endsection