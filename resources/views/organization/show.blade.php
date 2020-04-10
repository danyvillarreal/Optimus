@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <a href="{{action('OrganizationController@index')}}" class="btn btn-warning">Back</a>
            <div class="">
                <h2>{{ $organization->name }}</h2>
                <p>
                    <strong>name:</strong> {{ $organization->name }}
                    <strong>country:</strong> {{ $organization->country }}
                    <strong>address:</strong> {{ $organization->address }}
                    <strong>phone:</strong> {{ $organization->phone }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
