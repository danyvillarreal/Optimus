@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            @if (count($errors)>0)
                <div class="alert">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            @endif
            <h3>Edit user</h3>
            <form method="post" class="form-inline" action="{{action('UserController@update', $id)}}">
                @csrf
                @method('PUT')
                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="name" class="">{{ __('Name') }}</label>
                        <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{$user->name}}" required autofocus>

                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="email" class="">{{ __('E-Mail Address') }}</label>
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{$user->email}}" required>

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <div class="">
                    <label for="password-confirm" class="">Role</label>
                        @foreach($roles as $role)
                            @if (in_array($role->id, $currentRoles))
                                {{ $role->name }}<input type="checkbox" value="{{$role->name}}" name="role[{{$role->id}}]" checked="checked">
                            @else
                                {{ $role->name }}<input type="checkbox" value="{{$role->name}}" name="role[{{$role->id}}]">
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <div class="">
                        <label for="Register">&nbsp;</label>
                        <!-- <a href="{{action('UserController@index')}}" class="btn btn-sm btn-warning">Cancel</a> -->
                        <button type="submit" class="btn btn-sm btn-success">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
