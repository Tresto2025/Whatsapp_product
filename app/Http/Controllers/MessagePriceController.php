<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MessagePrices;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MessagePriceController extends Controller
{
    public function index()
    {
        $price = MessagePrices::first();
        return view('dashboard.message_price.index',compact('price'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'price_per_message' => 'required|numeric|min:1'
        ]);
        $price = MessagePrices::first();
        if($price){
            $price->update(['price_per_message' => $request->price_per_message]);
        }else{
            MessagePrices::create(['price_per_message' => $request->price_per_message]);
        }
        return redirect()->back()->with('success','Message price updated successfully');
    }
}
