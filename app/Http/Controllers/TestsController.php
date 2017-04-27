<?php

namespace Questionare\Http\Controllers;

use Questionare\User;
use Questionare\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TestsController extends Controller
{
    /**
     * Show list of tests finded by query.
     *
     * @return Response
     */
    public function seacrhingTests($query)
    {
        $data = DB::table('lists')
            ->where('name', 'like', $query . '%')
            ->get();
        $result = array();

        for ($i = 0; $i < sizeof($data); $i++) { 
            array_push($result, [ "id" => $data[$i]->id, "name" => $data[$i]->name ]);
        }

        return $result;

    }    

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
        shuffle($questions);
        $questions = array_slice($questions, 0, 36);

        for ($i=0; $i < sizeof($questions); $i++) { 
            $questions[$i] = [
                "id" => DB::table('questions')->where('id', $questions[$i])->value('id'),
                "question" => DB::table('questions')->where('id', $questions[$i])->value('question'),
                "answers" => json_decode(DB::table('questions')->where('id', $questions[$i])->value('answers'))
            ];
        }
        $test[0]->questions = $questions;
        return json_encode($test[0]);
    }

    /**
     * Uploading new test to DB.
     *
     * @return Response
     */
    public function add( Request $request ) {
        if(Auth::id() != 1) return 'You don`t have permissions!';
        $data = $request->input();
        $questions = [];
        foreach ($data['questions'] as $question) {
            $id = DB::table('questions')->insertGetId([
                    'question' => $question['question'],
                    'answers' => $question['answers'],
                    'right_answer' => $question['right_answer']
                ]);
            array_push($questions, $id);
        }
        $newTestId = DB::table('lists')->insertGetId([
            'name' => $data['name'],
            'questions' => json_encode($questions)
            ]);
        return json_encode(array( 'id' => $newTestId ));
    }

    /**
     * Remove test from DB.
     *
     * @return Response
     */
    public function remove( $id ) {        
        if(Auth::id() != 1) return 'You don`t have permissions!';
        $test = DB::table('lists')->where('id', $id)->get();
        $questions = json_decode($test[0]->questions);

        for ($i=0; $i < sizeof($questions); $i++) { 
            DB::table('questions')->where('id', $questions[$i])->delete();
        }

        DB::table('lists')->where('id', $id)->delete();
    }

    /**
     * Check answers of test.
     *
     * @return Response
     */
    public function check( Request $request ) {       
        $data = json_decode($request->input()['answers']);
        $id = $request->input()['id'];
        $test = DB::table('lists')->where('id', $id)->get();
        $questions = array();
        for ($i=0; $i < sizeof($data); $i++) { 
            array_push($questions, $data[$i]->id);
        }

        for ($i=0; $i < sizeof($questions); $i++) { 
            $questions[$i] = [
                "id" => DB::table('questions')->where('id', $questions[$i])->value('id'),
                "question" => DB::table('questions')->where('id', $questions[$i])->value('question'),
                "answers" => json_decode(DB::table('questions')->where('id', $questions[$i])->value('answers')),
                "right_answer" => json_decode(DB::table('questions')->where('id', $questions[$i])->value('right_answer'))
            ];
        }


        $response = array('id' => $test[0]->id, 'name' => $test[0]->name, 'results' => []);
        $corrects = 0;
        for ($i=0; $i < sizeof($data); $i++) { 
            $correct = $data[$i]->data == $questions[$i]['right_answer'];
            if($correct) $corrects++;
            $result = [
                'id' => $questions[$i]['id'],
                'question' =>  $questions[$i]['question'],
                'correct' => $correct
            ];
            array_push($response["results"], $result);
        }

        Mail::raw( Auth::user()->name . ' passed test "' . $test[0]->name . '" with result: ' . $corrects . ' of 36', function($message) {
            $message->from('us@example.com', 'Questionare');

            $message->to('sardor.umrdinov@gmail.com');
        });

        return $response;
    }
}