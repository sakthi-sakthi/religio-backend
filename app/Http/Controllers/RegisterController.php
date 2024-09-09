<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ForgetRequest;
use App\Http\Requests\ResetRequest;
use App\Mail\ForgetMail;
use App\Mail\Domain;
use Mail;
Use Auth;
use DB;


class RegisterController extends Controller
{
    Private $status = 200;
       
    public function Register(Request $req)
    {
       
             $user = new User;
             $user->username=$req->input('username');
             $user->email=$req->input('email');
             $user->password=Hash::make($req->input('password'));
             $user->confirmpassword=Hash::make($req->input('confirmpassword'));
             $user->role=$req->input('role');
             $user->save();

              return response()->json(["status" =>"success", "message" => "stored successfully"]);
       }  
               
          

    public function Login(Request $req)
    {
       $user = User::where('email',$req->email)->first();
       
       if(!$user || !Hash::check($req->password,$user->password))
       {
            return["loginVal"=>"error"];
       }else{
            return [ 
                "loginVal" => "true",
                "user" => $user,
            ]; 
       }
    }

    public function UsersList() {
    
      $userlist = User::orderBy('id','desc')->get();
          if(count($userlist) > 0) {
              return response()->json(["status" => $this->status, "success" => true, 
                          "count" => count($userlist), "data" => $userlist]);
          }
          else {
              return response()->json(["status" => "failed",
              "success" => false, "message" => "Whoops! no record found"]);
          }
      }

      public function UsersListDelete($id){

         $Userdel =User::find($id);
         $Userdel->delete();
         return response()->json(
             ["status" => $this->status, "success" => true, 
             "message" => " User Data deleted  successfully"]);
     }

     public function UserEdit($id){
           
      $UserEdit = User::where('id',$id)->get();
      if(count($UserEdit) > 0) {
          return response()->json(["status" => $this->status, "success" => true, 
                      "count" => count($UserEdit), "data" => $UserEdit]);
      }
      else {
          return response()->json(["status" => "failed",
          "success" => false, "message" => "Whoops! no record found"]);
      }
  }

    public function Userupdate($id,Request $request){
            
        $Userupdate = User::where('id',$id)
        ->update([
            "username" => $request->username,
            "email" => $request->email,
            "role" => $request->role,  
        ]);
        return response()->json(
            ["status" => $this->status, "success" => true, 
            "message" => " User Data updated  successfully"]);
    }

    public function ForgetPassword(ForgetRequest $request){
        $email = $request->email;

        if (User::where('email',$email)->doesntExist()) {
            return response([
                'message' => 'Email Invalid'
            ],401);
        }

        // generate Randome Token 
        $token = rand(100,1000000);

        try{
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token
            ]);

            // Mail Send to User 
            Mail::to($email)->send(new ForgetMail($token));

            return response([
                'message' => 'Reset Password Mail send on your email'
            ],200);

        }catch(Exception $exception){
            return response([
                'message' => $exception->getMessage()
            ],400);
        }
    } // end mehtod 

    public function ResetPassword(ResetRequest $request){

        $email = $request->email;
        $token = $request->token;
        $password = Hash::make($request->password);

        $emailcheck = DB::table('password_resets')->where('email',$email)->first();
        $pincheck = DB::table('password_resets')->where('token',$token)->first();

        if (!$emailcheck) {
            return response([
                'message' => "Email Not Found"
            ],401);          
         }
         if (!$pincheck) {
            return response([
                'message' => "Pin Code Invalid"
            ],401);          
         }

         DB::table('users')->where('email',$email)->update(['password' => $password]);
         DB::table('password_resets')->where('email',$email)->delete();

         return response([
            'message' => 'Password Change Successfully'
         ],200);
    

    }// end method 
public function Projectexpire(){
  $getcmonth =date('m')-1;
  $getday =date('d');

 if ($getday == '01') {
 $getdomain =  DB::table('domainrenewal')->where(DB::raw("(DATE_FORMAT(domain_expire_date,'%m'))"),$getcmonth)->get();

//  foreach ($getdomain as $key => $value) {
//     $exdate =$value->domain_expire_date;

//  }
 
 foreach ($getdomain as $key => $domain) {

                $email = "muni20002raj@gmail.com";
                $bodyContent = [
                    'sitename' => $domain->sitename,
                    'siteurl'   => $domain->siteurl,
                    'domailexdate'=> $domain->domain_expire_date,
                    ];

                {  
                    try {
                        Mail::to($email)->send(new Domain($bodyContent));
                        }
                        catch (Exception $e) {
                    }
                } 
            }
}
    
}

}  

