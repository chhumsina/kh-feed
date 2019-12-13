<?php

namespace App\Http\Controllers\api\Auth;

use App\Models\User;
use App\Models\UserSocial;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Two\InvalidStateException;
use Laravolt\Avatar\Avatar;
use Tymon\JWTAuth\JWTAuth;

class SocialLoginController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
        $this->middleware(['social', 'web']);
    }

    public function redirect($service)
    {
        return Socialite::driver($service)->redirect();
    }

    public function callback($service)
    {
        try {
            $serviceUser = Socialite::driver($service)->stateless()->user();
        } catch (\Exception $e) {
            return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?error=Unable to login using ' . $service . '. Please try again' . '&origin=login');
        }

        if ((env('RETRIEVE_UNVERIFIED_SOCIAL_EMAIL') == 0) && ($service != 'google')) {
            $email = $serviceUser->getId() . '@' . $service . '.local';
        } else {
            $email = $serviceUser->getEmail();
        }

        $user = $this->getExistingUser($serviceUser, $email, $service);
        $newUser = false;
        if (!$user) {

            try {

                $avatar = new Avatar();
                $colorRandom = rand(1,15);
                $backgroundRandom = Config::get('laravolt.avatar.backgrounds');
                $avatar->create($serviceUser->getName())->setBackground($backgroundRandom[$colorRandom])->save(public_path('/avatar/'.$serviceUser->getId().'.png'), 100);
                $newUser = true;
                $user = User::create([
                    'name' => $serviceUser->getName(),
                    'email' => $email,
                    'password' => '',
                    'avatar'=>$serviceUser->getId().'.png',
                    'user_type'=>'user',
                    'level'=>1,
                    'status'=>'active',
                    'phone'=>'',
                    'pay_name_1'=>'',
                    'pay_number_1'=>'',
                    'pay_name_2'=>'',
                    'pay_number_2'=>'',
                    'pay_name_3'=>'',
                    'pay_number_3'=>'',
                    'bio'=>''
                ]);

            } catch (\Exception $e) {
                return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?error=Unable to login using ' . $service . '. Please try again' . '&origin=login');
            }

        }

        if ($this->needsToCreateSocial($user, $service)) {
            UserSocial::create([
                'user_id' => $user->id,
                'social_id' => $serviceUser->getId(),
                'service' => $service
            ]);
        }

        return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?token=' . $this->auth->fromUser($user) . '&origin=' . ($newUser ? 'register' : 'login'));
    }

    public function needsToCreateSocial(User $user, $service)
    {
        return !$user->hasSocialLinked($service);
    }

    public function getExistingUser($serviceUser, $email, $service)
    {
        if ((env('RETRIEVE_UNVERIFIED_SOCIAL_EMAIL') == 0) && ($service != 'google')) {
            $userSocial = UserSocial::where('social_id', $serviceUser->getId())->first();
            return $userSocial ? $userSocial->user : null;
        }
        return User::where('email', $email)->where('status','!=','inactive')->whereHas('social', function($q) use ($serviceUser, $service) {
            $q->where('social_id', $serviceUser->getId())->where('service', $service);
        })->first();
    }
}
