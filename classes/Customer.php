<?php
include ("/classes/Validate.php");
class Customer extends Validate{

    private $db_table = "customers";
    public $ID;
    public $fullname;
    public $DOB;
    public $sex;
    public $phone_numbers;
    public $status;
    public $response;

    public function __construct($db){
        $this->conn = $db;

    }
   
    public function createCustomer(){

        $sqlQuery = "INSERT INTO
                    ". $this->db_table ."
                SET
                    ID = ?, 
                    fullname = ?, 
                    DOB = ?, 
                    sex = ?, 
                    phone_numbers = ?";
        $params = [
            $this->ID,
            $this->fullname,
            $this->DOB,
            $this->sex,
            $this->phone_numbers
        ];
        if($this->conn->query($sqlQuery,$params)){
            if($this->conn->getAffectedRows() > 0){
                $this->set_response(200,"new customer added successfully");
            }else{
                $this->set_response(204,"the Id of user alreadt exist");
            }
        }else{
            $this->set_response(500,"somthing faild");
        }
    }

    public function getCustomer(){
        $sqlQuery = "SELECT * FROM " . $this->db_table . " WHERE ID = ?";
        $params = [
            $this->ID
        ];
        if($this->conn->query($sqlQuery,$params)){
            if($this->conn->getNumRows() > 0){
                $this->set_response(200,$this->conn->fetch()[0]);
            }else{
                $this->set_response(204,"the customer not found");
            }
        }else{
            $this->set_response(500,"somthing faild");
        }
    }

    public function updateCustomer($data){
       
        $this->ID = $data['ID'];
        unset($data['ID']);
        $params = [];
        $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET ";
        foreach ($data as $key => $value){
            $sqlQuery .= "$key = ?, ";
            $params[] = $value;
        }
        $sqlQuery  = rtrim($sqlQuery, " ,");
        $sqlQuery .= " WHERE
                     ID = ?
                     ";
        $params[] = $this->ID;
              
        if($this->conn->query($sqlQuery,$params)){
            if($this->conn->getAffectedRows() > 0){
                $this->set_response(200,"the customer updated successfully");
            }else{
                $this->set_response(204,"the customer not found");
            }
        }else{
            $this->set_response(500,"somthing faild");
        }
    }


    public function deleteCustomer(){

        $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE ID = ?";
        
        $this->ID = htmlspecialchars(strip_tags($this->ID));
        
        $params = [$this->ID];
        if($this->conn->query($sqlQuery,$params)){
            if($this->conn->getAffectedRows() > 0){
                $this->set_response(200,"the customer as been deleted successfully");
            }else{
                $this->set_response(204,"the customer not found");
            }
        }else{
            $this->set_response(500,"somthing faild");
        }
        
    }


    private function set_response($status,$data) {
        $this->status = $status;
        $this->response = $data;
    }

    public function response(){
       
        echo  json_encode($this->response);
        http_response_code($this->status);                     
    }
}