<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Onlinemeet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OnlinemeetController extends Controller
{
    Private $status = 200;

    public function onlinemeetstatus($id , Request $request)
    {

        $file = $request->file('FileAttachment');
        if($file){
        $fileName = $file->getClientOriginalName();
        $file->move(public_path('Ourclient/online/'), $fileName);
        $getid  = onlinemeet::where('client_id', $id)->max('om_id');
             $onlinemeetstatusArray['params'] = array(
                "onlinemeeting" =>$request->onlinemeeting,
                "onlinedate"    =>$request->onlinedate,
                "onlinehours"   =>$request->onlinehours,
                "onlinerating"  =>$request->onlinerating,
                "client_id" => $id,
                "om_id" => $getid+1,
                'path' => $file->getClientOriginalName()
             );
        }else{
          $getid  = onlinemeet::where('client_id', $id)->max('om_id');
             $onlinemeetstatusArray['params'] = array(
                "onlinemeeting" =>$request->onlinemeeting,
                "onlinedate"    =>$request->onlinedate,
                "onlinehours"   =>$request->onlinehours,
                "onlinerating"  =>$request->onlinerating,
                "client_id" => $id,
                "om_id" => $getid+1
             );
        }
        
            $onlinemeetstatus  =  onlinemeet::create($onlinemeetstatusArray['params']);
            
            if(!is_null($onlinemeetstatus)){ 

                return response()->json(["status" => $this->status, "success" => true, 
                        "message" => "Online Status record created successfully", "data" => $onlinemeetstatus]);
            }    
            else {
                return response()->json(["status" => "failed", "success" => false,
                            "message" => "Whoops! failed to create."]);
        }      
    }

    // list value

    public function onlinemeetstatusList($id) {
    $online = onlinemeet::select('*' ,DB::raw("DATE_FORMAT(onlinedate, '%d-%m-%Y') as onlinedate"))->where('client_id',$id)
    ->whereNotNull('om_id')->orderBy('om_id','desc')->get();
    

    
        if(count($online) > 0) {
            return response()->json(["status" => $this->status, "success" => true, 
                        "count" => count($online), "data" => $online]);
        }
        else {
            return response()->json(["status" => "failed",
                        "success" => false, "message" => "Whoops! no record found"]);
        }
    }

      // edit
      public function onlinetatusedit($id)
      {
         $parts = explode('-', $id);
         $om_id = $parts[1]; 
         $clientid =$parts[0]; 
          $onlinestatus = onlinemeet::where('om_id',$om_id)->where('client_id',$clientid)->get();
          if(count($onlinestatus) > 0) {
              return response()->json(["status" => $this->status, "success" => true, 
                          "count" => count($onlinestatus), "data" => $onlinestatus]);
          }
          else {
              return response()->json(["status" => "failed",
              "success" => false, "message" => "Whoops! no record found"]);
          }
      }
  
      // update
      public function onlinestatusupdate($id,Request $request){
       
        $parts = explode('-', $id);
        $om_id = $parts[1]; 
        $clientid =$parts[0]; 
        $file = $request->file('FileAttachment');
        if($file){
        $fileName = $file->getClientOriginalName();
        $file->move(public_path('Ourclient/online/'), $fileName);
        $onlineupdate = onlinemeet::where('om_id',$om_id)->where('client_id',$clientid)
          ->update([
              "onlinemeeting" =>$request->onlinemeeting,
              "onlinedate"    =>$request->onlinedate,
              "onlinehours"   =>$request->onlinehours,
              "onlinerating"  =>$request->onlinerating,
              'path' => $file->getClientOriginalName() 
          ]);
        }else{
            $onlineupdate = onlinemeet::where('om_id',$om_id)->where('client_id',$clientid)
          ->update([
              "onlinemeeting" =>$request->onlinemeeting,
              "onlinedate"    =>$request->onlinedate,
              "onlinehours"   =>$request->onlinehours,
              "onlinerating"  =>$request->onlinerating,
          ]);
        }
        
          
          return response()->json(
              ["status" => $this->status, "success" => true, 
              "message" => " Online Status record updated  successfully"]);
      }
  
       // delete
       public function onlinestatusDelete($id){

         $parts = explode('-', $id);
         $om_id = $parts[1]; 
         $clientid =$parts[0]; 
          $onlinedelte = onlinemeet::where('om_id',$om_id)->where('client_id',$clientid)->first();
          if ($onlinedelte) {
            $onlinedelte->delete();
            return response()->json(
                ["status" => $this->status, "success" => true, 
                "message" => "online status record deleted  successfully"]);
        } else {
            return response()->json(
                ["status" => $this->status, "success" => false, 
                "message" => "Sorry this Record not found"]);
        }
      }

      public function upload(Request $request) 
      { 
        $getid = onlinemeet::latest('id')->first(); 
        $id = $getid->id;
        // dd($id);
        $validator    =  Validator::make($request->all(), 
            [     
             "online"  => 'required|mimes:doc,docx,pdf|max:2048', 
            ]
         
        );
           if($validator->fails()) {
            return response()->json(["status" => "failed", "validation_errors" => $validator->errors()]);
            }
           $file = $request->file('online'); 
           $filename = $file->getClientOriginalName();
           $extension = $file->getClientOriginalExtension();
           $location = 'online';

            $document = onlinemeet::where('id',$id)
            ->update([
                "online"   =>$file->getClientOriginalName()
            ]);
            $file->move($location,$filename);
            $filepath = url('online/'.$filename);

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
    public function onlineuploadid(Request $request , $id) 
    { 
      
        $validator    =  Validator::make($request->all(), 
            [     
                "online"  => 'required|mimes:doc,docx,pdf|max:2048', 
            ]
            
        );
            if($validator->fails()) {
            return response()->json(["status" => "failed", "validation_errors" => $validator->errors()]);
            }
            
            $file = $request->File('online'); 
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $location = 'online';

            $updocument = onlinemeet::where('id',$id)
            ->update([
                "online"   =>$file->getClientOriginalName()
            ]);
            $file->move($location,$filename);
            $filepath = url('online/'.$filename);

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
 
 




