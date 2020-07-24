<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Customer;



class CustomerController extends Controller
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
        $datas = Customer::all();
        if ( $datas ){
            return response($content = ["message" => "success retrive data", "status" => true,"data" => $datas], $status = 201);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Customer::find($id);

        $this->validate($request, [
            'data.attributes.full_name' => 'required',
            'data.attributes.username' => 'required',
            'data.attributes.email' => 'required|email',
            'data.attributes.phone_number' => 'required'
        ]);

        $data->full_name = $request->input('data.attributes.full_name');
        $data->username = $request->input('data.attributes.username');
        $data->email = $request->input('data.attributes.email');
        $data->phone_number = $request->input('data.attributes.phone_number');

            
        if ( $data->save() ){
            return response($content = ["status" => "success", "data" => $data], $status = 201);
        } else {
            return response($content = ["status" => "failed"]);
        }
    }


    public function create(Request $request)
    {
        $this->validate($request, [
            'data.attributes.full_name' => 'required',
            'data.attributes.username' => 'required',
            'data.attributes.email' => 'required|email',
            'data.attributes.phone_number' => 'required'
        ]);

        $response = [
            "full_name" => $request->input('data.attributes.full_name'),
            "username" => $request->input('data.attributes.username'),
            "email" => $request->input('data.attributes.email'),
            "phone_number" => $request->input('data.attributes.phone_number')
        ];
        $res = ["attributes" => $response];

            
        if ( Customer::create($response) ){
            return response($content = ["status" => "success", "data" => $res], $status = 201);
        } else {
            return response($content = ["status" => "failed"]);
        }
    }


    public function find($id)
    {
        $data = Customer::find($id);

        if ( $data ){
            return response($content = ["status" => "success", "data" => ["attributes" => $data]], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"customer not found!"]);
        }
    }

    public function delete($id)
    {
        $data = Customer::find($id);
        if ($data->delete()){
            return response($content = ["status" => "success", "messages" => "berhasil dihapus"], $status = 201);
        } else {
            return response($content = ["status" => "failed", "messages"=>"gagal dihapus!"]);
        }
    }

}
