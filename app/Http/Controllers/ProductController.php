<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Product;



class ProductController extends Controller
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
        $datas = Product::all();
        if ( $datas ){
            return response($content = ["status" => "success", "data" => ["attributes" => $datas]], $status = 201);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Product::find($id);

        $this->validate($request, [
            'name' => 'required',
            'price' => 'required'
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
            'name' => 'required',
            'price' => 'required'
        ]);

        $response = [
            "name" => $request->input('name'),
            "price" => $request->input('price')
        ];
        $res = ["attributes" => $response];

            
        if ( Product::create($response) ){
            return response($content = ["status" => "success", "data" => $res], $status = 201);
        } else {
            return response($content = ["status" => "failed"]);
        }
    }


    public function find($id)
    {
        $data = Product::find($id);

        if ( $data ){
            return response($content = ["status" => "success", "data" => ["attributes" => $data]], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"customer not found!"]);
        }
    }


    public function delete($id)
    {
        $data = Product::find($id);
        if ($data->delete()){
            return response($content = ["status" => "success", "messages" => "berhasil dihapus"], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"gagal dihapus!"]);
        }
    }
}
