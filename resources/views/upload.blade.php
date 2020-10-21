@extends('template/main')

@section('header')
<title>Upload</title>
<link rel="stylesheet" href="{{ asset('css/image-uploader.min.css') }}">
<script src="{{ asset('js/image-uploader.js')}}"></script>
@endsection

@section('content')
@error('type')
    <h4 class="text-danger">{{$message}}</h4>
@enderror
@error('visibility')
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

<form id="main_form" action="{{ isset($item->id) ? url("/edit/submit/$item->id") : url('/upload/submit') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <h2 class="head_2">{{ isset($item->name) ? 'Edit ' . $item->name : 'Upload' }}</h2>

    <table>
        <tr>
            <td><label for="type" id="type_label">Type*:</label></td>
            <td>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary">
                        <input type="radio" name="type" value="mod" {{ isset($item->type) ? $item->type == 'mod' ? 'checked' : '' : ''}} autocomplete="off"> Mod
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" name="type" value="skyline_plugin" {{ isset($item->type) ? $item->type == 'skyline_plugin' ? 'checked' : '' : ''}}
                            autocomplete="off"> Skyline Plugin
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label for="type" id="visibility_label">Visibility*:</label>
            </td>
            <td>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary">
                        <input type="radio" name="visibility" value="public" {{ isset($item->visibility) ? $item->visibility == 'public' ? 'checked' : '' : ''}} autocomplete="off">
                        Public
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" name="visibility" value="unlisted" {{ isset($item->visibility) ? $item->visibility == 'unlisted' ? 'checked' : '' : ''}}
                            autocomplete="off"> Unlisted
                    </label>
                    <label class="btn btn-secondary">
                        <input type="radio" name="visibility" value="private" {{ isset($item->visibility) ? $item->visibility == 'private' ? 'checked' : '' : ''}}
                            autocomplete="off"> Private
                    </label>
                </div>
            </td>
        </tr>
    </table>

    <label for="name" id="name_label">Name*:</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="{{ isset($item->name) ? $item->name : '' }}" required>
    <br>
    <label for="description" id="desc_label">Description*:</label>
    <textarea class="form-control" id="description" name="description" placeholder="Enter Description">{{ isset($item->description) ? $item->description : '' }}</textarea>
    <br>
    <label for="file" id="file_label">File{{!isset($item) ? '*' : " (Leave empty if you don't want to update files)"}}:</label>
    <input type="file" class="form-control" id="file" name="file" {{!isset($item) ? 'required' : ''}}>
    <br>
    <label for="semver" id="semver_label">Version (Semver)*:</label>
    <input type="text" class="form-control" id="semver" name="semver" placeholder="1.0.0 (Major.Minor.Patch)" value="{{ isset($item->version) ? $item->version : '' }}" required>
    <br>
    <div id="input-images"></div>
    <br>
    <button class="btn btn-info" type="button" id="submit_form">Submit!</button>
    @if(isset($item))
        <button class="btn btn-danger" type="button" id="delete">Delete!</button>
    @endif
</form>

<script>
    const semver_pattern = /^\d+\.\d+\.\d+$/;

    @if(isset($item))
        var preloaded = [
            @if(isset(unserialize($item->images)[0]))
                @foreach (unserialize($item->images) as $image)
                    {id: "{{basename($image)}}", src: "{{env('SERVER_IP', "localhost") . $image}}" },
                @endforeach
            @endif
        ];
    @else
        var preloaded = [];
    @endif

    $('#input-images').imageUploader({
        preloaded: preloaded,
        imagesInputName: 'images',
        preloadedInputName: 'old_images',
        label: "Upload Images (Supported Formats: *.jpg, *.jpeg, *.png)",
        extensions: ['.jpg', '.jpeg', '.png'],
        mimes: ['image/jpeg', 'image/png']
    });

    @if(isset($item))
        document.getElementById("delete").addEventListener("click", function(){
            if(confirm("Are you sure you want to delete this mod?")){
                window.location.replace("{{url("/delete/{$item->id}")}}");
            }
        });
    @endif
    document.getElementById("submit_form").addEventListener("click", function(){
        var failed = false;
        var one_radio_type_checked = false;
        var one_radio_visibility_checked = false;

        document.querySelectorAll("input[name='type']").forEach(function(item){
            if(item.checked){
                one_radio_type_checked = true;
            }
        });

        document.querySelectorAll("input[name='visibility']").forEach(function(item){
            if(item.checked){
                one_radio_visibility_checked = true;
            }
        });

        if(!one_radio_type_checked){
            $("#type_label").css("color", "#ee5f5b");
            $('#type_label').focus();
            failed = true;
        }

        if(!one_radio_visibility_checked){
            $("#visibility_label").css("color", "#ee5f5b");
            $('#visibility_label').focus();
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
            if($(this).prop('required')){
                $("#file_label").css("color", "#ee5f5b");
                if(!failed){
                    $('#file').focus();
                    failed = true;
                }
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