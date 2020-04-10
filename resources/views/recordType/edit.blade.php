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
            <h2>Edit record type</h2>
            <form method="post" class="form-inline" action="{{action('RecordTypeController@update', $id)}}">
                @csrf
                @method('PUT')
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Name">Name:</label>
                        <input type="text" class="form-control" name="name" value="{{$recordType->name}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="tax">tax:</label>
                        <input type="text" class="form-control" name="tax" value="{{$recordType->tax}}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Is Business">Is Business:</label>
                    @if ($recordType->is_business === 1)
                        <input type="checkbox" class="form-control" name="is_business" checked="true">
                    @else
                        <input type="checkbox" class="form-control" name="is_business">
                    @endif
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
