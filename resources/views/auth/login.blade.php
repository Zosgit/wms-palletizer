@extends('layouts.guest')

@section('content')
    <div class="col-lg-8">
        <div class="card-group d-block d-md-flex row">
            <div class="card col-md-7 p-0 mb-0">
                <div class="card-body">
                    <h2 class="text-center m-3">Zaloguj się</h2>
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="input-group mb-3"><span class="input-group-text">
                      <svg class="icon">
                        <use xlink:href="{{ asset('icons/coreui.svg#cil-envelope-open') }}"></use>
                      </svg></span>
                            <input class="form-control @error('email') is-invalid @enderror" type="text" name="email"
                                   placeholder="{{ __('Email') }}" required autofocus>
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="input-group mb-4"><span class="input-group-text">
                      <svg class="icon">
                        <use xlink:href="{{ asset('icons/coreui.svg#cil-lock-locked') }}"></use>
                      </svg></span>
                            <input class="form-control @error('password') is-invalid @enderror" type="password"
                                   name="password"
                                   placeholder="{{ __('Hasło') }}" required>
                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-6 text-center">
                                <button class="btn btn-primary  px-4 " type="submit">Zaloguj</button>
                            </div>

                        </div>
                    </form>

                </div>
                <div class="card-footer  justify-content-center py-3">
                    @if (Route::has('password.request'))
                    <div class=" text-center">
                        <a href="{{ route('password.request') }}" class="btn btn-link">
                        {{ __('Zapomniałeś hasła?') }}</a>
                    </div>
                @endif
                </div>
            </div>
            <div class="card col-md-5 text-white bg-primary py-5">
                <div class="card-body text-center mt-4">
                    <div>
                        <h3>Nie masz konta? <br> Zapisz się!</h3>
                        <a href="{{ route('register') }}"
                           class="btn btn-lg btn-outline-light mt-3">{{ __('Zarejestruj') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
