<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'classes/Route.php';
include 'classes/Customer.php';
include 'config/DB.php';

define('BASEPATH','/');


Route::add('/add_new_customer', function() {

    $db = new DB;
    $customer = new Customer($db);

    $data = $_POST;
    $customer->ID = $data['ID'];
    $customer->fullname = $data['fullname'];
    $customer->DOB = $data['DOB'];
    $customer->sex = $data['sex'];
    $customer->phone_numbers = $data['phone_numbers'];
    if($customer->validateApi("create")){
        $customer->createCustomer();
     }
     $customer->response();

}, 'post');


Route::add('/get_customer', function() {
    
    $db = new DB;
    $customer = new Customer($db);
    $data = $_GET;
    $customer->ID = $data['ID'];
    if($customer->validateApi("read")){
        $customer->getCustomer();
    }
    $customer->response();

}, 'get');


Route::add('/update_customer', function() {

    $db = new DB;
    $customer = new Customer($db);
    $customer = new Customer($db);
    $data = $_POST;
    $fields_to_validate = [];
    foreach ($data as $key => $value){
        $customer->{$key} = $value;
        $fields_to_validate[] = $key;
    }
    if($customer->validateApi("update",$fields_to_validate)){
        $customer->updateCustomer($data);
    }
    $customer->response();
}, 'post');


Route::add('/delete_customer', function() {

    $db = new DB;
    $customer = new Customer($db);
    $customer->ID = $_GET['ID'];
    if($customer->validateApi("delete")){
        $customer->deleteCustomer();
    }
    $customer->response();
    }, 'get');


Route::run(BASEPATH);