<?php

  define('STATIC_DIR', 'robo-memory/static/');

  class Module {
    // override to put mustache static text onto page
    public static $text = [];
    // override to put css this module depends on
    public static $css = [];
    // override to put javascript this module depends on
    public static $js = [];

    private function genCls(&$taboo, &$classes, &$the_class) {
      $props = get_object_vars($the_class);
      foreach($props as &$cls) {
        if(is_subclass_of($cls, 'Module')) {
          $this->genCls($taboo, $classes, $cls);
          if(!in_array(get_class($cls), $taboo)) {
            $taboo[] = get_class($cls);
            $classes[] = $cls;
          }
        }
      }
    }

    protected function getDependencies() {
      $classes = [];
      $taboo = [];
      $this->genCls($taboo, $classes, $this);
      return $classes;
    }

    protected function getCss($classes) {
      $taboo = [];
      foreach($classes as &$cls) {
        foreach($cls::$css as &$css_file) {
          if(strpos($css_file, 'http://') !== false) {
            $taboo[] = '<link rel="stylesheet" href="'.$css_file.'" />';
          } else {
            $taboo[] = '<link rel="stylesheet" href="/'.STATIC_DIR.$css_file.'" />';
          }
        }
      }
      foreach($this::$css as &$css_file) {
        if(!in_array($css_file, $taboo)) {
          if(!in_array($css_file, $taboo)) {
            if(strpos($css_file, 'http://') !== false) {
              $taboo[] = '<link rel="stylesheet" href="'.$css_file.'" />';
            } else {
              $taboo[] = '<link rel="stylesheet" href="/'.STATIC_DIR.$css_file.'" />';
            }
          }
        }
      }
      return join("\n", $taboo);
    }

    protected function getJs($classes) {
      $taboo = [];
      foreach($classes as &$cls) {
        foreach($cls::$js as &$js_file) {
          if(!in_array($js_file, $taboo)) {
            if(strpos($js_file, 'http://') !== false) {
              $taboo[] = '<script type="text/javascript" src="'.$js_file.'"></script>';
            } else {
              $taboo[] = '<script type="text/javascript" src="/'.STATIC_DIR.$js_file.'"></script>';
            }
          }
        }
      }
      foreach($this::$js as &$js_file) {
        if(!in_array($js_file, $taboo)) {
          if(strpos($js_file, 'http://') !== false) {
            $taboo[] = '<script type="text/javascript" src="'.$js_file.'"></script>';
          } else {
            $taboo[] = '<script type="text/javascript" src="/'.STATIC_DIR.$js_file.'"></script>';
          }
        }
      }
      return join("\n", $taboo);
    }

    protected function getMustache($classes) {
      $taboo = [];
      foreach($classes as &$cls) {
        foreach($cls::$text as $k => $v) {
          if(!in_array($k, $taboo)) {
            $taboo[$k] = file_get_contents('static/'.$v, $use_include_path = true);
          }
        }
      }
      foreach($this::$text as $k => $v) {
        if(!in_array($k, $taboo)) {
          $taboo[$k] = file_get_contents('static/'.$v, $use_include_path = true);
        }
      }
      return $taboo;
    }
  }

  class Page extends Module {
    protected $data = [];
    protected function isTokenValid($token) {
      ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
      $result = file_get_contents('https://api.github.com/user?access_token='.$token);
      $json = json_decode($result);
      return $json;
    }

    function __construct() {
      $this->data = [];
      session_start();
      // check to see if token still valid
      if(isset($_SESSION['access_token'])) {
        $user_data = $this->isTokenValid($_SESSION['access_token']);
        if($user_data) {
          $this->addData('user', $user_data);
        }
      } else {
        $this->addData('user', []);
      }

      $this->addData('get', $_GET);
      $this->addData('post', $_POST);
    }

    /*
    * override to put custom title in page
    * * * */
    public function getTitle() {
      return 'Robo Memory';
    }

    /*
    * override to put custom page description
    * * * */
    public function getDescription() {
      return 'Welcome to Robo Memory';
    }

    /*
    * used to add data to javascript
    * browser front end
    * * * */
    protected function addData($key, $arr) {
      $this->data[$key] = $arr;
    }

    public function render() {
      // topologically sorts dependencies
      $classes = $this->getDependencies();
      $css = $this->getCss($classes);
      $js = $this->getJs($classes);
      $mustache = $this->getMustache($classes);
      $data = $this->data;
      ?>
        <!doctype html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <title><?php echo $this->getTitle(); ?></title>
            <meta name="description" content="<?php echo $this->getDescription(); ?>">
            <meta name="author" content="Daniel Hood">
            <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
            <?php echo $css; ?>
          </head>
          <body>
            <script>
              var app = {
                modules: {}
              };
              app.mustache = <?php echo json_encode($mustache); ?>;
              app.data = <?php echo json_encode($data); ?>;
            </script>
            <?php echo $js; ?>
          </body>
        </html>
      <?php
    }
  }
?>