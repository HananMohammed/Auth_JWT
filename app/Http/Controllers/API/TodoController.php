<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    protected  $user ;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = $this->guard()->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $todos = $this->user->todos()->get(['title', 'body', 'completed', 'created_by']);
        return response()->json($todos->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(request $request)
    {
         $validators = Validator::make($request->all(),[
             'title' => 'required|string',
             'body' => 'required|string',
             'completed' => 'required|boolean'
         ]);
         if ($validators->fails())
         {
             return response()->json([
                 "status" =>"false",
                 "errors" => $validators->errors()
             ],400);
         }
         $todo = new Todo();
         $todo->title = $request->title;
         $todo->body = $request->body ;
         $todo->completed = $request->completed;
         if($this->user->todos()->save($todo)){
             return response()->json([
                 'status' => true,
                 'todo' => $todo
             ]);
         }else{
             return response()->json([
                 'status' => false,
                 'message' => 'Oops, The todo code not saved'
             ]);
         }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        return $todo;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        $validators = Validator::make($request->all(),[
            'title' => 'required|string',
            'body' => 'required|string',
            'completed' => 'required|boolean'
        ]);
        if ($validators->fails())
        {
            return response()->json([
                "status" =>"false",
                "errors" => $validators->errors()
            ],400);
        }

        $todo->title = $request->title;
        $todo->body = $request->body ;
        $todo->completed = $request->completed;
        if($this->user->todos()->save($todo)){
            return response()->json([
                'status' => true,
                'todo' => $todo
            ]);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Oops, The todo code not Updated'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        if($todo->delete()){
            return response()->json([
                'status' => true,
                'todo' => $todo
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Oops, The todo code not deleted'
            ]);
        }
    }

    protected function guard()
    {
        return Auth::guard();

    }
}
