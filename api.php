<?php

if(file_exists('database.json')){
    $db = json_decode(file_get_contents('database.json'), true);
}else{
    $db = [];
    save();
}
$post = json_decode(file_get_contents('php://input'), true, JSON_UNESCAPED_UNICODE);
if($post['method'] == 'getTheme'){
    $sid = $post['sid'];
    if(!isset($db[$sid])){
        $db[$sid] = ['data' => [], 'nightTheme' => false];
    }
    save();
    echo json_encode(['result' => $db[$post['sid']]['nightTheme']], JSON_UNESCAPED_UNICODE);
}else if($post['method'] == 'setTheme'){
    $db[$post['sid']]['nightTheme'] = $post['isNight'];
    echo json_encode(['result' => save()], JSON_UNESCAPED_UNICODE);
}else if($post['method'] == 'add'){
    $sid = $post['sid'];
    if(!isset($db[$sid])){
        $db[$sid] = ['data' => [], 'nightTheme' => false];
    }
    $db[$sid]['data'][] = [
        'gpt' => $post['gpt'],
        'msg' => $post['msg'],
        'time' => time()
    ];
    echo json_encode(['result' => save()], JSON_UNESCAPED_UNICODE);
}else if($post['method'] == 'get'){
    if(isset($db[$post['sid']])) {
        echo json_encode(get($post['sid']), JSON_UNESCAPED_UNICODE);
    }else{
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    }
}

function get($sid){
    global $db;
    if(!isset($db[$sid])){
        return [];
    }
    return $db[$sid]['data'];
}

function save(){
    global $db;
    return file_put_contents('database.json', json_encode($db, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}