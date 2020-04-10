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
            <h2>Edit document type</h2>
            <form method="post" class="form-inline" action="{{action('DocumentTypeController@update', $id)}}">
                @csrf
                @method('PUT')
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Name">Name:</label>
                        <input type="text" class="form-control" name="name" value="{{$documentType->name}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="Tipo de documento">Tipo:</label>
                        <select class="form-control" name="record_type_id" disabled="">
                            @foreach($recordTypes as $type)
                                <option value="{{$type['id']}}" {{($type['id'] === $documentType->record_type_id) ? "selected" : "" }}>{{$type['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="number">&nbsp;</label>
                        <button type="submit" class="btn btn-sm btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
