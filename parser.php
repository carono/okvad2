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
foreach ($table->find('tr') as $tr) {
    $tr = pq($tr);
    if ($tr->find('td')->attr('colspan')) {
        continue;
    }
    $name = $tr->find('td')->eq(0)->text();
    $value = $tr->find('td')->eq(1)->text();

    if (mb_stripos(trim($name), 'раздел', null, 'utf-8') === 0) {
        preg_match('/раздел (\w+)/iu', $name, $match);
        $section = $match[1];
        $result[$section] = ['description' => '', 'items' => [], 'caption' => mb_ucfirst($value)];
    } elseif ($name && $value) {
        $result[$section]['items'][$name] = $codes[$name] = ['caption' => $value, 'links' => []];
        foreach ($tr->find('td')->eq(1)->find('a') as $a) {
            $result[$section]['items'][$name]['links'][] = $codes[$name]['links'][] = pq($a)->text();
        }
    } else {
        $result[$section]['description'] = trim(strip_tags(str_replace('</div>', "</div>\n", $tr->html())));
    }
}
file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'data.json', json_encode($result));
file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'sorted.json', json_encode($codes));


function mb_ucfirst($str, $lower = true)
{
    $enc = 'utf-8';
    $str = $lower ? mb_strtolower($str, $enc) : $str;
    return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc) . mb_substr($str, 1, mb_strlen($str, $enc), $enc);
}