<ul class="sidebar-nav sidebar-dark sidebar-fixed" data-coreui="navigation" data-simplebar>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-view-quilt') }}"></use>
            </svg>
            {{ __('Strona główna') }}
        </a>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-truck') }}"></use>
            </svg>
            Dostawa na magazyn
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('shipments.index') }}" target="_top">
                    Dokumenty przyjęcia
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('control.index') }}" target="_top">
                    Kontrola dostawy
                </a>
            </li>
        </ul>
    </li>
    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-truck') }}"></use>
            </svg>
           Wydanie z magazynu
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('orders.index') }}" target="_top">
                    Dokumenty wydania
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('pick.index') }}" target="_top">
                    Planowanie kompletacji
                </a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link" href="{{ route('orderpickauto.index') }}" target="_top">
                    Zatwierdzone kompletacje
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-clipboard') }}"></use>
            </svg>
           Przesunięcia
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('move.su') }}" target="_top">
                    Opakowania
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('move.product') }}" target="_top">
                    Produkty
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('move.area') }}" target="_top">
                    Logiczne
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-3d') }}"></use>
            </svg>
            Budowa magazynu
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('locations.index') }}" target="_top">
                    Miejsca
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-spreadsheet') }}"></use>
            </svg>
            Produkty
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('products.index') }}" target="_top">
                    Definicja produktów
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('productsets.index') }}" target="_top">
                    Komplety
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('producttypes.index') }}" target="_top">
                    Kategoria produktów
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('productmetrics.index') }}" target="_top">
                    Jednostka miary
                </a>
            </li>
        </ul>
    </li>
    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-inbox') }}"></use>
            </svg>
            Opakowania
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('storeunits.index') }}" target="_top">
                    Definicja opakowań
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('storeunittypes.index') }}" target="_top">
                    Rodzaj opakowań
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('firms.index') }}">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-group') }}"></use>
            </svg>
            Kontrahenci
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('users.index') }}">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-people') }}"></use>
            </svg>
            Pracownicy
        </a>
    </li>

    <li class="nav-group" aria-expanded="false">
        <a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-inbox') }}"></use>
            </svg>
            Raporty
        </a>
        <ul class="nav-group-items" style="height: 0px;">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('report.stock') }}" target="_top">
                    Zapas magazynowy
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('about') }}">
            <svg class="nav-icon">
                <use xlink:href="{{ asset('icons/coreui.svg#cil-address-book') }}"></use>
            </svg>
            {{ __('O nas') }}
        </a>
    </li>
</ul>
