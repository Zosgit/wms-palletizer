<form action="{{ route('productmetrics.store') }}" class="forms-sample" method="POST">
    @csrf

{{--szablon ProductMetrics--}}
<div class="card-body"><canvas id="myBarChart" width="100%" height="10"></canvas>
    <div class="row p-2">
        <div class="col-md-4">
            <div class="form-group">
                <label for="code">Skró<table></table></label>
                <input type="text" name="code"
                    value="{{ isset($metric) ? $metric->code : '' }}" class="form-control" id="code" required/>

                    @if (isset($metric))
                        <input type="hidden" name="metric_id" value="{{ $metric->id }}">
                    @endif
            </div>
            <div class="col-md-12 pt-2">
                <div class="form-group">
                    <label for="amount">Liczba</label>
                    <input type="number" name="amount"
                        value="{{ isset($metric) ? $metric->amount : '' }}" class="number form-control" id="amount" required/>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="form-group">
                <label for="longdesc">Opis</label>
                <textarea type="text" name="longdesc"
                    value="{{ isset($metric) ? $metric->longdesc : '' }}" class="form-control" id="longdesc" rows="4" required>
                </textarea>
            </div>
        </div>
    </div>


    <div class="col-md-6 p-3">
        <button type="submit" class="btn btn-primary mr-4">Potwierdź</button>
        <a href="{{ route('productmetrics.index') }}" class="btn btn-light">Anuluj</a>
    </div>
</div>
</form>
@push('scripts')
<script>
    $('.number').on('change', function(){
        $(this).val(parseFloat($(this).val()).toFixed(2));
    });
</script>
@endpush
