<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $products=\App\Product::all();
        // $products = DB::table('products')->get();
        // $products = DB::table('products')->paginate(10);
        // $products = DB::table('products')
        //     ->where('status', 1)
        //     ->select('products.*')
        //     ->paginate(10);
        
        $user = auth()->user();
        $products = DB::table('products')
            ->select('users.id as userId','products.name','products.code',
                'products.price','products.priceusd','products.id')
            ->join('users', 'users.id', '=', 'products.created_by_id')
            ->where('users.organization_id', $user->organization_id)
            ->paginate(10);
        
        return view('product.index',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $secondCategories = \App\Place::all(['id', 'name']);
        // return view('product.create',compact('secondCategories'));
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if($request->hasfile('filename'))
        //  {
        //     $file = $request->file('filename');
        //     $name=time().$file->getClientOriginalName();
        //     $file->move(public_path().'/images/', $name);
        //  }
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required',
        ]);
        $user = auth()->user();
        $producto= new \App\Product;
        $producto->name=$request->get('name');
        $producto->price=$request->get('price');
        $producto->priceusd=$request->get('priceusd');
        $producto->created_by_id=$user->id;
        if ($producto->save()) {
            $message = 'Success';
            if ($request->get('code')) {
                $producto->code=$request->get('code');
            } else {
                $producto->code=$producto->id;
            }
            $producto->save();
        } else {
            $message = 'An internal server error has ocurred, please contact your administrator';
        }
        // $producto->categoria_secundaria_id=$request->get('categoria_secundaria_id');

        // if ($request->get('vencimiento') != '') {
        //     $vencimiento = date_create($request->get('vencimiento'));
        //     $vencimiento = date_format($vencimiento,"Y-m-d");
        //     $producto->vencimiento = $vencimiento;
        // }
        // $producto->filename=$name;
        
        return redirect('products')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user();
        $productUser = \App\Product::find($id)->users;
        if ($user->organization_id == $productUser->organization_id) {
            $producto = \App\Product::find($id);
            return view('product.show',compact('producto'));
        } else {
            return redirect('/home');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $productUser = \App\Product::find($id)->users;
        if ($user->organization_id == $productUser->organization_id) {
            $product = \App\Product::find($id);
            return view('product.edit',compact('product','id'));
        } else {
            return redirect('/home');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required',
        ]);
        $user = auth()->user();
        $productUser = \App\Product::find($id)->users;
        if ($user->organization_id == $productUser->organization_id) {
            $producto= \App\Product::find($id);
            $producto->name=$request->get('name');
            $producto->price=$request->get('price');
            $producto->priceusd=$request->get('priceusd');
            $producto->created_by_id=$user->id;
            if ($request->get('code')) {
                $producto->code=$request->get('code');
            }
            if ($producto->save()) {
                $message = 'Success';
            } else {
                $message = 'An internal server error has ocurred, please contact your administrator';
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('products')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $productUser = \App\Product::find($id)->users;
        $message = 'Success';
        if ($user->organization_id == $productUser->organization_id) {
            $producto = \App\Product::find($id);
            try {
                $producto->delete();
            } catch (\Exception $e) {
                $producto->status=0;
                $producto->save();
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('products')->with('success',$message);
    }
}
