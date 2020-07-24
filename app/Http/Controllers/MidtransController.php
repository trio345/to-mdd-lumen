<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Midtrans\Config;
use App\Http\Controllers\Midtrans\Transaction;
use App\Http\Controllers\Midtrans\ApiRequestor;
use App\Http\Controllers\Midtrans\SnapApiRequestor;
use App\Http\Controllers\Midtrans\Notification;
use App\Http\Controllers\Midtrans\CoreApi;
use App\Http\Controllers\Midtrans\Snap;

use App\Http\Controllers\Midtrans\Sanitizer;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
// use App\Customer;





class MidtransController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    //
    public function __construct($order_id, $gross_amount){
        $this->order_id = $order_id;
        $this->gross_amount = $gross_amount;
    }


    public function getSnapToken(Request $req){

        // $item_list = array();
        // $amount = 0;
        Config::$serverKey = 'SB-Mid-server-RhcTfWbUDIJG780Eu7fYZP25';
        if (!isset(Config::$serverKey)) {
            return "Please set your payment server key";
        }
        Config::$isSanitized = true;

        Config::$is3ds = true;

        $transaction_details = array(
            'order_id' => $this->order_id,
            'gross_amount' => $this->gross_amount, 
        );

        try {
            $snapToken = Snap::getSnapToken($transaction);
            return response()->json($snapToken);
            // return ['code' => 1 , 'message' => 'success' , 'result' => $snapToken];
        } catch (\Exception $e) {
            return ['status' => 400,'message' => 'failed'];
        }

    }
}