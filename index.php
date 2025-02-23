<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-markup.min.js"></script>

    <title>Чат-бот</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            justify-content: center;
        }
        .chat-container {
            width: 95%;
            max-width: 800px;
            height: 95%;
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
            padding: 10px;
            margin-bottom: 40px;
        }
        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            border-radius: 10px;
        }
        .input-container {
            display: flex;
            margin-top: 10px;
        }
        .input-container input {
            flex: 1;
            padding: 10px;
            border: 1px solid darkgrey;
            border-radius: 10px;
            margin-right: 5px;
        }
        .input-container button {
            padding: 10px;
            border: none;
            background: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 10px;
        }
        .message { margin: 5px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
            display: inline-block;
            white-space: pre-line;
        }
        .user {
            background: #007bff;
            color: white;
            align-self: flex-end;
        }
        .bot {
            background: #e0e0e0;
            align-self: flex-start;
        }
        .typing {
            font-style: italic;
            color: gray;
            align-self: flex-end;
        }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-x: auto;
            max-width: 100%;
        }

        img[id="nightImg"] {
            margin-left: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="chat-container">
    <div id="chat-box" class="chat-box">
        <?php
            require_once 'api.php';

            function html($text) {
                return str_replace(
                    ["&", "<", ">", "\"", "'", "\nhtml", "\n"],
                    ["&amp;", "&lt;", "&gt;", "&quot;", "&#39;", '<pre><code class="language-html">', '</code></pre>'],
                    $text
                );
            }

            $sid = '';
            if (!isset($_COOKIE["sid"])) {
                $sid = sha1('sid' . rand(0, PHP_INT_MAX) . time());
                setcookie('sid', $sid, time() + 31556926);
            } else {
                $sid = $_COOKIE["sid"];
            }
            foreach (get($sid) as $item){
                echo '<div class="'.($item['gpt'] ? 'message bot': 'message user').'">'.$item['msg'].'</div>';
            }
            ?>
    </div>
    <div class="input-container">
        <label for="chat-input"></label><input id="chat-input" type="text" placeholder="Введите сообщение...">
        <button id="btnSend" onclick="sendMessage()">Отправить</button>
        <div onclick="nightTheme()" style="width: 35px; background: #3f3f3f; margin-left: 5px; border-radius: 10px">
            <img id="nightImg" width="25px" src="https://static.vecteezy.com/system/resources/previews/001/189/147/original/moon-crescent-png.png" alt="luna">
        </div>
    </div>
</div>

<script>
    let press = document.getElementById("chat-input");
    let isNight = false;
    const sid = '<?php if(!isset($_COOKIE["sid"])){ $sid = sha1('sid'.rand(0, PHP_INT_MAX).time()); setcookie('sid', $sid, time() + 31556926); echo $sid; } else echo $_COOKIE["sid"];?>';

    window.onload = function (){
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'api.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        let data = {
            'method': 'getTheme',
            'sid': sid
        };
        xhr.send(JSON.stringify(data));
        xhr.onload = function () {
            if (xhr.status === 200 && xhr.readyState === 4) {
                let response = JSON.parse(xhr.responseText);
                nightTheme(response.result);
            }
        }
    };

    press.addEventListener('keypress', function (e) {
        let key = e.which || e.keyCode;
        if (key === 13) {
            sendMessage();
        }
    });

    function nightTheme(theme=null){
        if(theme === null) {
            isNight = !isNight;
        }else{
            isNight = theme;
        }
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'api.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        let data = {
            'method': 'setTheme',
            'isNight': isNight,
            'sid': sid
        };
        xhr.send(JSON.stringify(data));
        xhr.onload = function () {
            if (xhr.status === 200 && xhr.readyState === 4) {
                let response = JSON.parse(xhr.responseText);
                console.log(response)
            }
        }
        if(isNight){
            document.getElementById('chat-box').style.background = '#272525';
            document.getElementsByClassName('chat-container')[0].style.background = '#373737';
            document.getElementsByClassName('chat-container')[0].style.border = '1px solid #373737';
            document.getElementById('chat-input').style.background = '#272525';
            document.getElementById('chat-input').style.border = '1px solid white';
            document.getElementById('chat-input').style.color = 'white';
            document.getElementById('btnSend').style.border = '1px solid';
            document.getElementById('btnSend').style.background = '#373737';
            document.body.style.background = '#272525';
            Array.from(document.getElementsByClassName('message bot')).forEach((item) => {
                item.style.background = '#373737';
                item.style.color = '#dfdfdf';
            });

            Array.from(document.getElementsByClassName('message user')).forEach((item) => {
                item.style.background = '#717171';
            });
        }else{
            document.getElementById('chat-box').style.background = '#f9f9f9';
            document.getElementsByClassName('chat-container')[0].style.background = 'white';
            document.getElementsByClassName('chat-container')[0].style.border = '1px solid #ccc';
            document.getElementById('chat-input').style.background = 'white';
            document.getElementById('chat-input').style.border = '1px solid darkgrey';
            document.getElementById('chat-input').style.color = 'white';
            document.getElementById('btnSend').style.border = 'none';
            document.getElementById('btnSend').style.background = '#007bff';
            document.body.style.background = 'white';
            Array.from(document.getElementsByClassName('message bot')).forEach((item) => {
                item.style.background = '#e0e0e0';
                item.style.color = 'black';
            });
            Array.from(document.getElementsByClassName('message user')).forEach((item) => {
                item.style.background = '#007bff';
            });
        }
    }

    function addCacheMsg(text, isGpt){
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'api.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        let data = {
            'method': 'add',
            'gpt': isGpt,
            'msg': text,
            'sid': sid
        };
        xhr.send(JSON.stringify(data));
        xhr.onload = function () {
            if (xhr.status === 200 && xhr.readyState === 4) {
                let response = JSON.parse(xhr.responseText);
                console.log(response)
            }
        }
    }

    async function sendMessage() {
        const inputField = document.getElementById("chat-input");
        const chatBox = document.getElementById("chat-box");
        const userMessage = inputField.value.trim();
        if (!userMessage) return;
        addCacheMsg(userMessage, false);
        appendMessage(userMessage, "user");
        inputField.value = "";
        const botMessageDiv = document.createElement("div");
        if(isNight){
            botMessageDiv.style.background = '#373737';
            botMessageDiv.style.color = '#dfdfdf';
        }else{
            botMessageDiv.style.background = '#e0e0e0';
            botMessageDiv.style.color = 'black';
        }
        botMessageDiv.classList.add("message", "bot");
        chatBox.appendChild(botMessageDiv);
        chatBox.scrollTop = chatBox.scrollHeight;

        // в качестве ссылки стоит сервис тунель для обхода ограничений в России, можно поменять на оригинальный https://api.openai.com/
        const response = await fetch("https://api.aitunnel.ru/v1/chat/completions", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `API ТОКЕН`
            },
            body: JSON.stringify({
                model: "gpt-4o-mini",
                messages: [{ role: "user", content: userMessage }],
                stream: true
            })
        });

        const reader = response.body.getReader();
        const decoder = new TextDecoder("utf-8");
        let botMessage = "";

        while (true) {
            const { value, done } = await reader.read();
            if (done) break;
            const chunk = decoder.decode(value, { stream: true }).split("\n");
            for (const line of chunk) {
                if (line.startsWith("data:")) {
                    try {
                        const json = JSON.parse(line.substring(5));
                        if (json.choices && json.choices[0].delta && json.choices[0].delta.content) {
                            botMessage += json.choices[0].delta.content;
                            botMessageDiv.innerHTML = html(botMessage).replace(/\n/g, "<br>");
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    } catch (e) {
                        console.error("Ошибка обработки JSON:", e);
                    }
                }
            }
        }
        addCacheMsg(botMessage, true);
    }

    function html(text){
        return text.replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;")
            .replace("```html", '<pre><code class="language-html">')
            .replace('```', '</code></pre>');
    }

    function appendMessage(message, sender) {
        const chatBox = document.getElementById("chat-box");
        const messageDiv = document.createElement("div");
        if(sender === 'user') {
            if (isNight) {
                messageDiv.style.background = '#717171';
            } else {
                messageDiv.style.background = '#007bff';
            }
        }
        messageDiv.textContent = html(message.replace("<", "&lt;"));
        messageDiv.classList.add("message", sender);
        messageDiv.innerHTML = html(message).replace(/\n/g, "<br>");
        chatBox.appendChild(messageDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
    }
</script>
</body>
</html>
