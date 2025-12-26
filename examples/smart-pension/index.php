<?php

use SMART\Oauth2\AccessToken;
use SMART\Employee\EmployeeList;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/config.php';


session_start();
refreshAccessTokenIfNeeded();
$accessToken = AccessToken::get();
$company_id = '1016504';
$employees = [];
if($accessToken != ""){
    
    try {
        $request = new EmployeeList($company_id);
        $response = $request->fire();
        $result = $response->getArray();  
        $employees = $result['employees'] ?? [];
    } catch (\Throwable $th) {
        //throw $th;
        echo $th->getMessage();
    }
}



?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://cdn.tailwindcss.com"></script>

    <title>SMART API Examples</title>
    <style>
        body {
            margin-top: 10px;
        }

        td {
            vertical-align: middle !important;
        }

        td.test-btn {
            width: 70px !important;
            text-align: center !important;
        }
    </style>
</head>

<body x-data="test" class="container px-5 py-8">
    <h3>SMART API Examples</h3>
    <hr>
    <div class="mt-5" >
        <div class="grid grid-cols-2 gap-3">
            <div class="col-span-1">
            <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Client ID</label>
                <input type="text"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    name="client_id" x-model="client_id" placeholder="Client ID" value="<?php echo $clientId; ?>">
                    

            </div>
            <div class="col-span-1">
                <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Client Secrete</label>
                <input type="text"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    name="client_secret" x-model="client_secret" placeholder="Client Secret" value="<?php echo $clientSecret; ?>">
            </div>
        </div>
    </div>

    <div style="margin-top: 10px">
        <input type="hidden" name="access_token" value="<?php echo AccessToken::get(); ?>">
        <div class="w-full" id="access-token-container"></div>
        <div class="grid grid-cols-3">
            <a x-show="!destroySessionBtn" href="javascript:void(0)" @click="authorizeAdviser()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 btn btn-sm btn-warning"
                style="margin-top: 10px" id="create-access-token-btn">Create Adviser token</a>

            <a x-show="!destroySessionBtn" href="javascript:void(0)" @click="authorizeEmployer()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 btn btn-sm btn-warning"
                style="margin-top: 10px" id="create-access-token-btn">Create Employer token</a>

            <a x-show="destroySessionBtn" href="/oauth2/destroy-session.php" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 btn btn-sm btn-danger" style="margin-top: 10px"
                id="destroy-session-btn">Destroy session</a> 
        </div>
        
    </div>
    <hr>

    <div x-show="features" id="features" class="mt-5">

        <div>
            <h3 class="w-full flex justify-between">
                Company
                <div class="">
                    <button x-show="show_company_form == false"  @click="show_company_form = true" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Create New Company
                    </button>
                    <button x-show="show_company_form == true"  @click="show_company_form = false" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    Company List
                    </button>
                </div>
            </h3>
            <div class="relative overflow-x-auto mt-5" x-show="show_company_form == false">
            <div class="w-full">
                <?php  if(isset($_SESSION['company_error']) && $_SESSION['company_error'] != "" ) { ?>
                    <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                        <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-medium">Error!</span> <?= $_SESSION['company_error']; ?>
                        </div>
                    </div>
                    <?php $_SESSION['company_error'] = ""; ?>
                <?php } ?>
                <?php  if(isset($_SESSION['company_success']) && $_SESSION['company_success'] != "" ) { ?>
                    <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
                            <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                            </svg>
                            <span class="sr-only">Info</span>
                            <div>
                                <span class="font-medium">Success!</span> <?= $_SESSION['company_success']; ?>
                            </div>
                        </div>
                        <?php $_SESSION['company_success'] = ""; ?>
                    <?php } ?>

                </div>
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">S/N</th>
                            <th scope="col" class="px-6 py-3">Company name</th>
                            <th scope="col" class="px-6 py-3">Registration number</th>
                            <th scope="col" class="px-6 py-3">Legal structure</th>
                            <th scope="col" class="px-6 py-3">Postcode</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div x-show="show_company_form == true" class="table-responsive mt-5">
                <table class="w-full table table-striped table-bordered table-hover table-sm">
                    <tr>
                        <td>
                            <p>Create New Company</p>
                            <form action="<?= $base_url; ?>/company/create.php" method="post">
                                <div class="w-full grid grid-cols-3 gap-3 mt-2">
                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Name</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="text" id="company_name"  name="name" placeholder="name" x-model="company.name" value="<?= $_SESSION['company']['name'] ?? '' ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Registration Number</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="text" id="company_registration_number" name="registration_number"
                                            placeholder="reg no" x-model="company.registration_number" value="<?= $_SESSION['company']['registration_number'] ?? '' ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Legal Structure</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="text" id="company_legal_structure" name="legal_structure"
                                            x-model="company.legal_structure" value="<?= $_SESSION['company']['legal_structure'] ?? 'Limited Company' ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Signatory Email</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="email" id="company_signatory_email" name="signatory_email"
                                            x-model="company.signatory_email" value="<?= $_SESSION['company']['signatory_email'] ?? 'john@doe.fr' ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Signatory Forename</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="text" id="company_signatory_forename" name="signatory_forename"
                                            value="forename" x-model="company.signatory_forename" value="<?= $_SESSION['company']['signatory_forename'] ?? '' ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Signatory Surname</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="text" id="company_signatory_surname" name="signatory_surname"
                                            x-model="company.signatory_surname" value="<?= $_SESSION['company']['signatory_surname'] ?? '' ?>" />
                                    </div>


                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Admin Email</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="email" name="admin_email"
                                            x-model="company.admin_email" value="<?= $_SESSION['company']['admin_email'] ?? 'john@doe.fr' ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">admin Forename</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="text" id="company_admin_forename" name="admin_forename"
                                            value="forename" x-model="company.admin_forename" value="<?= $_SESSION['company']['admin_forename'] ?? '' ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">admin Surname</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="text" id="company_admin_surname" name="admin_surname"
                                            x-model="company.admin_surname" value="<?= $_SESSION['company']['admin_surname'] ?? '' ?>" />
                                    </div>



                                    <div class="form-group">
                                        <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tax Relief Basis Type</label>
                                        <input
                                            class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                            type="text" id="company_tax_relief_basis_type" name="tax_relief_basis_type"
                                            x-model="company.tax_relief_basis_type" value="<?= $_SESSION['company']['tax_relief_basis_type'] ?? '' ?>" />
                                    </div>
                                </div> 
                                <div class="mt-5">
                                    <button  type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 btn btn-sm btn-primary">Create</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                   
                    
                </table>
            </div>

        </div>
        <div class="mt-10">
            <h3 class="w-full flex justify-between">
                Employee
                <div class="">
                    <button x-show="show_employee_form == false"  @click="show_employee_form = true" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Create New Employee
                    </button>
                    <button x-show="show_employee_form == true"  @click="show_employee_form = false" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Employee List
                    </button>
                </div>
            </h3>
            <div class="w-full">
                <?php  if(isset($_SESSION['employee_error']) && $_SESSION['employee_error'] != "" ) { ?>
                    <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                        <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-medium">Error!</span> <?= $_SESSION['employee_error']; ?>
                        </div>
                    </div>
                    <?php $_SESSION['employee_error'] = ""; ?>
                <?php } ?>
                <?php  if(isset($_SESSION['employee_success']) && $_SESSION['employee_success'] != "" ) { ?>
                    <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
                        <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-medium">Success!</span> <?= $_SESSION['employee_success']; ?>
                        </div>
                    </div>
                    <?php $_SESSION['employee_success'] = ""; ?>
                <?php } ?>

            </div>
            <div class="relative overflow-x-auto mt-5" x-show="show_employee_form == false">
                <?php  if(!empty($employees)){ ?>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">S/N</th>
                                <th scope="col" class="px-6 py-3">Employee ID</th>
                                <th scope="col" class="px-6 py-3">Forename</th>
                                <th scope="col" class="px-6 py-3">Surname</th>
                                <th scope="col" class="px-6 py-3">Postcode</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($employees as $key => $employee) { ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <td class="px-6 py-4"><?= $key + 1; ?></td>
                                <td class="px-6 py-4"><?= $employee['id']; ?></td>
                                <td class="px-6 py-4"><?= $employee['forename']; ?></td>
                                <td class="px-6 py-4"><?= $employee['surname']; ?></td>
                                <td class="px-6 py-4"><?= $employee['postcode']; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>                    
                    </table>
                <?php } ?>
            </div>
            <div x-show="show_employee_form == true">
                <table class="w-full table table-striped table-bordered table-hover table-sm">
                <tr>
                    <td>
                        <p>Create New Employee</p>
                        <form action="<?= $base_url; ?>/employee/create.php" method="post">
                            <input type="hidden" name="company_id" value="<?= $company_id ?>">
                            <div class="w-full grid grid-cols-3 gap-3 mt-2">
                                <div class="form-group">
                                    <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date of birth</label>
                                    <input required
                                        class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        type="date" id="date_of_birth"  name="date_of_birth" placeholder="Date of birth" x-model="employee.date_of_birth" value="<?= $_SESSION['employee']['date_of_birth'] ?? '' ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start date of employment or membership</label>
                                    <input required
                                        class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        type="date" id="starts_on"  name="starts_on" placeholder="Start date of employment or membership" x-model="employee.starts_on" value="<?= $_SESSION['employee']['starts_on'] ?? '' ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Forename</label>
                                    <input required
                                        class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        type="text" name="forename" placeholder="Forename" x-model="employee.forename" value="<?= $_SESSION['employee']['forename'] ?? '' ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Surname</label>
                                    <input required
                                        class="company_form_data bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        type="text" name="surname" placeholder="Surname" x-model="employee.surname" value="<?= $_SESSION['employee']['surname'] ?? '' ?>" />
                                </div>
                                <div class="form-group">
                                    <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gender</label>
                                    <select required name="gender" x-model="employee.gender" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="large-input" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Postcode</label>
                                    <input required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        type="text" name="postcode" x-model="employee.postcode" value="<?= $_SESSION['employee']['postcode'] ?? '' ?>" />
                                </div>
                            </div> 
                            <div class="mt-5">
                                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 btn btn-sm btn-primary">
                                    Submit Employee Info
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>


                </table>
            </div>
        </div>
    </div>




    <script>
        window.clientId = '<?php echo $clientId; ?>';
        window.clientSecret = '<?php echo $clientSecret; ?>';
        window.accessToken = '<?php echo AccessToken::get(); ?>';
        
        document.addEventListener('alpine:init', () => {
            Alpine.data('test', () => ({
                accessToken:'',
                accessTokenContainer:'',
                destroySessionBtn:false,
                createAccessTokenBtn:true,
                features:false,
                client_id:'',
                client_secret:'',
                show_employee_form:false,
                show_company_form:false,
                company:{
                    registration_number:'',
                    legal_structure:'Limited Company',
                    signatory_email:'',
                    signatory_forename:'',
                    signatory_surname:'',
                    tax_relief_basis_type:'',
                },
                employee:{
                    registration_number:'',
                    legal_structure:'Limited Company',
                    signatory_email:'',
                    signatory_forename:'',
                    signatory_surname:'',
                    tax_relief_basis_type:'',
                },
                init() {
                     this.client_id =  window.clientId;
                     this.client_secret =  window.clientSecret;
                     this.accessToken =  window.accessToken;
                    if (this.accessToken === "") { // Doesn't have access token
                        this.accessTokenContainer = "Access Token: Doesn't exists.";
                        this.destroySessionBtn = false;
                        this.features = false;
                        this.createAccessTokenBtn = true;
                    } else {
                        this.accessTokenContainer = "Access Token: " + accessToken;
                        this.destroySessionBtn = true;
                        this.features = true;
                        this.createAccessTokenBtn = false;
                    }
                },
                authorizeAdviser() {
                    const clientId = this.client_id;
                    const clientSecret = this.client_secret;
                    let query = [];
                    if (clientId !== "") query.push(`client_id=${clientId}`);
                    if (clientSecret !== "") query.push(`client_secret=${clientSecret}`);
                    const queryString = query.join('&');
                    if (query.length) {
                        location.href = '/oauth2/adviser/create.php' + '?' + queryString;
                    } else {
                        location.href = '/oauth2/adviser/create.php';
                    }
                },
                authorizeEmployer() {
                    const clientId = this.client_id;
                    const clientSecret = this.client_secret;
                    let query = [];
                    if (clientId !== "") query.push(`client_id=${clientId}`);
                    if (clientSecret !== "") query.push(`client_secret=${clientSecret}`);
                    const queryString = query.join('&');
                    if (query.length) {
                        location.href = '/oauth2/employer/create.php' + '?' + queryString;
                    } else {
                        location.href = '/oauth2/employer/create.php';
                    }
                },
                createNewCompany() {
                    const query = new URLSearchParams(this.company);
                    queryString = qs.toString();
                    if (query.length) {
                        location.href = "/company/create-new-company.php" + '?' + queryString;
                    } else {
                        location.href = "/company/create-new-company.php";
                    }
                }
            }))
        })
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</body>

</html>