@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col m8 offset-m2">
            <div class="card blue-grey darken-1">
                <div class="card-content white-text"><span class="card-title">Login</span></div>
                <div class="card-action">
                    <form role="form" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="input-field{{ $errors->has('email') ? ' red' : '' }}">
                            <input id="email" type="email" class="validate" name="email" value="{{ old('email') }}" required autofocus>
                            <label for="email">E-Mail Address</label>
                        </div>

                        <div class="input-field{{ $errors->has('password') ? ' red' : '' }}">
                            <input id="password" type="password" class="validate" name="password" required>
                            <label for="password">Password</label>
                        </div>

                        <p>
                            <input id="remember"  class="filled-in" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember"> Remember Me </label>
                        </p>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
