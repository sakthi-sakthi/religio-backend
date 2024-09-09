<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Mail\CathedraDemo;
use App\Mail\CathedraContact;
use App\Mail\CathedraContactuser;
use App\Mail\CathedraDemouser;
use App\Models\CathedraDemos;
use App\Models\Cathedracontactus;
class MailController extends Controller
{
   Private $status = 200;
   public function sendContactMail (Request $request)
   {
    $contact_data = [];
    $contact_data['name'] = $request->input('name');
    $contact_data['email'] = $request->input('email');
    $contact_data['province'] = $request->input('province');
    $contact_data['mobile'] = $request->input('mobile');
    $contact_data['message'] = $request->input('message');

    Mail::to('elango@boscosofttech.com')->send(new ContactFormMail( $contact_data));
    return response()->json(
        ["status" => $this->status, "success" => true, 
        "message" => " Mail Sent successfully"]);
   }

   public function catedraDemo(Request $request){
      
      
            $data = [
               'name' =>  $request['name'],
               'email' => $request['email'],
               'mobile' => $request['mobile'],
         ];
         CathedraDemos::create($data);

                     $email = $request['email'];
                     $bodyContent = [
                         'toName' => $request['name'],
                         'toemail'   => $request['email'],
                         'tomobile'=> $request['mobile'],
                         ];
                     {  
                         try {
                           Mail::to('muni20002raj@gmail.com')->send(new CathedraDemo($bodyContent));
                           Mail::to($email)->send(new CathedraDemouser($bodyContent));
                            }
                             catch (Exception $e) {
                         }
                     } 
                  return response()->json('request sent sucessfully');
    }
    public function catedracontact(Request $request){
      

      $data = [
         'name' =>  $request['name'],
         'email' => $request['email'],
         'mobile' => $request['mobile'],
         'subject' => $request['subject'],
   ];
   Cathedracontactus::create($data);

      $email = $request['email'];
      $bodyContent = [
          'toName' => $request['name'],
          'toemail'   => $request['email'],
          'tomobile'=> $request['mobile'],
          'tosubject'=> $request['subject'],
          ];
      {  
          try {
            Mail::to('muni20002raj@gmail.com')->send(new CathedraContact($bodyContent));
            Mail::to($email)->send(new CathedraContactuser($bodyContent));
             }
              catch (Exception $e) {
          }
      } 
   return response()->json('request sent sucessfully');
}

}
