<?php

namespace App\Http\Controllers;
use App\Product;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Utilities\ApiCall;

class ProductsController extends Controller

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

  public function index()
  {

    try {
        $this->apiCall->setStatusCode(200);

            $products = Product::all();

        $this->apiCall->addData(['products' => $products]);
    }
    catch (Exception $e) {
        Log::error($e);
        $this->apiCall->setStatusCode(500);
    }
    finally {
        return response()->json($products);;
    }
  }

  /**
   * Create
   * $request 
   */
  public function create (Request $request)
  {

      try {
          $validator = Validator::make($request->all(), [
              'name' => 'required|string',
              'description' => 'required|string',
              'price' => 'required|integer',
              //'availability' => 'sometimes|string',
              'imagePath' => 'required|string',
              'imageThumbnail' => 'required|string'
          ]);

          if ($validator->fails()) {
              $this->apiCall->setStatusCode(422);
              $this->apiCall->setResponseData(['requestValidationFailed' => $validator->errors()->all(),
                  'name' => $request->input('name'),
                  'description' => $request->input('description'),
                  'price' => $request->input('price'),
                  //'availability' => $request->input('availability'),
                  'imagePath' => $request->input('imagePath'),
                  'imageThumbnail' => $request->input('imageThumbnail'),
              ]);
          } else {

              $this->apiCall->setStatusCode(200);
              $newProduct = Product::create([
                  'name' => $request->input('name'),
                  'description' => $request->input('description'),
                  'price' => $request->input('price'),
                  'availability' => $request->input('availability') ? $request->input('availability') : 'available' ,
                  'image_path' => $request->input('imagePath'),
                  'image_thumbnail' => $request->input('imageThumbnail'),
                  'total_price' => '0',
              ]);
          }

      } catch (Exception $e) {
          Log::error($e);
          $this->apiCall->setStatusCode(500);
      } finally {  
          return response()->json($newProduct);
      }
  }

   /**
     * @param $id
     *
     * @return mixed
     */
    public function removeProduct(Request $request)
    {
        try {
            $product = Product::where('id', '=', $request->input('id'))->first();

            if (is_null($product)) {
                $this->apiCall->setStatusCode(401);
                $this->apiCall->setResponseData(['productNotFoundWithId' => $id]);
            } else {
                $this->apiCall->setStatusCode(200);

                //DB::table('products')->delete($product);
                $product->delete();
            }

        }
        catch (Exception $e) {
            Log::error($e);
            $this->apiCall->setStatusCode(500);
        }
        finally {
            $data = 'product deleted';
            return response()->json($data);
        }
    }

    /**
     * 
     */
    public function updateProduct(Request $request)
    {
        try {
            $product = Product::where('id', '=', $request->input('productId'))->first();

            if (is_null($product)) {
                $this->apiCall->setStatusCode(401);
                $this->apiCall->setResponseData(['productNotFoundWithId' => $id]);
            } else {
                    $this->apiCall->setStatusCode(200);

                    if ($request->exists('name'))
                    $product->name = $request->input('name');

                    if ($request->exists('description'))
                    $product->description = $request->input('description');

                    if ($request->exists('price'))
                    $product->price = $request->input('price');

                    if ($request->exists('availability'))
                    $product->availability = $request->input('availability');

                    if ($request->exists('imagePath'))
                    $product->image_path = $request->input('imagePath');
                    
                    if ($request->exists('imageThumbnail'))
                    $product->image_thumbnail = $request->input('imageThumbnail');
                   
                    $product->save();
            }

        }
        catch (Exception $e) {
            Log::error($e);
            $this->apiCall->setStatusCode(500);
        }
        finally {
            return response()->json('update sucsses');
        }
    }
}