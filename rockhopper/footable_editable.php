<?php

/**
 * ****************************************************************************
 *
 * checkavailable.php 
 * 
 * Used by footable to add or edit or delete a line.
 * return data $str's structure is  -->
 * for (fooCommand == Add):
 * 			->fooCommand
 * 			->fooTable
 * 			-> ....... (row data)
 * 			->lastInsertId
 * 			->serverResponse
 * for (fooCommand == Update):
 * 			->fooCommand
 * 			->fooTable
 * 			->curRowId
 * 			-> ....... (row data)
 * 			->serverResponse
 * for (fooCommand == Delete):
 * 			->fooCommand
 * 			->fooTable
 * 			->curRowId
 * 			->serverResponse
 *                               
 */

// set the server info here
require_once 'connect.php';

//Right.php://input is a read-only stream that allows you to read raw data from the request body.
$inputData = json_decode(file_get_contents("php://input")); 

$command = $inputData->fooCommand; 
$dataTable = $inputData->fooTable;


if ($inputData->fooCommand == "Add") {
	$newKeyString = "(";
	$newValueString = "(";
	$newKeyMap = array();
	
	foreach ($inputData as $key => $value) {
		if ($key !== "fooCommand" && $key !== "fooTable" && $key !== "curRowId") {
			$newKeyString .= $key . ", ";
			$newValueString .= ":" . $key . ", ";
			$newKeyMap[":" . $key] = $value;
		}
	}
	
	// delete the last ", " of the string
	$newKeyString = substr($newKeyString, 0, -2) . ')';
	$newValueString = substr($newValueString, 0, -2) . ')';
	
	$sql = 'INSERT INTO ' . $dataTable . $newKeyString . ' VALUES ' . $newValueString; 
	$stmt = $dbh->prepare($sql);
	if ($stmt->execute($newKeyMap)){
		$inputData->lastInsertId = $dbh->lastInsertId();
		$inputData->serverResponse = "Success";
	}
	else $inputData->serverResponse = "Error";
}

if ($inputData->fooCommand == "Update") {
	$dataId = $inputData->curRowId;
	$updateKeyString = "";
	$updateKeyMap = array();
	
	foreach ($inputData as $key => $value) {
		if ($key !== "fooCommand" && $key !== "fooTable" && $key !== "curRowId") {
			$updateKeyString .= $key . "=:" . $key . ", ";
			$updateKeyMap[":" . $key] = $value;
		}
	}
	
	$updateKeyString = substr($updateKeyString, 0, -2);
	$updateKeyMap[':id'] = $dataId;
	
	
	$sql = 'UPDATE ' . $dataTable . ' SET ' . $updateKeyString . ' WHERE id = :id';
    $stmt = $dbh->prepare($sql);
	if ($stmt->execute($updateKeyMap))
		$inputData->serverResponse = "Success";
	else $inputData->serverResponse = "Error";
}

if ($inputData->fooCommand == "Delete") {
	$dataId = $inputData->curRowId;
	$sql = 'DELETE FROM ' . $dataTable . ' WHERE id = :id';
    $stmt = $dbh->prepare($sql);
    // $stmt->execute() returns true on success
    if ($stmt->execute(array(':id' => $dataId))) 
		$inputData->serverResponse = "Success";
	else $inputData->serverResponse = "Error";
}

//echo var_dump($inputData);
//echo $inputdata->command;

$str = json_encode ($inputData);
echo $str;
