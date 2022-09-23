<?php
//ini_set('display_errors', 'On'); // сообщения с ошибками будут показываться
//error_reporting(E_ALL); // E_ALL - отображаем ВСЕ ошибки

require 'lib.php';

$dir = new DirectoryIterator('/home/admin/php-apps/app/data');

foreach ($dir as $file){
    if($file->isFile()){
        $arr[] = $file->getFilename();
    }
}
foreach ($arr as $item){
    $data[] = file_get_contents('/home/admin/php-apps/app/data/' . $item);
}    
foreach ($data as $value){
    $newData[] = unserialize($value);
}

foreach($newData as $data){
    $app = AppInstance::loadApp($data->accountId);
    foreach($app->pendingTasks as $key => $item){
        if($item['moment'] <= strtotime(date('Y-m-d'))){
            $res = JsonApi()->createPayment($item['request_body']);
            unset($app->pendingTasks[$key]);
            $app->persist();
        }
    }
}

