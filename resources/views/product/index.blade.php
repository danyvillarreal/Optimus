@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div id="snackbar">
                @if (\Session::has('success'))
                    <script>
                        var x = document.getElementById("snackbar");
                        x.className = "show";
                        setTimeout(function(){
                            x.className = x.className.replace("show", "");
                        }, 2000);
                    </script>
                    {{ \Session::get('success') }}
                @endif
            </div>
            <h1>Products <a href="{{action('ProductController@create')}}" class="btn btn-sm btn-primary">Create</a></h1>
            <table class="table table-hover table-condensed table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Price EUR</th>
                        <th>Price USD</th>
                        <th colspan="3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>{{$product->name}}</td>
                        <td>{{$product->code}}</td>
                        <td>{{$product->price}}</td>
                        <td>{{$product->priceusd}}</td>
                        <!-- <td>
                            <a class="btn btn-sm btn-success" href="{{ URL::to('products/' . $product->id) }}">Show</a>
                        </td> -->
                        <td>
                            <form action="{{action('ProductController@destroy', $product->id)}}" method="post">
                                @csrf
                                <a href="{{action('ProductController@edit', $product->id)}}" class="btn btn-sm btn-warning">Edit</a>
                                <input name="_method" type="hidden" value="DELETE">
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
