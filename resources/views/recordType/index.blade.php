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
            <h1>Record types <a href="{{action('RecordTypeController@create')}}" class="btn btn-sm btn-primary">Create</a></h1>
            <table class="table table-hover table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>TAX</th>
                        <th>Is Business</th>
                        <th colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recordTypes as $record)
                    <tr>
                        <td>{{$record->name}}</td>
                        <td>{{$record->tax}}</td>
                        <td>{{$record->is_business}}</td>
                        <td>
                            <form action="{{action('RecordTypeController@destroy', $record->id)}}" method="post">
                                @csrf
                                <a href="{{action('RecordTypeController@edit', $record->id)}}" class="btn btn-sm btn-warning">Edit</a>
                                <input name="_method" type="hidden" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $recordTypes->links() }}
        </div>
    </div>
</div>
@endsection
