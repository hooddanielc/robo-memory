<?php
  class PageBase extends Module {
    public static $css = [
      'http://fonts.googleapis.com/css?family=Muli',
      'main/third-party/bootstrap/css/bootstrap.min.css',
      'main/third-party/bootstrap/css/bootstrap-theme.min.css',
      'modules/page-base/page-base.css'
    ];

    public static $js = [
      'main/third-party/jquery.min.js',
      'main/third-party/underscore.min.js',
      'main/third-party/backbone.min.js',
      'main/third-party/mustache.min.js',
      'main/third-party/bootstrap/js/bootstrap.min.js',
      'modules/page-base/page-base.js'
    ];

    public static $text = [
      'page-base' => 'modules/page-base/page-base.mustache',
      'page-user-nav' => 'modules/page-base/page-user-nav.mustache'
    ];
  }
?>