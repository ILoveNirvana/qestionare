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

        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="js/materialize.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.2.0/papaparse.min.js"></script>

        <script>

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#submit').click( function (event) {
                event.preventDefault();
                console.log('Success!');
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
                          type: "POST",
                          url: '/public/add',
                          data: data,
                          dataType: 'json'
                        });
                    }
                });
            })
            $('#file').change(function() {
                $('#file_name').val($('#file').val());
            })
        </script>

    </body>
</html>
