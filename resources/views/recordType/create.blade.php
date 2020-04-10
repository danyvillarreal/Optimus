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
            <h2>Create record type</h2><br/>
            <form method="post" class="form-inline" action="{{url('documentTypes')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Name">Name:</label>
                        <input type="text" class="form-control" name="name">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="tax">tax:</label>
                        <input type="text" class="form-control" name="tax">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Is Business">Is Business:</label>
                        <input type="checkbox" class="form-control" name="is_business">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="number">&nbsp;</label>
                        <!-- <a href="{{action('ProductController@index')}}" class="btn btn-sm btn-warning">Cancel</a> -->
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
