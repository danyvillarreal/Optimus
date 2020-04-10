<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $categoriasPrincipales = DB::table('categories')
        //     ->where('status', 1)
        //     ->paginate(10);
        
        $user = auth()->user();
        $categoriasPrincipales = DB::table('categories')
            ->select('users.id as userId','categories.name','categories.id')
            ->join('users', 'users.id', '=', 'categories.created_by_id')
            ->where('users.organization_id', $user->organization_id)
            ->paginate(10);
        return view('category.index', ['categoriasPrincipales'=> $categoriasPrincipales]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create');
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
        $categoria= new \App\Category;
        $categoria->created_by_id=$user->id;
        $categoria->name=$request->get('name');
        $categoria->save();
        return redirect('category')->with('success', 'Information has been added');
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
        $categoryUser = \App\Category::find($id)->users;
        if ($user->organization_id == $categoryUser->organization_id) {
            $categoria = \App\Category::find($id);
            return view('category.show',compact('categoria'));
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
        $categoryUser = \App\Category::find($id)->users;
        if ($user->organization_id == $categoryUser->organization_id) {
            $categoria = \App\Category::find($id);
            return view('category.edit',compact('categoria','id'));
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
        $user = auth()->user();
        $categoryUser = \App\Category::find($id)->users;
        $message = 'Success';
        if ($user->organization_id == $categoryUser->organization_id) {
            $categoria= \App\Category::find($id);
            $categoria->name=$request->get('name');
            $categoria->save();
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('category')->with('success',$message);
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
        $categoryUser = \App\Category::find($id)->users;
        $message = 'Success';
        if ($user->organization_id == $categoryUser->organization_id) {
            $categoria = \App\Category::find($id);
            try {
                $categoria->delete();
            } catch (\Exception $e) {
                $categoria->status=0;
                $categoria->save();
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('category')->with('success',$message);
    }
}
