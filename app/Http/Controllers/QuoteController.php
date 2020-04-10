<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Fpdf;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $org = \App\Organization::find($user->organization_id);
        // $primaryCategories = \App\Category::all(['id', 'name']);
        $primaryCategories = DB::table('categories')
            ->select('categories.id', 'categories.name')
            ->join('users', 'users.id', '=', 'categories.created_by_id')
            ->where('categories.status', 1)
            ->where('users.organization_id', $user->organization_id)->get();
        $places = DB::table('places')
            ->select('places.id', 'places.name')
            ->join('users', 'users.id', '=', 'places.created_by_id')
            ->where('places.status', 1)
            ->where('users.organization_id', $user->organization_id)->get();
        return view('quote.index', ['primaryCategories'=> $primaryCategories,
            'places'=> $places,'org'=> $org]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('quote.create');
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
        $datosVenta = DB::table('quotes')
            ->select('quotes.account_id', 'quotes.created_by_id', 'quotes.id as quoteId', 'quotes.quote_date',
                'accounts.name as accountName','users.name as userName','quotes.stage_id','locations.id as locationId',
                'locations.city','locations.address','countries.currency','countries.currency_abbreviation',
                'quotes.aditional_detail',
                'accounts.document_number','document_types.name as documenttype')
            ->join('accounts', 'accounts.id', '=', 'quotes.account_id')
            ->join('locations', 'locations.id', '=', 'quotes.location_id')
            ->join('countries', 'countries.id', '=', 'locations.country_id')
            ->join('document_types', 'document_types.id', '=', 'accounts.document_type_id')
            ->join('users', 'users.id', '=', 'quotes.created_by_id')
            ->where('users.organization_id', $user->organization_id)
            ->where('quotes.id', $id)->first();
        if (!$datosVenta) {
            return redirect('/home');
        }
        $files = DB::table('quotes')
        ->select('quote_files.name', 'quote_files.id')
        ->join('quote_files', 'quote_files.quote_id', '=', 'quotes.id')
        ->join('users', 'users.id', '=', 'quotes.created_by_id')
        ->where('quotes.id', $id)
        ->where('users.organization_id', $user->organization_id)->get();
        $primaryCategories = DB::table('categories')
            ->select('categories.id', 'categories.name')
            ->join('users', 'users.id', '=', 'categories.created_by_id')
            ->where('categories.status', 1)
            ->where('users.organization_id', $user->organization_id)->get();
        $places = DB::select('SELECT places.*
            FROM places
            INNER JOIN users ON users.id = places.created_by_id
            WHERE places.status = ? AND users.organization_id = ?',
            [1,$user->organization_id]);
        return view('quote.edit', ['id'=> $id,'datosVenta'=> $datosVenta,'primaryCategories'=> $primaryCategories,
            'places'=> $places,
            'files'=> $files]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function quotes()
    {
        $user = auth()->user();
        $quotes = DB::table('quotes')
            ->select('quotes.account_id', 'quotes.created_by_id', 'quotes.id as quoteId', 'quotes.quote_date',
                'accounts.name as accountName','users.name as userName','stages.name as stageName',
                'locations.city','locations.address','quotes.stage_id',
                DB::raw("upper(quotes.quote_number) AS quote_number"),
                'accounts.document_number','document_types.name as documenttype')
            ->join('accounts', 'accounts.id', '=', 'quotes.account_id')
            ->join('document_types', 'document_types.id', '=', 'accounts.document_type_id')
            ->join('locations', 'locations.id', '=', 'quotes.location_id')
            ->join('stages', 'stages.id', '=', 'quotes.stage_id')
            ->join('users', 'users.id', '=', 'quotes.created_by_id')
            ->orderByRaw('quotes.updated_at DESC')
            ->where('users.organization_id', $user->organization_id)
            ->paginate(10);
        return view('quote.quotes',compact('quotes'));
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
        $quoteUser = \App\Quote::find($id)->users;
        $quote = \App\Quote::find($id);
        if ($user->organization_id == $quoteUser->organization_id) {
            // try {
            $quoteItem = DB::table('quote_items')->where('quote_id', $id)->get();
            if (!$quoteItem) {
                DB::table('quote_item_packages')->where('quote_id', $id)->delete();
                DB::table('quote_item_categories')->where('quote_id', $id)->delete();
                DB::table('quote_items')->where('quote_id', $id)->delete();
                $quote->delete();
            } else {
            // } catch (\Exception $e) {
                $quote->stage_id=1;
                $quote->save();
            }
        } else {
            return redirect('/home');
        }
        // }
        return redirect('quote/quotes')->with('success','Information has been deleted');
    }

    public function quotePdf(Request $request) {
        $id = $request->id;
        $user = auth()->user();
        $quoteUser = \App\Quote::find($id)->users;
        if ($user->organization_id == $quoteUser->organization_id) {
            $org = \App\Organization::find($user->organization_id);
            $pdf = new Fpdf();
            $data = $this->inforQuoteDetail($id);
            $currency = $data['infoVenta']->currency;
            if ($data['infoVenta']->currency !== '$') {
                $currency = chr(128);
            }
            $pdf::SetMargins(20,20,20);
            $pdf::AliasNbPages();
            $pdf::AddPage();
            $pdf::SetAutoPageBreak('auto','10');

            $pdf::Image(storage_path().'\\app\uploads\\'.$org->id.'\\'.$org->logo,20,15,18,18);
            $pdf::SetFont('Arial','b',18);
            $pdf::Cell(20,5,"",0,"","L");
            $pdf::SetFont('Arial','b',10);
            $pdf::Cell(130,4,utf8_decode($data['infoVenta']->organizationName),0,1,"C");
            $pdf::SetFont('Arial','',10);
            $pdf::Cell(0,4,$data['infoVenta']->phone,0,1,"C");
            $pdf::Cell(0,4,$data['infoVenta']->email,0,1,"C");
            $pdf::Ln();

            $y = $pdf::getY();
            $x = $pdf::getX();
            $pdf::SetXY($x-12,$y-15);

            $pdf::SetFont('Arial','b',18);
            $pdf::Cell(0,10,"Devis",0,"","R");
            $pdf::Ln();
            $pdf::Ln();

            $y = $pdf::getY();
            $x = $pdf::getX();
            $pdf::Line($x, $y, $x+170, $y);
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,5,"Client:",0,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(110,5,utf8_decode($data['infoVenta']->accountName).' - '.$data['infoVenta']->documenttype.' '.$data['infoVenta']->document_number,0,"","L");
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(20,5,utf8_decode("N°").strval($data['infoVenta']->quote_number),0,"","R");
            $pdf::Ln();

            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,5,utf8_decode("Référence du chantier"),0,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(110,5,utf8_decode($data['infoVenta']->address.' '.$data['infoVenta']->city),0,"","L");
            $pdf::cell(20,5,$data['infoVenta']->quote_date,0,"","R");
            $pdf::Ln();

            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,5,utf8_decode("Référence de facturation"),0,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(130,5,utf8_decode($data['infoVenta']->billing_address),0,"","L");
            $pdf::Ln();
            $y = $pdf::getY();
            $x = $pdf::getX();
            $pdf::Line($x, $y, $x+170, $y);

            $pdf::Ln();

            $i = 0;
            foreach ($data['itemsCategories'] as $principal) {
                ++$i;

                $pdf::SetFont('Arial','b',10);
                $pdf::SetFillColor(230,230,230);
                $pdf::cell(170,8,utf8_decode($principal->nombrecategoriaprincipal),0,"","C",true);
                $pdf::Ln();

                $lines = 6;
                $categoryDescription = utf8_decode($principal->description);
                $strLenght = strlen($categoryDescription);
                if ($strLenght > 75 && $strLenght < 151) {
                    $lines = 12;
                } elseif ($strLenght > 150 && $strLenght < 226) {
                    $lines = 18;
                } elseif ($strLenght > 240) {
                    $lines = 24;
                }
                $pdf::SetFont('Arial','',9);
                $pdf::MultiCell(110, $lines, $categoryDescription,0,'L');

                foreach ($data['places'] as $secundaria) {
                    if ($principal->category_id == $secundaria->category_id) {
                        $hasPackageHeader = 0;
                        $countRows = 0;
                        foreach($data['quoteItems'] as $detalleProducto) {
                            if ($secundaria->place_id == $detalleProducto->place_id &&
                                $detalleProducto->category_id == $principal->category_id
                            ) {
                                if ($detalleProducto->package === 0) {
                                    ++$countRows;
                                }
                            }
                        }
                        $pdf::SetFillColor(244,244,244);
                        foreach($data['quoteItems'] as $detalleProducto) {
                            if ($secundaria->place_id == $detalleProducto->place_id &&
                                $detalleProducto->category_id == $principal->category_id
                            ) {
                                if ($hasPackageHeader === 0 && $detalleProducto->package === 0) {
                                    $hasPackageHeader = 1;
                                    $totalPlace = 0;
                                    $pdf::SetFont('Arial','B',10);
                                    $pdf::cell(10,6,'Qua.',"B","","L");
                                    $pdf::cell(110,6,utf8_decode($secundaria->name),"B",0,"L");
                                    $pdf::cell(25,6,'Autres',"B","","R");
                                    $pdf::cell(25,6,'Total',"B","","R");
                                    $pdf::Ln();
                                }
                            }
                        }
                        foreach($data['quoteItems'] as $detalleProducto) {
                            if ($secundaria->place_id == $detalleProducto->place_id &&
                                $detalleProducto->category_id == $principal->category_id
                            ) {
                                if ($detalleProducto->package === 0) {
                                    $fill = true;
                                    if ($countRows % 2 ===0) {
                                        $fill = false;
                                    }
                                    $productName = $detalleProducto->name;
                                    $lines = 6;
                                    $pdf::SetFont('Arial','',9);
                                    if ($detalleProducto->optional == 1) {
                                        $productName = utf8_decode('OPTION: '.$productName.$productName.$productName);
                                        $strLenght = strlen($productName);
                                        if ($strLenght > 75 && $strLenght < 151) {
                                            $lines = 12;
                                        } elseif ($strLenght > 150 && $strLenght < 226) {
                                            $lines = 18;
                                        } elseif ($strLenght > 240) {
                                            $lines = 24;
                                        }
                                        $pdf::cell(10,$lines,$detalleProducto->quantity,0,"","L",$fill);
                                        $pdf::MultiCell(110, 6, $productName,0,'L',$fill);
                                    } elseif ($detalleProducto->description != null) {
                                        $productName = utf8_decode($productName.' - '.$detalleProducto->description);
                                        $strLenght = strlen($productName);
                                        if ($strLenght > 75 && $strLenght < 151) {
                                            $lines = 12;
                                        } elseif ($strLenght > 150 && $strLenght < 226) {
                                            $lines = 18;
                                        } elseif ($strLenght > 240) {
                                            $lines = 24;
                                        }
                                        $pdf::cell(10,$lines,$detalleProducto->quantity,0,"","L",$fill);
                                        $pdf::MultiCell(110, 6, $productName,0,'L',$fill);
                                    } else {
                                        $productName = utf8_decode($productName);
                                        $strLenght = strlen($productName);
                                        if ($strLenght > 75 && $strLenght < 151) {
                                            $lines = 12;
                                        } elseif ($strLenght > 150 && $strLenght < 226) {
                                            $lines = 18;
                                        } elseif ($strLenght > 240) {
                                            $lines = 24;
                                        }
                                        $pdf::cell(10,$lines,$detalleProducto->quantity,0,"","L",$fill);
                                        $pdf::MultiCell(110, 6, $productName,0,'L',$fill);
                                    }
                                    $y = $pdf::getY();
                                    $x = $pdf::getX();
                                    $pdf::SetXY($x+120,$y-$lines);
                                    if ($detalleProducto->offer == 1) {
                                        $pdf::cell(50,$lines,'Oferta',0,"","R",$fill);
                                    } elseif ($detalleProducto->package === 0) {
                                        if ($detalleProducto->other != 0) {
                                            $pdf::cell(25,$lines,$currency.' '.number_format($detalleProducto->other,2,',','.'),0,"","R",$fill);
                                            $totalPlace = $totalPlace + $detalleProducto->other;
                                        } else {
                                            $pdf::cell(25,$lines,'',0,"","R",$fill);
                                        }
                                        $pdf::cell(25,$lines,$currency.' '.number_format($detalleProducto->price,2,',','.'),0,"","R",$fill);
                                        if ($detalleProducto->optional === 0) {
                                            $totalPlace = $totalPlace + $detalleProducto->price;
                                        }
                                        // <td colspan="1"></td>
                                    } else {
                                        $pdf::cell(50,$lines,'',0,"","R",$fill);
                                    }
                                    // <td colspan="1">
                                    if ($data['infoVenta']->stage_id === 2) {
                                        // botones
                                    }
                                    // </td>
                                    $pdf::Ln();
                                    --$countRows;
                                }
                            }
                        }
                        if ($hasPackageHeader === 1) {
                            $pdf::SetFont('Arial','',9);
                            $pdf::cell(170,6,$currency.' '.number_format($totalPlace,2,',','.'),"T","","R");
                            $pdf::Ln();
                            $pdf::Ln();
                            // </tbody>
                        }
                    }
                }
                foreach($data['places'] as $secundaria) {
                    if ($principal->category_id == $secundaria->category_id) {
                        $hasPackageHeader = 0;
                        foreach($data['itemsPackages'] as $itemPackage) {
                        $hasPackage = 0;
                        $countRows = 0;
                        foreach ($data['quoteItems'] as $detalleProducto) {
                            if ($secundaria->place_id == $detalleProducto->place_id &&
                                $detalleProducto->category_id == $principal->category_id
                            ) {
                                if ($detalleProducto->package === 1 &&
                                    $secundaria->place_id == $itemPackage->place_id &&
                                    $detalleProducto->category_id == $itemPackage->category_id
                                ) {
                                    ++$countRows;
                                }
                            }
                        }
                        $pdf::SetFillColor(244,244,244);
                        foreach($data['quoteItems'] as $detalleProducto) {
                            if ($secundaria->place_id == $detalleProducto->place_id &&
                                $detalleProducto->category_id == $principal->category_id
                            ) {
                                if ($hasPackageHeader === 0 && $detalleProducto->package === 1) {
                                    $hasPackageHeader = 1;
                                    $pdf::SetFont('Arial','B',9);
                                    $pdf::cell(10,6,'Qua.',"B","","L");
                                    $pdf::cell(110,6,utf8_decode($secundaria->name),"B",0,"L");
                                    $pdf::cell(25,6,'Autres',"B","","R");
                                    $pdf::cell(25,6,'Total',"B","","R");
                                    $pdf::Ln();
                                }
                            }
                        }
                        foreach ($data['quoteItems'] as $detalleProducto) {
                            if ($secundaria->place_id == $detalleProducto->place_id &&
                                $detalleProducto->category_id == $principal->category_id
                            ) {
                                if ($detalleProducto->package === 1 &&
                                    $secundaria->place_id == $itemPackage->place_id &&
                                    $detalleProducto->category_id == $itemPackage->category_id
                                ) {
                                    $fill = true;
                                    if ($countRows % 2 ===0) {
                                        $fill = false;
                                    }
                                    $hasPackage = 1;
                                    $productName = $detalleProducto->name;
                                    $lines = 6;
                                    $pdf::SetFont('Arial','',9);
                                    if ($detalleProducto->optional == 1) {
                                        $productName = utf8_decode('OPTION: '.$productName.$productName.$productName);
                                        $strLenght = strlen($productName);
                                        if ($strLenght > 75 && $strLenght < 151) {
                                            $lines = 12;
                                        } elseif ($strLenght > 150 && $strLenght < 226) {
                                            $lines = 18;
                                        } elseif ($strLenght > 240) {
                                            $lines = 24;
                                        }
                                        $pdf::cell(10,$lines,$detalleProducto->quantity,0,"","L",$fill);
                                        $pdf::MultiCell(110, 6, $productName,0,'L',$fill);
                                    } elseif ($detalleProducto->description != null) {
                                        $productName = utf8_decode($productName.' - '.$detalleProducto->description);
                                        $strLenght = strlen($productName);
                                        if ($strLenght > 75 && $strLenght < 151) {
                                            $lines = 12;
                                        } elseif ($strLenght > 150 && $strLenght < 226) {
                                            $lines = 18;
                                        } elseif ($strLenght > 240) {
                                            $lines = 24;
                                        }
                                        $pdf::cell(10,$lines,$detalleProducto->quantity,0,"","L",$fill);
                                        $pdf::MultiCell(110, 6, $productName,0,'L',$fill);
                                    } else {
                                        $productName = utf8_decode($productName);
                                        $strLenght = strlen($productName);
                                        if ($strLenght > 75 && $strLenght < 151) {
                                            $lines = 12;
                                        } elseif ($strLenght > 150 && $strLenght < 226) {
                                            $lines = 18;
                                        } elseif ($strLenght > 225) {
                                            $lines = 24;
                                        }
                                        $pdf::cell(10,$lines,$detalleProducto->quantity,0,"","L",$fill);
                                        $pdf::MultiCell(110, 6, $productName,0,'L',$fill);
                                    }
                                    $y = $pdf::getY();
                                    $x = $pdf::getX();
                                    $pdf::SetXY($x+120,$y-$lines);
                                    $pdf::cell(50,$lines,'',0,1,"L",$fill);
                                    if ($detalleProducto->offer == 1) {
                                        $pdf::cell(50,$lines,'Oferta',0,"","R",$fill);
                                    } elseif ($detalleProducto->package === 0) {
                                        // no entra
                                        // $pdf::cell(20,6,$currency.' '.utf8_decode($detalleProducto->price),1,"","L");
                                    } else {
                                        // <td colspan="2">
                                        // </td>
                                    }
                                    // <td colspan="1">
                                    if ($data['infoVenta']->stage_id === 2) {
                                        // <button onclick="editItem(this)" value="{{$detalleProducto->id}}">Edit</button>
                                        // <button onclick="removeItem(this)" value="{{$detalleProducto->id}}">Remove</button>
                                    }
                                    // </td>
                                    --$countRows;
                                }
                            }
                        }
                        if ($hasPackage === 1) {
                            if ($data['infoVenta']->stage_id === 2) {
                            } else {
                            // <td colspan="4"></td>
                            // <td colspan="1"></td>
                            $pdf::SetFont('Arial','',9);
                            $pdf::cell(170,6,$currency.' '.number_format($itemPackage->value,2,',','.'),"T","","R");
                            $pdf::Ln();
                            $pdf::Ln();
                            }
                        }
                        }
                    }
                }
            }


            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"MONTANT HT:","LT","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currency.' '.$data['totales']['HT'],"TR","","R");
            $pdf::Ln();

            if ($data['totales']['descuentos'] < 0) {
                $pdf::SetFont('Arial','b',9);
                $pdf::cell(110,5,"",0,"","L");
                $pdf::cell(30,5,utf8_decode("Supplémentaire:"),"LT","","R");
                $pdf::SetFont('Arial','',9);
                $pdf::cell(30,5,$currency.' '.$data['totales']['descuentos'],"TR","","R");
                $pdf::Ln();
            }
            
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"VTA ".$data['infoVenta']->tax."%:","LT","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currency.' '.$data['totales']['tax'],"TR","","R");
            $pdf::Ln();
            
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"MONTANT TTC:","LTB","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currency.' '.$data['totales']['total'],"TRB","","R");
            $pdf::Ln(10);

            $pdf::SetFont('Arial','b',9);
            $pdf::MultiCell(0, 5, utf8_decode($data['infoVenta']->aditional_detail),0,'L');
            $pdf::Ln(10);

            $y = $pdf::getY();
            $x = $pdf::getX();
            $pdf::Line($x+10, $y, $x+75, $y);
            $pdf::Line($x+95, $y, $x+160, $y);

            $pdf::SetFont('Arial','',9);
            $pdf::cell(85,5,utf8_decode("Le client"),0,"","C");
            $pdf::cell(85,5,utf8_decode("L'entreprise"),0,1,"C");
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(85,5,utf8_decode("Lu et approuvé, bon pour accord"),0,"","C");
            $pdf::cell(85,5,utf8_decode("Lu et approuvé, bon pour accord"),0,1,"C");


            // $imagePath = url('uploads/1_logo.png');
            // exit();
            // $pdf::Image($imagePath, 20, 20, 18, 18);
            // $pdf::Image(url('uploads/1_logo.png'), 20, 20, 18, 18);
            // $pdf::Image('http://localhost:8000/uploads/1_logo.png', 20, 20, 18, 18);
            // http://localhost:8000/uploads/1_logo.png



            $pdf::Output();
            exit;
        }
    }

    public function invoicePdf($id) {
        $user = auth()->user();
        $quoteUser = \App\Quote::find($id)->users;
        $quote = \App\Quote::find($id);
        if ($user->organization_id == $quoteUser->organization_id) {
            $pdf = new Fpdf();
            $pdf::SetMargins(10,10,10);
            $pdf::AliasNbPages();
            $pdf::AddPage();
            $pdf::SetAutoPageBreak('auto','10');

            // $imagePath = asset('storage/Libardo Henao.jpg');
            
            // $pdf::Image(storage_path().'\app\uploads\1\1_logo.png',10,10,18,18,'png');

            $pdf::SetFont('Arial','b',18);
            $pdf::Cell(0,10,"Factura",0,"","C");
            $pdf::Ln();
            $pdf::Ln();

            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,8,"Nom Du Client:",1,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(55,8,"Mme. Boulle",1,"","L");
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,8,"N. Factura",1,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(55,8,"00007622",1,"","L");
            $pdf::Ln();

            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,8,"Direccion",1,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(55,8,"6 Mail Des Betulas",1,"","L");
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,8,"Fecha",1,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(55,8,"02/03/2019",1,"","L");
            $pdf::Ln();

            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,8,"Code Postal et Ville:",1,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(55,8,"78180 Montigny le Bretonneux",1,"","L");
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,8,"Facture Entreprise:",1,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(55,8,"Vir Transport",1,"","L");
            $pdf::Ln();

            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,8,"Telephone:",1,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(55,8,"78180 Montigny le Bretonneux",1,"","L");
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(40,8,"Adresse de Facturation:",1,"","L");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(55,8,"370-380 Avenue de Paris",1,"","L");
            $pdf::Ln();
            $pdf::Ln();
            $pdf::Ln();

            $pdf::SetFont('Arial','b',9);
            $pdf::cell(95,8,"",0,0,"L");
            $pdf::cell(40,8,"TOTAL:",0,0,"R");
            $pdf::cell(55,8,"5.000",1,0,"R");
            $pdf::Ln();
            
            $pdf::Output();
            exit;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function findAccount(Request $request)
    {
        $user = auth()->user();
        $filter = '%'.$request->document.'%';
        $filter2 = '%'.$request->document.'%';
        $accounts = DB::select('SELECT accounts.*,document_types.name AS documentTypeName,
                accounts.document_number
            FROM accounts
            INNER JOIN document_types ON document_types.id = accounts.document_type_id
            INNER JOIN users ON users.id = accounts.created_by_id
            WHERE accounts.name LIKE ? OR accounts.document_number LIKE ? AND users.organization_id = ?
            LIMIT 10',
            [$filter,$filter,$user->organization_id]);
        $searchLocation = array();
        foreach ($accounts as $value) {
            array_push($searchLocation, $value->id);
        }
        $locations = DB::table('locations')
            ->select('locations.id','locations.account_id','locations.city','locations.address')
            ->join('accounts', 'accounts.id', '=', 'locations.account_id')
            ->join('countries', 'countries.id', '=', 'locations.country_id')
            ->join('users', 'users.id', '=', 'locations.created_by_id')
            ->whereIn('locations.account_id',$searchLocation)
            ->where('users.organization_id', $user->organization_id)
            ->get();


        $returnHTML = view('account.searchedData',['accounts'=> $accounts,'locations'=> $locations])->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function findProduct(Request $request)
    {
        $user = auth()->user();
        $limite = 3;
        if ($request->limit) {
            $limite = 10;
        }
        $product = '%'.$request->product.'%';
        $products = DB::select('SELECT products.name,products.id,
            products.code,products.price
            FROM products
            INNER JOIN users ON users.id = products.created_by_id
            WHERE products.name LIKE ? OR products.code LIKE ? AND users.organization_id = ? LIMIT ?',
            [$product,$product,$user->organization_id,$limite]);
        if ($request->limit) {
            $returnHTML = view('product.searchedDataModal',['products'=> $products])->render();
        } else {
            $returnHTML = view('product.searchedData',['products'=> $products])->render();
        }
        return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function choiceProduct(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;
        $id = $request->id;
        $productoUser = \App\Product::find($id)->users;
        $producto = \App\Product::find($id);
        if ($user->organization_id == $productoUser->organization_id) {
            if ($producto) {
                $success = true;
                $location = DB::table('locations')
                    ->select('locations.id','locations.account_id','countries.currency_abbreviation')
                    ->join('accounts', 'accounts.id', '=', 'locations.account_id')
                    ->join('countries', 'countries.id', '=', 'locations.country_id')
                    ->join('users', 'users.id', '=', 'locations.created_by_id')
                    ->where('locations.id',$request->locationId)
                    ->where('users.organization_id', $user->organization_id)
                    ->first();
                if ($location->currency_abbreviation === 'EUR') {
                    $producto->price = $producto->price;
                } elseif ($location->currency_abbreviation === 'USD') {
                    $producto->price = $producto->priceusd;
                }
                $returnResult['info'] = $producto;
                $returnResult['message'] = 'Success';
            } else {
                $returnResult['message'] = 'An internal server erros has ocurred, please contact your administrator';
            }
        } else {
            $returnResult['message'] = 'You have no permissions on this record';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage. se remueve funcionalidad, no utilizado
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSecondCategory(Request $request)
    {
        $user = auth()->user();
        $returnHTML = [];
        $success = false;
        $id = $request->id;
        $categories = DB::select('SELECT places.*
            FROM places
            INNER JOIN users ON users.id = places.created_by_id
            WHERE places.category_id = ? AND users.organization_id = ?',
            [$id,$user->organization_id]);
        if ($categories) {
            $quoteDetail = DB::table('quote_items')
                ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
                ->join('places', 'places.id', '=', 'quote_items.place_id')
                ->join('categories', 'categories.id', '=', 'quote_items.category_id')
                ->join('quote_item_categories', function ($join) {
                    $join->on('quote_item_categories.category_id', '=', 'categories.id')
                    ->on('quote_item_categories.quote_id', '=', 'quotes.id');
                })
                ->join('products', 'products.id', '=', 'quote_items.product_id')
                ->join('users', 'users.id', '=', 'quotes.created_by_id')
                ->select('quote_item_categories.description')
                ->where('users.organization_id', $user->organization_id)
                ->where('quote_item_categories.quote_id', $request->quoteId)
                ->where('quote_item_categories.category_id', $id)->first();

            $returnHTML['html'] = view('place.getCategories',['categories'=> $categories])->render();
            $returnHTML['info'] = $quoteDetail;
            $success = true;
        } else {
            $returnHTML['html'] = '';
            $returnHTML['info'] = null;
        }
        return response()->json(array('success' => $success, 'data'=>$returnHTML));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function finishSale(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;
        $id = $request->id;
        $quoteUser = \App\Quote::find($id)->users;
        $quote = \App\Quote::find($id);
        if ($user->organization_id == $quoteUser->organization_id) {
            $quote->stage_id=3;
            $quote->aditional_detail=$request->aditional_detail;
            $quote->quote_date=$request->quote_date;
            if ($quote->save()) {
                $returnResult['message'] = 'Success';
                $success = true;
            } else {
                $returnResult['message'] = 'An internal server erros has ocurred, please contact your administrator';
            }
        } else {
            $returnResult['message'] = 'You have no permissions on this record';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addProduct(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;
        $today = date("Y-m-d");
        if ($request->place) {
            if (!$request->quoteId) {
                $quote_number = DB::table('quotes')->max('quote_number');
                if ($quote_number === 0) {
                    $org = \App\Organization::find($user->organization_id);
                    $quote_number = --$org->quote_number;
                }

                $accountData = DB::table('accounts')
                    ->select('accounts.id as account_id',
                        'record_types.tax')
                    ->join('document_types', 'document_types.id', '=', 'accounts.document_type_id')
                    ->join('record_types', 'record_types.id', '=', 'document_types.record_type_id')
                    ->join('locations', 'locations.id', '=', 'accounts.location_id')
                    ->join('users', 'users.id', '=', 'accounts.created_by_id')
                    ->where('users.organization_id', $user->organization_id)
                    ->where('locations.id', $user->locationId)
                    ->where('quotes.id', $id)->first();

                $locationId = $request->locationId;
                $quote = new \App\Quote;
                $quote->account_id=$accountData->account_id;
                $quote->location_id=$locationId;
                $quote->quote_number=++$quote_number;
                $quote->aditional_detail=$request->aditional_detail;
                $quote->quote_date=$request->quote_date;
                $quote->tax=$accountData->tax;
                $quote->created_by_id=$user->id;
                $quote->stage_id=2;
                $quote->save();
            }
            $quoteId = $request->quoteId ? $request->quoteId : $quote->id;
            if ($quoteId) {

                $quoteItem= new \App\QuoteItem;
                $quoteUser = \App\Quote::find($quoteId)->users;
                $quote = \App\Quote::find($quoteId);
                if ($user->organization_id == $quoteUser->organization_id) {
                    $quoteItem->quote_id=$quoteId;
                    $quoteItem->product_id=$request->id;
                    $quoteItem->quantity=$request->quantity;
                    $quoteItem->description=$request->description;
                    $quoteItem->place_id=$request->place;
                    $quoteItem->category_id=$request->category;
                    if ($request->price){
                        $quoteItem->price=$request->price;
                    }
                    $quoteItem->package=0;
                    if ($request->package === "true"){
                        $quoteItem->package=1;
                        
                        $itemPackage = DB::table('quote_item_packages')
                            ->where('quote_id', $quoteId)
                            ->where('place_id', $request->place)
                            ->where('category_id', $request->category)
                            ->first();
                        if (!$itemPackage) {
                            $itemPackage = new \App\QuoteItemPackage;
                            $itemPackage->quote_id=$quoteId;
                            $itemPackage->place_id=$request->place;
                            $itemPackage->category_id=$request->category;
                            // $itemPackage->created_at = date('Y-m-d H:i:s');
                            // $itemPackage->updated_at = date('Y-m-d H:i:s');
                            $itemPackage->value=0;
                            $itemPackage->save();
                        }
                    } else {
                        if ($request->optional === "true" || $request->offer === "true"){
                            if ($request->other) {
                                $quoteItem->other = 0;
                            }
                        } else {
                            $quoteItem->other=$request->other !== null ? $request->other : 0;
                        }
                        if ($request->optional === "true"){
                            $quoteItem->optional=1;
                        } else {
                            $quoteItem->optional=0;
                        }
                        if ($request->offer === "true"){
                            $quoteItem->offer=1;
                        } else {
                            $quoteItem->offer=0;
                        }
                    }
                    if ($quote->stage_id === 2 && $quoteItem->save()) {
                        $quote->aditional_detail=$request->aditional_detail;
                        $quote->quote_date=$request->quote_date;
                        $quote->updated_at=date('Y-m-d H:i:s');
                        $quote->save();

                        // $product = DB::table('categories')
                        //     ->select('categories.id')
                        //     ->join('places',
                        //         'places.category_id', '=', 'categories.id')
                        //     ->join('users', 'users.id', '=', 'categories.created_by_id')
                        //     ->limit(1)
                        //     ->where('places.id', $request->place)
                        //     ->where('users.organization_id', $user->organization_id)->get();
                        \App\QuoteItemCategory::updateOrCreate(
                            ['quote_id' => $quoteId, 'category_id' => $request->category],
                            ['description' => $request->descripcionCat]
                        );
                        $returnResult = $this->QuoteDetail($quoteItem->quote_id);
                        $returnResult['quoteId'] = $quoteId;
                        $success = true;
                    } else {
                        $returnResult['message'] = 'Error while adding item, please contact your administrator';
                    }
                } else {
                    $returnResult['message'] = 'You have no permissions on this record';
                }
            } else {
                $returnResult['message'] = 'Error while saving record, please contact your administrator';
            }
        } else {
            $returnResult['message'] = 'Debe seleccionar un lugar';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateQuoteDetail(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;
        if ($request->place) {
            $quoteItem = \App\QuoteItem::find($request->id);
            $quoteUser = \App\Quote::find($quoteItem->quote_id)->users;
            $quote = \App\Quote::find($quoteItem->quote_id);
            if ($user->organization_id == $quoteUser->organization_id) {
                $quoteItem->product_id=$request->productId;
                $quoteItem->quantity=$request->quantity !== null ? $request->quantity : 1;
                $quoteItem->description=$request->description;
                $quoteItem->place_id=$request->place;
                $quoteItem->category_id=$request->category;
                $quoteItem->package=0;
                $currentPlace = 0;
                $currentCategory = 0;
                if ($request->package === "true"){
                    $quoteItem->package=1;
                    
                    $itemPackage = DB::table('quote_item_packages')
                        ->where('quote_id', $quoteItem->quote_id)
                        ->where('place_id', $request->place)
                        ->where('category_id', $request->category)
                        ->first();
                    if (!$itemPackage) {
                        $currentPlace = $itemPackage->place_id;
                        $currentCategory = $itemPackage->category_id;
                        $itemPackage = new \App\QuoteItemPackage;
                        $itemPackage->quote_id=$quoteItem->quote_id;
                        $itemPackage->place_id=$request->place;
                        $itemPackage->category_id=$request->category;
                        $itemPackage->value=0;
                        $itemPackage->save();
                    }
                } else {
                    if ($request->optional === "true" || $request->offer === "true"){
                        if ($request->other) {
                            $quoteItem->other = 0;
                        }
                    } else {
                        $quoteItem->other=$request->other !== null ? $request->other : 0;
                    }
                    if ($request->optional === "true"){
                        $quoteItem->optional=1;
                    } else {
                        $quoteItem->optional=0;
                    }
                    if ($request->offer === "true"){
                        $quoteItem->offer=1;
                    } else {
                        $quoteItem->offer=0;
                    }

                }
                if ($quote->stage_id === 2 && $quoteItem->save()) {
                    $quote->updated_at = date('Y-m-d H:i:s');
                    $quote->save();
                    $itemCategory = \App\QuoteItemCategory::find($request->itemCategory);
                    $quoteDetail = DB::table('quote_items')
                        ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
                        ->join('places', 'places.id', '=', 'quote_items.place_id')
                        ->join('categories', 'categories.id', '=', 'quote_items.category_id')
                        ->join('quote_item_categories', function ($join) {
                            $join->on('quote_item_categories.category_id', '=', 'categories.id')
                            ->on('quote_item_categories.quote_id', '=', 'quotes.id');
                        })
                        ->join('products', 'products.id', '=', 'quote_items.product_id')
                        ->join('users', 'users.id', '=', 'quotes.created_by_id')
                        ->select('quote_item_categories.id')
                        ->where('users.organization_id', $user->organization_id)
                        ->where('quote_item_categories.quote_id', $quoteItem->quote_id)
                        ->where('quote_item_categories.category_id', $itemCategory->category_id)->first();
                    if (!$quoteDetail && $request->category !== $itemCategory->category_id) {
                        $itemCategory->delete();
                    }
                    \App\QuoteItemCategory::updateOrCreate(
                        ['quote_id' => $quoteItem->quote_id, 'category_id' => $request->category],
                        ['description' => $request->categoryDescription]
                    );
                    $tienePaquete = DB::select('SELECT v.id
                        FROM quotes v
                        INNER JOIN quote_items dv on dv.quote_id = v.id and dv.package = 1
                        INNER JOIN quote_item_packages dp on dp.place_id = dv.place_id
                        INNER JOIN users ON users.id = v.created_by_id
                        WHERE dv.quote_id = ? AND dp.place_id = ?
                            AND users.organization_id = ?',
                        [$quoteItem->quote_id,$currentPlace,$user->organization_id]);
                    $itemPackage = DB::table('quote_item_packages')
                        ->select('quote_item_packages.id')
                        ->join('quotes', 'quotes.id', '=', 'quote_item_packages.quote_id')
                        ->join('users', 'users.id', '=', 'quotes.created_by_id')
                        ->where('quote_item_packages.place_id', $currentPlace)
                        ->where('quote_item_packages.category_id', $currentCategory)
                        ->where('quote_item_packages.quote_id', $quoteItem->quote_id)
                        ->where('users.organization_id', $user->organization_id)->first();
                    if (empty($tienePaquete) && !empty($itemPackage)) {
                        $itemPackage = \App\QuoteItemPackage::find($itemPackage->id);
                        $itemPackage->delete();
                    }
                    $returnResult = $this->QuoteDetail($quoteItem->quote_id);
                    $success = true;
                } else {
                    $returnResult['message'] = 'An internal server erros has ocurred, please contact your administrator';
                }
            } else {
                $returnResult['message'] = 'You have no permissions on this record';
            }
        } else {
            $returnResult['message'] = 'Debe seleccionar un lugar';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function getQuoteItems($id)
    {
        $user = auth()->user();
        $quoteItems = DB::select('SELECT dv.id,dv.quantity,dv.price,dv.other,dv.optional,dv.offer,
                                            dv.product_id,p.name,dv.description,dv.invoice,dv.package,
                                            dv.place_id,cs.name as secundariaNombre,
                                            dv.category_id, cp.name as principalNombre
                                        FROM quotes v
                                        INNER JOIN quote_items dv on dv.quote_id = v.id
                                        INNER JOIN places cs on cs.id = dv.place_id
                                        INNER JOIN categories cp on cp.id = dv.category_id
                                        INNER JOIN products p on p.id = dv.product_id
                                        INNER JOIN users ON users.id = v.created_by_id
                                        WHERE dv.quote_id = ? AND users.organization_id = ?',
                                        [$id,$user->organization_id]);
        return $quoteItems;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function getQuoteVales($id)
    {
        $user = auth()->user();
        $valoresVenta = DB::select('SELECT SUM(dv.quantity*dv.price) AS subTotal,
                                        SUM(dv.other) AS descuentos
                                    FROM quote_items dv
                                    INNER JOIN quotes v ON v.id = dv.quote_id
                                    INNER JOIN users ON users.id = v.created_by_id
                                    WHERE dv.quote_id = ? AND dv.offer <> 1
                                        AND dv.package = 0 AND dv.optional <> 1 AND users.organization_id = ?',
                                        [$id,$user->organization_id]);
        return $valoresVenta;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function getItemsCategories($id)
    {
        $user = auth()->user();
        $itemsCategories = DB::select('SELECT dc.id,dc.category_id,cp.name as nombrecategoriaprincipal,
                                dc.description
                                FROM quote_item_categories dc
                                INNER JOIN categories cp on cp.id = dc.category_id
                                INNER JOIN quotes v ON v.id = dc.quote_id
                                INNER JOIN users ON users.id = v.created_by_id
                                WHERE dc.quote_id = ? AND users.organization_id = ?',
                                [$id,$user->organization_id]);
        return $itemsCategories;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function getItemsPackages($id)
    {
        $user = auth()->user();
        $itemsPackages = DB::select('SELECT dp.id, dp.place_id, dp.value,dp.category_id
                                FROM quote_item_packages dp
                                INNER JOIN places cs on cs.id = dp.place_id
                                INNER JOIN quotes v ON v.id = dp.quote_id
                                INNER JOIN users ON users.id = v.created_by_id
                                WHERE dp.quote_id = ? AND users.organization_id = ?',
                                [$id,$user->organization_id]);
        return $itemsPackages;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function getInfoVenta($id)
    {
        $user = auth()->user();
        $infoVenta = DB::table('quotes')
            ->select('quotes.id as quote_id','quotes.account_id', 'quotes.created_by_id',
                'quotes.id as quoteId', 'quotes.quote_date','countries.currency',
                'accounts.name as accountName','users.name as userName',
                'quotes.tax','quotes.stage_id',
                DB::raw("upper(quotes.quote_number) AS quote_number"),
                'quotes.aditional_detail','organizations.email',
                'locations.address','accounts.billing_address',
                'locations.city','quotes.quote_date','organizations.phone',
                'organizations.name as organizationName',
                'accounts.document_number','document_types.name as documenttype')
            ->join('accounts', 'accounts.id', '=', 'quotes.account_id')
            ->join('document_types', 'document_types.id', '=', 'accounts.document_type_id')
            ->join('record_types', 'record_types.id', '=', 'document_types.record_type_id')
            ->join('users', 'users.id', '=', 'quotes.created_by_id')
            ->join('locations', 'locations.id', '=', 'quotes.location_id')
            ->join('countries', 'countries.id', '=', 'locations.country_id')
            ->join('organizations', 'organizations.id', '=', 'users.organization_id')
            ->where('users.organization_id', $user->organization_id)
            ->where('quotes.id', $id)->first();
        return $infoVenta;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function getPlaces($id)
    {
        $user = auth()->user();
        $places = DB::select('SELECT cs.name,dv.place_id,
                cp.id as category_id,dv.category_id
            FROM quotes v
            INNER JOIN quote_items dv ON dv.quote_id = v.id
            INNER JOIN places cs on cs.id = dv.place_id
            INNER JOIN categories cp on cp.id = dv.category_id
            INNER JOIN users ON users.id = v.created_by_id
            WHERE dv.quote_id = ? AND users.organization_id = ?
            GROUP BY cs.name,dv.place_id,cp.id,dv.category_id',
            [$id,$user->organization_id]);
        return $places;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function QuoteDetail($id)
    {
        $user = auth()->user();
        $organization = \App\Organization::find($user->organization_id);
        $returnResult = [];
        $quoteItems = $this->getQuoteItems($id);
        $valoresVenta = $this->getQuoteVales($id);
        $itemsCategories = $this->getItemsCategories($id);
        $itemsPackages = $this->getItemsPackages($id);
        $infoVenta = $this->getInfoVenta($id);
        $places = $this->getPlaces($id);
        $totales = [];
        $value = $valoresVenta[0];
        $aumentos = $value->descuentos;
        $total = $value->subTotal + $aumentos;
        $subTotal = $value->subTotal;
        $packageValue = 0;
        foreach ($itemsPackages as $value) {
            $packageValue += $value->value;
        }
        $total = $total + $packageValue;
        $HT = $total - (($infoVenta->tax * $total) / 100);
        $subTotal = $subTotal + $packageValue;
        $totales['tax'] = ($infoVenta->tax * $total) / 100;
        $totales['subTotal'] = number_format($subTotal, 2, ',', '.');
        $totales['tax'] = number_format($totales['tax'], 2, ',', '.');
        $totales['descuentos'] = number_format($aumentos, 2, ',', '.');
        $totales['total'] = number_format($total, 2, ',', '.');
        $totales['HT'] = number_format($HT, 2, ',', '.');

        $returnResult['html'] = view('quote.quoteItems',['quoteItems'=> $quoteItems,
                                        'itemsCategories'=> $itemsCategories, 'totales' => $totales,
                                        'infoVenta' => $infoVenta,
                                        'itemsPackages' => $itemsPackages,
                                        'places'=> $places])->render();
        $returnResult['message'] = 'Success';

        return $returnResult;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function inforQuoteDetail($id)
    {
        $user = auth()->user();
        $organization = \App\Organization::find($user->organization_id);
        $returnResult = [];
        $quoteItems = $this->getQuoteItems($id);
        $valoresVenta = $this->getQuoteVales($id);
        $itemsCategories = $this->getItemsCategories($id);
        $itemsPackages = $this->getItemsPackages($id);
        $infoVenta = $this->getInfoVenta($id);
        $places = $this->getPlaces($id);
        $totales = [];
        $value = $valoresVenta[0];
        $aumentos = $value->descuentos;
        $total = $value->subTotal + $aumentos;
        $subTotal = $value->subTotal;
        $packageValue = 0;
        foreach ($itemsPackages as $value) {
            $packageValue += $value->value;
        }
        $total = $total + $packageValue;
        $tax = ($infoVenta->tax * $total) / 100;
        $HT = $total - $tax;
        $subTotal = $subTotal + $packageValue;
        $totales['subTotal'] = number_format($subTotal, 2, ',', '.');
        $totales['tax'] = number_format($tax, 2, ',', '.');
        $totales['descuentos'] = number_format($aumentos, 2, ',', '.');
        $totales['total'] = number_format($total, 2, ',', '.');
        $totales['HT'] = number_format($HT, 2, ',', '.');

        $returnResult['quoteItems'] = $quoteItems;
        $returnResult['itemsCategories'] = $itemsCategories;
        $returnResult['totales'] = $totales;
        $returnResult['infoVenta'] = $infoVenta;
        $returnResult['itemsPackages'] = $itemsPackages;
        $returnResult['places'] = $places;

        return $returnResult;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function loadQuoteDetail(Request $request)
    {
        $id = $request->id;
        $returnResult = [];

        $places = $this->getPlaces($id);
        $quoteItems = $this->getQuoteItems($id);
        $valoresVenta = $this->getQuoteVales($id);
        $itemsCategories = $this->getItemsCategories($id);
        $itemsPackages = $this->getItemsPackages($id);
        $infoVenta = $this->getInfoVenta($id);

        $totales = [];
        $value = $valoresVenta[0];
        $aumentos = $value->descuentos;
        $total = $value->subTotal + $aumentos;
        $subTotal = $value->subTotal;
        $packageValue = 0;
        foreach ($itemsPackages as $value) {
            $packageValue += $value->value;
        }
        $total = $total + $packageValue;
        $HT = $total - (($infoVenta->tax * $total) / 100);
        $subTotal = $subTotal + $packageValue;
        $totales['tax'] = ($infoVenta->tax * $total) / 100;
        $totales['subTotal'] = number_format($subTotal, 2, ',', '.');
        $totales['tax'] = number_format($totales['tax'], 2, ',', '.');
        $totales['descuentos'] = number_format($aumentos, 2, ',', '.');
        $totales['total'] = number_format($total, 2, ',', '.');
        $totales['HT'] = number_format($HT, 2, ',', '.');

        $returnResult['html'] = view('quote.quoteItems',['quoteItems'=> $quoteItems,
                                        'itemsCategories'=> $itemsCategories, 'totales' => $totales,
                                        'infoVenta' => $infoVenta,
                                        'itemsPackages' => $itemsPackages,
                                        'places'=> $places])->render();
        
        $returnResult['message'] = 'Success';
        $success = true;

        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage. para facturacion
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function loadQuoteDetail2(Request $request)
    {
        $id = $request->id;
        $returnResult = [];

        $quoteItems = $this->getQuoteItems($id);
        $valoresVenta = $this->getQuoteVales($id);
        $itemsCategories = $this->getItemsCategories($id);
        $itemsPackages = $this->getItemsPackages($id);
        $infoVenta = $this->getInfoVenta($id);
        $places = $this->getPlaces($id);

        $totales = [];
        $value = $valoresVenta[0];
        $aumentos = $value->descuentos;
        $total = $value->subTotal + $aumentos;
        $subTotal = $value->subTotal;
        $packageValue = 0;
        foreach ($itemsPackages as $value) {
            $packageValue += $value->value;
        }
        $total = $total + $packageValue;
        $HT = $total - (($infoVenta->tax * $total) / 100);
        $subTotal = $subTotal + $packageValue;
        $totales['tax'] = ($infoVenta->tax * $total) / 100;
        $totales['subTotal'] = number_format($subTotal, 2, ',', '.');
        $totales['tax'] = number_format($totales['tax'], 2, ',', '.');
        $totales['descuentos'] = number_format($aumentos, 2, ',', '.');
        $totales['total'] = number_format($total, 2, ',', '.');
        $totales['HT'] = number_format($HT, 2, ',', '.');

        $returnResult['html'] = view('quote.quoteItemsInvoice',['quoteItems'=> $quoteItems,
                                        'itemsCategories'=> $itemsCategories, 'totales' => $totales,
                                        'infoVenta' => $infoVenta,
                                        'itemsPackages' => $itemsPackages,
                                        'places'=> $places])->render();
        
        $returnResult['message'] = 'Success';
        $success = true;

        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function savePackageValue(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;
        // $itemCategory = \App\QuoteItemCategory::find($request->id);

        $itemPackage = \App\QuoteItemPackage::find($request->id);

        $quoteUser = \App\Quote::find($itemPackage->quote_id)->users;
        if ($user->organization_id == $quoteUser->organization_id) {
            if ($request->packageValue > 0){
                $itemPackage->value = $request->packageValue;
                if ($itemPackage->save()) {

                    $valoresVenta = $this->getQuoteVales($itemPackage->quote_id);
                    $itemsPackages = $this->getItemsPackages($itemPackage->quote_id);
                    $infoVenta = $this->getInfoVenta($itemPackage->quote_id);
                    
                    $totales = [];
                    $value = $valoresVenta[0];
                    $aumentos = $value->descuentos;
                    $total = $value->subTotal + $aumentos;
                    $subTotal = $value->subTotal;
                    $packageValue = 0;
                    foreach ($itemsPackages as $value) {
                        $packageValue += $value->value;
                    }
                    $total = $total + $packageValue;
                    $HT = $total - (($infoVenta->tax * $total) / 100);
                    $subTotal = $subTotal + $packageValue;
                    $totales['tax'] = $infoVenta->tax * $total / 100;
                    $totales['subTotal'] = number_format($subTotal, 2, ',', '.');
                    $totales['tax'] = number_format($totales['tax'], 2, ',', '.');
                    $totales['descuentos'] = number_format($aumentos, 2, ',', '.');
                    $totales['total'] = number_format($total, 2, ',', '.');
                    $totales['HT'] = number_format($HT, 2, ',', '.');


                    $returnResult['html'] = view('quote.quoteTotal',['totales' => $totales,'infoVenta' => $infoVenta])->render();
                    $returnResult['message'] = 'Success';
                    $success = true;
                } else {
                    $returnResult['message'] = 'An internal server erros has ocurred, please contact your administrator';
                }
            } else {
                $returnResult['message'] = 'The value must be greater than zero';
            }
        } else {
            $returnResult['message'] = 'You have no permissions on this record';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeItem(Request $request)
    {
        $user = auth()->user();
        $quoteItem = \App\QuoteItem::find($request->id);
        $quoteUser = \App\Quote::find($request->quoteId)->users;
        $success = false;
        if ($user->organization_id == $quoteUser->organization_id) {
            $quoteItem->delete();
            $tienePaquete = DB::select('SELECT v.id
                FROM quotes v
                INNER JOIN quote_items dv on dv.quote_id = v.id and dv.package = 1
                INNER JOIN quote_item_packages dp on dp.place_id = dv.place_id
                INNER JOIN users ON users.id = v.created_by_id
                WHERE dv.quote_id = ? AND dp.place_id = ?
                    AND users.organization_id = ?',
                [$quoteItem->quote_id,$quoteItem->place_id,$user->organization_id]);
            $itemPackage = DB::table('quote_item_packages')
                ->select('quote_item_packages.id')
                ->join('quotes', 'quotes.id', '=', 'quote_item_packages.quote_id')
                ->join('users', 'users.id', '=', 'quotes.created_by_id')
                ->where('quote_item_packages.place_id', $quoteItem->place_id)
                ->where('quote_item_packages.quote_id', $quoteItem->quote_id)
                ->where('users.organization_id', $user->organization_id)->first();
            if ($itemPackage!==null && $tienePaquete===null) {
                $itemPackage = \App\QuoteItemPackage::find($itemPackage->id);
                $itemPackage->delete();
            }
            $itemCategory = DB::table('quote_item_categories')
                ->where('quote_id', $quoteItem->quote_id)
                ->where('category_id', $quoteItem->category_id)
                ->first();
            $quoteDetail = DB::table('quotes')
                ->join('quote_items', 'quote_items.quote_id', '=', 'quotes.id')
                ->join('places', 'places.id', '=', 'quote_items.place_id')
                ->join('categories', 'categories.id', '=', 'quote_items.category_id')
                ->join('products', 'products.id', '=', 'quote_items.product_id')
                ->join('users', 'users.id', '=', 'quotes.created_by_id')
                ->join('quote_item_categories', function ($join) {
                    $join->on('quote_item_categories.category_id', '=', 'categories.id')
                    ->on('quote_item_categories.quote_id', '=', 'quotes.id');
                })
                ->select('quote_item_categories.id')
                ->where('users.organization_id', $user->organization_id)
                ->where('quote_item_categories.quote_id', $quoteItem->quote_id)
                ->where('quote_item_categories.category_id', $itemCategory->category_id)->first();
            if ($itemCategory!==null && $quoteDetail===null) {
                $itemCategory = \App\QuoteItemCategory::find($itemCategory->id);
                $itemCategory->delete();
            }
            $returnResult = $this->QuoteDetail($request->quoteId);
            $success = true;
        } else {
            $returnResult['message'] = 'You have no permissions on this record';
        }

        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editQuoteDetail(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;

        $quoteDetail = DB::table('quote_items')
            ->select('places.id as place', 'categories.id as category',
                'products.name','products.code','products.price','products.priceusd',
                'quote_item_categories.description as categoryDescription',
                'quote_items.quantity','quote_items.other','quote_items.product_id',
                'quote_items.description','quote_items.offer','quote_items.optional',
                'quote_items.package','countries.currency','countries.currency_abbreviation',
                'quote_item_categories.id as itemCategory')
            ->join('quotes', 'quotes.id', '=', 'quote_items.quote_id')
            ->join('places', 'places.id', '=', 'quote_items.place_id')
            ->join('categories', 'categories.id', '=', 'quote_items.category_id')
            ->join('quote_item_categories', function ($join) {
                $join->on('quote_item_categories.category_id', '=', 'categories.id')
                ->on('quote_item_categories.quote_id', '=', 'quotes.id');
            })
            ->join('products', 'products.id', '=', 'quote_items.product_id')
            ->join('locations', 'locations.id', '=', 'quotes.location_id')
            ->join('countries', 'countries.id', '=', 'locations.country_id')
            ->join('users', 'users.id', '=', 'quotes.created_by_id')
            ->where('users.organization_id', $user->organization_id)
            ->where('quote_items.id', $request->id)->first();

        if ($quoteDetail) {
            if ($quoteDetail->currency_abbreviation === 'EUR') {
                $quoteDetail->price = $quoteDetail->price;
            } elseif ($quoteDetail->currency_abbreviation === 'USD') {
                $quoteDetail->price = $quoteDetail->preciousd;
            }
            $success = true;
            $returnResult['message'] = 'Success';
            $returnResult['info'] = $quoteDetail;
        } else {
            $returnResult['message'] = 'An internal server error has ocurred, please contact your administrator';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoices()
    {
        $user = auth()->user();
        $facturas = DB::table('quotes')
            ->select('quotes.account_id', 'quotes.created_by_id', 'quotes.id as quoteId', 'quotes.quote_date',
                DB::raw("upper(quotes.quote_number) AS quote_number"),
                'quotes.invoice_date','quotes.stage_id',
                'accounts.name as accountName','users.name as userName','stages.name as stageName',
                'accounts.document_number','document_types.name as documenttype')
            ->join('accounts', 'accounts.id', '=', 'quotes.account_id')
            ->join('document_types', 'document_types.id', '=', 'accounts.document_type_id')
            ->join('stages', 'stages.id', '=', 'quotes.stage_id')
            ->join('users', 'users.id', '=', 'quotes.created_by_id')
            ->whereIn('quotes.stage_id', array(3,4))
            ->where('users.organization_id', $user->organization_id)
            ->orderByRaw('quotes.updated_at DESC')
            ->paginate(10);
        return view('quote.invoices',compact('facturas'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editInvoice($id)
    {
        $user = auth()->user();
        $datosFactura = DB::table('quotes')
            ->select('quotes.account_id', 'quotes.created_by_id', 'quotes.id as quoteId', 'quotes.quote_date',
                'accounts.name as accountName','users.name as userName','quotes.stage_id',
                'quotes.invoice_date',
                'accounts.document_number','document_types.name as documenttype')
            ->join('accounts', 'accounts.id', '=', 'quotes.account_id')
            ->join('document_types', 'document_types.id', '=', 'accounts.document_type_id')
            ->join('users', 'users.id', '=', 'quotes.created_by_id')
            ->where('users.organization_id', $user->organization_id)
            ->where('quotes.id', $id)->first();
        $files = DB::table('quotes')
        ->select('quote_files.name', 'quote_files.id')
        ->join('quote_files', 'quote_files.quote_id', '=', 'quotes.id')
        ->join('users', 'users.id', '=', 'quotes.created_by_id')
        ->where('quotes.id', $id)
        ->where('users.organization_id', $user->organization_id)->get();
        $invoice_date = $datosFactura->invoice_date !== null ? $datosFactura->invoice_date : date("Y-m-d");
        return view('quote.editInvoice',compact('datosFactura','id','invoice_date','files'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approveItem(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;
        $quoteItem = \App\QuoteItem::find($request->id);
        $quoteUser = \App\Quote::find($quoteItem->quote_id)->users;
        if ($user->organization_id == $quoteUser->organization_id) {
            if ($request->invoice === "true") {
                $quoteItem->invoice = 1;
            } else {
                $quoteItem->invoice = 0;
            }
            if ($quoteItem->save()) {
                $success = true;
                $returnResult['message'] = 'Success';
            } else {
                $returnResult['message'] = 'An internal server erros has ocurred, please contact your administrator';
            }
        } else {
            $returnResult['message'] = 'You have no permissions on this record';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function finishFactura(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;
        $quoteUser = \App\Quote::find($request->id)->users;
        $quote = \App\Quote::find($request->id);
        if ($user->organization_id == $quoteUser->organization_id) {
            $quote->stage_id=4;
            if ($request->invoice_date) {
                $quote->invoice_date=$request->invoice_date;
            }
            if ($quote->save()) {
                $returnResult['message'] = 'Success';
                $success = true;
            } else {
                $returnResult['message'] = 'An internal server erros has ocurred, please contact your administrator';
            }
        } else {
            $returnResult['message'] = 'You have no permissions on this record';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function finishReactivar(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;
        $id = $request->id;
        $quoteUser = \App\Quote::find($id)->users;
        $quote = \App\Quote::find($id);
        if ($user->organization_id == $quoteUser->organization_id) {
            $quote->stage_id=2;
            // if ($request->quote_date) 
            //     $quote->quote_date=$request->quote_date;
            if ($quote->save()) {
                $returnResult['message'] = 'Success';
                $success = true;
            } else {
                $returnResult['message'] = 'An internal server error has ocurred, please contact your administrator';
            }
        } else {
            $returnResult['message'] = 'You have no permissions on this record';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadQuoteFiles(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $success = false;
        $id = $request->id;
        $quoteUser = \App\Quote::find($id)->users;
        $quote = \App\Quote::find($id);
        if ($user->organization_id == $quoteUser->organization_id) {
            $uploadedFiles = $request->file('quoteFiles');
            $request->validate([
                'quoteFiles' => 'required',
                'quoteFiles.*' => 'max:1240',
            ]);

            if ($uploadedFiles) {
                foreach($uploadedFiles as $uploadedFile) {
                    $fileExtension = array_slice(explode('.', $uploadedFile->getClientOriginalName()),-1,1);
                    $fileExtension = strtolower($fileExtension[0]);
                    $allowedExtension = array('jpg','jpeg','png');
                    if (in_array($fileExtension, $allowedExtension)) {
                        $fileName = $user->organization_id.'_attachment'.$id.'.'.$fileExtension;
                        $QuoteFile= new \App\QuoteFile;
                        $QuoteFile->name=$fileName;
                        $QuoteFile->quote_id=$id;
                        $QuoteFile->created_by_id=$user->id;
                        $QuoteFile->save();
                        $fileName = $user->organization_id.'_attachment'.$id.$QuoteFile->id.'.'.$fileExtension;
                        $QuoteFile->name=$fileName;
                        $QuoteFile->save();
                        Storage::disk('local')->put('uploads/'.$user->organization_id.'/'.$fileName, file_get_contents($uploadedFile));
                        $returnResult['message'] = 'Success';
                    } else {
                        $returnResult['message'] = 'File extension not allowed';
                    }
                }
                $success = true;
            }
            $files = DB::table('quotes')
            ->select('quote_files.name', 'quote_files.id')
            ->join('quote_files', 'quote_files.quote_id', '=', 'quotes.id')
            ->join('users', 'users.id', '=', 'quotes.created_by_id')
            ->where('quotes.id', $id)
            ->where('users.organization_id', $user->organization_id)->get();
            $returnResult['html'] = view('quote.uploadedFiles',['files'=> $files])->render();
        } else {
            $returnResult['message'] = 'You have no permissions on this record';
        }
        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeFile(Request $request)
    {
        $user = auth()->user();
        $returnResult = [];
        $message = 'Success';
        $userFile = \App\QuoteFile::find($request->id)->users;
        $file = \App\QuoteFile::find($request->id);
        $success = false;
        if ($user->organization_id == $userFile->organization_id) {
            $file->delete();
            Storage::delete('uploads/'.$user->organization_id.'/'.$file->name);
            $files = DB::table('quotes')
            ->select('quote_files.name', 'quote_files.id')
            ->join('quote_files', 'quote_files.quote_id', '=', 'quotes.id')
            ->join('users', 'users.id', '=', 'quotes.created_by_id')
            ->where('quotes.id', $file->quote_id)
            ->where('users.organization_id', $user->organization_id)->get();
            $returnResult['html'] = view('quote.uploadedFiles',['files'=> $files])->render();
            $success = true;
        } else {
            $message = 'You have no permissions on this record';
        }
        $returnResult['message'] = $message;

        return response()->json(array('success' => $success, 'data'=>$returnResult));
    }
}
