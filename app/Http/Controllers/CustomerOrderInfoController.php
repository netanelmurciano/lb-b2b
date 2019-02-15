<?php

namespace App\Http\Controllers;
use App\CustomerOrderInfo;
use App\Models\ProductOrder;
use App\Http\Utilities\ApiCall;
use App\Exceptions\ValidationException;
use Exception;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class CustomerOrderInfoController extends Controller
{
    /**
     * @var ApiCall
     */
    protected $apiCall;
     /**
     * CustomersController constructor.
     */
    function __construct()
    {
        //parent::__construct();

        $this->apiCall = new ApiCall();
    }
      public function create(Request $request) 
    {
        try {
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string',
                'lastName' => 'required|string',
                'address' => 'required|string',
                'deliveryTime' => 'required|string',
            ]);
  
            if ($validator->fails()) {
                $this->apiCall->setStatusCode(422);
                $this->apiCall->setResponseData(['requestValidationFailed' => $validator->errors()->all(),
                    'firstName' => $request->input('firstName'),
                    'lastName' => $request->input('lastName'),
                    'address' => $request->input('address'),
                    'deliveryTime' => $request->input('deliveryTime'),
                ]);
            } else {
                $this->apiCall->setStatusCode(200);
                $newCustomerOrderInfo = CustomerOrderInfo::create([
                    'firstName' => $request->input('firstName'),
                    'lastName' => $request->input('lastName'),
                    'address' => $request->input('address'),
                    'deliveryTime' => $request->input('deliveryTime') ? $request->input('deliveryTime') : 'morning' ,
                    'isIatHome' => $request->input('isIatHome') ? $request->input('isIatHome') : '1',
                    'user_id' => $request->input('userId'),
                ]);

                // get the newCustomerOrderInfo id (the last one we insert)
                $customerOrderInfoId = $newCustomerOrderInfo->id;
                
                $productsOrders = $request->input('productsOrders');

                foreach($productsOrders as $productOrder) {
                    if(!$productOrder || !$productOrder['itemsCount']) {
                        continue;
                    } else {
                        $newProductsOrders = ProductOrder::create([
                        'produtName' => $productOrder['productName'],
                        'numOfItems' => $productOrder['itemsCount'],
                        'totalPrice' => $productOrder['productPrice'],
                        'produtId' => $productOrder['product_id'],
                        'customerUserId' => $request->input('userId'),
                        'customerOrderInfoId' => $customerOrderInfoId,  
                        ]);
                    }
                }
            }
  
        } catch (Exception $e) {
            Log::error($e);
            $this->apiCall->setStatusCode(500);
        } finally { 
            $data = [
                'customerOrderInfo' => 'newCustomerOrderInfo',
                'productOrder' => $newProductsOrders,
                'status' => 'orderSuccess'
            ]; 
            return response()->json($data);
        }  
    } 
}
