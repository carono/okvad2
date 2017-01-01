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

    public static function getCaptionByCode($code)
    {
        return self::getByCode($code) ? self::getByCode($code)['caption'] : '';
    }

    public static function getCodesInSection($section)
    {
        return isset(self::getSections()[$section]) ? self::getSections()[$section] : [];
    }

    public static function getSection($code)
    {
        return isset(self::getSections()[$code]) ? self::getSections()[$code] : false;
    }

    public static function getSections()
    {
        return self::getData();
    }

    public static function getSorted()
    {
        return self::$_sorted ? self::$_sorted : self::$_sorted = self::getContent('sorted.json');
    }

    public static function getData()
    {
        return self::$_data ? self::$_data : self::$_data = self::getContent('data.json');
    }

    private static function getContent($file)
    {
        return json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file), true);
    }
}