<?php

namespace Core;

class Core
{
  public $url;
  public $array;
  public $class;

  public function run()
  {
    require_once './src/routes.php';
    if ($this->array = Router::get($this->url())) {
      echo 'STATIC [ OK ]' . PHP_EOL;
      $this->static();
    } else {
      echo 'DYNAMIC [ OK ]' . PHP_EOL;
      $this->dynamic();
    }
  } 

  protected function static()
  {
    $this->getClass();
    $this->redirect();
  }

  protected function dynamic()
  {
    $this->url = array_values(array_filter((explode('/', $_SERVER['REQUEST_URI'])), function ($v) {
      return $v !== 'PiePHP' && $v !== '';
    }, ARRAY_FILTER_USE_BOTH));

    //Lorsque le controller ou l’action n’est pas présent, il faudra les remplacer par « app » et « index » respectivement.
    if ($this->verifyEmptyInput()) {
      echo 'INPUT MISSING , Calling App and indexAction || ' . count($_GET) . ' || ' . count($this->url) . PHP_EOL;
    }

    $this->getClass();

    //Si le controller ou l’action fournie n’existe pas, il faut afficher le message : « 404 »
    echo 'Class verification: ' . $this->class . PHP_EOL;
    if (
      !file_exists( PATH_ORIGIN . '/src/' .  url($this->class) . '.php')
      || !method_exists($this->class, $this->array['action'] . 'Action')
    ) {
      echo "404 : File doesn't exist || " .  $this->class . '.php' . PHP_EOL;
      return;
    }

    $this->redirect();
  }

  protected function url()
  {
    $nbr = strlen(array_reverse(array_filter(explode('/', PATH_ORIGIN)), '')[0]) + 1;
    return substr($_SERVER['REQUEST_URI'], $nbr);
  }

  protected function verifyEmptyInput()
  {
    switch (true) {
      case count($_GET) == 1:
        $this->array['controller'] = array_values($_GET)[0];
        $this->array['action'] = 'index';
        break;
      case count($_GET) == 2:
        $this->array['controller'] = array_values($_GET)[0];
        $this->array['action'] = array_values($_GET)[1];
        break;
      case count($this->url) == 1:
        $this->array['controller'] = $this->url[0];
        $this->array['action'] = 'index';
        break;
      case count($this->url) == 2:
        $this->array['controller'] = $this->url[0];
        $this->array['action'] = $this->url[1];
        break;
      default:
        $this->array = ['App', 'index'];
        return true;
        break;
    }
  }

  protected function getClass()
  {
    $this->class = 'Controller\\' . ucfirst($this->array['controller']) . 'Controller';
  }

  protected function redirect()
  {
    if ( isset(Router::$id) ) {
      $cl = new $this->class ();
      $cl->{$this->array['action'] . 'Action'}(Router::$id);
    } else {
      $cl = new $this->class ();
      $cl->{$this->array['action'] . 'Action'}();
    }
  }
}