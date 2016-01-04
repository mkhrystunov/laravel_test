<?php

namespace App\Http\Controllers;

use App\Todo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['index']]);
    }

    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $todos = Todo::where('owner_id', $user->id)->get();

        return $todos;
    }

    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $newTodo = $request->all();
        $newTodo['owner_id'] = $user->id;
        return Todo::create($newTodo);
    }

    public function update(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $todo = Todo::where('owner_id', $user->id)->where('id', $id)->first();

        if ($todo) {
            $todo->is_done = $request->input('is_done');
            $todo->save();
            return $todo;
        } else {
            return response('Unathorized', 403);
        }
    }

    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $todo = Todo::where('owner_id', $user->id)->where('id', $id)->first();

        if ($todo) {
            Todo::destroy($id);
            return response('Success');
        } else {
            return response('Unathorized', 403);
        }
    }
}
