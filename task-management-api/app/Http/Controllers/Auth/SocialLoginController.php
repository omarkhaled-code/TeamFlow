<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class SocialLoginController extends Controller
{
    // Redirect to provider
    public function redirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    // Handle callback
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return "<script>
                if (window.opener) {
                    window.opener.postMessage({
                        success: false,
                        message: 'Login failed: {$e->getMessage()}'
                    }, '*');
                }
                window.close();
            </script>";
        }

        // Account linking logic
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // إذا نفس provider موجود → لا حاجة لتغيير شيء
            // إذا provider جديد → اربطه بالحساب الحالي
            if ($user->provider !== $provider) {
                $user->provider = $provider;
                $user->provider_id = $socialUser->getId();
                $user->save();
            }
        } else {
            // حساب جديد
            $user = User::create([
                'email' => $socialUser->getEmail(),
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'avatar' => $socialUser->getAvatar(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'plan_id' => 1, // خطة مجانية افتراضية  
            ]);
        }

        // إنشاء token
        $token = $user->createToken('spa-token')->plainTextToken;

        // إرسال البيانات للنافذة الأم وإغلاق popup
        return "<script>
            if (window.opener) {
                window.opener.postMessage({
                    success: true,
                    token: '{$token}',
                    user: " . json_encode($user) . "
                }, '*');
            }
            window.close();
        </script>";
    }
}
