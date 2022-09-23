<?php
//ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
//error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки

require_once 'lib.php';

if(!empty(file_get_contents('php://input'))){

$requesthook = file_get_contents('php://input');
//loginfo('hook:', print_r($requesthook, true));
$hook = json_decode($requesthook);
//loginfo('hook', print_r($hook, true));
$accountId = $hook->events[0]->accountId;
$objectLink = $hook->events[0]->meta->href;

$objectId = getId($objectLink);
loginfo('objectId', print_r($objectId,true));

//$objectLink = null;
//$objectLink = 'https://online.moysklad.ru/api/remap/1.2/entity/supply/c45e6ebd-29c4-11ed-0a80-08fc0005c4a3';    
//$accountId = '35036f89-c946-11e8-9109-f8fc00007a53';

$app = AppInstance::loadApp($accountId);

$object = JsonApi()->getEntity($objectLink,'organization,agent');

$data = [
   'moment' => $object->moment,
   'sum' => $object->sum,
   'organization_href' => $object->organization->meta->href,
   'agent_href' => $object->agent->meta->href,
   'agent_id' => $object->agent->id,
   ];

foreach($app->rules as $rule){
   if($rule['counterparty']['id'] == $data['agent_id'] ){
           $momentOfPayment = in_array($rule['amountDays'],$rule) ? 
                    date('Y-m-d',strtotime($data['moment'] . '+' .  $rule['due-date'] .  'days' . '-' . $rule['amountDays'] . 'days')) :
                    date('Y-m-d',strtotime($data['moment'] . '+' .  $rule['due-date'] .  'days'));
                    
   

  
    $requestBody = [
       'sum' => $data['sum'],
       'organization'=>[
           'meta'=>[
               'href'=> $data['organization_href'],
               'metadataHref' => 'https://online.moysklad.ru/api/remap/1.2/entity/organization/metadata',
               'type'=>'organization',
               'mediaType'=>'application/json'
           ]
       ],
       'agent'=>[
           'meta'=>[
               'href'=> $data['agent_href'],
               'metadataHref' => 'https://online.moysklad.ru/api/remap/1.2/entity/organization/metadata',
               'type'=>'counterparty',
               'mediaType'=>'application/json'
           ]
       ],    
       'expenseItem'=>[
           'meta'=>[
               'href' => 'https://online.moysklad.ru/api/remap/1.2/entity/expenseitem/353d93ef-c946-11e8-9ff4-34e800254c31',
               'metadataHref' => 'https://online.moysklad.ru/api/remap/1.2/entity/expenseitem/metadata',
               'type'=>'expenseitem',
               'mediaType'=>'application/json'
           ]
       ]
    ];
    $body = json_encode($requestBody);
    $dataForCron = [
    "number" => sprintf("%04d", count($app->pendingTasks) + 1),
    'moment' => strtotime($momentOfPayment),
//    'moment' => $momentOfPayment, // для теста
    'sum' => $object->sum,
    'organization_href' => $object->organization->meta->href,
    'agent_href' => $object->agent->meta->href,
    'request_body' => $body,
    'supplyHref' => 'https://online.moysklad.ru/app/#supply/edit?id=' . $objectId
    ];

    if($momentOfPayment <= date('Y-m-d')){
        $res = JsonApi()->createPayment($body);
        loginfo('create: ', print_r($res,true));
    }
    else{
        array_push($app->pendingTasks, $dataForCron);
        $app->persist();
        
//debug($app->pendingTasks);

// unset($app->pendingTasks);
// $app->persist();
// debug($app->pendingTasks);
    }
}
}
}


