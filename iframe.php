<?php
//ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
//error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки
require_once 'lib.php';

$contextName = 'IFRAME';
require_once 'user-context-loader.inc.php';

$app = AppInstance::loadApp($accountId);

$isSettingsRequired = $app->status != AppInstance::ACTIVATED;
$counterparties = JsonApi()->counterparties();
$counterpartiesValues = [];
array_push($counterpartiesValues,["name"=>'',"id"=>""]);
foreach ($counterparties as $item){
    array_push($counterpartiesValues,["name"=>"$item->name","id"=>"$item->id"]);
}
$rules = $app->rules;

//
// проверка работы сравнения дат в формате UNIX
//$dateOfCreatePayment = '2022-08-14';
//$todayDate = date('Y-m-d');
//
//
//debug('Дата создания платежа:');
//debug($dateOfCreatePayment);
//$dateOfCreatePaymentUnix = strtotime($dateOfCreatePayment);
//debug($dateOfCreatePaymentUnix);
//
//debug('Сегодняшняя дата:');
//debug($todayDate);
//$todayDateUnix = strtotime($todayDate);
//debug ($todayDateUnix);
//
//if($dateOfCreatePaymentUnix <= $todayDateUnix){
//    echo 'Платеж будет создан';
//}else{
//    echo 'Платеж не будет создан';
//}
//debug(date('Y-m-d'));
require 'iframe.html';
//require_once 'webhook.php';
//require_once('scheduled-tasks.php');
//debug(date('Y-m-d'));
//debug($app->pendingTasks[0]['moment']);
//if($app->pendingTasks[0]['moment'] <= date('Y-m-d')){
//    echo 'success';
//}
//debug($app->pendingTasks);
//debug($app->rules);
//debug($app->pendingTasks);
//$res = @file_get_contents('test.app');
//$res1 = unserialize($res);
//debug($res);
//$resres1 = json_decode($res1);
//debug($resres1);