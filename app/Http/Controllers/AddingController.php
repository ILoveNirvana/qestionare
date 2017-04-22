<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddingController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @return Response
     */
    public function show()
    {
        return view('adding');
    }

    public function add( Request $request ) {
    	$data = $request->input();
    	$questions_ids = array();
    	for ($i=0; $i < sizeof($data['questions']); $i++) { 
    		$answers = array("data" => $data['answers'][$i]['data']);
    		$right_answers = array("data" => $data['right_answers'][$i]['data']);
    		array_push($questions_ids, DB::table('questions')->insertGetId(
    			['question' => $data['questions'][$i][0],
    			 'answers' => json_encode($answers),
    			 'right_answer' => json_encode($right_answers),
    			]
			));
    	}
    	DB::table('lists')->insert(['name' => $data['name'], 'questions' => '{"data": ['. implode(',', $questions_ids) .']}']);
    }
}