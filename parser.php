<?php
require 'vendor/autoload.php';
if (!class_exists('\phpQuery')){
    echo 'Class phpQuery not found, install first: composer require electrolinux/phpquery';
    exit;
}

$content = file_get_contents('content.html');

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
    if (stripos($name, 'РАЗДЕЛ') !== false) {
        preg_match('/раздел (\w+)/iu', $name, $match);
        $section = $match[1];
        $result[$section] = ['description' => $value, 'items' => []];
    } elseif ($name) {
        $result[$section]['items'][$name] = $codes[$name] = ['description' => $value, 'links' => []];
        foreach ($tr->find('td')->eq(1)->find('a') as $a) {
            $result[$section]['items'][$name]['links'][] = $codes[$name]['links'][] = pq($a)->text();
        }
    }
}
file_put_contents('data.json', json_encode($result));
file_put_contents('sorted.json', json_encode($codes));
