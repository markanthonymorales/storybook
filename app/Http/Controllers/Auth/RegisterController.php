<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\UserRegisteredSuccessfully;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use App\User;
use App\Repository\UserRepository;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/mystorybook';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    // public function showRegistrationForm()
    // {
    //     return redirect('/');
    // }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'lastname' => ['required', 'string', 'max:255', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms_policies' => ['required'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'role_id' => config('auth.default.role'),
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'activation_code' => $data['activation_code'],
        ]);

        // check user if user email is added to a story as contributor
        UserRepository::checkSharedStory($user);

        return $user;
    }

    public function register(Request $request)
    {
        $validation = $this->validator($request->all());
        if ($validation->fails())  {

            if ($request->expectsJson())
                return response()->json(['errors' => $validation->errors()], 422);

            return redirect()->back()->with(['errors' => $validation->errors()])->withInput();
        }

        try {

            $user = $this->create(array_merge($request->all(), ['activation_code' => str_random(30).time()]));
        
        } catch (\Exception $exception) {
            
            logger()->error($exception);
            return redirect()->back()->with('message', 'Unable to create new user.');
        }
        
        $user->notify(new UserRegisteredSuccessfully($user));
        
        return redirect()->back()->with('message', 'Successfully created a new account. Please check your email and activate your account.');

    }

    /**
     * Activate the user with given activation code.
     * @param string $activationCode
     * @return string
     */
    public function activateUser(string $activationCode)
    {
        try {
            $user = app(User::class)->where('activation_code', $activationCode)->first();
            if (!$user) 
                return "The code does not exist for any user in our system.";

            $user->status = 1;
            $user->activation_code = null;
            $user->save();

            auth()->login($user);

        } catch (\Exception $exception) {
            logger()->error($exception);
            return "Whoops! something went wrong.";
        }

        return redirect()->to('/mystorybook');
    }
}
