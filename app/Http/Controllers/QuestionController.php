<?php

namespace Questionare\Http\Controllers;

use Questionare\User;
use Questionare\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @return Response
     */
    public function get($id)
    {
        return DB::table('questions')->where('id', $id)->get();

    }    
    public function check($id, Request $request)
    {
        if( json_decode(DB::table('questions')->where('id', $id)->value('right_answer'))->data == $request->input()["data"] ) return 'TRUE';
        else return 'FALSE';
    }

}