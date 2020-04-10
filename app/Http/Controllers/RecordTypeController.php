<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RecordTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $recordTypes = DB::table('record_types')
            ->select('users.id as userId','record_types.id','record_types.name',
                'record_types.tax','record_types.is_business')
            ->join('users', 'users.id', '=', 'record_types.created_by_id')
            ->where('users.organization_id', $user->organization_id)
            ->paginate(10);
        return view('recordType.index',compact('recordTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('recordType.create');
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
            'name' => 'required|max:100',
        ]);
        $user = auth()->user();
        $recordTypes= new \App\RecordType;
        $recordTypes->name=$request->get('name');
        $recordTypes->tax=$request->get('tax');
        $recordTypes->is_business=0;
        if ($request->get('is_business')) {
            $recordTypes->is_business=1;
        }
        $recordTypes->created_by_id=$user->id;
        if ($recordTypes->save()) {
            $message = 'Success';
        } else {
            $message = 'An internal server error has ocurred, please contact your administrator';
        }
        return redirect('recordTypes')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $recordTypeUser = \App\RecordType::find($id)->users;
        if ($user->organization_id == $recordTypeUser->organization_id) {
            $recordType = \App\RecordType::find($id);
            return view('recordType.edit',compact('recordType','id'));
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
            'name' => 'required|max:100',
        ]);
        $user = auth()->user();
        $recordTypeUser = \App\RecordType::find($id)->users;
        if ($user->organization_id == $recordTypeUser->organization_id) {
            $recordTypes= \App\RecordType::find($id);
            $recordTypes->name=$request->get('name');
            $recordTypes->tax=$request->get('tax');
            $recordTypes->is_business=0;
            if ($request->get('is_business')) {
                $recordTypes->is_business=1;
            }
            if ($recordTypes->save()) {
                $message = 'Success';
            } else {
                $message = 'An internal server error has ocurred, please contact your administrator';
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('recordTypes')->with('success', $message);
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
        $recordTypeUser = \App\RecordType::find($id)->users;
        $message = 'Success';
        if ($user->organization_id == $recordTypeUser->organization_id) {
            $recordTypes = \App\RecordType::find($id);
            try {
                $recordTypes->delete();
            } catch (\Exception $e) {
                $recordTypes->status=0;
                $recordTypes->save();
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('recordTypes')->with('success',$message);
    }
}
