<?php 

class Db_object{

    public $errors = array();
    public $upload_error_array = array(

        UPLOAD_ERR_OK             => "There is no error",
        UPLOAD_ERR_INI_SIZE       => "The uploaded file exceeds the upload_max_filesize directive in php.ini",  
        UPLOAD_ERR_FORM_SIZE      => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",  
        UPLOAD_ERR_PARTIAL        => "The uploaded file was only partially uploaded.",
        UPLOAD_ERR_NO_FILE        => "No file was uploaded.",
        UPLOAD_ERR_NO_TMP_DIR     => "Missing a temporary folder. Introduced in PHP 5.0.3.",
        UPLOAD_ERR_CANT_WRITE     => "Failed to write file to disk. Introduced in PHP 5.1.0.",
        UPLOAD_ERR_EXTENSION      => "A PHP extension stopped the file upload"
      
    );

    // This is passing $_FILES['uploadesd_file'] as an argument
    public function set_file($file){

        if(empty($file) || !$file || !is_array($file)){
            $this->errors[] = "There was no file uploaded here";
            return false;

        }elseif($file['error'] != 0){

            $this->errors[] = $this->upload_errors_array[$file['error']];
            return false;

        }else{

            $this->filename = basename($file['name']);
            $this->tmp_path = $file['tmp_name'];
            $this->type     = $file['type'];
            $this->size     = $file['size'];

        }        
    }

        
    public static function find_all(){
        return static::find_by_query("SELECT * FROM " . static::$db_table . "");
   }

    public static function find_by_id($id){
        global $database;
        $the_result_array = static::find_by_query("SELECT * FROM " . static::$db_table . " WHERE id = $id");
       
        return !empty($the_result_array) ? array_shift($the_result_array) : false;
        
    }

    public static function find_by_query($sql){
        global $database;
        $result_set = $database->query($sql);
            
        $the_object_array = array();

        //atribuindo os resultados da query num objeto
        while($row = mysqli_fetch_array($result_set)){
            $the_object_array[]= static::instantiation($row);
        }
        
        return $the_object_array;
    }

    public static function instantiation($the_record){

        $calling_class = get_called_class();       
        $the_object = new $calling_class;
        //$the_object->id = $found_user['id'];
        //$the_object->username = $found_user['username'];
        //$the_object->password = $found_user['password'];
        //$the_object->first_name = $found_user['first_name'];
        //$the_object->last_name = $found_user['last_name'];

        foreach($the_record as $the_attribute => $value){
            if($the_object->has_the_attribute($the_attribute)){
                $the_object->$the_attribute = $value;
            }
        }

        return $the_object;

    }

    //"private" pq nao precisa ser usado fora da classe
    private function has_the_attribute($the_attribute){
        //get_object_vars e metodo do PHP
        $object_properties = get_object_vars($this);
        return array_key_exists($the_attribute, $object_properties);

    }

    protected function properties(){
  
        $properties = array();
        foreach(static::$db_table_fields as $db_field){

            if(property_exists($this, $db_field)){
                $properties[$db_field] = $this->$db_field;
            }
        }
        return $properties;
    }
    

    protected function clean_properties(){
        global $database;
        $clean_properties = array();

        foreach($this->properties() as $key =>$value){
            $clean_properties[$key] = $database->escape_string($value);
        }        
        return $clean_properties;
    }

    public function save(){
        return isset($this->id) ? $this->update() : $this->create();

    }

    public function create(){
        global $database;
        $properties = $this->clean_properties();

        $sql = "INSERT INTO " .static::$db_table . "(" . implode(",", array_keys($properties))   . ")";
        $sql .= "VALUES ('". implode("','", array_values($properties))   ."')";
       
        if($database->query($sql)){
            $this->id = $database->the_insert_id();
            return true;
        }else{
            return false;
        }
    }//End of Create method


    public function update(){
        global $database;
        $properties = $this->clean_properties();
        
        //esse array so serve pra montar os pares e usar na query abaixo, pra ficaar mais facil
        // o valor "username=talita" ja eh um elemento por exemplo.
        $properties_pairs = array();
        foreach ($properties as $key => $value){
            $properties_pairs[] = "{$key}='{$value}'"; 

        }

        $sql = "UPDATE " .static::$db_table . " SET ";
        $sql .= implode(", ", $properties_pairs);
        $sql .= " WHERE id= " . $database->escape_string($this->id);        
        
        $database->query($sql);
        return (mysqli_affected_rows($database->connection) == 1) ? true : false;

    }//End of Update method

    public function delete(){
        global $database;

        $sql = "DELETE FROM " .static::$db_table . " WHERE id= '" . $database->escape_string($this->id) . "'";

        $database->query($sql);
        return (mysqli_affected_rows($database->connection) == 1) ? true : false;


    }






}





?>