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
            <h3>Create account</h3>
            <form method="post" class="form-inline" action="{{action('AccountController@postCreateAccount')}}">
                @csrf
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Tipo de documento">Tipo:</label>
                        <select class="form-control" name="document_type_id">
                            @foreach($documentTypes as $type)
                                @php
                                $selected = $type['id'] == old('document_type_id') ? 'selected' : '';
                                @endphp
                                <option value="{{$type['id']}}" {{ $selected }}>{{$type['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="Número de documento">Número de documento:</label>
                        <input type="text" class="form-control" name="document_number" value="{{ old('document_number') }}">
                    </div>
                </div>
                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="Files">&nbsp;</label>
                        <button type="submit" class="btn btn-sm btn-success">Continuar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
