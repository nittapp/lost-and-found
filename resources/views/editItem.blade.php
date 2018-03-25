@extends('layouts.base')

@section('content')

    <form method="post" enctype="multipart/form-data">
        <p class="h4 text-center mb-4">Add a Lost/Found Post</p>
        <label for="title" class="grey-text">Title</label>
        <input type="text" id="title" name="title" class="form-control" value="{{$item->title}}">

        <p style="font-size: 90%"><b>Tip:</b> start with keyword "Lost" or "Found" for readability.</p>
        
        <label for="description" class="grey-text">Description</label>
        <textarea type="text" id="description" name="description" class="form-control" rows="3">{{$item->description}}</textarea>
        <p style="font-size: 90%"><b>Tip:</b> Add where and when you found/lost the item. Also, describe the item if that helps!</p>
        <br/>
        <img src="/images/{{$item->image_path}}" class="img-fluid" style="height: 200px">
        <label for="image" class="grey-text">Edit this current Image</label>
        <input type="file" id="image" name="image" class="form-control">
        <p style="font-size: 90%"><b>Tip:</b> images can help other students to identify the item better.</p>
        <div class="text-center mt-4">
            <button class="btn btn-outline-warning submit-form">Update<i class="fa fa-paper-plane-o ml-2"></i></button>
        </div>
    </form>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $('form').submit(function(e){
                e.preventDefault(); 
                var formData = new FormData(this);
                $.ajaxSetup({
                     headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/item/{{$item->id}}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false, 
                    success: function(data){
                       toastr.success(data['message']);
                    },
                    error: function(data){
                        toastr.error(data.responseJSON['message']);
                    },
                });
            });
        });
    </script>
@endsection