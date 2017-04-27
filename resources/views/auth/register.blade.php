@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col m8 offset-m2">
            <div class="card blue-grey darken-1">
                <div class="card-content white-text"><span class="card-title">Register</span></div>
                <div class="card-action">
                    <form role="form" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="input-field{{ $errors->has('name') ? ' red' : '' }}">
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                            <label for="name" class="col-md-4 control-label">Name</label>
                        </div>

                        <div class="input-field{{ $errors->has('email') ? ' red' : '' }}">
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                        </div>

                        <div class="input-field{{ $errors->has('password') ? ' red' : '' }}">
                            <input id="password" type="password" class="form-control" name="password" required>
                            <label for="password" class="col-md-4 control-label">Password</label>
                        </div>

                        <div class="input-field">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                        </div>

                        <div class="input-field">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
