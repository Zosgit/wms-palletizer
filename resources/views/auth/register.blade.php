@extends('layouts.guest')

@section('content')
    <div class="col-md-6">
        <div class="card mb-4 mx-4">
            <div class="card-body p-4">
                <h2 class="text-center m-3">Zarejestruj się</h2>

                <form method="POST" action="{{ route('register') }}">
                    @csrf


                    <div class="input-group mb-3"><span class="input-group-text">
                        <svg class="icon">
                          <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg></span>
                            <input class="form-control" type="text" name="firstname" placeholder="{{ __('Imię') }}" required
                                   autocomplete="firstname">
                            @error('firstname')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                            @enderror
                    </div>

                    <div class="input-group mb-3"><span class="input-group-text">
                        <svg class="icon">
                          <use xlink:href="{{ asset('icons/coreui.svg#cil-user') }}"></use>
                        </svg></span>
                            <input class="form-control" type="text" name="lastname" placeholder="{{ __('Nazwisko') }}" required
                                   autocomplete="lastname">
                            @error('lastname')
                            <span class="invalid-feedback">
                                {{ $message }}
                            </span>
                            @enderror
                    </div>

                    <div class="input-group mb-3"><span class="input-group-text">
                    <svg class="icon">
                      <use xlink:href="{{ asset('icons/coreui.svg#cil-envelope-open') }}"></use>
                    </svg></span>
                        <input class="form-control" type="text" name="email" placeholder="{{ __('Email') }}" required
                               autocomplete="email">
                        @error('email')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    <div class="input-group mb-3"><span class="input-group-text">
                    <svg class="icon">
                      <use xlink:href="{{ asset('icons/coreui.svg#cil-lock-locked') }}"></use>
                    </svg></span>
                        <input class="form-control @error('password') is-invalid @enderror" type="password"
                               name="password" placeholder="{{ __('Hasło') }}" required autocomplete="new-password">
                        @error('password')
                        <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    <div class="input-group mb-4"><span class="input-group-text">
                    <svg class="icon">
                      <use xlink:href="{{ asset('icons/coreui.svg#cil-lock-locked') }}"></use>
                    </svg></span>
                        <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password"
                               name="password_confirmation" placeholder="{{ __('Potwierdź hasło') }}" required
                               autocomplete="new-password">
                    </div>

                    <div class="text-center">
                        <button class="btn btn-block btn-primary col-md-4 " type="submit">Zarejestruj</button>
                    </div>

                </form>

            </div>
            <div class="card-footer text-center py-3">
                @if (Route::has('login'))
                <a class="big" href="{{ route('login') }}">Posiadasz już konto? &nbsp; Zaloguj się!</a>
                @endif
            </div>
        </div>
    </div>

@endsection
