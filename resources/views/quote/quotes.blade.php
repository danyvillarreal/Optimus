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
            <h1>Quotes <a href="{{action('QuoteController@index')}}" class="btn btn-sm btn-primary">Create</a></h1>
            <table class="table table-hover table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Client</th>
                        <th>Identification</th>
                        <th>Address</th>
                        <th>Stage</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotes as $record)
                    <tr>
                        <td>{{$record->quote_number}}</td>
                        <td>{{$record->accountName}}</td>
                        <td>{{$record->documenttype}} {{$record->document_number}}</td>
                        <td>{{$record->city}}-{{$record->address}}</td>
                        <td>{{$record->stageName}}</td>
                        <td>{{$record->quote_date}}</td>
                        <td>
                            <form action="{{action('QuoteController@destroy', $record->quoteId)}}" method="post" onsubmit="return confirm('Seguro que quieres eliminar esta información!')">
                                <a href="{{action('QuoteController@edit', $record->quoteId)}}" class="btn btn-sm btn-success">{{$record->stage_id===2?'Edit':'View'}}</a>
                                @csrf
                                <input name="_method" type="hidden" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $quotes->links() }}
        </div>
    </div>
</div>
@endsection
