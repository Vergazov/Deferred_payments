<?php
require_once 'lib.php';

$counterpartiesValues = json_decode($_POST['counterpartiesValues'], true);
$rule_number = $_POST['rule_number'];

$accountId = $_POST['accountId'];

$app = AppInstance::loadApp($accountId);

foreach ($app->rules as $rule_key => $rule_value) {
    if (in_array($rule_number, $rule_value)) {
        unset($app->rules[$rule_key]);
        break;
    }
}
$num = 0;
foreach ($app->rules as $rule_key => $rule_value) {
    $num++;
    $app->rules[$rule_key]['number'] = sprintf("%04d", $num);
}
if (num == 0) {
    $notify = $app->status != AppInstance::ACTIVATED;
    $app->status = AppInstance::SETTINGS_REQUIRED;
    vendorApi()->updateAppStatus(cfg()->appId, $accountId, $app->getStatusName());
}

$app->persist();
$rules = $app->rules;
$successMessage = 'Правило успешно удалено';
require 'iframe.html';
