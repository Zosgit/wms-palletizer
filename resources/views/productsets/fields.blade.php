<form action="{{ route('producttypes.store') }}" class="forms-sample" method="POST">
    @csrf

<div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
    <div class="card-body">
        <div class="row p-1">
            {{-- Kolumna 1: Nazwa kompletu --}}
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label for="name">Nazwa kompletu</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>

            {{-- Kolumna 2: Lista produktów --}}
            <div class="col-md-9">
                <div class="form-group mb-3">
                    <label for="products">Wybierz produkty do kompletu</label>
                    <select name="products[]" id="products" class="form-select select_2" multiple required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->code }} – {{ $product->longdesc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 p-3">
        <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
        <a href="{{ route('producttypes.index') }}"
                class="btn btn-light">Anuluj</a>
    </div>
</div>
</form>

@push('scripts')
    <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.select_2').select2({
                placeholder: "Wybierz produkty",
                width: '100%'
            });
        });
    </script>
@endpush
