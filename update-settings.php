<?php
//ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
//error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки
require_once 'lib.php';
$counterpartiesValues = json_decode($_POST['counterpartiesValues'], true);
$accountId = $_POST['accountId'];
$app = AppInstance::loadApp($accountId);

    //проверяем форму

if(!empty($_POST['counterparty'])){
    $counterparty = clean($_POST['counterparty']);
}
if($_POST['due-date'] >= 0 and $_POST['due-date'] != '' ){
    $dueDate = clean($_POST['due-date']);
}

if (isset($counterparty) and isset($dueDate) ) {
    
    //парсим правила, если находим совпадение, меняем в найденном правиле due-date
    
    $mark = false;
    foreach($app->rules as &$rule){
        if($rule['counterparty']['id'] === $counterparty){
            $rule['due-date'] = $dueDate;
            $app->persist();
            $successMessage = 'Правило успешно отредактировано!';
            $mark = true;
        }
    }
    unset($rule);
    
    // отрабатывает только в том случае если было найдено совпадение в правилах    
    if($mark){
        $rules = $app->rules;
        require 'iframe.html';
        exit;
    }
    
    //если совпадений найдено не было, значит это новое правило, добавляем его:
    
    $agent = jsonApi()->getObject('counterparty', $counterparty);
    $agent = ['id' => $agent->id, 'name' => $agent->name];    
    
    if($app->daw['daw'] == 'on'){
    $rule = [
            "number" => sprintf("%04d", count($app->rules) + 1),
            "counterparty" => $agent,
            "due-date" => $dueDate,
            "delete_button" => "Удалить",
        ];    
    }else{
        $rule = [
            "number" => sprintf("%04d", count($app->rules) + 1),
            "counterparty" => $agent,
            "due-date" => $dueDate,
            "delete_button" => "Удалить",
        ];
    }

    array_push($app->rules, $rule);
    $notify = $app->status != AppInstance::ACTIVATED;
    $app->status = AppInstance::ACTIVATED;

    vendorApi()->updateAppStatus(cfg()->appId, $accountId, $app->getStatusName());

    $app->persist();

    $rules = $app->rules;

    $successMessage = 'Добавлено новое правило!';
    require 'iframe.html';    
}
else{        
    $rules = $app->rules;
    $errorMessage = 'Ошибка, не заполнены поля';
    require 'iframe.html';
}
