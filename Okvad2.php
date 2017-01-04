<?php
namespace carono\okvad;

class Okvad2
{
    private static $_data;
    private static $_sorted;

    /**
     * @param $code
     *
     * @return array
     */
    public static function getByCode($code)
    {
        return isset(self::getSorted()[$code]) ? self::getSorted()[$code] : [];
    }

    /**
     * @param $code
     *
     * @return mixed|string
     */
    public static function getCaptionByCode($code)
    {
        return self::getByCode($code) ? self::getByCode($code)['caption'] : '';
    }

    /**
     * @param $code
     *
     * @return mixed|string
     */
    public static function getDescriptionByCode($code)
    {
        return self::getByCode($code) ? self::getByCode($code)['description'] : '';
    }

    /**
     * @param $section
     *
     * @return array
     */
    public static function getCodesInSection($section)
    {
        return isset(self::getSections()[$section]) ? self::getSections()[$section]['items'] : [];
    }

    /**
     * @param $code
     *
     * @return bool
     */
    public static function getSection($code)
    {
        return isset(self::getSections()[$code]) ? self::getSections()[$code] : false;
    }

    /**
     * @return mixed
     */
    public static function getSections()
    {
        return self::getData();
    }

    /**
     * @return mixed
     */
    public static function getSorted()
    {
        return self::$_sorted ? self::$_sorted : self::$_sorted = self::getContent('sorted.json');
    }

    /**
     * @return mixed
     */
    public static function getData()
    {
        return self::$_data ? self::$_data : self::$_data = self::getContent('data.json');
    }

    /**
     * @param $file
     *
     * @return mixed
     */
    private static function getContent($file)
    {
        return json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . $file), true);
    }
}