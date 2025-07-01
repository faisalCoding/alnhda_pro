<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Validate;

class Register extends Component
{
    public string $name = '';

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
    public string $password_confirmation = '';

    public $messages = [
        'email.required' => 'البريد الالكتروني مطلوب',
        'email.email' => 'البريد الالكتروني غير صحيح',
        'email.max' => 'البريد الالكتروني يجب ان يحتوي على 255 حرف',
        'password.required' => 'كلمة المرور مطلوبة',
        'password.min' => 'كلمة المرور يجب ان تكون 8 احرف',
        'password.regex' => 'كلمة المرور يجب ان تحتوي على حرف و رقم و حرف كبير و رمز',
    ];
    
    public function render()
    {
        return view('livewire.admin.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . Admin::class],
            'password' => [
                'required',
                'string',
                'confirmed',
                Rules\Password::defaults(),
                'regex:/^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!$#%]).*$/',
            ],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($Admin = Admin::create($validated))));

        Auth::guard("admin")->login($Admin);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}

