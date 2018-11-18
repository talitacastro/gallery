<?php

//Se tiver algum include faltando, ele valida aqui.
function classAutoLoader($class){

    $class = strtolower($class);
    $the_path = "includes/{$class}.php";
   
  if(is_file($the_path) && !class_exists($class)){
    include $the_path;
  }

}

function redirect($location){
    //funcao pronta do php que faz redirect
    header("Location: {$location}");

}


spl_autoload_register('classAutoLoader');


?>