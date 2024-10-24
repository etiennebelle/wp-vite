<?php

  class Theme
  {
    public function __construct()
    {
      add_action('wp_enqueue_scripts', [$this, 'enqueue_styles_scripts'], 20);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function enqueue_styles_scripts(): void
    {
      Vite::enqueue_module();

      $filename = Vite::asset('src/sass/main.scss');
      wp_enqueue_style('theme-style', $filename, [], null, 'screen');

      $filename = Vite::asset('src/js/main.js');
      wp_enqueue_script('theme-script', $filename, [], null, false);

      Vite::script_type_module('theme-script');
    }
  }

  new Theme();