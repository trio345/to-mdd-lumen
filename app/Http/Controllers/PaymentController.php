<?php

namespace App\Http\Controllers;

// midtrans
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
use App\Order;
use App\OrderItem;
use App\Payment;



class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(){
        $datas = Payment::all();
        if ( $datas ){
            return response($content = ["status" => "success", "data" => ["attributes" => $datas]], $status = 201);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Payment::find($id);

        $this->validate($request, [
            'data.attributes.order_id' => 'required',
            'data.attributes.transaction_id' => 'required',
            'data.attributes.payment_type' => 'required',
            'data.attributes.gross_amount' => 'required',
            'data.attributes.transaction_time' => 'required',
            'data.attributes.transaction_status' => 'required'
                    ]);

        $data->order_id = $request->input('data.attributes.order_id');
        $data->transaction_id = $request->input('data.attributes.transaction_id');
        $data->payment_type = $request->input('data.attributes.payment_type');
        $data->gross_amount = $request->input('data.attributes.gross_amount');
        $data->transaction_time = $request->input('data.attributes.transaction_time');
        $data->transaction_status = $request->input('data.attributes.transaction_status');

            
        if ( $data->save() ){
            return response($content = ["status" => "success", "data" => ["attributes" => $data]], $status = 201);
        } else {
            return response($content = ["status" => "failed"]);
        }
    }


    public function create(Request $request)
    {
        $this->validate($request, [
            'data.attributes.order_id' => 'required',
            'data.attributes.transaction_id' => 'required',
            'data.attributes.payment_type' => 'required',
            'data.attributes.gross_amount' => 'required',
            'data.attributes.transaction_time' => 'required',
            'data.attributes.transaction_status' => 'required'            
        ]);

        $req = [
            "order_id" => $request->input('data.attributes.order_id'),
            "transaction_id" => $request->input('data.attributes.transaction_id'),
            "payment_type" => $request->input('data.attributes.payment_type'),
            "gross_amount" => $request->input('data.attributes.gross_amount'),
            "transaction_time" => $request->input('data.attributes.transaction_time'),
            "transaction_status" => $request->input('data.attributes.transaction_status')
        ];
        Payment::create($req);
        

        $order_items = OrderItem::with('products')->get();
        // midtrans setup
        $item_list = array();
        Config::$serverKey = 'SB-Mid-server-RhcTfWbUDIJG780Eu7fYZP25';
        if (!isset(Config::$serverKey)) {
            return "Please enter the correct key";
        }
        Config::$isSanitized = true;

        Config::$is3ds = true;

         $item_list[] = [
                'id' => $order_items->products->id,
                'price' => $order_items->products->price,
                'quantity' => $order_items->products->quantity,
                'name' => $order_items->products->name
        ];
        
        $item_details = $item_list;
        $enable_payments = array('bank_transfer');

        // Fill transaction details
        $transaction = array(
            'enabled_payments' => $enable_payments,
            'transaction_details' => $request,
            'item_details' => $item_details
        );
        // return $transaction;
        try {
            $snapToken = Snap::getSnapToken($transaction);
            return response()->json([
                "message"=> "success",
                "status" => true,
                "token" => $snapToken,
                "results" => $request
            ]);
        } catch (\Exception $e) {
            dd($e);
            return ['code' => 0 , 'message' => 'failed'];
        }
        
    }

    public function find($id)
    {
        $data = Payment::find($id);

        if ( $data ){
            return response($content = ["status" => "success", "data" => ["attributes" => $data]], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"customer not found!"]);
        }
    }


    public function delete($id)
    {
        $data = Payment::find($id);
        if ($data->delete()){
            return response($content = ["status" => "success", "messages" => "berhasil dihapus"], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"gagal dihapus!"]);
        }
    }
}
