<?php
namespace App\Livewire\Admin;


use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;




class Login extends Component
{
    #[Validate('required|string|email|max:255')]
    public string $email = ''; //ايميل موديل
    
    /*
    |--------------------------------------------------------------------------
    | Regex Means
    |--------------------------------------------------------------------------
    |
    |   1- /^.* means: any character
    |   2- (?=.{8,}) means: at least 8 characters
    |   3- (?=.*[a-zA-Z]) means: at least one letter
    |   4- (?=.*[0-9]) means: at least one number
    |   5- (?=.*[!$#%]) means: at least one special character
    |   6- ^.*$ means: any character
    */
    #[Validate('required|string|max:255|min:8|regex:/^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!$#%]).*$/')]
    public string $password = '';// باسوورد موديل

    public $messages = [
        'email.required' => 'البريد الالكتروني مطلوب',
        'email.email' => 'البريد الالكتروني غير صحيح',
        'email.max' => 'البريد الالكتروني يجب ان يحتوي على 255 حرف',
        'password.required' => 'كلمة المرور مطلوبة',
        'password.min' => 'كلمة المرور يجب ان تكون 8 احرف',
        'password.regex' => 'كلمة المرور يجب ان تحتوي على حرف و رقم و حرف كبير و رمز',
    ];

    public bool $remember = false;  // تذكر تسجيل الدخول

    public function render()
    {
        return view('livewire.admin.login');
    }


    /**
     * Handle an incoming authentication request.
     */

     public function login(): void
     {
         $this->validate();
 
         $this->ensureIsNotRateLimited(); //ensure Is Not Rate Limited = تأكد من الحد من المحاولات الكثيرة
 


         if (! Auth::guard("admin")->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
             RateLimiter::hit($this->throttleKey()); //اذا فشلت بيانات التسجيل سجل محتاولة خاطئة
 
             throw ValidationException::withMessages([
                 'email' => __('auth.failed'),
             ]);
         }
 
         RateLimiter::clear($this->throttleKey()); // ازل كل المحاولات الخاطئة اذا وجدت
         Session::regenerate();
 
         $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
     }
 
     /**
      * Ensure the authentication request is not rate limited.
      */
     protected function ensureIsNotRateLimited(): void
     {
         if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
             return;
         }
 
         event(new Lockout(request()));
 
         $seconds = RateLimiter::availableIn($this->throttleKey());
 
         throw ValidationException::withMessages([
             'email' => __('auth.throttle', [
                 'seconds' => $seconds,
                 'minutes' => ceil($seconds / 60),
             ]),
         ]);
     }
 
     /**
      * Get the authentication rate limiting throttle key.
      */
     protected function throttleKey(): string
     {
         return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
     }
}
