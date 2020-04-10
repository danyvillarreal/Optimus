<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ISO 3166-1 alfa-2
        
        $user = auth()->user();
        $users = DB::table('users')
            ->where('users.status', 1)
            ->where('users.organization_id', $user->organization_id)->count();
        $licence = DB::table('licenses')
            ->orderByRaw('licenses.expiration DESC')
            ->where('licenses.organization_id', $user->organization_id)->first();
        $organizations = DB::table('organizations')
            ->where('organizations.id', $user->organization_id)
            ->paginate(10);
        return view('organization.index', ['organizations'=> $organizations,'users'=> $users,'number_users'=> $licence->number_users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $organization = \App\Organization::find($id);
        $user = auth()->user();
        if ($user->organization_id == $organization->id) {
            return view('organization.show',compact('organization'));
        } else {
            return redirect('organizations');
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
        $organization = \App\Organization::find($id);
        $countries = array('FR' => 'FR', 'DE' => 'DE', 'US' => 'US', 'CO' => 'CO');
        if ($user->organization_id == $organization->id) {
            return view('organization.edit',compact('organization','id','countries'));
        } else {
            return redirect('organizations');
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
        $organization= \App\Organization::find($id);
        $message = 'Success';
        if ($user->organization_id == $organization->id) {
            $uploadedFile = $request->file('logo');
            if ($uploadedFile) {
                $request->validate([
                    'logo' => 'max:5120',
                ]);
                $fileExtension = array_slice(explode('.', $uploadedFile->getClientOriginalName()),-1,1);
                $fileExtension = strtolower($fileExtension[0]);
                $allowedExtension = array('jpg','jpeg','png');
                if (in_array($fileExtension, $allowedExtension)) {
                    // $filename = time().$uploadedFile->getClientOriginalName();
                    $fileName = $organization->id.'_logo.'.$fileExtension;
                    Storage::disk('local')->put('uploads/'.$organization->id.'/'.$fileName, file_get_contents($uploadedFile));
                    $organization->logo=$fileName;
                } else {
                    $message = 'File extension not allowed';
                }
            }
            $organization->name=$request->get('name');
            $organization->country=$request->get('country');
            $organization->address=$request->get('address');
            $organization->phone=$request->get('phone');
            $organization->quote_number=$request->get('quote_number');
            $organization->aditional_detail=$request->get('aditional_detail');
            $organization->save();
            return redirect('organizations')->with('success', $message);
        } else {
            return redirect('organizations');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
