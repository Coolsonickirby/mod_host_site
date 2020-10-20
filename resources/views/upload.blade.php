@extends('template/main')

@section('header')
<title>Upload</title>
<link rel="stylesheet" href="{{ asset('css/image-uploader.min.css') }}">
<script src="{{ asset('js/image-uploader.min.js')}}"></script>
@endsection

@section('content')
@error('type')
    <h4 class="text-danger">{{$message}}</h4>
@enderror
@error('name')
    <h4 class="text-danger">{{$message}}</h4>
@enderror
@error('desc')
    <h4 class="text-danger">{{$message}}</h4>
@enderror
@error('file')
    <h4 class="text-danger">{{$message}}</h4>
@enderror
@error('semver')
    <h4 class="text-danger">{{$message}}</h4>
    <br>
@enderror
<form id="main_form" action="{{url('/upload/submit')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <h2 class="head_2">Upload</h2>

    <label for="type" id="type_label">Type*:</label>
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-secondary">
            <input type="radio" name="type" value="mod" autocomplete="off"> Mod
        </label>
        <label class="btn btn-secondary">
            <input type="radio" name="type" value="skyline_plugin" autocomplete="off"> Skyline Plugin
        </label>
    </div>
    <br>

    <label for="name" id="name_label">Name*:</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name">
    <br>
    <label for="description" id="desc_label">Description*:</label>
    <textarea class="form-control" id="description" name="description" placeholder="Enter Description"></textarea>
    <br>
    <label for="file" id="file_label">File*:</label>
    <input type="file" class="form-control" id="file" name="file">
    <br>
    <label for="semver" id="semver_label">Version (Semver)*:</label>
    <input type="text" class="form-control" id="semver" name="semver" placeholder="1.0.0 (Major.Minor.Patch)">
    <br>
    <div class="input-images"></div>
    <br>
    <button class="btn btn-primary" type="button" id="submit_form">Submit!</button>
</form>

<script>
    const semver_pattern = /^\d+\.\d+\.\d+$/;

    $('.input-images').imageUploader({
        label: "Upload Images (Supported Formats: *.jpg, *.jpeg, *.png)",
        extensions: ['.jpg', '.jpeg', '.png'],
        mimes: ['image/jpeg', 'image/png']
    });

    document.getElementById("submit_form").addEventListener("click", function(){
        var failed = false;
        var one_radio_checked = false;

        document.querySelectorAll("input[type='radio']").forEach(function(item){
            if(item.checked){
                one_radio_checked = true;
            }
        });

        if(!one_radio_checked){
            $("#type_label").css("color", "#ee5f5b");
            $('#type_label').focus();
            failed = true;
        }

        if($.trim($('#name').val()) == ''){
            $("#name_label").css("color", "#ee5f5b");
            $('#name').focus();
            failed = true;
        }

        if($.trim($('#description').val()) == ''){
            $("#desc_label").css("color", "#ee5f5b");
            if(!failed){
                $('#desc').focus();
                failed = true;
            }
        }

        if($("#file").val() == ''){
            $("#file_label").css("color", "#ee5f5b");
            if(!failed){
                $('#file').focus();
                failed = true;
            }
        }

        if(!semver_pattern.test($('#semver').val())){
            $("#semver_label").css("color", "#ee5f5b");
            if(!failed){
                $('#semver').focus();
                failed = true;
            }
        }

        if(!failed){
            document.getElementById("main_form").submit();
        };

    });
</script>
@endsection