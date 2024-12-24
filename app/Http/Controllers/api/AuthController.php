<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\FormSubmitted;

class AuthController extends Controller
{


    function register(Request $request)
    {
        $check = User::where('email', $request->email)->exists();
//        return $check;
        if ($check == false) {

            $user = [
                'surname' => $request->surname,
                'name' => $request->name,
//                'phone' => $request->phone,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'device_type' => $request->device_type ?? null,
                'device_token' => $request->device_token ?? null
            ];

            User::create($user);

            if (Auth::guard('web')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                $token = Auth::guard('web')->user()->createToken('auth')->plainTextToken;
                $token = explode('|', $token)[1];

                $data = [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'ttl' => 315569520,
                    'role' => 'user'
                ];

                $res = array_merge($request->user('web')->toArray(), ['token' => $data]);

                return result($res, 200, 'Успешно');

            }
        }
        else{
            return response()->json(['message' => 'Почта существует'], 400);
        }
    }


    function login(Request $request){

        if(User::query()->where('email',$request->get('email'))->exists()){
            $data = [];
            if (Auth::guard('web')->attempt(request()->only(['email','password']))) {
                $token = request()->user('web')->createToken('auth')->plainTextToken;
                $token = explode('|', $token)[1];
                $data = [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'ttl' => 315569520,
                    'role'=>'user'
                ];

                if($request->device_token and $request->device_type){
                    $user = $request->user('web');
//                return $user;
                    $user->device_type = $request->device_type;
                    $user->device_token = $request->device_token;
                    $user->save();
                }
            }



            return $data != []
                ? result(array_merge($request->user('web')->toArray(), ['token' => $data]),200,'Успешно')
                : result(null,422,"Логин или пароль неправильный");

        }else{
            return result(false,422,'Почта не найдено');
        }



    }


    function forgotPassword(Request $request){
        $email = $request->get('email');
        if($email){

            if(User::query()->where('email',$request->get('email'))->exists()){
                $cacheKey = "sms_code_{$email}";
//            $code = 1111;
                $code = rand(1111,9999);
                Cache::put($cacheKey, $code, now()->addMinutes(10));

                $details = [
                    'code' => $code
                ];
                Mail::to("$email")->send(new FormSubmitted($details));
                return result(message: 'Code успешно отправился на email');
            }else{
                return result(message: 'Такая почта не существует',status_code:404);
            }

//            $cleanedPhoneNumber = str_replace(['(', ')', '-', ' '], '', $phone);

        }
    }


    function checkCode(Request $request){
        $code = $request->input('code');
        $email = $request->email;
        $cacheKey = "sms_code_{$email}";
        $cachedCode = Cache::get($cacheKey);
        if ($cachedCode && $code == $cachedCode) {
            Cache::forget($cacheKey);
            $person =  User::where('email', $email)->first();
            return result($person, 200, 'Успешно');
        } else {
            return result(message: 'Код не верный',status_code: 422);
        }
    }

    function restorePassword(Request $request){
        $request->validate([
            'password' => 'required', 'string', 'min:5', 'confirmed',
        ]);
//return $request->email;
        if($request->email){
            User::where('email',$request->email)->update([
                'password'=>bcrypt($request->password)
            ]);
            return result(data:User::query()->where('email',$request->email)->first(),message: 'Пароль успешно восстановился');
        }else{
            return result(status_code: 422,message: 'email не правильном формате');
        }
    }


}
