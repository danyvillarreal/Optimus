<div class="col-xs-12 col-md-12 col-lg-12 article-box" id="divAddProduct" style="display: none;">
    <div >
    <!-- <h3>Agregar material</h3> -->
        <form method="POST" class="form-inline" action="">
            
            <div class="form-group">
                <div class="">
                <label class="">Code</label>
                    <input name="code" style="width: 140px;" id="code" type="text" class="form-control" value="" disabled />
                </div>
            </div>

            <div class="form-group">
                <div class="">
                <label class="">Name</label>
                    <input name="name" id="productName" type="text" class="form-control" value="" disabled />
                        <!-- <textarea id="productName" class="form-control" rows="1" disabled></textarea> -->
                </div>
            </div>

            <div class="form-group">
                <div class="">
                <label class="">Quantity</label>
                    <input name="quantity" style="width: 100px;" id="quantity" type="number" class="form-control" tabindex="3" min="1" max="99999999" value="1" />
                </div>
            </div>

            <div class="form-group product">
                <div class="">
                <label class="">Price</label>
                    <input name="price" style="width: 140px;" id="price" type="number" class="form-control" value="" disabled="" />
                </div>
            </div>
            
            <div class="form-group">
                <div class="">
                <label class="">Detail</label>
                        <textarea id="description" class="form-control" rows="2" cols="46" name="description"></textarea>
                </div>
            </div>

            <div class="form-group product">
                <div class="">
                <label class="">Other</label>
                    <input name="other" style="width: 140px;" id="other" type="number" class="form-control" step="0.01" placeholder="" value="" />
                </div>
            </div>

            <div class="form-group product">
                <div class="demo">
                    <!-- <label class="">Optional</label>
                    <input type="checkbox" id="optional" name="optional" class="optionalOffer form-control" value="" /> -->
                    <input type="checkbox" id="optional" name="optional" class="optionalOffer">
                    <label for="optional" title="Optional"><span></span>Optional</label>
                </div>
            </div>

            <div class="form-group product">
                <div class="demo">
                    <!-- <label class="">Offer</label> -->
                    <!-- <input type="checkbox" id="offer" name="offer" class="optionalOffer form-control" value="" /> -->
                    <input type="checkbox" id="offer" name="offer" class="optionalOffer">
                    <label for="offer" title="Offer"><span></span>Offer</label>
                </div>
            </div>
            <input name="itemCategory" id="itemCategory" type="hidden" value=""/>
            <input name="cancel" id="cancel" type="hidden" value="{{$id}}"/>
        </form>
        @if ($datosVenta->stage_id === 2)
            <button class="btn btn-warning btn-sm" value="" id="cancelItem" disabled="">Cancel</button>
            <button class="btn btn-primary btn-sm" value="" id="addToSale" disabled="">Add</button>
            <button class="btn btn-primary btn-sm" value="" id="updateQuoteDetail" style="display: none;">Save</button>
        @endif
    </div>
</div>
@if ($datosVenta->stage_id === 2)
    <input name="quote_date" id="quote_date" type="date" class="form-control" value="{{date('Y-m-d')}}" />
    <div class="form-group">
        <div class="">
            <textarea id="aditional_detail" placeholder="Aditional Quote Detail" class="form-control" rows="2" cols="46" name="aditional_detail">{{$datosVenta->aditional_detail}}</textarea>
        </div>
    </div>
    <button class="btn btn-success btn-sm" value="{{$id}}" id="finish">Finish</button>
    <div id="uploaded_image">
        @include('quote.uploadedFiles')
    </div>
    <form method="post" class="form-inline" id="uploadDevisFiles" enctype="multipart/form-data">
        @csrf
        <div class="form-group form-group-sm">
            <div class="">
            <label for="Files">Files:</label>
                <input type="file" class="form-control" name="quoteFiles[]" multiple />
            </div>
        </div>
        <div class="form-group form-group-sm">
            <div class="">
            <label for="Files">&nbsp;</label>
                <button type="submit" class="btn btn-sm btn-success">Upload</button>
            </div>
        </div>
    </form>
@elseif ($datosVenta->stage_id !== 2)
    <div id="uploaded_image">
        @include('quote.uploadedFiles')
    </div>
    @if (Auth::user()->hasRole('admin') && $datosVenta->stage_id === 1)
        <button class="btn btn-success btn-sm" id="finish" value="{{$id}}">Volver a activar</button>
    @endif
    @if ($datosVenta->stage_id !== 1)
        <a href="{{action('QuoteController@quotePdf',$datosVenta->quoteId)}}" target="blank" class="btn btn-sm btn-danger">Generate PDF</a>
    @endif
@endif
