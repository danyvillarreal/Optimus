@extends('layouts.app')
                
@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            @if ($errors->any())
                <div class="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            @endif
            <h2>Quotes report</h2><br/>
            <form method="post" class="form-inline" target="blank" action="{{action('ReportController@quotesReportPdf')}}">
                @csrf
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Number">From Date:</label>
                        <input type="date" class="form-control" name="from_date" value="{{$fromDate}}" required="">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Number">To Date:</label>
                        <input type="date" class="form-control" name="to_date" value="{{$fromDate}}" required="">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="">&nbsp;</label>
                        <button type="submit" class="btn btn-sm btn-success">Generate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
