<?php
//ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
//error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки

require_once 'lib.php';
$counterpartiesValues = json_decode($_POST['counterpartiesValues'], true);
$accountId = $_POST['accountId'];
$app = AppInstance::loadApp($accountId);

if(!empty($_POST['daw'])){
    $daw = clean($_POST['daw']);
    if(!empty($_POST['amount-days'])){
        $amount = clean($_POST['amount-days']);
        
        $app->daw['amount'] = $amount;
        $app->daw['daw'] = $daw;
        
        if($app->rules == []){
        $rules = $app->rules;
        $errorMessage = 'Ошибка, внесите хотябы одно правило';
        require 'iframe.html';
        exit;    
    }
    $amountDays = $amount;
    
    foreach ($app->rules as &$rule){
        $rule['amountDays'] = $amountDays;
        $app->persist();
    }
    unset($rule);
    
    $rules = $app->rules;
    $successMessage = 'Успешно';
    require 'iframe.html';
    }else{
        $rules = $app->rules;
        $errorMessage = 'Ошибка, не заполнены поля';
        require 'iframe.html';
        exit; 
    }
}else{
    $daw = 'off';
    $amount = '';
    
    $app->daw['amount'] = $amount;
    $app->daw['daw'] = $daw;
    foreach($app->rules as &$rule){
        unset($rule['amountDays']);
    }
    unset($rule);
    $app->persist();
    $successMessage = 'Успешно';
    $rules = $app->rules;
    require 'iframe.html';
}


