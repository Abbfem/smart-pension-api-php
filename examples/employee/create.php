<?php

use SMART\Employee\Crud\Create;
use SMART\Employee\Request\PostData;
use Illuminate\Support\Facades\Session;

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../helpers.php';


$data = [
    'date_of_birth' => $_POST['date_of_birth'],
    'starts_on' => $_POST['starts_on'],
    'forename' => $_POST['forename'],
    'surname' => $_POST['surname'],
    'gender' => $_POST['gender'],
    'postcode' => $_POST['postcode'],
];

if (
    !isset($_POST['company_id']) ||
    !isset($_POST['date_of_birth']) ||
    !isset($_POST['starts_on']) ||
    !isset($_POST['forename']) ||
    !isset($_POST['surname']) ||
    !isset($_POST['gender']) ||
    !isset($_POST['postcode'])
) {
    Session::put('employee', $data);
    Session::put('employee_error', 'ERROR: Please fill company_id, date_of_birth, starts_on, forename, surname, gender, postcode and submit the form again');
    header('Location: /index.php');
    exit;
}



try {
    $data = new PostData($data);
    $request = new Create($_POST['company_id'], $data);
    $response = $request->fire();
    $result = $response->getArray();
    if(!empty($result)){
        Session::put('employee_success', "Employee created successfully");
    }else{
        Session::put('employee_error', "Unable to create new employee");
    }
} catch (\Throwable $th) {
    Session::put('employee_error', $th->getMessage());
}

header('Location: /index.php');
exit;