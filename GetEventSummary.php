<?php
/**
 * Created by PhpStorm.
 * User: Stephen
 * Date: 2016-09-30
 * Time: 15:01
 */
include_once "common.php";
$db = OpenPDO();

$where = ' WHERE 1=1 ';

if (IsSet($_REQUEST["event"]))
{
    $event = $_REQUEST["event"];
    TraceMsg("GetEventSummary for $event");
    $eventData = explode(" ",$event);
    $eventCode = $eventData[0];
    $eventYear = $eventData[1];
    $eventCode = addslashes($eventCode);
    $eventYear = addslashes($eventYear);
    $where .= " AND eventCode = '$eventCode' AND eventYear = '$eventYear'";
}
else
{
    TraceMsg("GetEventSummary for all events");
}

$data = new stdClass();

$stmt = RunSql($db, "SELECT count(*) cnt FROM registration $where" );
$data->regCount = $stmt->fetchcolumn(0);
//$data->regCount = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = RunSql($db, "SELECT count(*) cnt FROM person p INNER JOIN registration r on r.regID=p.regID $where" );
$data->personCount = $stmt->fetchcolumn(0);

$stmt = RunSql($db, "SELECT optionCode, count(*) cnt FROM registration $where GROUP BY optionCode");
$data->regOptionCount = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = RunSql($db, "SELECT mealPlan, count(*) cnt FROM person p INNER JOIN registration r on r.regID=p.regID $where GROUP BY mealPlan;" );
$data->mealPlanCount = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = RunSql($db, "SELECT CAST(age as UNSIGNED) age, cnt FROM (SELECT age, count(*) cnt FROM person p INNER JOIN registration r on r.regID=p.regID $where  GROUP BY age HAVING age > 0) x ORDER BY age;" );
$data->ageCount = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);


