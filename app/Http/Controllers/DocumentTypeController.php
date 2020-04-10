<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $documentTypes = DB::table('document_types')
            ->select('users.id as userId','document_types.id',
                'document_types.name','record_types.name as recordTypeName')
            ->join('record_types', 'record_types.id', '=', 'document_types.record_type_id')
            ->join('users', 'users.id', '=', 'document_types.created_by_id')
            ->where('users.organization_id', $user->organization_id)
            ->paginate(10);
        return view('documentType.index',compact('documentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        $recordTypesLst = DB::table('record_types')
            ->select('record_types.id as recordTypeId', 'record_types.name as recordTypeName')
            ->join('users', 'users.id', '=', 'record_types.created_by_id')
            ->where('users.organization_id', $user->organization_id)->get();
        $recordTypes = [];
        $i = 0;
        foreach ($recordTypesLst as $documento) {
            $recordTypes[$i]['id'] = $documento->recordTypeId;
            $recordTypes[$i]['name'] = $documento->recordTypeName;
            ++$i;
        }
        return view('documentType.create',compact('recordTypes'));
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
        $documentTypes= new \App\DocumentType;
        $documentTypes->name=$request->get('name');
        $documentTypes->record_type_id=$request->get('record_type_id');
        $documentTypes->created_by_id=$user->id;
        if ($documentTypes->save()) {
            $message = 'Success';
        } else {
            $message = 'An internal server error has ocurred, please contact your administrator';
        }
        return redirect('documentTypes')->with('success', $message);
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
        $documentTypeUser = \App\RecordType::find($id)->users;
        if ($user->organization_id == $documentTypeUser->organization_id) {
            $recordTypesLst = DB::table('record_types')
                ->select('record_types.id as recordTypeId', 'record_types.name as recordTypeName')
                ->join('users', 'users.id', '=', 'record_types.created_by_id')
                ->where('users.organization_id', $user->organization_id)->get();
            $recordTypes = [];
            $i = 0;
            foreach ($recordTypesLst as $documento) {
                $recordTypes[$i]['id'] = $documento->recordTypeId;
                $recordTypes[$i]['name'] = $documento->recordTypeName;
                ++$i;
            }
            $documentType = \App\DocumentType::find($id);
            return view('documentType.edit',compact('documentType','id','recordTypes'));
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
        $documentTypeUser = \App\DocumentType::find($id)->users;
        if ($user->organization_id == $documentTypeUser->organization_id) {
            $documentTypes= \App\DocumentType::find($id);
            $documentTypes->name=$request->get('name');
            $documentTypes->record_type_id=$request->get('record_type_id');
            if ($documentTypes->save()) {
                $message = 'Success';
            } else {
                $message = 'An internal server error has ocurred, please contact your administrator';
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('documentTypes')->with('success', $message);
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
        $documentTypeUser = \App\DocumentType::find($id)->users;
        $message = 'Success';
        if ($user->organization_id == $documentTypeUser->organization_id) {
            $documentTypes = \App\DocumentType::find($id);
            try {
                $documentTypes->delete();
            } catch (\Exception $e) {
                $documentTypes->status=0;
                $documentTypes->save();
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('documentTypes')->with('success',$message);
    }
}
