<?php
namespace carono\okvad;

class Okvad2
{
    private static $_data;
    private static $_sorted;

    public static function getByCode($code)
    {
        return isset(self::getSorted()[$code]) ? self::getSorted()[$code] : [];
    }

    public static function getCodesInSection($section)
    {
        return isset(self::getSections()[$section]) ? self::getSections()[$section] : [];
    }

    public static function getSections()
    {
        return self::getData();
    }

    public static function getSorted()
    {
        return self::$_sorted ? self::$_sorted : self::$_sorted = json_decode(file_get_contents('sorted.json'), true);
    }

    public static function getData()
    {
        return self::$_data ? self::$_data : self::$_data = json_decode(file_get_contents('data.json'), true);
    }

}