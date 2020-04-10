<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = DB::table('locations')
            ->join('accounts', 'accounts.id', '=', 'locations.account_id')
            ->join('countries', 'countries.id', '=', 'locations.country_id')
            ->select('locations.*', 'accounts.name as accountName', 'countries.name as countryName')
            ->where('locations.status', 1)
            ->paginate(10);
        return view('location.index',compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('location.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $location= new \App\Location;
        if ($request->get('address') && $request->get('account_id')) {
            $location->account_id=$request->get('account_id');
            $location->country_id=$request->get('country_id');
            $location->city=$request->get('city');
            $location->address=$request->get('address');
            $location->phone=$request->get('phone');
            if ($location->save()) {
                $message = 'Success';
            } else {
                $message = 'An internal server erros has ocurred, please contact your administrator';
            }
        } else {
            $message = 'Please fill all data';
        }
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
        $categoria = \App\Place::find($id);
        $location = DB::table('locations')
            ->join('accounts', 'accounts.id', '=', 'locations.account_id')
            ->select('locations.*', 'accounts.name as accountName')
            ->where('locations.id', $id)->first();
        return view('location.show',compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $location = \App\Location::find($id);
        return view('location.edit',compact('location','id'));
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
        $location= \App\Location::find($id);
        if ($request->get('country_id') && $request->get('account_id')) {
            $location->account_id=$request->get('account_id');
            $location->country_id=$request->get('country_id');
            $location->city=$request->get('city');
            $location->address=$request->get('address');
            $location->phone=$request->get('phone');
            if ($location->save()) {
                $message = 'Success';
            } else {
                $message = 'An internal server erros has ocurred, please contact your administrator';
            }
        } else {
            $message = 'Please fill all data';
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
        $message = 'Success';
        $user = auth()->user();
        $location = \App\Location::find($id);
        $locationUser = \App\Location::find($request->get('id'))->users;
        if ($user->organization_id == $locationUser->organization_id) {
            try {
                $location->delete();
            } catch (\Exception $e) {
                $location->status=0;
                $location->save();
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('locations')->with('success','Information has been deleted');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateLocation(Request $request)
    {
        $user = auth()->user();
        $returnHTML = [];
        $success = false;
        $message = 'Success';
        $locationUser = \App\Location::find($request->get('id'))->users;
        if ($user->organization_id == $locationUser->organization_id) {
            $location= \App\Location::find($request->get('id'));
            $location->city=$request->get('city');
            $location->address=$request->get('address');
            $location->phone=$request->get('phone');
            if ($location->save()) {
                $success = true;
            } else {
                $message = 'An internal server erros has ocurred, please contact your administrator';
            }
        } else {
            $message = 'You have no permissions on this record';
        }
        $returnHTML['message'] = $message;
        return response()->json(array('success' => $success, 'data'=>$returnHTML));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function insertLocation(Request $request)
    {
        $user = auth()->user();
        $returnHTML = [];
        $success = false;
        $message = 'Success';
        $accountUser = \App\Account::find($request->get('account_id'))->users;
        if ($user->organization_id == $accountUser->organization_id) {
            $location= new \App\Location;
            $location->account_id=$request->get('account_id');
            $location->country_id=$request->get('country_id');
            $location->city=$request->get('city');
            $location->address=$request->get('address');
            $location->phone=$request->get('phone');
            $location->created_by_id=$user->id;
            if ($location->save()) {
                $success = true;
            } else {
                $message = 'An internal server erros has ocurred, please contact your administrator';
            }
        } else {
            $message = 'You have no permissions on this record';
        }
        $countries = \App\Country::all(['id', 'name']);
        $locations = DB::table('locations')
            ->join('accounts', 'accounts.id', '=', 'locations.account_id')
            ->join('users', 'users.id', '=', 'locations.created_by_id')
            ->select('locations.*', 'accounts.name as accountName')
            ->where('accounts.id', $request->get('account_id'))
            ->where('locations.status', 1)
            ->where('users.organization_id', $user->organization_id)->get();
        $returnHTML['message'] = $message;
        $returnHTML['html'] = view('account.currentLocations',['countries'=> $countries,'locations'=> $locations])->render();
        return response()->json(array('success' => $success, 'data'=>$returnHTML));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteLocation(Request $request)
    {
        $user = auth()->user();
        $returnHTML = [];
        $success = false;
        $message = 'Success';
        $user = auth()->user();
        $id = $request->get('id');
        $account_id = 0;
        $locationUser = \App\Location::find($id)->users;
        if ($user->organization_id == $locationUser->organization_id) {
            $location = \App\Location::find($id);
            $account_id = $location->account_id;
            try {
                $location->delete();
            } catch (\Exception $e) {
                $location->status=0;
                $location->save();
            }
            $success = true;
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        $countries = \App\Country::all(['id', 'name']);
        $locations = DB::table('locations')
            ->join('accounts', 'accounts.id', '=', 'locations.account_id')
            ->join('users', 'users.id', '=', 'locations.created_by_id')
            ->select('locations.*', 'accounts.name as accountName')
            ->where('accounts.id', $account_id)
            ->where('locations.status', 1)
            ->where('users.organization_id', $user->organization_id)->get();
        $returnHTML['message'] = $message;
        $returnHTML['html'] = view('account.currentLocations',['countries'=> $countries,'locations'=> $locations])->render();
        return response()->json(array('success' => $success, 'data'=>$returnHTML));
    }
}
