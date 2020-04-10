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
            <h3>Create business account</h3>
            <form method="post" class="form-inline" action="{{action('AccountController@postCreateBA')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Type">Type:</label>
                        <select class="form-control" name="document_type" disabled="">
                            @foreach($documentTypes as $type)
                                <option value="{{$type['id']}}" {{($type['id'] == $documentType) ? "selected" : "" }}>{{$type['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Document Number">Document Number:</label>
                        <input type="text" class="form-control" name="document_number" value="{{$documentNumber}}" disabled="">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Name">Name:</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Email">Email:</label>
                        <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Movile">Movile:</label>
                        <input type="text" class="form-control" name="movile" value="{{ old('movile') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Phone">Phone:</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="postal_code">Postal code:</label>
                        <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="billing_address">Billing Address:</label>
                        <input type="text" class="form-control" name="billing_address" value="{{ old('billing_address') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="shipping_address">Shipping Address:</label>
                        <input type="text" class="form-control" name="shipping_address" value="{{ old('shipping_address') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Country">Country location:</label>
                        <select class="form-control" name="country">
                            @foreach($countries as $country)
                                <option value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="City">City location:</label>
                        <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Address">Address location:</label>
                        <input type="text" class="form-control" name="location_address" value="{{ old('location_address') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Phone">Phone location:</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                    </div>
                </div>

                <input type="hidden" name="document_number" value="{{$documentNumber}}">
                <input type="hidden" name="document_type_id" value="{{$documentType}}">
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="Files">&nbsp;</label>
                        <a href="{{action('AccountController@create')}}" class="btn btn-sm btn-warning">Cancel</a>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                    </div>
                </div>
            </form>
            <div id="listLocations"></div>
        </div>
    </div>
</div>
@endsection
