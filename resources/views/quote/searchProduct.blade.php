<div class="col-xs-12 col-md-12 col-lg-12 article-box" id="divSelectProduct" style="display: none;">
    <!-- <span>N° Factura: '.numFactura.'</span><br> -->
    
        <!-- <span style='text-align:left; font-size:150%;'>Total: $".number_format(total, 0, ',', '.')."</span><br> -->
    <!-- }else { -->
        <!-- <span style='text-align:left; font-size:150%;'>Total: ".number_format(fila2[0], 0, ',', '.')."</span> -->
    <!-- } -->
    <h2>Quote</h2>
    <div class="form-group">
		    <!-- <label class="control-label">Categoria ipal</label> -->
        <div style="display: inline-flex; width: 100%;">
			<select id="category" class="form-control">
				@foreach($primaryCategories as $category)
                    <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
		    </select>
            <label class="containerCheck">
                <input name="package" class="switch" id="package" type="checkbox" value="" title="Package"/>
                <span class="checkmark" title="Package"></span>
            </label>
        </div>
        <!-- </div>
    </div>
    <div class="form-group">
        <div class=""> -->
	        <!-- <label class="control-label">Descripción</label> -->
        <div class="">
            <textarea id="categoryDescription" class="form-control" rows="2" placeholder="Description"></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="">
		    <!-- <label class="control-label">Categoria secundaria</label> -->
			<select id="place" onchange="findProduct(this)" class="form-control">
				<!-- <option value="">-- Categoría secundaria --</option> -->
                @foreach($places as $place)
                    <option value="{{$place->id}}">{{$place->name}}</option>
                @endforeach
		    </select>
        <!-- </div>
    </div>
    <div class="form-group">
        <div class="">
	    <label class="control-label">Actividad/Material</label> -->
	        <input type="text" name="product" id="product" class="form-control" placeholder="Name/Code" tabindex="1" autofocus/>
    <!-- <button class="btn btn-success btn-sm" value="" id="addAnother">Otro</button> -->
        </div>
    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal" id="searchProduct">Search</button>
    </div>
        <!-- if(!isset($_POST['finventa'])) { -->
        <!-- <input type="hidden" name="buscarp"/> -->
        <!-- } //primero en aparecer -->
    <!-- </form> -->
    <input name="locationId" id="locationId" type="hidden" value=""/>
    <div id="name-data-material"></div>
</div>
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Select a product</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <div id="product-search-result"></div>
        </div>

        </div>
    </div>
</div>