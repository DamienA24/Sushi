<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        #site {
            position: absolute;
        }

        div {
            position: absolute;
            right: 0;
            bottom: 0;
        }

        input {
            width: 400px;
        }

        button {
            width: 50px;
        }

        textarea {
            width: 100%;
        }

    </style>
    <title>Document</title>
</head>
<body>


<iframe id="site" width="100%" height="100%" src="https://sushishop.fr"></iframe>
.com/api-client/demo/embedded/7f79e29c-08f4-4d87-93a3-90f7a763f48e">

<div>
    <input id="input" type="text">
    <button id="rec">Speak</button>
    <br>Response<br> <textarea id="response" cols="40" rows="20"></textarea>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {

        var accessToken = "82f504bafe3342cb83b872c00602b826";
        var baseUrl = "https://api.api.ai/v1/";

        $("#input").keypress(function (event) {
            if (event.which == 13) {
                event.preventDefault();
                send();
            }
        });
        $("#rec").click(function (event) {
            switchRecognition();
        });


        var recognition;

        function startRecognition() {
            recognition = new webkitSpeechRecognition();
            recognition.onstart = function (event) {
                updateRec();
            };
            recognition.onresult = function (event) {
                var text = "";
                for (var i = event.resultIndex; i < event.results.length; ++i) {
                    text += event.results[i][0].transcript;
                }
                setInput(text);
                stopRecognition();
            };
            recognition.onend = function () {
                stopRecognition();
            };
            recognition.lang = "en-US";
            recognition.start();
        }

        function stopRecognition() {
            if (recognition) {
                recognition.stop();
                recognition = null;
            }
            updateRec();
        }

        function switchRecognition() {
            if (recognition) {
                stopRecognition();
            } else {
                startRecognition();
            }
        }

        function setInput(text) {
            $("#input").val(text);
            send();
        }

        function updateRec() {
            $("#rec").text(recognition ? "Stop" : "Speak");
        }

        function send() {
            var text = $("#input").val();
            $.ajax({
                type: "POST",
                url: baseUrl + "query?v=20150910",
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                headers: {
                    "Authorization": "Bearer " + accessToken
                },
                data: JSON.stringify({query: text, lang: "en", sessionId: "somerandomthing"}),
                success: function (data) {
                    setResponse(JSON.stringify(data, undefined, 2));
                    console.log(data.result.metadata.intentName);
                    setResponse(data.result.fulfillment.speech);
                    if (data.result.metadata.intentName === "voirSushi") {
                        console.log('if');
                        $('iframe').attr('src', 'https://www.sushishop.fr/fr/all-categories');
                        //$('#response').text(data.result.fulfillment.speech)
                    } else if (data.result.metadata.intentName === "voirDetailBoxe") {
                        $('iframe').attr('src', 'https://www.sushishop.fr/fr/livraison/california-rolls');
                    } else if (data.result.metadata.intentName === "home") {
                        $('iframe').attr('src', 'https://sushishop.fr');
                    }
                },
                error: function () {
                    setResponse("Internal Server Error");
                }
            });
            setResponse("Loading...");
        }

        function setResponse(val) {
            $("#response").text(val);
        }


    })

</script>
</body>

</html>