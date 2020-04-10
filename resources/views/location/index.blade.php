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
            <h1>Locations <a href="{{action('LocationController@create')}}" class="btn btn-sm btn-primary">Create</a></h1>
            <table class="table table-hover table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th colspan="4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($locations as $location)
                    <tr>
                        <td>{{$location->accountName}}</td>
                        <td>{{$location->countryName}}</td>
                        <td>{{$location->city}}</td>
                        <td>{{$location->address}}</td>
                        <td>{{$location->phone}}</td>
                        <td>
                            <a class="btn btn-sm btn-success" href="{{ URL::to('locations/' . $location->id) }}">Show</a>
                        </td>
                        <td><a href="{{action('LocationController@edit', $location->id)}}" class="btn btn-sm btn-warning">Edit</a></td>
                        <td>
                            <form action="{{action('LocationController@destroy', $location->id)}}" method="post">
                                @csrf
                                <input name="_method" type="hidden" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $locations->links() }}
        </div>
    </div>
</div>
@endsection
