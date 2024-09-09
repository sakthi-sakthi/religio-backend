<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Congregation;
use App\Models\Payment;
use App\Models\Province;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProvinceController extends Controller
{ 
        Private $status = 200;
       
        public function Provincestore(Request $request)
        {
           
            $validator    =  Validator::make($request->all(), 
            [
                "congregation" => 'required',
                "province"   => "required",
                "address1"  => "required",
                "state"  => "required",
                "postcode"=> "required",
                "city"  => "required",
                "country"  => "required",
                "mobile"  => "required",
                "email"  => "required",
            ]
           );
                if($validator->fails()) {
                    return response()->json(["status" => "failed", 
                                    "validation_errors" => $validator->errors()]);
                }
                 $ProvinceArray['params'] = array(
                                "congregation" => $request->congregation,
                                "province" => $request->province,
                                "address1" => $request->address1,
                                "state" => $request->state,
                                "address2" => $request->address2,
                                "postcode"   => $request->postcode, 
                                "city"   => $request->city, 
                                "country"   => $request->country, 
                                "mobile"   => $request->mobile, 
                                "email"   => $request->email, 
                                "contactname"   => $request->contactname, 
                                "contactrole"   => $request->contactrole, 
                                "contactemail"   => $request->contactemail, 
                                "contactmobile"   => $request->contactmobile, 
                                "contactstatus"   => $request->contactstatus, 
                         );
    
                $Province  = Province::create($ProvinceArray['params']);
    
                if(!is_null($Province)){ 
    
                    return response()->json(["status" => $this->status, "success" => true, 
                            "message" => "Province created successfully", "data" => $Province]);
                }    
                else {
                    return response()->json(["status" => "failed", "success" => false,
                                "message" => "Whoops! failed to create."]);
            }      
        }
    
        // list value
    
        public function ProvinceList() {
    
            $ProvinceAll = DB::table('provinces as pr')
            ->select('pr.*','co.congregation')
            ->leftjoin('congregation as co','co.id','pr.congregation')
            ->orderBy('pr.id','desc')
            ->get();
            if(count($ProvinceAll) > 0) {
                return response()->json(["status" => $this->status, "success" => true, 
                            "count" => count($ProvinceAll), "data" => $ProvinceAll]);
            }
            else {
                return response()->json(["status" => "failed",
                "success" => false, "message" => "Whoops! no record found"]);
            }
        }

        public function ProvinceDelete($id){

            $Congregationdel =Province::find($id);
            $Congregationdel->delete();
            return response()->json(
                ["status" => $this->status, "success" => true, 
                "message" => " Province deleted  successfully"]);
        }
        public function ProvinceCongregation(){

            $Congregation =Congregation::all();
            if(count($Congregation) > 0) {
                return response()->json(["status" => $this->status, "success" => true, 
                            "count" => count($Congregation), "data" => $Congregation]);
            }
            else {
                return response()->json(["status" => "failed",
                "success" => false, "message" => "Whoops! no record found"]);
            }
        }

        public function Provinceverifydelete($id){

        $verifyData =DB::table('client_registrations')->select('id')->where('province',$id)->first();
       
        if($verifyData !=null){
            return response()->json(["status" => $this->status, "success" => true 
           , "message" => "true"]);
        }else{
            return response()->json(["status" => "failed",
            "success" => false, "message" => "false"]);
        }


        }
        public function ProvinceEdit($id){
           
            $Congregationedit = Province::where('id',$id)->get();
            if(count($Congregationedit) > 0) {
                return response()->json(["status" => $this->status, "success" => true, 
                            "count" => count($Congregationedit), "data" => $Congregationedit]);
            }
            else {
                return response()->json(["status" => "failed",
                "success" => false, "message" => "Whoops! no record found"]);
            }

        }

        public function Provinceget($id){
         
            // $Provinceget = Province::where('id',$id)->get();
            
            $Provinceget = DB::table('provinces as pr')
            ->select('pr.*','co.congregation')
            ->leftjoin('congregation as co','co.id','pr.congregation')
            ->where('co.id',$id)
            ->get();
          
            if(count($Provinceget) > 0) {
                return response()->json(["status" => $this->status, "success" => true, 
                            "count" => count($Provinceget), "data" => $Provinceget]);
            }
            else {
                return response()->json(["status" => "failed",
                "success" => false, "message" => "Whoops! no record found"]);
            }

        }
        public function Provinceupdate($id,Request $request){
           
            $Congregationupdate = Province::where('id',$id)
            ->update([
                "congregation" => $request->congregation,
                "province" => $request->province,
                "address1" => $request->address1,
                "state" => $request->state,
                "address2" => $request->address2,
                "postcode"   => $request->postcode, 
                "city"   => $request->city, 
                "country"   => $request->country, 
                "mobile"   => $request->mobile, 
                "email"   => $request->email, 
                "contactname"   => $request->contactname, 
                "contactrole"   => $request->contactrole, 
                "contactemail"   => $request->contactemail, 
                "contactmobile"   => $request->contactmobile, 
                "contactstatus"   => $request->contactstatus, 
            ]);

            return response()->json(
                ["status" => $this->status, "success" => true, 
                "message" => " Congregation updated  successfully"]);
        }

        public function GetBalance($value){
          
            $Year = date('Y');
            $dately = $Year.'-'.$Year + 1;
            if ($value != "Clients") {
    
            $Balancefilter =Payment::where('clienttype',$value)
            ->where('financialyear',$dately)->get();
          
            $yearmonth =Payment::select('financialyear')->where('clienttype',$value)->orderBy('financialyear', 'desc')->get();
           
            $GetallData = DB::table('payments as pay')
            ->select('pay.*','co.congregation','pr.province')
            ->leftjoin('congregation as co','co.id','pay.congregation')
            ->leftjoin('provinces as pr','pr.id','pay.province')
            ->where('financialyear',$dately)
            ->where('clienttype',$value)
            ->get();
            $NotCpayments =DB::table('client_registrations')->select('projectvalue')->where('financialyear',$dately)->get();
            if($value == 'NewSales'){

                $totalval =DB::table('client_registrations')->select('projectvalue')->where('financialyear',$dately)->get();
                $total=[];
                foreach ($totalval as $key => $value) {
                    $totalss[]=$value->projectvalue;
                   $total[] = ($value->projectvalue + intdiv($value->projectvalue,100) * 18);
                }
    
             }else{
                $totalval =DB::table('client_registrations')->select('amcvalue')->where('financialyear',$dately)->get();
                $total=[];
                foreach ($totalval as $key => $value) {
                    $totaljj[]=$value->amcvalue;
                   $total[] = ($value->amcvalue + intdiv($value->amcvalue,100) * 18);
                }
    
             }
            // $balance=[];
            // $total=[];
            $Paid=[];
            $Getmonth =[];

          foreach ($Balancefilter as $key => $value) {
            // $balance[]= $value->balance;
            // $total[]=$value->total;
            $Paid[]= $value->paid;
            $month = Carbon::parse($value->created_at)->format('F');

            if(!in_array($month,$Getmonth)){
                $Getmonth[] = $month;
            }
          }  
            $year =[];
            foreach ($yearmonth as $key => $value) {
                $yeardata =$value->financialyear;
                        if(!in_array($yeardata,$year)){
                            $year[] = $yeardata;
                        }  
            }
            $balance =array_sum($total) - array_sum($Paid);
          if(count($NotCpayments) > 0) {

           $balances = $balance;
           $totalval = array_sum($total);
           $paidval = array_sum($Paid);
           $balanceamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $balances);
           $totalamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $totalval);
           $paidamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $paidval);
 
           $perbal =  round(($balances * 100) / $totalval ,2);
           $perpaid = round(($paidval * 100) / $totalval,2);
         
                return response()->json(["status" => $this->status, "success" => true, 
                            "count" => count($Balancefilter), "data" => [
                                "balance" =>$balanceamount ?? '0',
                                "total" => $totalamount ?? '0',
                                "paid" => $paidamount ?? '0',
                                "year" =>$year ?? explode(",", "") ,
                                "balPer" =>$perbal ?? '0' ,
                                "paidPer" =>$perpaid ?? '0',
                                "Month" =>  $Getmonth?? explode(",", "")
                            ],
                                "dataall"=>$GetallData,
                        ]);
            }
            else {
                return response()->json(["status" => "failed",
                "success" => false,  "count" => count($Balancefilter), "data" => [
                    "balance" =>'0',
                    "total" =>  '0',
                    "paid" =>  '0',
                    "year" => $year ?? explode(",", "") ,
                    "balPer" => '0' ,
                    "paidPer" => '0',
                    "Month" => explode(",", ""),
                    ]
            
            ]);
            }
        }else{

            $clients = DB::table('client_registrations')
            ->select('financialyear')
            ->orderBy('financialyear', 'desc')
            ->get();
            
            $clientstatus = DB::table('client_registrations')
                ->where('projectstatus','InProgress')
                ->where('financialyear',$dately)
                ->get();

            $clientfyear = DB::table('client_registrations')
            ->where('financialyear',$dately)
            ->get();


            $GetallData = DB::table('client_registrations as cr')
            ->select('cr.*','co.congregation','pr.province')
            ->leftjoin('congregation as co','co.id','cr.congregation')
            ->leftjoin('provinces as pr','pr.id','cr.province')
            ->orderBy('cr.id','desc')
            ->where('financialyear',$dately)
            ->get();

            $year=[];
            foreach ($clients as $key => $value) {
                $yeardata =$value->financialyear;
                        if(!in_array($yeardata,$year)){
                            $year[] = $yeardata;
                        } 
            }
            $clientscount= count($clients);
            $newclients= count($clientfyear);
            $clientstatuscount =count($clientstatus);
            if(count($clients) > 0) {

                return response()->json([
                    "status" => $this->status,
                    "success" => true, 
                    "count" => count($clients),
                    "data" => [
                        "balance" =>$clientstatuscount ?? '0',
                        "total" => $clientscount ?? '0',
                         "paid" => $newclients ?? '0',
                         "year" =>$year ?? explode(",", "") ],
                         "dataall"=>$GetallData,
                ]);
            }else{
                return response()->json([
                    "status" => "failed",
                    "success" => false, 
                    "data" => [
                        "balance" =>'0',
                        "total" =>  '0',
                        "paid" => '0']
                ]);
            }
        }
        }
    public function GetBalanceall($value){
        $dataquery = $value;
           if ($value != "Clients") {
           
             $Balancefilter = Payment::where('clienttype',$value)->get();
// dd($Balancefilter);
             $Cpayments =DB::table('payments')->select('payments.*')
            ->where('clienttype',$value)
            ->get();
            // dd($Cpayments);
            $NotCpayments =DB::table('client_registrations')->select('client_registrations.*')->get();

            $datafilter= DB::table('payments')->select('clientcode')
            ->where('clienttype',$value)
            ->get();
             $clientcodesToRemove =[];
            foreach ($datafilter as $key => $value) {
                $clientcodesToRemove[]=$value->clientcode;
            }
            $filteredMergedData = $NotCpayments->concat($Cpayments)->reject(function ($item) use ($clientcodesToRemove) {
                return in_array($item->clientcode, $clientcodesToRemove);
            });

            $data=[];
            foreach ($filteredMergedData as $key => $value) {
               $data[]= $value->clientcode;
            }
           
        
             if($dataquery == 'NewSales'){
             $GetallData = DB::table('payments as pay')
             ->select('pay.*','co.congregation','pr.province')
             ->leftjoin('congregation as co','co.id','pay.congregation')
             ->leftjoin('provinces as pr','pr.id','pay.province')
             ->where('clienttype',$dataquery)
             ->orderBy('financialyear', 'desc')
             ->get();
                $totalval =DB::table('client_registrations')->select('projectvalue')->get();
                $total=[];
                foreach ($totalval as $key => $value) {
                    $totalss[]=$value->projectvalue;
                   $total[] = ($value->projectvalue + intdiv($value->projectvalue,100) * 18);
                }
             }else{
                // dd($data);
                $GetallData = DB::table('client_registrations as pay')
            ->select('pay.*','co.congregation','pr.province')
            ->selectRaw('(amcvalue + (amcvalue * 0.18)) as amcvalue')
            ->leftjoin('congregation as co','co.id','pay.congregation')
            ->leftjoin('provinces as pr','pr.id','pay.province')
            ->whereIn('pay.clientcode',$data)
            ->get();
            // $payment=[];
            // foreach ($GetallData as $key => $value) {
            //     $payment[]= $value->amcvalue;
            // }
            // dd(array_sum($payment));
                $totalval =DB::table('client_registrations')->select('amcvalue')->get();
                $total=[];
                foreach ($totalval as $key => $value) {
                    $totaljj[]=$value->amcvalue;
                   $total[] = ($value->amcvalue + intdiv($value->amcvalue,100) * 18);
                }
                

             }
           

            // $balance=[];
            $Paid=[];
            $Getmonth =[];
            $year =[];
            foreach ($Balancefilter as $key => $value) {
                // $balance[]= $value->balance;
                //
                $yeardata =$value->financialyear;
                $Paid[]= $value->paid;
                $month = Carbon::parse($value->created_at)->format('F');
                if(!in_array($month,$Getmonth)){
                    $Getmonth[] = $month;
                }
                if(!in_array($yeardata,$year)){
                    $year[] = $yeardata;
                }
            }
           $balance =array_sum($total) - array_sum($Paid);
        //    dd($balance , array_sum($Paid) , array_sum($total));
        // dd($Balancefilter);
            if(count($GetallData) > 0) {
            $balances =$balance;
            $totalval = array_sum($total);
            $paidval = array_sum($Paid);
            $balanceamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $balances);
            $totalamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $totalval);
            $paidamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $paidval);

            $perbal =  round(($balances * 100) / $totalval ,2);
            $perpaid = round(($paidval * 100) / $totalval,2);
            // dd($totalamount);
                return response()->json(["status" => $this->status, "success" => true, 
                            "count" => count($Balancefilter), "data" => [
                                "balance" =>$balanceamount ?? '0',
                                "total" => $totalamount ?? '0',
                                "paid" => $paidamount ?? '0',
                                "year" =>$year ?? explode(",", "") ,
                                "balPer" =>$perbal ?? '0' ,
                                "paidPer" =>$perpaid ?? '0',
                                "Month" =>  $Getmonth?? explode(",", "")
                            ],
                                "dataall"=>$GetallData,
                        ]);
            }
            else {
                return response()->json(["status" => "failed",
                "success" => false,  "count" => count($Balancefilter), "data" => [
                    "balance" =>'0',
                    "total" =>  $totalamount ?? '0',
                    "paid" =>  '0',
                    "year" => explode(",", ""),
                    "balPer" => '0' ,
                    "paidPer" => '0',
                    "Month" => explode(",", ""),
                    ]
            
            ]);
            }

        }else{
          
            $clients = DB::table('client_registrations')
            ->select('financialyear')
            ->orderBy('financialyear', 'desc')
            ->get();

            $clientstatus = DB::table('client_registrations')
            ->where('projectstatus','InProgress')
            // ->where('financialyear',$request->year)
            ->get();

            $GetallData = DB::table('client_registrations as cr')
            ->select('cr.*','co.congregation','pr.province')
            ->leftjoin('congregation as co','co.id','cr.congregation')
            ->leftjoin('provinces as pr','pr.id','cr.province')
            ->orderBy('cr.id','desc')
            ->get();

            $clientscount= count($clients);
            $clientstatuscount =count($clientstatus);

            if(count($clients) > 0) {

                return response()->json([
                    "status" => $this->status,
                    "success" => true, 
                    "count" => count($clients),
                    "data" => [
                        "balance" =>$clientstatuscount ?? '0',
                        "total" => $clientscount ?? '0',
                         "paid" => $clientscount ?? '0',],
                         "dataall"=>$GetallData,
                ]);
            }else{
                return response()->json([
                    "status" => "failed",
                    "success" => false, 
                    "data" => [
                        "balance" =>'0',
                        "total" =>  '0',
                        "paid" => '0']
                ]);
            }
           
        }
    }
        public function GetFinancialyear()
        {
          
            $years = DB::table('client_registrations')->select('financialyear','dateofjoining')->groupby('financialyear')->orderBy('financialyear', 'desc')->get();
            $finnacialyear =[];
            $Getmonth =[];
            foreach ($years as $key => $value) {
                $month = Carbon::parse($value->dateofjoining)->format('F');
                if(!in_array($month,$Getmonth)){
                    $Getmonth[] = $month;
                }
                $finnacialyear[]=$value->financialyear;
            }
                if(count($finnacialyear) > 0) {
                    return response()->json(["status" => $this->status, "success" => true, 
                                "count" => count($finnacialyear), "data" => $finnacialyear ,'month' => $Getmonth]);
                }
                else {
                    return response()->json(["status" => "failed",
                    "success" => false, "message" => "Whoops! no record found"]);
                } 
        }

        public function financialyear(Request $request){
           
           if ($request->type != "Clients") {
          
            $getBalance = Payment::where('clienttype',$request->type)->where('financialyear',$request->year)->orderBy('financialyear', 'desc')->get();
            
        //    dd($getBalance);
            $Cpayments =DB::table('payments')->select('payments.*')
            ->where('financialyear',$request->year)
            ->where('clienttype',$request->type)
            ->get();

            $NotCpayments =DB::table('client_registrations')->select('client_registrations.*')->where('financialyear',$request->year)->get();

            $datafilter= DB::table('payments')->select('clientcode')
            ->where('financialyear',$request->year)
            ->where('clienttype',$request->type)
            ->get();
             $clientcodesToRemove =[];
            foreach ($datafilter as $key => $value) {
                $clientcodesToRemove[]=$value->clientcode;
            }
            $filteredMergedData = $NotCpayments->concat($Cpayments)->reject(function ($item) use ($clientcodesToRemove) {
                return in_array($item->clientcode, $clientcodesToRemove);
            });

            $data=[];
            foreach ($filteredMergedData as $key => $value) {
               $data[]= $value->clientcode;
            }
// dd($data);
            if($request->type == 'NewSales'){

                $GetallData = DB::table('payments as pay')
                ->select('pay.*','co.congregation','pr.province')
                ->leftjoin('congregation as co','co.id','pay.congregation')
                ->leftjoin('provinces as pr','pr.id','pay.province')
                ->where('financialyear',$request->year)
                ->where('clienttype',$request->type)
                ->get();

                $totalval =DB::table('client_registrations')->select('projectvalue')->where('financialyear',$request->year)->get();
                $total=[];
                foreach ($totalval as $key => $value) {
                    $totalss[]=$value->projectvalue;
                   $total[] = ($value->projectvalue + intdiv($value->projectvalue,100) * 18);
                }
                // dd(array_sum($total));
             }else{  
             
            $GetallData = DB::table('client_registrations as pay')
            ->select('pay.*','co.congregation','pr.province')
            ->selectRaw('(amcvalue + (amcvalue * 0.18)) as amcvalue')
            ->leftjoin('congregation as co','co.id','pay.congregation')
            ->leftjoin('provinces as pr','pr.id','pay.province')
            // ->where('financialyear',$request->year)
            ->whereIn('pay.clientcode',$data)
            // ->where('clienttype',$request->type)
            ->get();


                $totalval =DB::table('client_registrations')->select('amcvalue')->where('financialyear',$request->year)->get();
                $total=[];
                foreach ($totalval as $key => $value) {
                    $totaljj[]=$value->amcvalue;
                   $total[] = ($value->amcvalue + intdiv($value->amcvalue,100) * 18);
                } 
                
             }
            //  $balance=[];
             $Paid=[];
             $Getmonth =[];
            foreach ($getBalance as $key => $value) {
                // $balance[]= $value->balance;
                // $total[]=$value->total;
                $Paid[]= $value->paid;
                //  dd($Paid);
                $month=Carbon::parse($value->created_at)->format('F'); 

                if(!in_array($month,$Getmonth)){
                    $Getmonth[] = $month;
                }

            }
            $balance =array_sum($total) - array_sum($Paid);
            
            $balances =$balance;
            $totalval = array_sum($total);
            $paidval = array_sum($Paid);
            //  dd( $totalval,$balances,$paidval);
            $balanceamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $balances);
            $totalamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $totalval);
            $paidamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $paidval);
        
            $perbal =  round(($balances * 100) / $totalval ,2);
            $perpaid = round(($paidval * 100) / $totalval,2);

            if(count($NotCpayments) > 0) {
                return response()->json(["status" => $this->status, "success" => true, 
                            "count" => count($filteredMergedData), "data" => [
                                "balance" =>$balanceamount ?? '0',
                                "total" => $totalamount ?? '0',
                                "paid" => $paidamount ?? '0',
                                "balPer" =>$perbal ?? '0' ,
                                "paidPer" =>$perpaid ?? '0',
                                "Month" =>  $Getmonth?? explode(",", "")
                            ],
                            "dataall"=>$GetallData,
                ]);
            }
            else {
                return response()->json(["status" => "failed",
                "success" => false,  "count" => count($getBalance), "data" => [
                    "balance" => '0',
                    "total" =>  '0',
                    "paid" =>'0',
                    "balPer" => '0' ,
                     "paidPer" => '0',
                     "Month" =>  explode(",", "")
                    ]
            ]);
        }
            } else {
                $clients = DB::table('client_registrations')
                ->select('financialyear')
                ->orderBy('financialyear', 'desc')
                ->get();
                $clientstatus = DB::table('client_registrations')
                ->where('projectstatus','InProgress')
                ->where('financialyear',$request->year)
                ->get();
                
                $clientfyear = DB::table('client_registrations')
                ->where('financialyear',$request->year)
                ->get();
    
    
                $GetallData = DB::table('client_registrations as cr')
                ->select('cr.*','co.congregation','pr.province')
                ->leftjoin('congregation as co','co.id','cr.congregation')
                ->leftjoin('provinces as pr','pr.id','cr.province')
                ->orderBy('cr.id','desc')
                ->where('financialyear',$request->year)
                ->get();
    
                
                $clientscount= count($clients);
                $newclients= count($clientfyear);
                $clientstatuscount =count($clientstatus);
                if(count($clients) > 0) {
    
                    return response()->json([
                        "status" => $this->status,
                        "success" => true, 
                        "count" => count($clients),
                        "data" => [
                            "balance" =>$clientstatuscount ?? '0',
                            "total" => $clientscount ?? '0',
                             "paid" => $newclients ?? '0', ],
                             "dataall"=>$GetallData,
                    ]);
                }else{
                    return response()->json([
                        "status" => "failed",
                        "success" => false, 
                        "data" => [
                            "balance" =>'0',
                            "total" =>  '0',
                            "paid" => '0']
                    ]);
                }
            }
        }

        public function financialmonth(Request $request)
        {
            $month = $request->month;
            if ($month == "Select All") {
                $getBalance = DB::table('payments')
                ->select('balance','total','paid','financialyear')
                ->where('clienttype',$request->type)
                ->where('financialyear',$request->year)
                // ->groupby('financialyear')
                ->get();

             $GetallData = DB::table('payments as pay')
            ->select('pay.*','co.congregation','pr.province')
            ->leftjoin('congregation as co','co.id','pay.congregation')
            ->leftjoin('provinces as pr','pr.id','pay.province')
            ->where('financialyear',$request->year)
            ->where('clienttype',$request->type)
            ->get();
            
            }else{

                $getBalance = DB::table('payments')
            ->select('balance','total','paid','financialyear')
            ->where('clienttype',$request->type)
            ->where(DB::raw("(DATE_FORMAT(created_at,'%M'))"),$request->month)
            ->where('financialyear',$request->year)
            ->get();
           
            $GetallData = DB::table('payments as pay')
            ->select('pay.*','co.congregation','pr.province')
            ->leftjoin('congregation as co','co.id','pay.congregation')
            ->leftjoin('provinces as pr','pr.id','pay.province')
            ->where(DB::raw("(DATE_FORMAT(pay.created_at,'%M'))"),$request->month)
            ->where('financialyear',$request->year)
            ->where('clienttype',$request->type)
            ->get();

            }
            
            $balance=[];
            $total=[];
            $Paid=[];
            

            foreach ($getBalance as $key => $value) {
                $balance[]= $value->balance;
                $total[]=$value->total;
                $Paid[]= $value->paid;
                
            }
         
            $balances =array_sum($balance);
            $totalval = array_sum($total);
            $paidval = array_sum($Paid);
            $balanceamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $balances);
            $totalamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $totalval);
            $paidamount = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $paidval);

            $perbal =  round(($balances * 100) / $totalval ,2);
            $perpaid = round(($paidval * 100) / $totalval,2);

            if(count($getBalance) > 0) {
                return response()->json(["status" => $this->status, "success" => true, 
                            "count" => count($getBalance), "data" => [
                                "balance" =>$balanceamount ?? '0',
                                "total" => $totalamount ?? '0',
                                "paid" => $paidamount ?? '0',
                               
                            ],
                            "dataall"=>$GetallData,
                ]);
            }
            else {
                return response()->json(["status" => "failed",
                "success" => false,  "count" => count($getBalance), "data" => [
                    "balance" => '0',
                    "total" =>  '0',
                    "paid" =>'0',
                    ]
                ]);
            }

        }
}

