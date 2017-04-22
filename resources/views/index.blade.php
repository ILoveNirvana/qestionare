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
        
        <div id="root" class="container">

        </div>
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="js/materialize.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.2.0/papaparse.min.js"></script>

        <script>
            // Pre-Config
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Pre-Config \\
            // Functions
            
            function clearRoot() { $('#root').html('') } 
            
            function createTableOfTests( tests ) {

                clearRoot();

                const table = `
                                <div class="row">
                                    <table class="bordered centered col s6 offset-s3">
                                        <caption>List of Tests</caption>
                                        <thead>
                                            <tr>
                                                <th>Test</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="list-of-tests">
                                        </tbody>
                                    </table>
                                    <a class="waves-effect waves-light btn cyan accent-3" id="add-new">Add new test</a>
                                </div>

                `;

                $('#root').html(table)
                for (var i = 0; i < tests.length; i++) {
                    $('#list-of-tests').append(`

                                                <tr data-id="` + tests[i].id + `">
                                                    <td>` + tests[i].name + `</td>
                                                    <td>                                
                                                        <a class="waves-effect waves-light btn light-green" data-action="test">Go test!</a>
                                                        <a class="waves-effect waves-light btn red darken-2" data-action="delete">Delete</a>
                                                    </td>
                                                </tr>

                        `);
                }

                goToTestTrigger();
                deleteTestTrigger();
                initializeAdding();
            }

            function createTestField() {
                clearRoot();

                $('#root').html(`
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
                `);
            }

            function testing( data ) {
                createTestField();
                $('#test_name').text(data.name);

                var currentQuestion  = 0,
                answers = [];

                printQuestion(data.questions[currentQuestion]);

                $("#send").click(function(){
                    if(currentQuestion == data.questions.length ) { checkResultsOfTest( data.id, answers, resultTable );
                    console.log(answers);}
                    var answer = [];
                    for (var i = 0; i < $('#form>p').length; i++) {
                        isChecked = document.getElementById('answ' + (i)) ? document.getElementById('answ' + (i)).checked : false;
                        if( isChecked ) {
                            answer.push(i + 1);
                        }
                    }
                    answers.push(answer.length != 0 ? answer : [0]);
                    currentQuestion++;
                    printQuestion(data.questions[currentQuestion]);
                })
            }

            function clearQuestion() {
                $('#question, #form').html('');
            }

            function printQuestion(question) {
                clearQuestion();
                $('#question').text(question.question);

                for(var i = 0; i < question.answers.length; i++) {
                    if (question.answers[i] == null) continue;
                    $('#form').append( `
                                        <p>
                                            <input type="checkbox" id="answ`+ i +`" />
                                            <label for="answ`+ i +`" id="answer-`+ i +`">`+ question.answers[i] +`</label>
                                        </p>
                                        ` );
                }
            }

            function resultTable(data) {
                clearRoot();
                console.log(data);
                const table = `
                                <div class="row">
                                    <table class="bordered centered col s6 offset-s3">
                                        <caption>Results of Test: `+ data.name +`</caption>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Question</th>
                                            </tr>
                                        </thead>
                                        <tbody id="results">
                                        </tbody>
                                    </table>
                                </div>

                `;

                $('#root').html(table);

                for(var i = 0; i < data.results.length; i++) {
                    var color = data.results[i].correct ? 'green' : 'red';
                    $('#results').append(`
                        <tr class="` + color + `">
                            <td>` + (i + 1) + `</td>
                            <td>` + data.results[i].question + `</td>
                        </tr>
                        `);
                }

            }

            function addNewTest() {
                clearRoot();
                $('#root').html(`
        <div class="row">
            <div class="col s12 m6 offset-m3">
                <div class="card blue-grey darken-1">
                <div class="card-content white-text">
                    <h1 class="card-title">Add List of Questions</h1>
                    <hr>
                    <span>Load please CSV file</span>
                        <form action="#" id="form">
                            <div class="file-field input-field">
                                <div class="btn">
                                    <span>File</span>
                                    <input type="file" id="file">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" id="file_name">
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-field col s12">
                                    <input placeholder="Name of List" id="name_of_list" type="text" class="validate">
                                </div>
                            </div>
                        </form>
                </div>
                <div class="card-action">
                    <a href="#" id="submit">Upload</a>
                </div>
                </div>
            </div>
        </div>
                    `);
                $('#submit').click( function (event) {
                event.preventDefault();
                var file = document.getElementById('file').files[0];
                var questions;
                Papa.parse(file, {
                    complete: function(results, file) {
                        var data =  { name: $('#name_of_list').val(),
                                     questions: [],
                                     answers: [],
                                     right_answers: []
                                    };
                        for (var i =  1; i < results.data.length; i++) {
                            data.questions.push(results.data[i].slice(1,2));
                            data.answers.push({data: results.data[i].slice(2, results.data[i].length - 1)})
                            var rightAnswer = results.data[i].slice(results.data[i].length - 1, results.data[i].length);
                            rightAnswer = String.toUpperCase(rightAnswer).replace(/[^A-Z]/g, '').split('');
                            for (var j = 0; j < rightAnswer.length; j++) {
                                rightAnswer[j] = rightAnswer[j].charCodeAt(0) - 64;
                            }
                            data.right_answers.push({data: rightAnswer.length != 0 ? rightAnswer : null});
                        }
                        $.ajax({
                          type: "PUT",
                          url: './test',
                          data: data,
                          dataType: 'json'
                        });
                    }
                });
                setTimeout( () => getAllTests( createTableOfTests ), 3000);
            })
            $('#file').change(function() {
                $('#file_name').val($('#file').val());
            })
            }
            // Functions \\

            //Ajax Requests
            function getAllTests( handler ) {
                $.get( "./tests", (data) => handler(data) );
            }

            function getTest( id, handler ) {
                $.get("./test/" + id, (data) => handler( JSON.parse(data) ));
            }

            function deleteTest( id ) {
                $.ajax({
                          type: "DELETE",
                          url: './test/' + id,
                          success: () => $('tr[data-id="' + id + '"]').remove()
                        });
            }
            //Ajax Requests \\
            //Event-handlers
            $().ready(() => getAllTests( createTableOfTests ));

            function goToTestTrigger() {

                $('a[data-action="test"]').click(function( e ) {
                    id = $(e.target).parent().parent().attr('data-id');
                    getTest(id, testing)
                });
            }

            function deleteTestTrigger() {

                $('a[data-action="delete"]').click(function( e ) {
                    id = $(e.target).parent().parent().attr('data-id');
                    deleteTest(id)
                });
            }

            function checkResultsOfTest( id, data, handler ) {
                $.ajax({
                    method: 'PATCH',
                    url: './test/' + id + '/check',
                    data: {data: data},
                    success: (data) => handler( data )
                })
            }

            function initializeAdding() {
                $('#add-new').click( () => addNewTest());
            }
        </script>
    </body>
</html>
