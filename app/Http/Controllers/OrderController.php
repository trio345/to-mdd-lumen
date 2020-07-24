<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\MidtransController;
use App\Order;
use App\OrderItem;
use App\Product;



class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->gross_amount = 0;
    }

    public function index(){
        $datas = Order::all();
        if ( $datas ){
            return response($content = ["status" => "success", "data" => ["attributes" => $datas]], $status = 201);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Order::find($id);

        $this->validate($request, [
            'user_id' => 'required'
        ]);

        $data->name = $request->input('name');
        $data->price = $request->input('price');

            
        if ( $data->save() ){
            return response($content = ["status" => "success", "data" => ["attributes" => $data]], $status = 201);
        } else {
            return response($content = ["status" => "failed"]);
        }
    }


    public function create(Request $request)
    {
        $this->validate($request, [
            'data.attributes.user_id' => 'required',
            'data.attributes.order_detail.*' => 'present|array'

        ]);
        $order = new Order();
        $request_all = $request->all();
        $order->user_id = $request_all["data"]["attributes"]["user_id"];
        $order->status = 'created';
        
        if ( $order->save() ){
            
            
            $request_order = $request_all["data"]["attributes"]["order_detail"];
            for ($i = 0; $i < count($request_order); $i++){
                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->product_id = $request_order[$i]["product_id"];
                $order_item->quantity = $request_order[$i]["quantity"];
                $order_item->save();
            }

            
        }
        return response($content = ["status" => "success", "messages" => "Data berhasil ditambahkan"], 201);

    }


    public function find($id)
    {
        $data = Order::find($id);

        if ( $data ){
            return response($content = ["status" => "success", "data" => ["attributes" => $data]], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"customer not found!"]);
        }
    }


    public function delete($id)
    {
        $order = Order::with('order_items')->where('order_id', $id);
        
        if ($order){
            $order->delete();
            return response($content = ["status" => "success", "messages" => "berhasil dihapus"], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"gagal dihapus!"]);
        }
    }
}
