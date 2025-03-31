@extends('layouts.guest')

@section('content')
    <div class="col-md-6">
        <div class="card mb-4 mx-4">
            <div class="card-body p-4">
                <h2 class="text-center m-3">Zresetuj hasło</h2>
                <div class="big mb-3 text-muted">Wpisz swój email, a my wyślemy link resetujący Twoje hasło.</div>
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    @if(session('status'))
                        <div role="alert" class="alert alert-success py-2 ">
                            <ul class="py-0 m-0">
                                <li>{{ session('status') }}</li>
                            </ul>
                        </div>
                    @endif
                    <div class="input-group mb-3"><span class="input-group-text">
                    <svg class="icon">
                      <use xlink:href="{{ asset('icons/coreui.svg#cil-envelope-open') }}"></use>
                    </svg></span>
                        <input class="form-control @error('email') is-invalid @enderror" type="email"
                               id="email" name="email" placeholder="{{ __('Email') }}">
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                        @if (Route::has('login'))

                            <a class="big" href="{{ route('login') }}"><svg class="icon">
                                <use xlink:href="{{ asset('icons/coreui.svg#cil-arrow-left') }}"></use>
                              </svg>&nbsp;Powrót do logowania</a>
                        @endif
                        <a class="btn btn-primary px-4" href="login.html">Resetuj hasło</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
