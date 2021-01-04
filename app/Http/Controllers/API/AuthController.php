<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['login','register']]);
    }
    public function login(Request $request)
    {
        $validator  = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' =>'required|string|min:6'
        ]);

        if($validator->fails())
            return response()->json($validator->errors(),400);

        $token_validity = 24*60;

        $this->guard()->factory()->setTTL($token_validity);

        if (!$token = $this->guard()->attempt($validator->validated()))
            return response()->json(['error' => 'Un Authorized'], 401);

        return $this->responseWithToken($token);
    }
    public function register(request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|between:2,100',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed|min:5'
        ]);

        if($validator->fails())
            return response()->json($validator->errors() , '422');

        $user = User::create(
            array_merge($validator->validated(),
                ['password', bcrypt($request->password)])
        );

        return response()->json(['message' => 'user created successfully', 'user'=>$user]);
    }
    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Logged Out successfully']);
    }
    public function profile($token)
    {
        return response()->json($this->guard()->user());
    }
    public function refresh()
    {
        return $this->responseWithToken($this->guard()->refresh());
    }
    protected function responseWithToken($token)
    {
        return response()->json([
            "token" => $token,
            "token_type" => "bearer",
            "toke_validity" => ($this->guard()->factory()->getTTL() * 60)
        ]);
    }
    protected function guard()
    {
        return Auth::guard();
    }

}
