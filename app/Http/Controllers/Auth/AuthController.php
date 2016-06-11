<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Models\User;
use App\Repository\BlocklandAuthentication;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Where to redirect guests in need of login.
     *
     * @var string
     */
    protected $loginPath = '/user/login';

    /**
     * The field to look for when logging in.
     *
     * @var string
     */
    protected $username = 'username';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), [
            'except' => 'logout'
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|max:32|unique:users',
            'email' => 'required|email|max:254|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Verify user through IP and name
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function validateAuthIP(Request $request)
    {
        $data = [];
        if ($request->has('name'))
        {
            // TOOD: Check cache in own database to avoid hammering

            $bl_id = BlocklandAuthentication::CheckAuthServer($request->input('name'));

            if ($bl_id === null)
            {
                $data['msg'] = 'Unable to authenticate';
                $data['code'] = 'NO_SERVER';
            }
            elseif ($bl_id === false)
            {
                $data['msg'] = 'Invalid';
                $data['code'] = 'INVALID';
            }
            else
            {
                $data['msg'] = 'Verified';
                $data['code'] = 'VERIFIED';
            }
        }
        else
        {
            $data['msg'] = 'Missing required "name" field';
            $data['code'] = 'MISSING_FIELD';
        }


        if ($request->ajax())
        {
            return response()->json((object)$data);
        }
        else
        {
            return response($data['msg']);
        }
    }
}
