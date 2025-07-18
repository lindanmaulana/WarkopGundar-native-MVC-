<?php

namespace App\Core;

class Controller
{
  public function view($viewPath, $data = [], $layout = "/layout")
  {
    require_once __DIR__ . '/helpers.php';
    extract($data);
    ob_start();
    
    require_once __DIR__ . "/../views{$viewPath}.php";
    $content = ob_get_clean();

    extract($data);
    require_once __DIR__ . "/../views/layouts{$layout}.php";
  }
}
