<?php
include '../../DBconnection/auth.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// Including database connections start here
require_once '../../DBconnection/database_connections.php';
// Including database connections end here
//json data encoding passing start here
$data = json_decode(file_get_contents("php://input"));
//json data encoding passing end here

$id = $data->venue_id;
$object = array();

$sql = "Select * FROM event_venues WHERE venue_id='$id' ORDER BY venue_id DESC";
$result = mysqli_query($con, $sql);
while ($row = mysqli_fetch_array($result)) {
    $object[] = $row;
}
echo json_encode($object);
