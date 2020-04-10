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
            <h1>Organization</h1>
            <table class="table table-hover table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Country</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Aditional Quote Detail</th>
                        <th>Number users</th>
                        <th>Allowed users</th>
                        <th colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($organizations as $organization)
                    <tr>
                        <td>{{$organization->name}}</td>
                        <td>{{$organization->country}}</td>
                        <td>{{$organization->address}}</td>
                        <td>{{$organization->phone}}</td>
                        <td>{{$organization->aditional_detail}}</td>
                        <td>{{$users}}</td>
                        <td>{{$number_users}}</td>
                        <td>
                            <!-- <a class="btn btn-sm btn-success" href="{{ URL::to('organizations/' . $organization->id) }}">Show</a> -->
                            <a href="{{action('OrganizationController@edit', $organization->id)}}" class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $organizations->links() }}
        </div>
    </div>
</div>
@endsection
