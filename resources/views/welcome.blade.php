@extends('template/main')

@section('header')
<title>Items</title>
@endsection

@section('content')
@if (count($items) > 0)
    <div class="card-columns">
        @foreach ($items as $item)
            <div class="card">
                <a href="./item/{{$item->id}}">
                    <img class="card-img-top"
                        src="{{ isset(unserialize($item->images)[0]) ? asset(unserialize($item->images)[0]) : asset("/img/tenor.gif")  }}"
                        alt="{{$item->name}}">
                    <div class="card-body text-center">
                        <p class="card-text">{{$item->name}}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@else
    <h1>No Items as of now!</h1>
@endif
@endsection