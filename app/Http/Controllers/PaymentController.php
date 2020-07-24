<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MidtransController;
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

        $response = [
            "order_id" => $request->input('data.attributes.order_id'),
            "transaction_id" => $request->input('data.attributes.transaction_id'),
            "payment_type" => $request->input('data.attributes.payment_type'),
            "gross_amount" => $request->input('data.attributes.gross_amount'),
            "transaction_time" => $request->input('data.attributes.transaction_time'),
            "transaction_status" => $request->input('data.attributes.transaction_status')
        ];
        // $res = ["attributes" => $response];
        $mid_res = new MidtransController(10, 20000);

        if ( Payment::create($response) ){
            return $mid_res->getSnapToken();
        } else {
            return response($content = ["status" => "failed"]);
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
