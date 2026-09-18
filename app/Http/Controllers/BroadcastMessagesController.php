<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BroadcastMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Twilio\Rest\Client;


class BroadcastMessagesController extends Controller
{
    private $token;
    private $phone_number_id;
    
    public function __construct()
    {
        $this->token = env('WHATSAPP_TOKEN');
        $this->phone_number_id = env('PHONE_NUMBER_ID');
    }
    
    public function index(){
        $messages = BroadcastMessages::latest()->get();
        $doctors  = User::where('role', 2)->get(); 
        //echo "<pre>";
        //print_r($doctors->toArray());die;
        return view('dashboard.broadcast_messages.index', compact('messages', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|max:8048',
            'send_to'     => 'nullable|array',
        ]);
    
        // Upload Image
        $imagePath = null;
    
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/broadcasts'), $fileName);
            $imagePath = 'uploads/broadcasts/'.$fileName;
        }
    
        // Recipients
        if ($request->send_to && in_array('all', $request->send_to)) {
            $doctors = User::where('role',2)->get();
        } elseif ($request->send_to) {
            $doctorIds = $request->send_to;
            $doctors = User::where('role',2)->whereIn('id',$doctorIds)->get();
        } else {
            $doctors = User::where('role',2)->get();
        }
        // Broadcast record
        $broadcast = BroadcastMessages::create([
            'send_by'     => auth()->id(),
            'send_to'     => json_encode($doctors->pluck('id')),
            'title'       => Str::ucfirst($request->title),
            'description' => Str::ucfirst($request->description),
            'image'       => $imagePath,
            'status'      => 2,
        ]);
    
        foreach ($doctors as $doctor) {
            if(!$doctor->phone) continue;
            $phone = preg_replace('/\D/', '', $doctor->phone);

            // agar 10 digit hai to 91 add karo
            if(strlen($phone) == 10){
                $phone = '91'.$phone;
            }
            try {
                if($broadcast->image){
                    $this->sendDoctorWhatsAppTemplateWithImage(
                        $phone,
                        $doctor->first_name.' '.$doctor->last_name,
                        $broadcast->title,
                        $broadcast->description,
                        asset($broadcast->image)
                    );
                }else{
                    $this->sendDoctorWhatsAppTemplate(
                        $phone,
                        $doctor->first_name.' '.$doctor->last_name,
                        $broadcast->title,
                        $broadcast->description
                    );
                }
                \Log::info("WhatsApp sent to Doctor {$phone}");
            } catch (\Exception $e) {
                \Log::warning("WhatsApp failed for {$phone}: ".$e->getMessage());
            }
    
        }
        $broadcast->update(['status'=>1]);
        return redirect()->route('broadcast_messages.index')->with('success','Message broadcasted successfully.');
    }
    
    private function sendDoctorWhatsAppTemplate($to,$name,$title,$description)
    {
        $payload = [
            "messaging_product"=>"whatsapp",
            "to"=>$to,
            "type"=>"template",
            "template"=>[
                "name"=>"doctor_update_notification",
                "language"=>[
                    "code"=>"en_GB"
                ],
                "components"=>[
                    [
                        "type"=>"body",
                        "parameters"=>[
                            ["type"=>"text","text"=>$name],
                            ["type"=>"text","text"=>$title],
                            ["type"=>"text","text"=>$description]
                        ]
                    ]
                ]
            ]
        ];
        $url = "https://graph.facebook.com/v22.0/{$this->phone_number_id}/messages";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        \Log::info('Meta API Response: '.$response);
        curl_close($ch);
        return $response;
    }
    
    private function sendDoctorWhatsAppTemplateWithImage($to,$name,$title,$description,$image)
    {
        $payload = [
            "messaging_product"=>"whatsapp",
            "to"=>$to,
            "type"=>"template",
            "template"=>[
                "name"=>"doctor_message_with_image",
                "language"=>[
                    "code"=>"en"
                ],
                "components"=>[
                    [
                        "type"=>"header",
                        "parameters"=>[
                            [
                                "type"=>"image",
                                "image"=>[
                                    "link"=>$image
                                ]
                            ]
                        ]
                    ],
                    [
                        "type"=>"body",
                        "parameters"=>[
                            ["type"=>"text","text"=>$name],
                            ["type"=>"text","text"=>$title],
                            ["type"=>"text","text"=>$description]
                        ]
                    ]
                ]
            ]
        ];
        $url = "https://graph.facebook.com/v22.0/{$this->phone_number_id}/messages";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        \Log::info('Meta API Response: '.$response);
        curl_close($ch);
        return $response;
    }

    public function resend($id){
        $message = BroadcastMessages::findOrFail($id);
        return redirect()->back()->with('success', 'Message resent successfully.');
    }

    public function destroy($id){
        $message = BroadcastMessages::findOrFail($id);
    
        if ($message->image && file_exists(public_path($message->image))) {
            unlink(public_path($message->image));
        }
    
        $message->delete();
    
        return redirect()->back()->with('success', 'Message deleted successfully.');
    }
}
