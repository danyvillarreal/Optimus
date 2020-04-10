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
            <h3>Create person account</h3>
            <form method="post" class="form-inline" action="{{action('AccountController@postCreateBA')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Tipo de documento">Tipo:</label>
                        <select class="form-control" name="documenttype" disabled="">
                            @foreach($documentTypes as $type)
                                <option value="{{$type['id']}}" {{($type['id'] == $documentType) ? "selected" : "" }}>{{$type['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Número de documento">Número de documento:</label>
                        <input type="text" class="form-control" name="document_number" value="{{$documentNumber}}" disabled="">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Nombre">Nombre:</label>
                        <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Apellidos">Apellidos:</label>
                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}">
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
                    <label for="Celular">Celular:</label>
                        <input type="text" class="form-control" name="movile" value="{{ old('movile') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Teléfono">Teléfono:</label>
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
                    <label for="address">Address:</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address') }}">
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
                <input name="document_number" type="hidden" value="{{$documentNumber}}">
                <input name="document_type_id" type="hidden" value="{{$documentType}}">
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="Files">&nbsp;</label>
                        <a href="{{action('AccountController@create')}}" class="btn btn-sm btn-warning">Cancel</a>
                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
