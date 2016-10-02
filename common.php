<?php
include_once "../common/OpenDb.php";
$dbName = "rowan_RitesTest";

function TraceMsg($msg)
{
    error_log( date('[ymd-His]') . ':' .$msg . "\n", 3, "trace.log");
}

function RunSql($db, $sql)
{
    $stmt = $db->query($sql);
    if (!$stmt)
    {
        TraceMsg($sql);
        $msg = print_r($db->errorInfo(), true);
        TraceMsg($msg);
        echo "$sql<br>";
        echo str_replace("\n", "<br>", $msg);
        die('Invalid query');
    }
    return $stmt;
}
?>
