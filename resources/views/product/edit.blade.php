@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <h2>Edit product</h2>
            <form method="post" class="form-inline" action="{{action('ProductController@update', $id)}}">
                @csrf
                <!-- <input name="_method" type="hidden" value="PATCH"> -->
                @method('PUT')
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Name">Name:</label>
                        <input type="text" class="form-control" name="name" value="{{$product->name}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Code">Code:</label>
                        <input type="text" class="form-control" name="code" value="{{$product->code}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="number">Price EUR:</label>
                        <input type="text" class="form-control" name="price" value="{{$product->price}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Number">Price USD:</label>
                        <input type="text" class="form-control" step="0.01" min="0.01" max="99999999" name="priceusd">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="number">&nbsp;</label>
                        <!-- <a href="{{action('ProductController@index')}}" class="btn btn-sm btn-warning">Cancel</a> -->
                        <button type="submit" class="btn btn-sm btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
