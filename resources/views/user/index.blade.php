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
            <h1>Users</h1>
            <table class="table table-hover table-condensed table-bordered table-striped">
                <a href="{{route('register')}}" class="btn btn-sm btn-primary">Create</a>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        @if ($user->status == 1)
                            <td>Active</td>
                        @elseif ($user->status == 0)
                            <td>Inactive</td>
                        @endif
                        <td>
                            <form action="{{action('UserController@destroy', $user->id)}}" method="post">
                                <!-- <a class="btn btn-sm btn-success" href="{{ URL::to('users/' . $user->id) }}">Show</a> -->
                                <a href="{{action('UserController@edit', $user->id)}}" class="btn btn-sm btn-warning">Edit</a>
                                @csrf
                                <input name="_method" type="hidden" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
