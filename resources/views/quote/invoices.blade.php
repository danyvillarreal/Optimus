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
            <h1>Invoices</h1>
            <table class="table table-hover table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Client</th>
                        <th>Document</th>
                        <th>Stage</th>
                        <th>Quote date</th>
                        <th>Invoice date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facturas as $record)
                    <tr>
                        <td>{{$record->quote_number}}</td>
                        <td>{{$record->accountName}}</td>
                        <td>{{$record->documenttype}} {{$record->document_number}}</td>
                        <td>{{$record->stageName}}</td>
                        <td>{{$record->quote_date}}</td>
                        <td>{{$record->invoice_date}}</td>
                        <td>
                            <a href="{{action('QuoteController@editInvoice', $record->quoteId)}}" class="btn btn-sm btn-success">{{$record->stage_id===3?'Edit':'View'}}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $facturas->links() }}
        </div>
    </div>
</div>
@endsection
