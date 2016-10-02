<?php
include_once "common.php";

/*
TraceMsg("Post Dump");
foreach($_POST as $key => $value)
{
    TraceMsg("$key=$value");
}
*/

$where = '';

if ( $_SERVER["REQUEST_METHOD"] == "POST")
{
    $postData = file_get_contents("php://input");
    TraceMsg("$postData");
    $request = json_decode($postData);

    // $where = " WHERE regID=1001";
    // $where = " WHERE lastName like '%orle%'";
    $where = " WHERE 1=1 ";

    if (property_exists($request, "firstName"))
    {
        $firstName = addslashes($request->firstName);
        TraceMsg("FirstName=$firstName");
        $where .= " AND firstName like '%$firstName%'";
    }
    if (property_exists($request, "lastName"))
    {
        $lastName = addslashes($request->lastName);
        TraceMsg("LastName=$lastName");
        $where .= " AND lastName like '%$lastName%'";
    }
    if (property_exists($request, "event"))
    {
        TraceMsg("event=$request->event");
        $eventData = explode(" ",$request->event);
        $eventCode = $eventData[0];
        $eventYear = $eventData[1];
        $eventCode = addslashes($eventCode);
        $eventYear = addslashes($eventYear);
        $where .= " AND eventCode = '$eventCode' AND eventYear = '$eventYear'";
    }
}

$data = new stdClass();
$data->SQL = new stdClass();

$db = OpenPDO();

$regSql = "SELECT * FROM registration $where ORDER BY firstName, lastName";


TraceMsg($regSql);
$stmt = RunSql($db, $regSql);

$data->SQL->regSql = $regSql;
$data->registration = $stmt->fetchAll(PDO::FETCH_ASSOC);


$personSql = "SELECT * FROM person";
if ( strlen($where) > 0 ) $personSql .= " WHERE regID IN (SELECT regID FROM registration $where)";
$personSql .= " ORDER BY regID, idx";

TraceMsg($personSql);
$stmt = RunSql($db, $personSql);
$data->SQL->personSql = $personSql;

$people = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach( $people as $person )
{
    //echo "Processing RegId ", $person['regID'], " PersonId ", $person['personID'],  "<br>";
    foreach( $data->registration as &$reg )
    {
        if ( $reg['regID'] == $person['regID'] )
        {
            //echo " Found maching registration<br>";
            if ( !key_exists('people',$reg)) $reg['PEOPLE'] = array();
            array_push($reg['PEOPLE'], $person);
            break;
        }
    }

}

/*
*/

$paymentSql = "SELECT * FROM payment";
if ( strlen($where) > 0 ) $paymentSql .= " WHERE regID IN (SELECT regID FROM registration $where)";
$paymentSql .= " ORDER BY regID";

TraceMsg($paymentSql);
$stmt = RunSql($db, $paymentSql);
$data->SQL->paymentSql = $paymentSql;
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach( $payments as $payment )
{
    foreach( $data->registration as &$reg )
    {
        if ( $reg['regID'] == $payment['regID'] )
        {
            //echo " Found maching registration<br>";
            if ( !key_exists('payment',$reg)) $reg['PAYMENT'] = array();
            array_push($reg['PAYMENT'], $payment);
            break;
        }
    }

}

echo json_encode($data);
