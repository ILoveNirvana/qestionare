<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>Questionare</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">
    </head>
    <body>
        
        <div class="row">
            <div class="col s12 m6 offset-m3">
                <div class="card blue-grey darken-1">
                <div class="card-content white-text">
                    <h1 class="card-title" id="test_name"></h1>
                    <hr>
                    <span id="question"></span>
                        <form action="#" id="form">
                        </form>
                </div>
                <div class="card-action">
                    <a href="#" id="send">Send</a>
                </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="js/materialize.min.js"></script>

        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var questionsList,
            currentQuestion = 0,
            answers = [],
            rightAnswer;
            $.get('./test/{{ $id }}', function( data ) {
                $('#test_name').text(data[0].name);
                questionsList = JSON.parse(data[0].questions).data;
                getQuestion(questionsList[0]);
            })

            function getQuestion(id) {$.get('./question/' + id, function( data ) {
                $('#question').text(data[0].question);
                var answers = JSON.parse(data[0].answers).data;
                rightAnswer = JSON.parse(data[0].right_answer).data;
                for (var i = 0; i < answers.length; i++) {
                    if (answers[i] == null) continue;
                    $('#form').append( `
                                        <p>
                                            <input type="checkbox" id="answ`+ i +`" />
                                            <label for="answ`+ i +`" id="answer-`+ i +`">`+ answers[i] +`</label>
                                        </p>
                                        ` );
                }
            })}
            function sendAnswer(rightAnswer) {
                if(currentQuestion == questionsList.length - 1) {
                    $('#form').text('');
                    $('#question').text('Test end!');
                    checkAnswers(answers);
                    console.log("end");
                    return;
                }
                var answer = [];
                for (var i = 0; i < $('#form>p').length; i++) {
                    isChecked = document.getElementById('answ' + (i)) ? document.getElementById('answ' + (i)).checked : false;
                    if(  isChecked ) {
                        answer.push(i + 1);
                    }
                }
                answers.push( answer.join() == rightAnswer.join());
                $('#form').html('');
                getQuestion(questionsList[ ++currentQuestion ]);
            }

            function checkAnswers(answers) {
                var total = answers.length;
                var rights = 0;
                answers.map( function (val) {
                    console.log(val);
                    if(val) rights++;
                });
                $('#form').text('Results: ' + rights + " of " + total);
            }
            $( "#send" ).click(function() {
                sendAnswer(rightAnswer ? rightAnswer : ['n', 'o', 'n', 'e']);
            });
        </script>
    </body>
</html>
