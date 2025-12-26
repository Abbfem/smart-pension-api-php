<?php

use SMART\Group\Crud\Create;
use SMART\Group\Request\PostData;
use Illuminate\Support\Facades\Session;

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../helpers.php';

$data = [
    'name' => $_POST['name'],
    'employee_percentage' => $_POST['employee_percentage'],
    'company_percentage' => $_POST['company_percentage'],
    'payment_frequency' => $_POST['payment_frequency'],
];

if (
    !isset($_POST['company_id']) ||
    !isset($_POST['name']) ||
    !isset($_POST['employee_percentage']) ||
    !isset($_POST['company_percentage']) ||
    !isset($_POST['payment_frequency']) 
) {
    Session::put('employee', $data);
    Session::put('employee_error', 'ERROR: Please fill company id, name, employee percentage, company percentage, payment frequency and submit the form again');
    header('Location: /index.php');
    exit;
}
// payment_frequency value must be any value from the below
// annually
// weekly
// fortnightly
// four_weekly
// monthly
// quarterly
// bi_annually


try {
    $data = new PostData($data);
    $request = new Create($_POST['company_id'], $data);
    $response = $request->fire();
    $result = $response->getArray();
    if(!empty($result)){
        Session::put('group_success', "Group created successfully");
    }else{
        Session::put('group_error', "Unable to create new group");
    }
} catch (\Throwable $th) {
    Session::put('group_error', $th->getMessage());
}

header('Location: /index.php');
exit;