<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $categoria = DB::table('places')
            ->select('places.*')
            ->join('users', 'users.id', '=', 'places.created_by_id')
            ->where('users.organization_id', $user->organization_id)
            ->where('places.status', 1)
            ->paginate(10);
        return view('place.index',compact('categoria'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        // $lstCategorias = DB::table('categories')
        //     ->select('categories.id','categories.name',
        //         'users.id as userId','users.name as userName')
        //     ->join('users', 'users.id', '=', 'categories.created_by_id')
        //     ->where('users.organization_id', $user->organization_id)->get();
        // $categorias = [];
        // $i = 0;
        // foreach ($lstCategorias as $categoria) {
        //     $categorias[$i]['id'] = $categoria->id;
        //     $categorias[$i]['name'] = $categoria->name;
        //     ++$i;
        // }
        // $categorias = \App\Category::all(['id', 'name']);
        return view('place.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);
        $user = auth()->user();
        $categoria= new \App\Place;
        $categoria->created_by_id=$user->id;
        $categoria->name=$request->get('name');
        $categoria->save();
        return redirect('place')->with('success', 'Success');
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
        $placeUser = \App\Place::find($id)->users;
        if ($user->organization_id == $placeUser->organization_id) {
            $categoria = \App\Place::find($id);
            $categoria = DB::table('places')
                ->select('places.name')
                ->where('places.id', $id)->first();
            return view('place.show',compact('categoria'));
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
        $placeUser = \App\Place::find($id)->users;
        if ($user->organization_id == $placeUser->organization_id) {
            $categoria = \App\Place::find($id);
            // $lstCategorias = DB::table('categories')
            //     ->select('categories.id','categories.name',
            //         'users.id as userId','users.name as userName')
            //     ->join('users', 'users.id', '=', 'categories.created_by_id')
            //     ->where('users.organization_id', $user->organization_id)->get();
            // $categorias = [];
            // $i = 0;
            // foreach ($lstCategorias as $category) {
            //     $categorias[$i]['id'] = $category->id;
            //     $categorias[$i]['name'] = $category->name;
            //     ++$i;
            // }
            return view('place.edit',compact('categoria','id'));
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
        ]);
        $user = auth()->user();
        $placeUser = \App\Place::find($id)->users;
        $message = 'Success';
        if ($user->organization_id == $placeUser->organization_id) {
            $categoria= \App\Place::find($id);
            $categoria->name=$request->get('name');
            $categoria->save();
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('place')->with('success',$message);
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
        $categoryUser = \App\Place::find($id)->users;
        $message = 'Success';
        if ($user->organization_id == $categoryUser->organization_id) {
            $categoria = \App\Place::find($id);
            try {
                $categoria->delete();
            } catch (\Exception $e) {
                $categoria->status=0;
                $categoria->save();
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('place')->with('success',$message);
    }
}
