<?php
namespace App;

class Shortcode {

    public static $shortcodes = [];

    public static function addShortcode($name, $function) {
        return self::$shortcodes[] = [
             'name' => $name,
             'function' => $function
        ];
    }

    public static function registerShortcodes()
    {
        foreach (self::$shortcodes as $shortcode) {
            add_shortcode($shortcode['name'], $shortcode['function']);
        }
    }
}