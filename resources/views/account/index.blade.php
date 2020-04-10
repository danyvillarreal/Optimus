@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div id="snackbar">
                @if (\Session::has('success'))
                    <script>
                        var x = document.getElementById("snackbar");
                        x.className = "show";
                        setTimeout(function(){
                            x.className = x.className.replace("show", "");
                        }, 2000);
                    </script>
                    {{ \Session::get('success') }}
                @endif
            </div>
            <h1>Accounts</h1>
            <table class="table table-hover table-condensed table-bordered table-striped">
                <a href="{{action('AccountController@create')}}" class="btn btn-sm btn-primary">Create</a>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Document type</th>
                        <th>Document number</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Teléfono</th>
                        <th colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($accounts as $account)
                    <tr>
                        <td>{{$account->name}}</td>
                        <td>{{$account->documentTypeName}}</td>
                        <td>{{$account->document_number}}</td>
                        <td>{{$account->email}}</td>
                        <td>{{$account->movile}}</td>
                        <td>{{$account->phone}}</td>
                        <td>
                            <form action="{{action('AccountController@destroy', $account->id)}}" method="post">
                                <!-- <a class="btn btn-sm btn-success" href="{{ URL::to('accounts/' . $account->id) }}">Show</a> -->
                                <a href="{{action('AccountController@edit', $account->id)}}" class="btn btn-sm btn-warning">Edit</a>
                                @csrf
                                <input name="_method" type="hidden" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $accounts->links() }}
        </div>
    </div>
</div>
@endsection
