<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidationException;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Order;

class OrderController extends Controller
{
   public function store(Request $request) 
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'address' => 'required|string',
                'deliveryTime' => 'required|string',
                //'isIatHome' => 'required|boolean'
            ]);
            if ($validator->fails()) {
                [
                'requestValidationFailed'   => $validator->errors()->all(),
                'firstName'                 => $request->input('firstName'),
                'lastName'                  => $request->input('lastName'),
                'address'                   => $request->input('address'),
                'deliveryTime'              => $request->input('homedeliveryTimePhone')
                ];
            } else {
                //$order  = new Order();
                $newOrder = ([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'address' => $request->address,
                'deliveryTime' => $request->deliveryTime,
                'isIatHome' => $request->isIatHome
                ]);

                $data = $newOrder;
                
                //DB::table('orders')->insert($data);
                Order::create($data);
                
                
            } 

        }
        catch (Exception $e) {
            Log::error($e);
        }   
    }
}
