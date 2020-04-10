@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <h2>Create category</h2><br/>
            <form method="post" class="form-inline" action="{{url('category')}}">
                @csrf
                <!-- <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Name">Name:</label> -->
                        <input type="text" class="form-control" name="name" placeholder="Name" style="width: 300px;">
                    <!-- </div>
                </div> -->
                <div class="form-group form-group-sm">
                    <div class="">
                        <!-- <a href="{{action('CategoryController@index')}}" class="btn btn-sm btn-warning">Cancel</a> -->
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
