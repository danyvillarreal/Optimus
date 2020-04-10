<table class="table table-hover table-condensed">
    <thead>
        <tr>
            <th>Name</th>
            <th>Code</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        
        @foreach($products as $product)
            <tr>

                <td>{{$product->name}}</td>
                <td>{{$product->code}}</td>
                <td>{{$product->price}}</td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="choiceProduct(this)" value="{{$product->id}}" type="submit">Select</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>