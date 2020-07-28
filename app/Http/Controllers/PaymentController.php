<?php

namespace App\Http\Controllers;

// midtrans
// use App\Http\Controllers\Midtrans\Config;
// use App\Http\Controllers\Midtrans\Transaction;
// use App\Http\Controllers\Midtrans\ApiRequestor;
// use App\Http\Controllers\Midtrans\SnapApiRequestor;
// use App\Http\Controllers\Midtrans\Notification;
// use App\Http\Controllers\Midtrans\CoreApi;
// use App\Http\Controllers\Midtrans\Snap;

// use App\Http\Controllers\Midtrans\Sanitizer;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Order;
use App\OrderItem;
use App\Payment;
use Illuminate\Support\Facades\Http;




class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $auth;
    public function __construct()
    {
        $this->auth = base64_encode('SB-Mid-server-RhcTfWbUDIJG780Eu7fYZP25:');
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
            'data.attributes.payment_type' => 'required',
            'data.attributes.gross_amount' => 'required',
            'data.attributes.bank' => 'required',
            'data.attributes.order_id' => 'required'            
        ]);

        $req = $request->all();

        $transaction_req = [
            "payment_type" => $req['data']['attributes']['payment_type'],
            "bank_transfer" => [
                "bank" => $req['data']['attributes']['bank']
            ],
            "transaction_details" => [
                "order_id" => $req['data']['attributes']["order_id"],
                "gross_amount" => $req['data']['attributes']["gross_amount"]
            ]
        ];

        $url = 'https://api.sandbox.midtrans.com/v2/charge';
        
        $http_header = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.$this->auth,
            'Accept' => 'application/json'
        ];

        $response = Http::withHeaders($http_header)->post($url, $transaction_req);
        $data = $response->json();
        if ( $data["status_code"] == "406") {
            return response()->json(["status" => "failed", 
                                     "message" => "Transaksi sudah dilakukan! periksa kembali order_id anda"], 406);
        }else {
            $insertData = [
                "order_id" => $data["order_id"],
                "transaction_id" => $data["transaction_id"],
                "payment_type" => $data["payment_type"],
                "gross_amount" => $data["gross_amount"],
                "transaction_time" => $data["transaction_time"],
                "transaction_status" => $data["transaction_status"]
            ];
            if (Payment::create($insertData)){
                return response()->json(["status" => "success", 
                                        "message" => "Transaksi berhasil mohon untuk menunggu konfirmasi!",
                                        "results" => $insertData
            ], 200);
            } else {
                return response()->json(["status" => "failed",
                                         "message" => "Data gagal disimpan!"
            ], 401);
            }
        }

        // $response = Http::withBasicAuth('SB-Mid-server-RhcTfWbUDIJG780Eu7fYZP25', '')->post($url, $transaction_req);
        
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


    public function pushNotif(Response $request){
        $payment = Payment::where('order_id', $request->order_id);
        $payment->transaction_status = $request->transaction_status;
        $payment->transaction_time = $request->transaction_time;
        $payment->save();

    }
}
