@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <h2>Edit place</h2>
            <form method="post" class="form-inline" action="{{action('PlaceController@update', $id)}}">
                @csrf
                <!-- <input name="_method" type="hidden" value="PATCH"> -->
                @method('PUT')
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Name">Name:</label>
                        <input type="text" class="form-control" name="name" value="{{$categoria->name}}" style="width: 300px;">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="Files">&nbsp;</label>
                        <!-- <a href="{{action('PlaceController@index')}}" class="btn btn-sm btn-warning">Cancel</a> -->
                        <button type="submit" class="btn btn-sm btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
