@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <a href="{{action('CategoryController@index')}}" class="btn btn-warning">Back</a>
            <h2>{{$categoria->name}}</h2>
            <div class="">
                <p>
                    <strong>Name:</strong> {{$categoria->name}}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
