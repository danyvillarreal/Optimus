<strong style='text-align:left;'>Montant HT: </strong><span id="subTotal">{{$infoVenta->currency}}{{$totales['HT']}}</span>
<strong  style='text-align:left;'>VTA {{$infoVenta->tax}}%: </strong><span id="tax">{{$infoVenta->currency}}{{$totales['tax']}}</span>
@if ($totales['descuentos'] < 0)
    <strong style='text-align:left;'>Remise: </strong><span id="totalOthers">{{$infoVenta->currency}}{{$totales['descuentos']}}</span>
@endif
<br><span style='text-align:left; font-size:150%;'>Montant TTC: </span><span id="total">{{$infoVenta->currency}}{{$totales['total']}}</span>
