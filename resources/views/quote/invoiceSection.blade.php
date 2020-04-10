<div class="col-xs-12 col-md-12 col-lg-12 article-box" id="divAddProduct">
    <div>
        <form method="" action="">
            <input name="cancel" id="cancel" type="hidden" value="{{$id}}" id="cancelar" />
    	    <div class="">
    			<label class="control-label">Invoice date</label>
    			<div class="">
    	        	<input name="invoice_date" id="invoice_date" type="date" class="form-control" value="{{$invoice_date}}" />
        		</div>
    	    </div>
        </form>
        <div id="uploaded_image">
            @include('quote.uploadedFiles')
        </div>
        @if (Auth::user()->hasRole('admin'))
            @if ($datosFactura->stage_id === 4)
                <button class="btn btn-warning btn-sm" id="finish" value="{{$id}}">Previous Stage</button>
            @endif
        @endif
        @if ($datosFactura->stage_id === 3)
            <button class="btn btn-warning btn-sm" value="{{$id}}" onclick="finishReactivar(this)">Previous Stage</button>
            <button class="btn btn-success btn-sm" value="{{$id}}" onclick="finishFacturaa(this)">Finish</button>
        @else
            <a href="{{action('QuoteController@invoicePdf',$datosFactura->quoteId)}}" target="blank" class="btn btn-sm btn-danger">Generate PDF</a>
        @endif
    </div>
</div>