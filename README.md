https://www.positronx.io/laravel-jwt-authentication-tutorial-user-login-signup-api/

composer require tymon/jwt-auth

config/app.php

'providers' => [
    ....
    ....
    Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
],
'aliases' => [
    ....
    'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
    'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,
    ....
],


php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

php artisan jwt:secret


Open the app/Models/User.php file and replace the following code with the existing code.

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    
    public function getJWTIdentifier() {
        return $this->getKey();
    }
   
    public function getJWTCustomClaims() {
        return [];
    }    

  
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

   
    protected $hidden = [
        'password',
        'remember_token',
    ];

   
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}



config/auth.php

<?php
return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
            'hash' => false,
        ],
    ],




php artisan make:controller AuthController


<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user =User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => hash::make($request->password),
        ]);
        return response()->json([
            'message'=> 'user registered successsfully',
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            // 'user' => auth()->user()
        ]);
    }

    public function profile()
    {
       return response()->json(auth()->user());
    }

    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    
}


routes/api.php


<?php

use App\Http\Controllers\UserController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
route::group(['middleware' => 'api'],function($routes){
    Route::post('register' ,[UserController::class ,'register']);
    Route::post('login' ,[UserController::class ,'login']);
    Route::post('profile' ,[UserController::class ,'profile']);
    Route::post('refresh' ,[UserController::class ,'refresh']);
    Route::post('logout' ,[UserController::class ,'logout']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


php artisan serve

Method	Endpoint
POST	/api/auth/register
POST	/api/auth/login
GET	/api/auth/user-profile
POST	/api/auth/refresh
POST	/api/auth/logout

