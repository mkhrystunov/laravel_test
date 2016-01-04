<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['store']]);
    }

    public function store(Request $request)
    {
        $newUser = $request->all();
        $password = Hash::make($request->input('password'));

        $newUser['password'] = $password;

        return User::create($newUser);
    }

    public function update(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user || $user->id != $id) {
            return response('Unathorized', 403);
        } else {
            $user->name = $request->input('name');
            $user->save();
            return $user;
        }
    }

    public function destroy()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            User::destroy($user->id);
            return response('Success');
        } else {
            return response('Unathorized', 403);
        }
    }
}
