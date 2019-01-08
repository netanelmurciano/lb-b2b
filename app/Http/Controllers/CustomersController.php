<?php

namespace App\Http\Controllers;
use App\User;
use App\Repositories\UsersRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Utilities\ApiCall;

class CustomersController extends Controller
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

    public function login(Request $request)
    {
       $credentials = 
       [
          'email' => $request->input('email'),
          'password' => $request->input('password'),
       ];
       
       if(Auth:: attempt($credentials)) {
        $user = User::where('email', '=', $request->input('email'))->first();   
        $data = [
            'status' => 'login',
            'data' => $user
        ];   
        return response()->json($data);
       } else {
        $data = [
            'status' => 'notLogin',
            'data' => ''
        ]; 
       return response()->json($data);
      }
    }

    public function register (Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:6',
                'firstName' => 'required|string',
                'lastName' => 'required|string',
            ]);

            if ($validator->fails()) {
                $this->apiCall->setStatusCode(422);
                $this->apiCall->setResponseData(['requestValidationFailed' => $validator->errors()->all(),
                    'email' => $request->input('email'),
                    'password' => $request->input('password'),
                    'firstName' => $request->input('firstName'),
                    'lastName' => $request->input('lastName'),
                ]);
            } else {

                $this->apiCall->setStatusCode(200);
                $newCustomer = User::create([
                    'email' => $request->input('email'),
                    'password' => bcrypt($request->input('password')),
                    'name' => $request->input('firstName'),
                    'last_name' => $request->input('lastName'),
                    'type' => 'customer'
                ]);

                $credentials = $request->only('email', 'password');

                try {
                    // verify the credentials and create a token for the user
                    if (! $token = JWTAuth::attempt($credentials)) {
                        $this->apiCall->setStatusCode(401);
                        $this->apiCall->addData(['error' => 'invalidCredentials', 'result' => $token]);
                        return $this->apiCall->makeResponse();
                    }
                } catch (JWTException $e) {
                    // something went wrong
                    $this->apiCall->setStatusCode(500);
                    $this->apiCall->addData(['error' => 'couldNotCreateToken']);
                    return $this->apiCall->makeResponse();
                }

                $this->apiCall->setResponseData(compact('token'));
            }

        } catch (Exception $e) {
            Log::error($e);
            $this->apiCall->setStatusCode(500);
        } finally {
            $data = [
                'status' => 'registered',
                'data' => $newCustomer
            ];  
            return response()->json($data);
        }
    }

    /**
     * @param $email
     * @return mixed
     */
    public function customerByEmail($email)
    {
        $data = '';
        try {
            $this->apiCall->setStatusCode(200);

            $customers = [];

            if (filter_var($email, FILTER_VALIDATE_EMAIL))
                //$customers = $this->customersRepo->findBy('email', $email);
                $customers = User::where('email', '=', '$email')->firstOrFail();

            $emailExists = (count($customers) > 0);

//            $this->websiteCall->setResponseData(compact('emailExists', 'customers'));

            $this->apiCall->addData(compact('emailExists'));

        } catch (Exception $e) {
            Log::error($e);
            $this->apiCall->setStatusCode(500);
        } finally {
            return $this->apiCall->makeResponse();
        }
    }

}
