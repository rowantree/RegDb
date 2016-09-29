<?php
/**
 * Created by PhpStorm.
 * User: smorley
 * Date: 2016-09-29
 * Time: 13:51
 */
include_once "../common/OpenDb.php";
$db = OpenPDO();
$sql = "SELECT CONCAT(eventCode, ' ', eventYear) event FROM (SELECT DISTINCT eventCode, eventYear FROM registration ORDER BY 1,2) x";

$stmt = $db->query($sql);
if (!$stmt) {
    TraceMsg($sql);
    $msg = print_r($db->errorInfo(), true);
    TraceMsg($msg);
    echo "$sql<br>";
    echo str_replace("\n", "<br>", $msg);
    die('Invalid query');
}

$data = new stdClass();
$data->events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);

function TraceMsg($msg)
{
    error_log( date('[ymd-His]') . ':' .$msg . "\n", 3, "trace.log");
}
