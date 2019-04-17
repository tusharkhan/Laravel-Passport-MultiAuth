<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>


## Laravel Passport Multu auth version > 5.8.*

To make multiAuth support in larave we need some command.

We have to make same authentication system like laravel provide us.<br> 
I'm using laravel 5.8 for that and also <a href="https://laravel.com/docs/5.8/passport">passport</a> 


<h2>Installation</h2>

First we have to make multiple authentication support. For that run this command <br>

<code>composer require hesto/multi-auth</code>
<code>composer require hesto/multi-auth</code>

Make Admin Authentication <br>
<code>php artisan multi-auth:install admin -f --views</code> <br>
this will make migration, Admin Model Class, Login, Registration Controller for you<br>
   
   
##Passport Install
<code>composer require laravel/passport</code>

After installing [passport](https://laravel.com/docs/5.8/passport) run migrate. <br> 

<code>php artisan migrate</code> <br>
then run this command <br>
<code>php artisan passport:install</code> <br>

 After that change guard in config/auth.php file
 
  ```php
  'guards' => [
         'admin' => [               //for admin 
             'driver' => 'passport', //passport driver
             'provider' => 'admins', //provider for admin
         ],
 
         'web' => [
             'driver' => 'session',
             'provider' => 'users',
         ],
 
         'api' => [
             'driver' => 'passport',
             'provider' => 'users',
             'hash' => false,
         ],
     ],
     
     
     'providers' => [
             'admins' => [
                 'driver' => 'eloquent',
                 'model' => App\Admin::class,  //Admin model
             ],
     
             'users' => [
                 'driver' => 'eloquent',
                 'model' => App\User::class,
             ]
         ],
  ```    

After running this command, **_Laravel\Passport\HasApiTokens_** add the  trait to your **_App\User_** and **_App\User_** model. This trait will provide a few helper methods to your model which allow you to inspect the authenticated user's token and scopes
<br>


<br>


```php
namespace App;
use App\Notifications\AdminResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
class Admin extends Authenticatable
{
    use Notifiable, HasApiTokens;
 ....

 ``` 
 
 After that make a controller. Name it as you, for this example i'm going to call id `PasspoerController`. <br>
 
 <h2>PassportController</h2>
 

 ```php
 namespace App\Http\Controllers;
 
 use App\Admin; 
 use Illuminate\Http\Request;
 
 class PassportController extends Controller
 {
     /**
      * Handles Registration Request
      *
      * @param Request $request
      * @return \Illuminate\Http\JsonResponse
      */
     public function register(Request $request)
     {
         $this->validate($request, [
             'name' => 'required|min:3',
             'email' => 'required|email|unique:users',
             'password' => 'required|min:6',
         ]);
  
         $user = Admin::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => bcrypt($request->password)
         ]);
  
         $token = $user->createToken('TutsForWeb')->accessToken;
  
         return response()->json(['token' => $token], 200);
     }
  
     /**
      * Handles Login Request
      *
      * @param Request $request
      * @return \Illuminate\Http\JsonResponse
      */
     public function login(Request $request)
     {
         $credentials = [
             'email' => $request->email,
             'password' => $request->password
         ];
  
         if (auth()->attempt($credentials)) {
             $token = auth()->user()->createToken('TutsForWeb')->accessToken;
             return response()->json(['token' => $token], 200);
         } else {
             return response()->json(['error' => 'UnAuthorised'], 401);
         }
     }
  
     /**
      * Returns Authenticated User Details
      *
      * @return \Illuminate\Http\JsonResponse
      */
     public function details()
     {
         return response()->json(['user' => auth("admin")->user()], 200);
     }
 }

 ```
 
 Fro default user you can do the same. <br>
 
 
 <h2>Route</h2>
 
 For routing go to `routes/api.php` and 
 
 ```php
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


Route::post('user/login', 'UserController@login');
Route::post('user/register', 'UserController@register');

Route::middleware('auth:api')->group(function () {
    Route::get('user/user', 'UserController@details');
});

Route::post('login', 'PassportController@login');
Route::post('register', 'PassportController@register');
 
Route::middleware('auth:admin')->group(function () {
    Route::get('user', 'PassportController@details');
});
``` 
 
 
 After that go to `app/Providers/AuthServiceProvider.php` class an make changes like bellow. <br>
 
 ```php
namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->registerPolicies();

        $this->registerPolicies();
 
        Passport::routes();
    }
}

```
 
 
 Just like that you can make multiple authentication. <br>
