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
            <h1>Places <a href="{{action('PlaceController@create')}}" class="btn btn-sm btn-primary">Create</a></h1>
            <table class="table table-hover table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoria as $categoriaSecundaria)
                    <tr>
                        <td>{{$categoriaSecundaria->name}}</td>
                        <td>
                            <form action="{{action('PlaceController@destroy', $categoriaSecundaria->id)}}" method="post">
                                <!-- <a class="btn btn-sm btn-success" href="{{ URL::to('place/' . $categoriaSecundaria->id) }}">Show</a> -->
                                <a href="{{action('PlaceController@edit', $categoriaSecundaria->id)}}" class="btn btn-sm btn-warning">Edit</a>
                                @csrf
                                <input name="_method" type="hidden" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $categoria->links() }}
        </div>
    </div>
</div>
@endsection
