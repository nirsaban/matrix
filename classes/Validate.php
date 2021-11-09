<?php



class Validate {


    private $errors = [];

    public function validateApi($action,$fields_to_validate = null) {

        if($action == "create"){
            return $this->validate_create();
        }else if ($action == "delete"){
            return $this->validate_id();
        }else if($action == 'update'){
            return $this->validate_update($fields_to_validate);
        }else if($action == "read"){
            return $this->validate_read();
        }
    }

    private function validate_update($fields_to_validate){
        
        $is_valid = true;
        foreach($fields_to_validate as $field){
            $function = "validate_".strtolower($field);
            if(!$this->$function()) $is_valid = false;
        }

        if(!$is_valid) $this->validatation_faild();

        return $is_valid;
    }
    private function validate_create(){

        if($this->validate_fullname() && $this->validate_phone_nambers()
         && $this->validate_id() && $this->validate_sex()
         && $this->validate_dob()){
             return true;
         }else{
            $this->validatation_faild();
         }
         
         return false;
    }

      function validate_read(){

        if($this->validate_id()){
             return true;
         }else{
            $this->validatation_faild();
         }

         return false;
    }
    
     function validate_fullname () {

        if($this->fullname == ''){
            $this->errors['fullname'][] = "the full name must be grather than 3 chars"; 
            return false;
        }
        return true;
    }
    function validate_phone_nambers(){

        $filtered_phone_number = filter_var($this->phone_numbers, FILTER_SANITIZE_NUMBER_INT);
        $phone_to_check = str_replace("-", "", $filtered_phone_number);
         
        if (strlen($phone_to_check) > 10 || strlen($phone_to_check) < 7) {
            $this->errors['phone_numbers'][] = "the phone number  is not valid "; 
            return false;
        }
        return true;

    }
     function validate_id(){
        if(!is_numeric($this->ID) || strlen($this->ID) < 9 || strlen($this->ID) > 10){
            $this->errors['id'][] = "the ID number is not valid"; 
            return false;
        }
        return true;
    }
    private function validate_dob(){

        $date_arr  = explode('-', $this->DOB);
        // DD-MM-YYYY
        if (!checkdate($date_arr[1], $date_arr[0], $date_arr[2])) {
            $this->errors['dob'][] = "the date is not valid"; 
            return false;
        }
            return true;
    }

    private function validate_sex(){

        if($this->sex != 'male' && $this->sex != 'female'){
            $this->errors['sex'][] = "the sex  field must be male or female 😜";
            return false; 
        }
        return true;
    }

    public function validatation_faild(){
        echo json_encode($this->errors);
    }


}