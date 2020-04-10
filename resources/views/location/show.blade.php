@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <a href="{{action('LocationController@index')}}" class="btn btn-warning">Atrás</a>
            <h2>{{$location->accountName}}</h2>
            <div class="">
                <h2>{{$location->accountName}}</h2>
                <p>
                    <strong>address:</strong> {{$location->address}}
                    <strong>accountName</strong> {{$location->accountName}}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
