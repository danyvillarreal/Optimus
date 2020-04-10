@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <h2>Edit Organization</h2>
            @if ($errors->any())
                <div class="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            @endif
            <form method="post" class="form-inline" action="{{action('OrganizationController@update', $id)}}" enctype="multipart/form-data">
                @csrf
                <!-- <input name="_method" type="hidden" value="PATCH"> -->
                @method('PUT')
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="name">Name:</label>
                        <input type="text" class="form-control" name="name" value="{{$organization->name}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Country">Country:</label>
                        <select class="form-control" name="country">
                            @foreach($countries as $key => $country)
                                <option value="{{$country}}" {{ ($country === $organization->country ) ? "selected=true" : "" }}>{{$country}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="number">Address:</label>
                        <input type="text" class="form-control" name="address" value="{{$organization->address}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Phone">Phone:</label>
                        <input type="text" class="form-control" name="phone" value="{{$organization->phone}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Quote Number">Quote Number:</label>
                        <input type="number" class="form-control" name="quote_number" value="{{$organization->quote_number}}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="">
                    <label for="Phone">Aditional Quote Detail:</label>
                        <textarea id="aditional_detail" class="form-control devis-stared" rows="2" cols="46" name="aditional_detail">{{$organization->aditional_detail}}</textarea>
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Logo">Logo:</label>

                        <input type="file" class="form-control" name="logo" />

                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <!-- <a href="{{action('OrganizationController@index')}}" class="btn btn-sm btn-warning">Cancel</a> -->
                        <button type="submit" class="btn btn-sm btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
