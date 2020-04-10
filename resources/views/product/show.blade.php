@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <a href="{{action('ProductController@index')}}" class="btn btn-warning">Atrás</a>
            <h2>{{$product->name}}</h2>
            <div class="">
                <h2>{{$product->name}}</h2>
                <p>
                    <strong>Name:</strong> {{$product->name}}
                    <strong>Code</strong> {{$product->code}}
                    <strong>Price:</strong> {{$product->price}}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
