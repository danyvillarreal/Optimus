@extends('layouts.app')

@push('account')
    <script src="{{ asset('js/account.js') }}"></script>
@endpush

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
            <section class="article-box">
            <h3>Edit account</h3>
            <form method="post" class="form-inline" action="{{action('AccountController@update', $id)}}">
                @csrf
                @method('PUT')
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="Tipo de documento">Tipo:</label>
                        <select class="form-control" name="document_type_id" disabled="">
                            @foreach($documentTypes as $type)
                                <option value="{{$type['id']}}" {{($type['id'] === $account->document_type_id) ? "selected" : "" }}>{{$type['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Número de documento">Número de documento:</label>
                        <input type="text" class="form-control" name="document_number" value="{{$account->document_number}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Nombre">Nombre:</label>
                        <input type="text" class="form-control" name="name" value="{{$account->name}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Email">Email:</label>
                        <input type="text" class="form-control" name="email" value="{{$account->email}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Celular">Celular:</label>
                        <input type="text" class="form-control" name="movile" value="{{$account->movile}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Teléfono">Teléfono:</label>
                        <input type="text" class="form-control" name="phone" value="{{$account->phone}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="postal_code">Postal code:</label>
                        <input type="text" class="form-control" name="postal_code" value="{{$account->postal_code}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="billing_address">Billing Address:</label>
                        <input type="text" class="form-control" name="billing_address" value="{{$account->billing_address}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="shipping_address">shipping_address:</label>
                        <input type="text" class="form-control" name="shipping_address" value="{{$account->shipping_address}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="Files">&nbsp;</label>
                        <!-- <a href="{{action('AccountController@index')}}" class="btn btn-sm btn-warning">Cancel</a> -->
                        <button type="submit" class="btn btn-sm btn-success">Update</button>
                    </div>
                </div>
            </form>
            </section>
            <br>
            <section class="article-box">
                <h4>New location</h4>
                <form method="post" class="form-inline" id="newLocation" action="{{url('locations')}}">
                    @csrf
                    <div class="form-group form-group-sm">
                        <div class="">
                        <label for="Country">Country location:</label>
                            <select class="form-control" name="country_id">
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
                            <input type="text" class="form-control" name="address" value="{{ old('address') }}">
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <div class="">
                        <label for="Phone">Phone location:</label>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <div class="">
                            <label for="Files">&nbsp;</label>
                            <button type="button" class="btn btn-sm btn-success" onclick="insertLocation(this)" value="{{$account->id}}">Save</button>
                        </div>
                    </div>
                </form>
            </section>
            <br>
            <section class="article-box">
            @include('account.currentLocations')
            </section>
        </div>
    </div>
    <div id="snackbar"></div>
    <div id="snackbarError"></div>
    <script type="text/javascript">
        var urlUpdateLocation = "{{ url('location/updateLocation') }}";
        var urlInsertLocation = "{{ url('location/insertLocation') }}";
        var urlDeleteLocation = "{{ url('location/deleteLocation') }}";
    </script>
</div>
@endsection
