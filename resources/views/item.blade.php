@extends('template/main')

@section('header')
<title>{{$item->name}}</title>
<link rel="stylesheet" href="{{ asset("css/lightgallery.css") }}">
@endsection

@section('content')
<h2>{{$item->name}}</h2>

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-6">
            <div id="lightgallery">
                @foreach (unserialize($item->images) as $image)
                    <a href="{{ asset($image) }}">
                        @if ($loop->first)
                            <img style="width:100%" src="{{ asset($image) }}">
                            <br>
                            <br>
                        @else
                            <img style="width:25%" src="{{ asset($image) }}">
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
        <div class="col-xl-6">
            <h4>Author: {{App\Models\User::where('id', $item->owner_id)->first()->name}}</h4>
            <h4>Version: {{$item->version}}</h4>
            <p>{{$item->description}}</p>
            <br>
            <a style="float: right;" href="{{ url("/get_toml/" . $item->id)}}" class="btn btn-primary">Get TOML</a>
            @if ($item->owner_id == Illuminate\Support\Facades\Auth::id())
                <a style="float: right;margin-right:5px;" href="{{ url("/edit/" . $item->id)}}" class="btn btn-warning">Edit</a>
            @endif
        </div>
    </div>
</div>


<script src="{{ asset("js/lightgallery.min.js")}}"></script>
<script src="{{ asset("js/lg-thumbnail.min.js") }}"></script>
<script src="{{ asset("js/lg-fullscreen.min.js")}}"></script>
<script>
    lightGallery(document.getElementById('lightgallery'));
</script>
@endsection