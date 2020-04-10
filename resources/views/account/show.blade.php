@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <a href="{{action('AccountController@index')}}" class="btn btn-warning">Back</a>
            <div class="">
                <h2>{{ $account->nombre }}</h2>
                <p>
                    <strong>Correo:</strong> {{ $account->email }}<br>
                    <strong>Celular:</strong> {{ $account->phone }}<br>
                    <strong>Teléfono:</strong> {{ $account->movile }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
