<?php
require 'vendor/autoload.php';

if (!class_exists('\phpQuery')) {
    echo 'Class phpQuery not found, install first: composer require electrolinux/phpquery';
    exit;
}

$content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'content.html');

$query = phpQuery::newDocument($content);

$table = $query->find(
    "table[style*='border-collapse: collapse; table-layout: fixed; word-wrap: break-word; width: 480pt; text-align: left; margin-left: 0pt; margin-right: auto;']"
)->eq(1);
$section = null;
$result = [];
$codes = [];
$trIsSection = false;
$okvadCode = null;
$x = 0;
foreach ($table->find('tr') as $tr) {
    $tr = pq($tr);
    if ($tr->find('td')->attr('colspan')) {
        continue;
    }
    $name = $tr->find('td')->eq(0)->text();
    $value = $tr->find('td')->eq(1)->html();
    if (mb_stripos(trim($name), 'раздел', null, 'utf-8') === 0) {
        preg_match('/раздел (\w+)/iu', $name, $match);
        $section = $match[1];
        if (($idx = mb_stripos($value, 'Этот раздел')) !== false) {
            $description = strip(mb_substr($value, $idx, null, 'utf-8'));
            $caption = strip(mb_substr($value, 0, $idx, 'utf-8'));
        } else {
            $description = '';
            $caption = strip($value);
        }
        $result[$section] = ['description' => $description, 'items' => [], 'caption' => mb_ucfirst($caption)];
        $trIsSection = true;
    } elseif ($name && $value) {
        $okvadCode = $name;
        $trIsSection = false;
        if (($idx = mb_stripos($value, 'Эта группировка')) !== false) {
            $description = strip(mb_substr($value, $idx, null, 'utf-8'));
            $caption = strip(mb_substr($value, 0, $idx, 'utf-8'));
        } else {
            $description = '';
            $caption = strip($value);
        }
        $result[$section]['items'][$name] = $codes[$name] = [
            'caption'     => $caption,
            'notes'       => [],
            'description' => $description
        ];
        $codes[$name]['section'] = $section;
        foreach ($tr->find('td')->eq(1)->find('a') as $a) {
            $result[$section]['items'][$name]['notes'][] = $codes[$name]['notes'][] = pq($a)->text();
        }
    } else {
        $description = strip($tr->html());
        if ($trIsSection) {
            $result[$section]['description'] = $description;
        } else {
            $result[$section]['items'][$okvadCode]['description'] = $codes[$okvadCode]['description'] = $description;
        }
    }

    file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data.json', json_encode($result));
    file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'sorted.json', json_encode($codes));
}

function strip($html)
{
    return trim(strip_tags($html));
}

function mb_ucfirst($str, $lower = true)
{
    $enc = 'utf-8';
    $str = $lower ? mb_strtolower($str, $enc) : $str;
    return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc) . mb_substr($str, 1, mb_strlen($str, $enc), $enc);
}