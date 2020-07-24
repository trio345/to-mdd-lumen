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
        // /
    }

    public function index(){
        $datas = Order::with('order_items')->get();
        if ( $datas ){
            return response($content = ["status" => "success", "data" => ["attributes" => $datas]], $status = 201);
        }
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        $this->validate($request, [
            'user_id' => 'required'
        ]);

        $request_all = $request->all();
        $order->user_id = $request_all["data"]["attributes"]["user_id"];
        $order->status = 'created';
        
        if ( $order->save() ){
            $request_order = $request_all["data"]["attributes"]["order_detail"];
            for ($i = 0; $i < count($request_order); $i++){
                $order_item = OrderItem::where('order_id', $id)->first();
                $order_item->product_id = $request_order[$i]["product_id"];
                $order_item->quantity = $request_order[$i]["quantity"];
                $order_item->save();
            }
    }

    return response($content = ["status" => "success", "messages" => "Data berhasil diupdate!"], 201);
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


    public function delete($id)
    {
        $order = Order::find($id);
        if ( $order->delete() ){
            $order_item = OrderItem::where('order_id', $id)->delete();
        
            return response($content = ["status" => "success", "message" => "Data berhasil dihapus!"], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"customer not found!"]);
        }
    }


    public function find($id)
    {
        $order_id = Order::find($id);
        $getJoin = Order::where('id', $id)->with('order_items');
        
        if ($order_id){
            return response()->json(["messages"=>"success retrive data", "status" => true, "data" => $getJoin], 201);
        } else{ 
            return response()->json(["messages"=>"data not found"], 403);
        }
    }
}
