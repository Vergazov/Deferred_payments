<?php
//ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
//error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки

require 'lib.php';

//debug($_POST);

$accountId = $_POST['accountId'];
$supplyHref = trim(stripcslashes($_POST['createTask']), "\"");
//debug($supplyHref);
$app = AppInstance::loadApp($accountId);
// удаление
if($_POST['delete'] == 'deletePayment'){
//    echo 'success';
    foreach ($app->pendingTasks as $key => $task){
        if($task['supplyHref'] == $supplyHref ){
//            echo 'success';
            unset($app->pendingTasks[$key]);
        }
    }
    
    $num = 0;
    foreach ($app->pendingTasks as $task_key => $task_value) {
        $num++;
        $app->pendingTasks[$task_key]['number'] = sprintf("%04d", $num);
    }
    
    $app->persist();
    $successMessage = 'Платеж успешно удален';
    $rules = $app->rules;
    require 'iframe.html';
    exit;
    
    
}
if($_POST['create'] == 'createPayment'){
    foreach ($app->pendingTasks as $key => $task){
        if($task['supplyHref'] == $supplyHref ){
            JsonApi()->createPayment($task['request_body']);
            unset($app->pendingTasks[$key]);
            $app->persist();
            $successMessage = 'Платеж успешно создан';
            $rules = $app->rules;
            require 'iframe.html';
            exit;
        }
    }
}



//$arr = json_decode($_POST['createTask']);
//$arr = $_POST['createTask'];
//debug($arr['']);
//$body = $_POST['create'];
//var_dump($body);
//$app->pendingTasks = [];
//$app->persist();
//debug($app);





