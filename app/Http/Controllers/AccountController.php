<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $accounts = DB::table('Accounts')->get();
        // $accounts = Accounts::paginate(15);
        // $accounts = App\Account::paginate(15);
        $user = auth()->user();
        $accounts = DB::table('accounts')
            ->select('users.id as userId','accounts.document_type_id','accounts.id','accounts.name',
                'document_types.name as documentTypeName',
                'accounts.document_number','accounts.first_name','accounts.last_name',
                'accounts.shipping_address','accounts.email','accounts.movile','accounts.phone')
            ->join('users', 'users.id', '=', 'accounts.created_by_id')
            ->join('document_types', 'document_types.id', '=', 'accounts.document_type_id')
            ->where('users.organization_id', $user->organization_id)
            ->paginate(10);
        return view('account.index', ['accounts'=> $accounts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // $request->session()->forget('account');
        $account = $request->session()->get('account');
        // if ($request->session()->get('account')) {
        // }

        $user = auth()->user();
        $tiposDocumentos = DB::table('document_types')
            ->select('document_types.id as tipoId', 'document_types.name as tipoName')
            ->join('users', 'users.id', '=', 'document_types.created_by_id')
            ->where('users.organization_id', $user->organization_id)->get();
        $documentTypes = [];
        $i = 0;
        foreach ($tiposDocumentos as $documento) {
            $documentTypes[$i]['id'] = $documento->tipoId;
            $documentTypes[$i]['name'] = $documento->tipoName;
            ++$i;
        }

        return view('account.create',compact('documentTypes','account'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'document_type_id' => 'required|max:1',
        //     'document_number' => 'required|max:11',
        //     'name' => 'required|max:100',
        // ]);
        // $account= new \App\Account;
        // if ($request->get('first_name') && $request->get('last_name') && $request->get('document_type_id') != 1) {
        //     $validatedData = $request->validate([
        //         'document_type_id' => 'required|max:1',
        //         'document_number' => 'required|max:11',
        //         'first_name' => 'required|max:30',
        //         'last_name' => 'required|max:30',
        //     ]);
        //     $account->first_name=$request->get('first_name');
        //     $account->last_name=$request->get('last_name');
        //     $account->address=$request->get('address');
        //     $account->name=trim($account->first_name).' '.trim($account->last_name);
        // } else {
        //     $account->name=$request->get('name');
        // }
        // $user = auth()->user();
        // $account->created_by_id=$user->id;
        // $account->email=$request->get('email');
        // $account->movile=$request->get('movile');
        // $account->phone=$request->get('phone');
        // $account->postal_code=$request->get('postal_code');
        // $account->billing_address=$request->get('billing_address');
        // $account->shipping_address=$request->get('shipping_address');
        // $account->document_type_id=$request->get('document_type_id');
        // $account->document_number=$request->get('document_number');
        // $message = 'Success';
        
        // // location info
        // // Revisar este metodo
        // $location= new \App\Location;
        // $location->country_id=$request->get('country');
        // $location->city=$request->get('city');
        // $location->address=$request->get('location_address');
        // $location->phone=$request->get('phone');
        // $location->save();
        // // location info fin

        // if (!$account->save()) {
        //     $message = 'An internal server error has ocurred, please contact your administrator';
        // }
        // return redirect('accounts')->with('success',$message);
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
        $accountUser = \App\Account::find($id)->users;
        if ($user->organization_id == $accountUser->organization_id) {
            $account = \App\Account::find($id);
            return view('account.show',compact('account'));
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
        $accountUser = \App\Account::find($id)->users;
        if ($user->organization_id == $accountUser->organization_id) {
            $account = \App\Account::find($id);

            $locations = DB::table('locations')
                ->join('accounts', 'accounts.id', '=', 'locations.account_id')
                ->join('users', 'users.id', '=', 'locations.created_by_id')
                ->select('locations.*', 'accounts.name as accountName')
                ->where('accounts.id', $id)
                ->where('locations.status', 1)
                ->where('users.organization_id', $user->organization_id)->get();

            $tiposDocumentos = DB::table('document_types')
                ->select('document_types.id as tipoId', 'document_types.name as tipoName')
                ->join('users', 'users.id', '=', 'document_types.created_by_id')
                ->where('users.organization_id', $user->organization_id)->get();
            $documentTypes = [];
            $i = 0;
            foreach ($tiposDocumentos as $documento) {
                $documentTypes[$i]['id'] = $documento->tipoId;
                $documentTypes[$i]['name'] = $documento->tipoName;
                ++$i;
            }
            $countries = \App\Country::all(['id', 'name']);
            if ($account->document_type_id === 1) {
                return view('account.edit',compact('account','id','documentTypes','countries','locations'));
            } else {
                return view('account.editPA',compact('account','id','documentTypes','countries','locations'));
            }
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
        $account= \App\Account::find($id);
        $user = auth()->user();
        $accountUser = \App\Account::find($id)->users;
        if ($user->organization_id == $accountUser->organization_id) {
            $documentType = \App\DocumentType::find($account->document_type_id);
            if ($documentType->is_business === 1) {
                $validatedData = $request->validate([
                    // 'document_type_id' => 'required|max:1',
                    'document_number' => 'required|max:11',
                    'name' => 'required|max:100',
                ]);
                $account->name=trim($request->get('name'));
            } else {
                $validatedData = $request->validate([
                    // 'document_type_id' => 'required|max:1',
                    'document_number' => 'required|max:10',
                    'first_name' => 'required|max:30',
                    'last_name' => 'required|max:30',
                ]);
                $account->first_name=trim($request->get('first_name'));
                $account->last_name=trim($request->get('last_name'));
                $account->address=$request->get('address');
                $account->name=trim($account->first_name).' '.trim($account->last_name);
            }
            $user = auth()->user();
            $account->email=$request->get('email');
            $account->movile=$request->get('movile');
            $account->phone=$request->get('phone');
            $account->postal_code=$request->get('postal_code');
            $account->billing_address=$request->get('billing_address');
            $account->shipping_address=$request->get('shipping_address');
            // $account->document_type_id=$request->get('document_type_id');
            $account->document_number=$request->get('document_number');
            // location info
            // $location= new \App\Location;
            // $location->account_id=$request->get('account_id');
            // $location->account_id=$request->get('account_id');
            // $location->city=$request->get('city');
            // $location->address=$request->get('location_address');
            // $location->phone=$request->get('phone');
            // $location->save();
            // location info fin
            $account->save();
            $message = 'Success';
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('accounts')->with('success',$message);
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
        $accountUser = \App\Product::find($id)->users;
        $message = 'Success';
        if ($user->organization_id == $accountUser->organization_id) {
            $account = \App\Account::find($id);
            try {
                $account->delete();
            } catch (\Exception $e) {
                $account->status=0;
                $account->save();
            }
        } else {
            $message = 'You are not authorized to performe this action';
        } 
        return redirect('accounts')->with('success',$message);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createBA(Request $request)
    {
        // se reciben datos desde session
        $user = auth()->user();
        $account = $request->session()->get('account');
        $tiposDocumentos = DB::table('document_types')
            ->select('document_types.id as tipoId', 'document_types.name as tipoName')
            ->join('users', 'users.id', '=', 'document_types.created_by_id')
            ->where('users.organization_id', $user->organization_id)->get();
        $documentTypes = [];
        $i = 0;
        foreach ($tiposDocumentos as $documento) {
            $documentTypes[$i]['id'] = $documento->tipoId;
            $documentTypes[$i]['name'] = $documento->tipoName;
            ++$i;
        }
        if ($account) {
            $documentType = $account->document_type_id;
            $documentNumber = $account->document_number;
            $account = $request->session()->get('account');
            $countries = \App\Country::all(['id', 'name']);
            if ($account->document_type_id === '1') {
                return view('account.createBA',compact('documentType','documentNumber','documentTypes','countries'));
            } else {
                return view('account.createPA',compact('documentType','documentNumber','documentTypes','countries'));
            }
        } else {
            return view('account.create',compact('documentTypes'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postCreateAccount(Request $request)
    {
        // se reciben datos desde post
        $validatedData = $request->validate([
            'document_type_id' => 'required|max:1',
            'document_number' => 'required|max:11',
        ]);
        if(empty($request->session()->get('account'))){
            $account= new \App\Account;
            $account->fill($validatedData);
            $request->session()->put('account', $account);
        }else{
            $account = $request->session()->get('account');
            $account->fill($validatedData);
            $request->session()->put('account', $account);
        }

        $documentType = $request->document_type_id;
        $documentNumber = $request->document_number;
        // $documentTypes = \App\DocumentType::all(['id', 'name']);
        $user = auth()->user();
        $tiposDocumentos = DB::table('document_types')
            ->select('document_types.id as tipoId', 'document_types.name as tipoName')
            ->join('users', 'users.id', '=', 'document_types.created_by_id')
            ->where('users.organization_id', $user->organization_id)->get();
        $documentTypes = [];
        $i = 0;
        foreach ($tiposDocumentos as $documento) {
            $documentTypes[$i]['id'] = $documento->tipoId;
            $documentTypes[$i]['name'] = $documento->tipoName;
            ++$i;
        }

        $account = $request->session()->get('account');
        $countries = \App\Country::all(['id', 'name']);
        if ($documentType === '1') {
            return view('account.createBA',compact('documentType','documentNumber','documentTypes','countries'));
        }
        return view('account.createPA',compact('documentType','documentNumber','documentTypes','countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function postCreateBA(Request $request)
    {
        $user = auth()->user();
        $account= new \App\Account;
        $documentType = \App\DocumentType::find($request->get('document_type_id'));
        if ($documentType->is_business === 1) {
            // $validatedData = $request->all();
            $validatedData = $request->validate([
                'document_type_id' => 'required|max:1',
                'document_number' => 'required|max:11',
                'name' => 'required|max:100',
            ]);
            $account->name=$request->get('name');
        } else {
            $validatedData = $request->validate([
                'document_type_id' => 'required|max:1',
                'document_number' => 'required|max:11',
                'first_name' => 'required|max:30',
                'last_name' => 'required|max:30',
            ]);
            $account->first_name=$request->get('first_name');
            $account->last_name=$request->get('last_name');
            $account->name=trim($account->first_name).' '.trim($account->last_name);
        }
        $account->email=$request->get('email');
        $account->movile=$request->get('movile');
        $account->phone=$request->get('phone');
        $account->shipping_address=$request->get('shipping_address');
        $account->document_type_id=$request->get('document_type_id');
        $account->document_number=$request->get('document_number');
        $account->created_by_id=$user->id;
        $message = 'Success';
        // location info fin

        if ($account->save()) {
            // location info
            $location= new \App\Location;
            $location->account_id=$account->id;
            $location->country_id=$request->get('country');
            $location->city=$request->get('city');
            $location->address=$request->get('location_address');
            $location->phone=$request->get('phone');
            $location->created_by_id=$user->id;
            $location->save();
        } else {
            $message = 'An internal server error has ocurred, please contact your administrator';
        }
        $request->session()->forget('account');
        return redirect('accounts')->with('success',$message);
    }
}
