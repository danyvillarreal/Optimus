@extends('layouts.app')

@push('quote')
    <script src="{{ asset('js/quote.js') }}"></script>
@endpush

@section('content')

<div class="container">
    <div class="row">
        @if (\Session::has('success'))
            <div class="alert alert-success">
                <p>{{ \Session::get('success') }}</p>
            </div>
        @endif
        @include('quote.quoteItemsInvoiceSection')
        <div class="col-xs-10 col-md-10 col-lg-5">
            @include('quote.invoiceSection')
        </div>
    </div>
    <div id="snackbar"></div>
    <div id="snackbarError"></div>
    <script type="text/javascript">
        var urlFindAccount = "{{ url('quote/findAccount') }}";
        var urlSaveAccount = "{{ url('account/saveAccount') }}";
        var urlFindProduct = "{{ url('quote/findProduct') }}";
        var urlFinishSale = "{{ url('quote/finishSale') }}";
        var urlAddProduct = "{{ url('quote/addProduct') }}";
        var urlStartSale = "{{ url('quote/startSale') }}";
        var urlChoiceProduct = "{{ url('quote/choiceProduct') }}";
        var urlGetSecondCategory = "{{ url('quote/getSecondCategory') }}";
        var urlLoadQuoteDetail = "{{ url('quote/loadQuoteDetail') }}";
        var urlRemoveItem = "{{ url('quote/removeItem') }}";
        var urlFinishReactivar = "{{ url('quote/finishReactivar') }}";
        var urlEditQuoteDetail = "{{ url('quote/editQuoteDetail') }}";
        var urlUpdateQuoteDetail = "{{ url('quote/updateQuoteDetail') }}";
        var urlApproveItem = "{{ url('quote/invoices/approveItem') }}";
        var urlFinishFactura = "{{ url('quote/finishFactura') }}";
        var urlLoadQuoteDetail2 = "{{ url('quote/loadQuoteDetail2') }}";
        initFactura();
    </script>
</div>

@endsection
