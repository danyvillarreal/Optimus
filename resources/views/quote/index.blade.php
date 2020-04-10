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
            </div><br/>
        @endif
        <div class="col-xs-10 col-md-10 col-lg-7">
            @include('quote.searchAccount')
            @include('quote.searchProduct')
            @include('quote.productDetail')
        </div>
        @include('quote.quoteItemsSection')
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
        var urlEditQuoteDetail = "{{ url('quote/editQuoteDetail') }}";
        var urlUpdateQuoteDetail = "{{ url('quote/updateQuoteDetail') }}";
        var urlSavePackageValue = "{{ url('quote/savePackageValue') }}";
        
        var urlautocomplete = "{{ url('location/select2-autocomplete') }}";
        var urlautocompleteAjax = "{{ url('location/select2-autocomplete-ajax') }}";
        
        var urlUploadQuoteFiles = "{{ url('quote/uploadQuoteFiles') }}";
        var urlRemoveFile = "{{ url('quote/removeFile') }}";
    </script>
</div>

@endsection
