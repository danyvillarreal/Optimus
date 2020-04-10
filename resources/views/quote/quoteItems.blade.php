<?php $i = 0; ?>

<strong>Client: </strong><span id="cliente">{{$infoVenta->accountName}}</span>
<span id="documentoCliente">{{$infoVenta->documenttype}} {{$infoVenta->document_number}}</span><br>

<div id="totales">
    @include('quote.quoteTotal')
</div>

<div class="accordion" id="accordionExample">
@foreach($itemsCategories as $principal)
    <?php ++$i; ?>

    <!-- <tr> 1-->
    <div class="card">

        <div class="card-header" id="headingOne">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?php echo $i; ?>" aria-expanded="true" aria-controls="collapse<?php echo $i; ?>">
                    {{$principal->nombrecategoriaprincipal}}
                </button>
            </h2>
        </div>




        <div id="collapse<?php echo $i; ?>" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
                <p>{!! nl2br(e($principal->description)) !!}</p>
    <!-- </tr> 1-->
                <table class="table table-hover table-condensed">
                    @foreach($places as $secundaria)
                        @if ($principal->category_id == $secundaria->category_id)
                            <?php $hasPackageHeader = 0; ?>
                            <!-- <tr> -->
                            @foreach($quoteItems as $detalleProducto)
                                @if ($secundaria->place_id == $detalleProducto->place_id &&
                                    $detalleProducto->category_id == $principal->category_id)
                                    @if ($hasPackageHeader === 0 && $detalleProducto->package === 0)
                                        <?php $hasPackageHeader = 1; ?>
                                        <thead>
                                            <tr>
                                                <th colspan="6">{{$secundaria->name}}</th>
                                                <th colspan="1"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    @endif
                                @endif
                            @endforeach
                            <!-- </tr> -->
                            @foreach($quoteItems as $detalleProducto)
                                @if ($secundaria->place_id == $detalleProducto->place_id &&
                                    $detalleProducto->category_id == $principal->category_id)
                                    @if ($detalleProducto->package === 0)
                                        <?php
                                            $productName = strlen($detalleProducto->name) > 60 ? substr($detalleProducto->name,0,60).'...' : $detalleProducto->name;
                                        ?>
                                    <tr>
                                        <td colspan="1">{{$detalleProducto->quantity}}</td>
                                        @if ($detalleProducto->optional == 1)
                                            <td colspan="3">OPTION: {{$productName}} - {{$detalleProducto->description}}</td>
                                        @elseif ($detalleProducto->description != null)
                                            <td colspan="3">{{$productName}} - {{$detalleProducto->description}}</td>
                                        @else
                                            <td colspan="3">{{$productName}}</td>
                                        @endif

                                        @if ($detalleProducto->offer == 1)
                                            <td colspan="1"></td>
                                            <td colspan="1">Oferta</td>
                                        @elseif ($detalleProducto->package === 0)
                                            @if ($detalleProducto->other != 0)
                                                <td colspan="1">{{$infoVenta->currency}}{{$detalleProducto->other}}</td>
                                            @else
                                                <td colspan="1"></td>
                                            @endif
                                            <td colspan="1">{{$infoVenta->currency}}{{$detalleProducto->price}}</td>
                                        @else
                                            <td colspan="2">
                                            </td>
                                        @endif

                                        <td colspan="1">
                                        @if ($infoVenta->stage_id === 2)
                                            <button class="btn btn-success btn-sm" onclick="editItem(this)" value="{{$detalleProducto->id}}">Edit</button>
                                            <button class="btn btn-warning btn-sm" onclick="removeItem(this)" value="{{$detalleProducto->id}}" id=>Remove</button>
                                        @endif
                                            <!-- <form method="post" action=""> -->
                                                <!-- <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> -->
                                                <!-- <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> -->
                                            <!-- </form> FastIdea Company Name -->
                                        </td>
                                    </tr>
                                    @endif
                                @endif
                            @endforeach
                            @if ($hasPackageHeader === 1)
                                </tbody>
                            @endif
                        @endif
                    @endforeach
                    @foreach($places as $secundaria)
                        @if ($principal->category_id == $secundaria->category_id)
                        <?php $hasPackageHeader = 0; ?>
                            <!-- <tr> -->
                            @foreach($itemsPackages as $itemPackage)
                            <?php $hasPackage = 0; ?>
                            @foreach($quoteItems as $detalleProducto)
                                @if ($secundaria->place_id == $detalleProducto->place_id &&
                                    $detalleProducto->category_id == $principal->category_id)
                                    @if ($hasPackageHeader === 0 && $detalleProducto->package === 1)
                                        <?php $hasPackageHeader = 1; ?>
                                        <thead>
                                            <tr>
                                                <th colspan="6">{{$secundaria->name}}</th>
                                                <th colspan="1"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                    @endif
                                @endif
                            @endforeach
                            <!-- </tr> -->
                            @foreach($quoteItems as $detalleProducto)
                                @if ($secundaria->place_id == $detalleProducto->place_id &&
                                    $detalleProducto->category_id == $principal->category_id)
                                    @if ($detalleProducto->package === 1 &&
                                        $secundaria->place_id == $itemPackage->place_id &&
                                        $detalleProducto->category_id == $itemPackage->category_id)
                                        <?php $hasPackage = 1;
                                            $productName = strlen($detalleProducto->name) > 60 ? substr($detalleProducto->name,0,60).'...' : $detalleProducto->name;
                                        ?>
                                    <tr>
                                        <td colspan="1">{{$detalleProducto->quantity}}</td>
                                        @if ($detalleProducto->optional == 1)
                                            <td colspan="3">OPTION: {{$productName}} - {{$detalleProducto->description}}</td>
                                        @elseif ($detalleProducto->description != null)
                                            <td colspan="3">{{$productName}} - {{$detalleProducto->description}}</td>
                                        @else
                                            <td colspan="3">{{$productName}}</td>
                                        @endif

                                        @if ($detalleProducto->offer == 1)
                                            <td colspan="2">Oferta</td>
                                        @elseif ($detalleProducto->package === 0)
                                            <td colspan="2">{{$infoVenta->currency}}{{$detalleProducto->price}}</td>
                                        @else
                                            <td colspan="2">
                                            </td>
                                        @endif

                                        <td colspan="1">
                                        @if ($infoVenta->stage_id === 2)
                    <button class="btn btn-success btn-sm" onclick="editItem(this)" value="{{$detalleProducto->id}}">Edit</button>
                    <button class="btn btn-warning btn-sm" onclick="removeItem(this)" value="{{$detalleProducto->id}}">Remove</button>
                                        @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endif
                            @endforeach
                            @if ($hasPackage === 1)
                            <tr>
                                @if ($infoVenta->stage_id === 2)
                                <td colspan="6">
                                    <input name="packageValue" id="packageValue-{{$itemPackage->id}}" type="number" class="form-control" step="0.01" min="0.01" max="99999999" value="{{$itemPackage->value}}" />
                                </td>
                                <td colspan="1">
                                    <button class="btn btn-success btn-sm" onclick="savePackageValue(this)" value="{{$itemPackage->id}}" id=>Save</button>
                                </td>
                                @else
                                <td colspan="4"></td>
                                <td colspan="1"></td>
                                <td colspan="1">{{$infoVenta->currency}}{{$itemPackage->value}}</td>
                                @endif
                            </tr>
                            @endif
                            @endforeach
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endforeach
</div>