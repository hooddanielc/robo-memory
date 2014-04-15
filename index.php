<?php

  /*
  * Created by Daniel Hood
  * * * */

  include 'havenofcode.php';
  include 'modules/page_base.php';

  // testing page
  class MyPage extends Page {
    function __construct() {
      parent::__construct();
      $this->page_base = new PageBase();
    }

    public static $js = [
      'pages/home-page/jquery.quickflip.min.js',
      'pages/home-page/home-page.js'
    ];

    public static $css = [
      'pages/home-page/home-page.css'
    ];

    public static $text = [
      'robo-memory-grid' => 'pages/home-page/robo-memory-grid.mustache',
      'robo-memory-card' => 'pages/home-page/robo-memory-card.mustache'
    ];
  }

  $page = new MyPage();
  $page->render();
?>