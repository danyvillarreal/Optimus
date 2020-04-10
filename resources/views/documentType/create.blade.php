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
            <h2>Create document type</h2><br/>
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
                    <label for="Tipo de documento">Tipo:</label>
                        <select class="form-control" name="record_type_id">
                            @foreach($recordTypes as $type)
                                @php
                                $selected = $type['id'] == old('record_type_id') ? 'selected' : '';
                                @endphp
                                <option value="{{$type['id']}}" {{ $selected }}>{{$type['name']}}</option>
                            @endforeach
                        </select>
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
