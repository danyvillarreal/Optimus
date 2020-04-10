<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Fpdf;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fromDate = date('Y-m-d', strtotime('first day of this month'));
        $toDate = date('Y-m-d', strtotime('last day of this month'));
        return view('report.quotes',['fromDate'=>$fromDate,'toDate'=>$toDate]);
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
        //
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function getQuotes($fromDate,$toDate)
    {
        $user = auth()->user();
        $infoVenta = DB::table('quotes')
            ->select('quotes.id as quote_id','quotes.account_id', 'quotes.created_by_id',
                'quotes.id as quoteId', 'quotes.quote_date','countries.currency',
                'countries.currency_abbreviation',
                'accounts.name as accountName','users.name as userName',
                'quotes.tax','quotes.stage_id',
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
            ->where('quotes.quote_date', '>=', $fromDate)
            ->where('quotes.quote_date', '<=', $toDate)->get();
        return $infoVenta;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function getItemsPackages($fromDate,$toDate)
    {
        $user = auth()->user();
        $itemsPackages = DB::select('SELECT v.id, dp.place_id, dp.value,dp.category_id
                                FROM quote_item_packages dp
                                INNER JOIN places cs on cs.id = dp.place_id
                                INNER JOIN quotes v ON v.id = dp.quote_id
                                INNER JOIN users ON users.id = v.created_by_id
                                WHERE v.quote_date >= ? AND v.quote_date <= ? AND  users.organization_id = ?',
                                    [$fromDate,$toDate,$user->organization_id]);
        return $itemsPackages;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function getQuotesValues($fromDate,$toDate)
    {
        $user = auth()->user();
        $valoresVenta = DB::select('SELECT SUM(dv.quantity*dv.price) AS subTotal,
                                        SUM(dv.other) AS descuentos, v.id
                                    FROM quote_items dv
                                    INNER JOIN quotes v ON v.id = dv.quote_id
                                    INNER JOIN users ON users.id = v.created_by_id
                                    WHERE v.quote_date >= ? AND v.quote_date <= ? AND dv.offer <> 1
                                        AND dv.package = 0 AND dv.optional <> 1 AND users.organization_id = ?
                                    GROUP BY v.id',
                                        [$fromDate,$toDate,$user->organization_id]);
        return $valoresVenta;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function quotesReportPdf(Request $request) {
        $id = 1;
        $user = auth()->user();
        $org = \App\Organization::find($user->organization_id);
        $pdf = new Fpdf();

        $pdf::SetMargins(20,20,20);
        $pdf::AliasNbPages();
        $pdf::AddPage();
        $pdf::SetAutoPageBreak('auto','10');

        $pdf::SetFont('Arial','b',18);
        $pdf::Cell(20,5,"",0,"","L");
        $pdf::SetFont('Arial','b',10);
        $pdf::Cell(130,5,'Devis rapport',0,1,"C");
        $pdf::Cell(0,4,utf8_decode($org->name),0,1,"C");
        $pdf::SetFont('Arial','',10);
        $pdf::Cell(0,4,$org->phone,0,1,"C");
        $pdf::Cell(0,4,$org->email,0,1,"C");
        $pdf::Cell(0,4,$request->from_date.' - '.$request->to_date,0,1,"L");
        $pdf::Ln(10);

        $pdf::SetFont('Arial','B',10);
        $pdf::cell(80,6,'Client',"B","","L");
        $pdf::cell(30,6,'HT',"B","","R");
        $pdf::cell(30,6,'VTA',"B","","R");
        $pdf::cell(30,6,'TTC',"B",1,"R");

        $quotes = $this->getQuotes($request->from_date,$request->to_date);
        $itemsPackages = $this->getItemsPackages($request->from_date,$request->to_date);
        $valoresVenta = $this->getQuotesValues($request->from_date,$request->to_date);

        $objectPackages = [];
        foreach ($itemsPackages as $value) {
            $objectPackages[$value->id][] = $value;
        }

        $objectValoresVenta = [];
        foreach ($valoresVenta as $value) {
            $objectValoresVenta[$value->id] = $value;
        }

        $countRows = count($quotes);

        $usdFlag = false;
        $eurFlag = false;
        $copFlag = false;
        $currencyUSD = '';
        $currencyEUR = chr(128);

        $totalesHTUSD = 0;
        $totalesdescuentosUSD = 0;
        $totalestaxUSD = 0;
        $totalestotalUSD = 0;

        $totalesHT = 0;
        $totalesdescuentos = 0;
        $totalestax = 0;
        $totalestotal = 0;

        $totalesHTCOP = 0;
        $totalesdescuentosCOP = 0;
        $totalestaxCOP = 0;
        $totalestotalCOP = 0;
        foreach ($quotes as $quote) {
            $usd = false;
            $eur = false;
            $cop = false;
            $fill = true;
            if ($countRows % 2 ===0) {
                $fill = false;
            }

            $currency = $quote->currency;
            if ($quote->currency_abbreviation === 'EUR') {
                $currency = chr(128);
                $eurFlag = true;
                $eur = true;
            } elseif ($quote->currency_abbreviation === 'USD') {
                $usd = true;
                $usdFlag = true;
                $currencyUSD = $quote->currency;
            } elseif ($quote->currency_abbreviation === 'COP') {
                $copFlag = true;
                $cop = true;
                $currencyCOP = $quote->currency;
            }
            $returnResult = [];
            $totales = [];
            $value = $objectValoresVenta[$quote->quote_id];
            $aumentos = $value->descuentos;
            $total = $value->subTotal + $aumentos;
            $subTotal = $value->subTotal;

            $packageValue = 0;
            if (in_array($quote->quote_id,$objectPackages)) {
                foreach ($objectPackages[$quote->quote_id] as $value) {
                    $packageValue += $value->value;
                }
            }
            $total = $total + $packageValue;
            $tax = ($quote->tax * $total) / 100;
            $HT = $total - $tax;
            $subTotal = $subTotal + $packageValue;
            $totales['subTotal'] = number_format($subTotal, 2, ',', '.');
            $totales['HT'] = number_format($HT, 2, ',', '.');
            $totales['descuentos'] = number_format($aumentos, 2, ',', '.');
            $totales['tax'] = number_format($tax, 2, ',', '.');
            $totales['total'] = number_format($total, 2, ',', '.');

            $pdf::SetFillColor(244,244,244);
            $pdf::SetFont('Arial','',9);
            $pdf::cell(80,5,utf8_decode($quote->accountName).' - '.$quote->documenttype.' '.$quote->document_number,0,"","L",$fill);

            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currency.''.$totales['HT'],0,"","R",$fill);

            $pdf::cell(30,5,$quote->tax."% ".$currency.''.$totales['tax'],0,"","R",$fill);
            $pdf::SetFont('Arial','',9);
            
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currency.''.$totales['total'],0,1,"R",$fill);

            if ($eur) {
                $totalesHT = $totalesHT + $HT;
                $totalesdescuentos = $totalesdescuentos + $aumentos;
                $totalestax = $totalestax + $tax;
                $totalestotal = $totalestotal + $total;
            } elseif ($usd) {
                $totalesHTUSD = $totalesHTUSD + $HT;
                $totalesdescuentosUSD = $totalesdescuentosUSD + $aumentos;
                $totalestaxUSD = $totalestaxUSD + $tax;
                $totalestotalUSD = $totalestotalUSD + $total;
            } elseif ($cop) {
                $totalesHTCOP = $totalesHTCOP + $HT;
                $totalesdescuentosCOP = $totalesdescuentosCOP + $aumentos;
                $totalestaxCOP = $totalestaxCOP + $tax;
                $totalestotalCOP = $totalestotalCOP + $total;
            }
            --$countRows;
        }

        $pdf::Ln(10);
        if ($eurFlag) {
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"MONTANT HT:","LT","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currencyEUR.' '.number_format($totalesHT, 2, ',', '.'),"TR",1,"R");

            if ($totalesdescuentos < 0) {
                $pdf::SetFont('Arial','b',9);
                $pdf::cell(110,5,"",0,"","L");
                $pdf::cell(30,5,utf8_decode("Supplémentaire:"),"LT","","R");
                $pdf::SetFont('Arial','',9);
                $pdf::cell(30,5,$currencyEUR.' '.number_format($totalesdescuentos, 2, ',', '.'),"TR",1,"R");
            }
            
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"VTA:","LT","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currencyEUR.' '.number_format($totalestax, 2, ',', '.'),"TR",1,"R");
            
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"MONTANT TTC:","LTB","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currencyEUR.' '.number_format($totalestotal, 2, ',', '.'),"TRB",1,"R");
            $pdf::Ln();
        }
        if ($usdFlag) {
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"MONTANT HT:","LT","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currencyUSD.' '.number_format($totalesHTUSD, 2, ',', '.'),"TR",1,"R");

            if ($totalesdescuentosUSD < 0) {
                $pdf::SetFont('Arial','b',9);
                $pdf::cell(110,5,"",0,"","L");
                $pdf::cell(30,5,utf8_decode("Supplémentaire:"),"LT","","R");
                $pdf::SetFont('Arial','',9);
                $pdf::cell(30,5,$currencyUSD.' '.number_format($totalesdescuentosUSD, 2, ',', '.'),"TR",1,"R");
            }
            
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"VTA:","LT","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currencyUSD.' '.number_format($totalestaxUSD, 2, ',', '.'),"TR",1,"R");
            
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"MONTANT TTC:","LTB","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currencyUSD.' '.number_format($totalestotalUSD, 2, ',', '.'),"TRB",1,"R");
            $pdf::Ln();
        }
        if ($copFlag) {
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"MONTANT HT:","LT","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currencyCOP.' '.number_format($totalesHTCOP, 2, ',', '.'),"TR",1,"R");

            if ($totalesdescuentosCOP < 0) {
                $pdf::SetFont('Arial','b',9);
                $pdf::cell(110,5,"",0,"","L");
                $pdf::cell(30,5,utf8_decode("Supplémentaire:"),"LT","","R");
                $pdf::SetFont('Arial','',9);
                $pdf::cell(30,5,$currencyCOP.' '.number_format($totalesdescuentosCOP, 2, ',', '.'),"TR",1,"R");
            }
            
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"VTA:","LT","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currencyCOP.' '.number_format($totalestaxCOP, 2, ',', '.'),"TR",1,"R");
            
            $pdf::SetFont('Arial','b',9);
            $pdf::cell(110,5,"",0,"","L");
            $pdf::cell(30,5,"MONTANT TTC:","LTB","","R");
            $pdf::SetFont('Arial','',9);
            $pdf::cell(30,5,$currencyCOP.' '.number_format($totalestotalCOP, 2, ',', '.'),"TRB",1,"R");
            $pdf::Ln();
        }
        $pdf::Image(storage_path().'\\app\uploads\\'.$org->id.'\\'.$org->logo,20,15,18,18);
        $pdf::Output();
        exit;
    }

}
