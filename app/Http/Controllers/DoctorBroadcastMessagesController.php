<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointments;
use App\Models\BroadcastMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\SmsLogs;
use App\Models\SmsBalance;
use App\Models\WalletBalance;
use App\Models\MessagePrices;
use Twilio\Rest\Client;

class DoctorBroadcastMessagesController extends Controller
{
    private $token;
    private $phone_number_id;
    
    public function __construct()
    {
        $this->token = env('WHATSAPP_TOKEN');
        $this->phone_number_id = env('PHONE_NUMBER_ID');
    }
    public function index(){
        $doctorId = Auth::id();
        $patients = Appointments::where('doctor_id', $doctorId)->orderBy('id', 'desc')->get()->unique('phone')->values();
        $messages = BroadcastMessages::where('send_by', $doctorId)->latest()->get();
        return view('doctor-dashboard.broadcast_messages.index', compact('messages', 'patients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|max:2048',
            'send_to'     => 'nullable|array',
        ]);
    
        $doctorId = auth()->id();
        \Log::info("Broadcast start by doctor: ".$doctorId);
        $wallet = WalletBalance::where('doctor_id',$doctorId)->first();
        $price = MessagePrices::first()->price_per_message;
        if(!$wallet || $wallet->wallet_balance <= 0){
            return redirect()->back()->with('error','Your wallet balance is 0. Please recharge your wallet.');
        }
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/broadcasts'), $fileName);
            $imagePath = 'uploads/broadcasts/'.$fileName;
        }
        $sendTo = null;
        if ($request->send_to) {
            if (in_array('all', $request->send_to)) {
                $sendTo = 'all';
            } else {
                $sendTo = json_encode($request->send_to);
            }
        }
        $broadcast = BroadcastMessages::create([
            'send_by'     => $doctorId,
            'send_to'     => $sendTo,
            'title'       => Str::ucfirst($request->title),
            'description' => Str::ucfirst($request->description),
            'image'       => $imagePath,
            'status'      => 2,
        ]);
        if ($sendTo === 'all') {
            $patients = Appointments::where('doctor_id',$doctorId)->orderBy('id','desc')->get()->unique('phone');
        } else {
            $phones = json_decode($sendTo,true);
            $patients = Appointments::where('doctor_id',$doctorId)->whereIn('phone',$phones)->orderBy('id','desc')->get()->unique('phone');
        }
        $sentCount = 0;
        foreach ($patients as $patient) {
            $wallet = WalletBalance::where('doctor_id',$doctorId)->first();
            if(!$wallet || $wallet->wallet_balance <= 0){
                \Log::warning("Wallet finished during broadcast");
                break;
            }
    
            // ₹1 DEDUCT
            $wallet->decrement('wallet_balance',$price);
            $wallet->increment('total_spent',$price);
            \Log::info("Wallet deducted. Remaining balance: ".$wallet->wallet_balance);
            $phone = str_replace('+','',$patient->phone);
            if(substr($phone,0,2) != '91'){
                $phone = '91'.$phone;
            }
    
            try {
                if($broadcast->image){
                    $response = $this->sendWhatsAppTemplateWithImage(
                        $phone,
                        $patient->name ?? 'Patient',
                        $broadcast->title,
                        $broadcast->description,
                        asset($broadcast->image)
                    );
                } else {
                    $response = $this->sendWhatsAppTemplate(
                        $phone,
                        $patient->name ?? 'Patient',
                        $broadcast->title,
                        $broadcast->description
                    );
                }
                $sentCount++;
                SmsLogs::create([
                    'doctor_id'   => $doctorId,
                    'sms_to'      => $phone,
                    'sms_from'    => 'Meta WhatsApp',
                    'sid'         => null,
                    'body'        => $broadcast->description,
                    'status'      => 'sent',
                    'broadcast_id'=> $broadcast->id,
                    'title'       => $broadcast->title,
                    'description' => $broadcast->description,
                ]);
            } catch (\Exception $e) {
                \Log::error("WhatsApp send failed: ".$e->getMessage());
            }
        }
        $broadcast->update(['status'=>1,'total_send_messages' => $sentCount]);
        return redirect()->route('doctor.broadcast_messages.index')->with('success','Message broadcasted successfully.');
    }
        
    private function sendWhatsAppTemplate($to,$name,$title,$description)
    {
        $payload = [
            "messaging_product"=>"whatsapp",
            "to"=>$to,
            "type"=>"template",
            "template"=>[
                "name"=>"new_doctor_message",
                "language"=>[
                    "code"=>"en"
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
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($payload));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    private function sendWhatsAppTemplateWithImage($to,$name,$title,$description,$image)
    {
        $payload = [
            "messaging_product"=>"whatsapp",
            "to"=>$to,
            "type"=>"template",
            "template"=>[
                "name"=>"new_doctor_message_with_image",
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
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($payload));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($ch);
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
