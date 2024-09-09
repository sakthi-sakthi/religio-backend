<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Onsitemeet;
use App\Models\Onlinemeet;
use Illuminate\Support\Facades\DB;

class OnsitemeetController extends Controller
{
    Private $status = 200;

    public function onsitemeetstatus($id,Request $request)
    {
        $file = $request->file('onsite');
        if($file){
        $fileName = $file->getClientOriginalName();
        $file->move(public_path('Ourclient/onsite/'), $fileName);
        $getid = Onsitemeet::where('client_id', $id)->max('os_id');
             $onsitemeetstatusArray['params'] = array(
                "onsitedate"    =>$request->onsitedate,
                "onsitedays"    =>$request->onsitedays,
                "expensive"     =>$request->expensive,
                "onsiterating"  =>$request->onsiterating,
                "onsiteplace"  =>$request->onsiteplace,
                "client_id" =>$id,
                "os_id"=> $getid+1,
                'onsite' =>$file->getClientOriginalName()
             );
        }else{ 
        $getid = Onsitemeet::where('client_id', $id)->max('os_id');
             $onsitemeetstatusArray['params'] = array(
                "onsitedate"    =>$request->onsitedate,
                "onsitedays"    =>$request->onsitedays,
                "expensive"     =>$request->expensive,
                "onsiterating"  =>$request->onsiterating,
                "onsiteplace"  =>$request->onsiteplace,
                "client_id" =>$id,
                "os_id"=> $getid+1,
             );
        }
        

            $onsitemeetstatus  =  Onsitemeet::create($onsitemeetstatusArray['params'] );
      
            if(!is_null($onsitemeetstatus)){ 

                return response()->json(["status" => $this->status, "success" => true, 
                        "message" => "onsitestatus record created successfully", "data" => $onsitemeetstatus]);
            }    
            else {
                return response()->json(["status" => "failed", "success" => false,
                            "message" => "Whoops! failed to create."]);
        }      
    }

    // list value
    public function onsitemeetstatusList($id) {
        
        $onsite = Onsitemeet::select( '*',DB::raw("DATE_FORMAT(onsitedate, '%d-%m-%Y') as onsitedate"))->where('client_id',$id)
        ->whereNotNull('os_id')->orderBy('os_id','desc')->get();
      
        if(count($onsite) > 0) {
            return response()->json(["status" => $this->status, "success" => true, 
                        "count" => count($onsite), "data" => $onsite]);
        }
        else {
            return response()->json(["status" => "failed",
                        "success" => false, "message" => "Whoops! no record found"]);
        }
    }

      // edit
      public function onsitestatusedit($id)
      {
        $parts = explode('-', $id);
        $os_id = $parts[1]; 
        $clientid =$parts[0]; 
        $onsitestatus = Onsitemeet::where('os_id',$os_id)->where('client_id',$clientid)->get();
        
          if(count($onsitestatus) > 0) {
              return response()->json(["status" => $this->status, "success" => true, 
                          "count" => count($onsitestatus), "data" => $onsitestatus]);
          }
          else {
              return response()->json(["status" => "failed",
              "success" => false, "message" => "Whoops! no record found"]);
          }
      }

      // update
      public function onsitestatusupdate($id,Request $request){
         
        $parts = explode('-', $id);
        $os_id = $parts[1]; 
        $clientid =$parts[0]; 
        $file = $request->file('onsite');
        if($file){
        $fileName = $file->getClientOriginalName();
        $file->move(public_path('Ourclient/onsite/'), $fileName);
        $onsitestatusupdate = Onsitemeet::where('os_id',$os_id)->where('client_id',$clientid)
          ->update([
              "onsitedate"    =>$request->onsitedate,
              "onsitedays"    =>$request->onsitedays,
              "expensive"     =>$request->expensive,
              "onsiterating"  =>$request->onsiterating,
              "onsiteplace"  =>$request->onsiteplace,
              'onsite' => $file->getClientOriginalName()
          ]);
        }else{
            $onsitestatusupdate = Onsitemeet::where('os_id',$os_id)->where('client_id',$clientid)
          ->update([

              "onsitedate"    =>$request->onsitedate,
              "onsitedays"    =>$request->onsitedays,
              "expensive"     =>$request->expensive,
              "onsiterating"  =>$request->onsiterating,
              "onsiteplace"  =>$request->onsiteplace,
            ]);
        }
          return response()->json(
              ["status" => $this->status, "success" => true, 
              "message" => " Onsite Status record updated  successfully"]);
      }
  
       // delete
       public function onsitestatusDelete($id){
        $parts = explode('-', $id);
        $os_id = $parts[1]; 
        $clientid =$parts[0]; 
         $onsitestatusdelte = Onsitemeet::where('os_id',$os_id)->where('client_id',$clientid)->first();
         if ($onsitestatusdelte) {
            $onsitestatusdelte->delete();
            return response()->json(
                ["status" => $this->status, "success" => true, 
                "message" => "Onsite status record deleted  successfully"]);
        } else {
            return response()->json(
                ["status" => $this->status, "success" => false, 
                "message" => "Sorry this Record not found"]);
        }
           
      }

    //   file upload
            public function onsiteupload(Request $request) 
            { 
                $getid = Onsitemeet::latest('id')->first(); 
                $id = $getid->id;
                $validator    =  Validator::make($request->all(), 
                    [     
                     "onsite"  => 'required|mimes:doc,docx,pdf,csv|max:2048', 
                    ]
                 
                );
                   if($validator->fails()) {
                    return response()->json(["status" => "failed", "validation_errors" => $validator->errors()]);
                    }
                   $file = $request->file('onsite'); 
                   $filename = $file->getClientOriginalName();
                   $extension = $file->getClientOriginalExtension();
                   $location = 'onsite';
        
                    $document = Onsitemeet::where('id',$id)
                    ->update([
                        "onsite"   =>$file->getClientOriginalName()
                    ]);
                    $file->move($location,$filename);
                    $filepath = url('onsite/'.$filename);
        
                        if(!is_null($document)){ 
        
                            return response()->json(["status" => $this->status, "success" => true, 
                                    "message" => "upload  successfully", "data" => $document]);
                        }    
                        else {
                            return response()->json(["status" => "failed", "success" => false,
                                        "message" => "Whoops! failed to create."]);
                        }   
                  
                    }

//    file upload
    public function onsiteuploadid(Request $request , $id) 
    { 
      
        $validator    =  Validator::make($request->all(), 
            [     
                "onsite"  => 'required|mimes:doc,docx,pdf,csv|max:2048', 
            ]
            
        );
            if($validator->fails()) {
            return response()->json(["status" => "failed", "validation_errors" => $validator->errors()]);
            }
            
            $file = $request->File('onsite'); 
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $location = 'onsite';

            $updocument = Onsitemeet::where('id',$id)
            ->update([
                "onsite"   =>$file->getClientOriginalName()
            ]);
            $file->move($location,$filename);
            $filepath = url('onsite/'.$filename);

                if(!is_null($updocument)){ 

                    return response()->json(["status" => $this->status, "success" => true, 
                            "message" => "upload  successfully", "data" => $updocument]);
                }    
                else {
                    return response()->json(["status" => "failed", "success" => false,
                                "message" => "Whoops! failed to create."]);
                }   
            
            }

 }




