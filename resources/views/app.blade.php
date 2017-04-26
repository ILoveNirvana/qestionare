<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>Questionare</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body>
        
        <div id="app" class="container">
            <router-view></router-view>
        </div>

        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.2.0/papaparse.min.js"></script>
        <script src ="https://unpkg.com/vue"></script>
        <script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>

        <script>
            window.Laravel = {
                token: '{{ csrf_token() }}'
            }
        </script>
        @verbatim
        <script>
            const TestTable = Vue.component('tests-table', {
                data: function() {
                    return {
                        tests: null,
                        query: null
                    }
                },
                created: function() {
                    this.getAllTests()
                },
                methods: {
                    getAllTests: function() {
                        var xhr = new XMLHttpRequest()
                        var self = this
                        xhr.open('GET', './tests')
                        xhr.onload = function() {
                            self.tests = JSON.parse(xhr.responseText)
                        }
                        xhr.send()
                    },
                    searchTests: function () {
                        var xhr = new XMLHttpRequest()
                        var self = this
                        if(self.query.length > 0) {
                            xhr.open('OPTIONS', './seacrh/tests/' + self.query)
                            xhr.onload = function() {
                                self.tests = JSON.parse(xhr.responseText)
                            }
                            xhr.send()
                        }
                        else { self.getAllTests() }
                    }
                },
                template: `
                                <div class="row">
                                    <table class="bordered centered col s6 offset-s3">
                                        <caption>List of Tests</caption>
                                        
                                        <thead>
                                            <tr>
                                                <th>Test</th>
                                                <th>Actions</th>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><router-link class="btn-floating btn-large waves-effect waves-light green" to="/add-new"><i class="material-icons">add</i></router-link></td>
                                                <td><input type="search" placeholder="Search test" v-on:change="searchTests" v-model="query"></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="test in tests">
                                                <test-field :test="test"></test-field>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>`
            })
            Vue.component('test-field', {
                props: ['test'],
                methods: {
                    deleteTest: function() {
                        var xhr = new XMLHttpRequest()
                        var self = this
                        xhr.open('DELETE', './test/' + self.test.id)
                        xhr.setRequestHeader('X-CSRF-TOKEN', Laravel.token)
                        xhr.onload = function() {
                            self.$parent.getAllTests()
                        }
                        xhr.send();
                    }
                },
                template: `                     
                    <tr>
                        <td>{{ test.name }}</td>
                        <td>
                            <router-link class="waves-effect waves-light btn light-green" :to="'/test/' + test.id">Go test!</router-link>
                            <a class="waves-effect waves-light btn red darken-2" v-on:click="deleteTest">Delete</a>
                        </td>
                    </tr>`
            })
            const AddNewTest = Vue.component('add-test-form', {
                data: function() {
                    return {
                        testName: null,
                        testData: null
                    }
                },
                methods: {
                    sendTest: function() {
                        var xhr = new XMLHttpRequest()
                        var self = this
                        xhr.open('PUT', 'test')
                        xhr.setRequestHeader('X-CSRF-TOKEN', Laravel.token)
                        xhr.setRequestHeader("Content-Type", "application/json")
                        xhr.onload = function () {
                            router.push('/')
                        }
                        xhr.send(JSON.stringify(self.testData))
                    },
                    getData: function() {
                        var file = document.getElementById('file').files[0];
                        var questions;
                        var self = this
                        Papa.parse(file, {
                            complete: function(results, file) {
                                var data =  { name: self.testName,
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
                                    self.testData = data;
                                }
                            }
                        });
                    }
                },
                template: `        
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
                                    <input type="file" id="file" v-on:change="getData">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text">
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-field col s12">
                                    <input placeholder="Name of List" id="name_of_list" type="text" class="validate" v-model="testName" v-on:change="getData">
                                </div>
                            </div>
                        </form>
                </div>
                <div class="card-action">
                    <a v-on:click.prevent="sendTest">Upload</a>
                    <router-link to="/">Back</router-link>
                </div>
                </div>
            </div>
        </div>`
            })
            const Test = Vue.component('test', {
                data: function() {
                    return {
                        id: this.$route.params.id,
                        data: null,
                        answers: [],
                        currentQuestion: 0
                    }
                },
                created: function () {
                    this.getTestData()
                },
                methods: {
                    getTestData: function() {
                        var xhr = new XMLHttpRequest()
                        var self = this
                        xhr.open('GET', './test/' + this.id)
                        xhr.onload = function() {
                            self.data = JSON.parse((JSON.parse(xhr.responseText)))
                        }
                        xhr.send()
                    },
                    getAnswers: function() {
                        if( this.currentQuestion == this.data.questions.length ) { this.checkAnswers() }
                        else {
                            var answer = []
                            for (var i = 0; i < document.querySelectorAll('#form>p').length; i++) {
                                isChecked = document.getElementById('answ' + (i)) ? document.getElementById('answ' + (i)).checked : false;
                                if( isChecked ) {
                                    answer.push(i + 1);
                                }
                            }
                            this.answers.push(answer.length != 0 ? answer : [0]);
                            this.currentQuestion++;
                            document.getElementById('form').reset();
                        }
                    }
                },
                template: `    <div class="row" v-if="data">
                                    <div class="col s12 m6 offset-m3" v-if="data.questions[currentQuestion]">
                                        <div class="card blue-grey darken-1">
                                        <div class="card-content white-text">
                                            <h1 class="card-title">{{ data.name }}</h1>
                                            <hr>
                                            <span>{{ data.questions[currentQuestion].question }}</span>
                                            <form action="#" id="form">
                                            <p v-for="(answer, id) in data.questions[currentQuestion].answers" v-if="answer">
                                                <input type="checkbox" :id="'answ' + id" />
                                                <label :for="'answ' + id" :id="'answer' + id">{{ answer }}</label>
                                            </p>
                                            </form>
                                        </div>
                                        <div class="card-action">
                                            <a id="send" href="#" v-on:click.prevent="getAnswers">Send</a>
                                        </div>
                                        </div>
                                    </div>
                                    <results-table v-if="data.questions.length == currentQuestion" :answers="answers" :id="id"></results-table>
                                </div>`
            })
            Vue.component('results-table', {
                props: ['answers', 'id'],
                data: function () {
                    return { data: null }
                },
                created: function() {
                    this.checkAnswers()
                },
                methods: {
                    checkAnswers: function() {
                        var xhr = new XMLHttpRequest()
                        var self = this
                        xhr.open('PATCH', './test/' + this.id + '/check')
                        xhr.setRequestHeader('X-CSRF-TOKEN', Laravel.token)
                        xhr.setRequestHeader("Content-Type", "application/json")
                        xhr.onload = function() {
                            self.data = (JSON.parse(xhr.responseText))
                        }
                        xhr.send( JSON.stringify(this.answers) )
                    }
                },
                computed: {
                    rightAnswers: function() {
                        var count = 0;
                        for (var i = this.data.results.length - 1; i >= 0; i--) {
                            if(this.data.results[i].correct) count++
                        }
                        return count
                    },
                    totalQuestions: function () {
                        return this.data.results.length
                    }
                },
                template: `<table class="bordered centered col s6 offset-s3">
                            <template v-if="data">
                                <caption>Results of Test: {{ data.name }}</caption>
                                <span>{{ rightAnswers }} of {{totalQuestions}}</span>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Question</th>
                                    </tr>
                                </thead>
                                <tbody id="results">
                                <template v-for="(result, index) in data.results">
                                    <single-question-result :number="index + 1" :text="result.question" :correct="result.correct" />
                                </template>
                                </tbody>
                            </template>
                            </table>`
            })
            Vue.component('single-question-result', {
                props: ['number', 'text', 'correct'],
                template: `<tr :class="correct ? 'green' : 'red'">
                                <td>{{ id }}</td>
                                <td>{{ text }}</td>
                            </tr>`
            })
        </script>
        
        <script>
            const routes = [
                { path: '/', component: TestTable },
                { path: '/add-new', component: AddNewTest},
                { path: '/test/:id', component: Test},
            ]
            const router = new VueRouter({
                routes
            })
        </script>

        <script>
            var app = new Vue({
                router
            }).$mount('#app')
        </script>
        @endverbatim
    </body>
</html>
