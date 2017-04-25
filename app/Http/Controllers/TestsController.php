<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestsController extends Controller
{
    /**
     * Show list of tests.
     *
     * @return Response
     */
    public function getAll()
    {
        $data = DB::table('lists')->get();
        $result = array();

        for ($i = 0; $i < sizeof($data); $i++) { 
            array_push($result, [ "id" => $data[$i]->id, "name" => $data[$i]->name ]);
        }

        return $result;

    }

    /**
     * Show specifyed test.
     *
     * @return Response
     */
    public function get($id)
    {
        $test = DB::table('lists')->where('id', $id)->get();
        $questions = json_decode($test[0]->questions);

        for ($i=0; $i < sizeof($questions->data); $i++) { 
            $questions->data[$i] = [
                "id" => DB::table('questions')->where('id', $questions->data[$i])->value('id'),
                "question" => DB::table('questions')->where('id', $questions->data[$i])->value('question'),
                "answers" => json_decode(DB::table('questions')->where('id', $questions->data[$i])->value('answers'))->data
            ];
        }
        $test[0]->questions = $questions->data;
        return response()->json(json_encode($test[0]));
    }

    /**
     * Uploading new test to DB.
     *
     * @return Response
     */
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

    /**
     * Remove test from DB.
     *
     * @return Response
     */
    public function remove( $id ) {
        $test = DB::table('lists')->where('id', $id)->get();
        $questions = json_decode($test[0]->questions);

        for ($i=0; $i < sizeof($questions->data); $i++) { 
            DB::table('questions')->where('id', $questions->data[$i])->delete();
        }

        DB::table('lists')->where('id', $id)->delete();
    }

    /**
     * Check answers of test.
     *
     * @return Response
     */
    public function check( $id, Request $request ) {
        $data = $request->input();
        $test = DB::table('lists')->where('id', $id)->get();
        $questions = json_decode($test[0]->questions);
        for ($i=0; $i < sizeof($questions->data); $i++) { 
            $questions->data[$i] = [
                "id" => DB::table('questions')->where('id', $questions->data[$i])->value('id'),
                "question" => DB::table('questions')->where('id', $questions->data[$i])->value('question'),
                "answers" => json_decode(DB::table('questions')->where('id', $questions->data[$i])->value('answers'))->data,
                "right_answer" => json_decode(DB::table('questions')->where('id', $questions->data[$i])->value('right_answer'))->data
            ];
        }


        $response = array('name' => $test[0]->name, 'results' => []);
        for ($i=0; $i < sizeof($data); $i++) { 
            $correct = $data[$i] == $questions->data[$i]['right_answer'];
            $result = [
                'question' =>  $questions->data[$i]['question'],
                'correct' => $correct
            ];
            array_push($response["results"], $result);
        }
        return $response;
    }
}