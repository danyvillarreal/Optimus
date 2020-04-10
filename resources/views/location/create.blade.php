@extends('layouts.app')
                
@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <h2>Create location</h2><br/>
            <form method="post" class="form-inline" action="{{url('locations')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="address">address:</label>
                        <input type="text" class="form-control" name="address">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="account_id">account_id:</label>
                        <input type="text" class="form-control" name="account_id">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <a href="{{action('LocationController@index')}}" class="btn btn-sm btn-warning">Cancel</a>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
