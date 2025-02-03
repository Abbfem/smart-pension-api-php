<?php

use SMART\Company\CreateCompany;
use SMART\Company\Request\NewPostBody;


require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../helpers.php';

session_start();


$data = [
    'name' => $_POST['name'],
    'registration_number' => $_POST['registration_number'],
    'legal_structure' => $_POST['legal_structure'],
    'signatories' => [
        array('email' => $_POST['signatory_email'], 'forename' => $_POST['signatory_forename'], 'surname' => $_POST['signatory_surname'])
    ],
    'admins' => [
        array('email' => $_POST['admin_email'],'forename' => $_POST['admin_forename'],'surname' => $_POST['admin_surname'], 'password' => "WDJK/82s@2016)99.ks")
    ],
    'scheme_detail' => ['tax_relief_basis_type' => 'net_pay_arrangement']
];


try {
    $data = new NewPostBody($data);
    $request = new CreateCompany($data);
    $response = $request->fire();
    $result = $response->getArray();
    if(!empty($result)){
        $_SESSION['company_success'] = "Company created successfully";
        if(!empty($result) && isset($result['authorization_redirect_uri'])){
            header('Location: '.$result['authorization_redirect_uri']);
            exit;
        }  
    }else{
        $_SESSION['company_error'] = "Unable to create new company";
    }
} catch (\Throwable $th) {
    $_SESSION['company_error'] = $th->getMessage();
}

header('Location: /index.php');
exit;