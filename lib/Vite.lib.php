<?php

  class Vite
  {

    /** @var bool */
    private static bool $isHot = false;

    /** @var string */
    private static string $server;

    /** @var string */
    private static string $buildPath = 'dist';

    /** @var array */
    private static array $manifest = [];

    /**
     * @param string|null $buildPath
     * @param bool $output
     * @return string|null
     * @throws Exception
     */
    public static function init(string $buildPath = null, bool $output = true): string|null
    {

      static::$isHot = file_exists(static::hotFilePath());

      if ($buildPath) {
        static::$buildPath = $buildPath;
      }

      if (static::$isHot) {
        static::$server = file_get_contents(static::hotFilePath());
        $client = static::$server . '/@vite/client';

        if ($output) {
          printf(/** @lang text */ '<script type="module" src="%s"></script>', $client);
        }

        return $client;
      }

      if (!file_exists($manifestPath = static::buildPath() . '/.vite/manifest.json')) {
        throw new Exception('No Vite Manifest exists.');
      }
      static::$manifest = json_decode(file_get_contents($manifestPath), true);

      return null;
    }

    /**
     * @param string|null $buildPath
     * @return void
     * @throws Exception
     */
    public static function enqueue_module(string $buildPath = null): void
    {
      if (!$client = Vite::init($buildPath, false)) {
        return;
      }

      wp_enqueue_script('vite-client', $client, [], null);
      Vite::script_type_module('vite-client');

    }

    /**
     * @param $asset
     * @return string
     * @throws Exception
     */
    public static function asset($asset): string
    {
      if (static::$isHot) {
        return static::$server . '/' . ltrim($asset, '/');
      }

      if (!array_key_exists($asset, static::$manifest)) {
        throw new Exception('Unknown Vite build asset: ' . $asset);
      }

      return implode('/', [get_stylesheet_directory_uri(), static::$buildPath, static::$manifest[$asset]['file']]);
    }

    /**
     * @return string
     */
    private static function hotFilePath(): string
    {
      return implode('/', [static::buildPath(), 'hot']);
    }

    /**
     * @return string
     */
    private static function buildPath(): string
    {
      return implode('/', [get_stylesheet_directory(), static::$buildPath]);
    }

    /**
     * @param $img
     * @return string|null
     * @throws Exception
     */
    public static function img($img): ?string
    {
      try {
        $asset = 'src/img/' . ltrim($img, '/');
        return static::asset($asset);
      } catch (Exception $e) {
        return $e->getMessage();
      }
    }

    /**
     * @param $scriptHandle bool|string
     * @return mixed
     */
    public static function script_type_module(bool|string $scriptHandle = false): string
    {
      add_filter('script_loader_tag', function ($tag, $handle, $src) use ($scriptHandle) {

        if ($scriptHandle !== $handle) {
          return $tag;
        }
        return '<script type="module" src="' . esc_url($src) . '" id="' . $handle . '-js"></script>';

      }, 10, 3);
      return false;
    }
  }