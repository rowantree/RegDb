<?php
/**
 * Created by PhpStorm.
 * User: smorley
 * Date: 2016-09-29
 * Time: 13:51
 */
include_once "common.php";

$db = OpenPDO();
$sql = "SELECT CONCAT(eventCode, ' ', eventYear) event FROM (SELECT DISTINCT eventCode, eventYear FROM registration ORDER BY 1,2) x";

$stmt = $db->query($sql);
$stmt = RunSql($db, $sql);

$data = new stdClass();
$data->events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data->config = new stdClass();
$data->config->databaseName = $dbName;

$data->config->RegOptions = array(
    'Unknown',
    'Full Rites of Spring (Wed to Mon)',
    'Weekend Only (Fri to Mon)',
    'Village Builders (with Full Rites of Spring)'
);

$data->config->MealPlanOptions = array(
    0 => 'None',
    1 => 'Full',
    2 => 'Dinner Only',
);


echo json_encode($data);


