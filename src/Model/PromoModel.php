<?php

namespace Model;

class PromoModel
{
  public function __construct() {
  }
  
  public function display() {
    echo 'DISPLAY OF PromoModel';
    var_dump($this);
    echo '<hr>';
  }
}
