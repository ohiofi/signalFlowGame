<?php require_once('Connections/ohiofi.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "0,1,2";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}






/*

----------------------------------------------HERE IS THE SURVEY STUFF----------------------------------------------

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form3")) {
  $insertSQL = sprintf("INSERT INTO survey (entryNumber, userName, gameNumber, questionNumber, q1, q2, q3, q4, q5) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['entry'], "int"),
                       GetSQLValueString($_SESSION['MM_Username'], "text"),
                       GetSQLValueString($_POST['gamenum'], "int"),
                       GetSQLValueString($_POST['ques'], "int"),
                       GetSQLValueString($_POST['RadioGroup1'], "text"),
                       GetSQLValueString($_POST['RadioGroup2'], "text"),
                       GetSQLValueString($_POST['RadioGroup3'], "text"),
                       GetSQLValueString($_POST['RadioGroup4'], "text"),
                       GetSQLValueString($_POST['RadioGroup5'], "text"));

  mysql_select_db($database_ohiofi, $ohiofi);
  $Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());

  $insertGoTo = "mainmenu.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}*/


$editFormAction = $_SERVER['PHP_SELF'];

if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_rsGame3 = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsGame3 = $_SESSION['MM_Username'];
}
mysql_select_db($database_ohiofi, $ohiofi);
$query_rsGame3=sprintf("SELECT game2responses.entryNumber, game2responses.questionNumber FROM game2responses WHERE userName=%s AND game2responses.tally=1 ORDER BY game2responses.questionNumber DESC",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsGame3 = mysql_query($query_rsGame3, $ohiofi) or die(mysql_error());
$row_rsGame3 = mysql_fetch_assoc($rsGame3);
$totalRows_rsGame3 = mysql_num_rows($rsGame3);




mysql_select_db($database_ohiofi, $ohiofi);
$query_rsScore3=sprintf("SELECT game2responses.entryNumber, game2responses.questionNumber FROM game2responses WHERE userName=%s",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsScore3 = mysql_query($query_rsScore3, $ohiofi) or die(mysql_error());
$row_rsScore3 = mysql_fetch_assoc($rsScore3);
$totalRows_rsScore3 = mysql_num_rows($rsScore3);


/* ------------------------------------- rsGame3_check is the fail safe -----------------------------------------*/

mysql_select_db($database_ohiofi, $ohiofi);
$query_rsGame3_check=sprintf("SELECT game2responses.entryNumber, game2responses.questionNumber FROM game2responses WHERE userName=%s AND game2responses.questionNumber=%s",
  GetSQLValueString($colname_rsGame3, "text"), GetSQLValueString($_POST['questionNumber'], "int")); 
$rsGame3_check = mysql_query($query_rsGame3_check, $ohiofi) or die(mysql_error());
$row_rsGame3_check = mysql_fetch_assoc($rsGame3_check);
$totalRows_rsGame3_check = mysql_num_rows($rsGame3_check);


mysql_select_db($database_ohiofi, $ohiofi);
$query_rsUser=sprintf("SELECT users.userID FROM users WHERE userName=%s ",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsUser = mysql_query($query_rsUser, $ohiofi) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
$totalRows_rsUser = mysql_num_rows($rsUser);





/*
mysql_select_db($database_ohiofi, $ohiofi);
$query_rsSurvey=sprintf("SELECT survey.questionNumber FROM survey WHERE survey.userName=%s AND survey.gameNumber=3 ORDER BY survey.questionNumber DESC",
  GetSQLValueString($colname_rsGame3, "text")); 
$rsSurvey = mysql_query($query_rsSurvey, $ohiofi) or die(mysql_error());
$row_rsSurvey = mysql_fetch_assoc($rsSurvey);
$totalRows_rsSurvey = mysql_num_rows($rsSurvey);


*/


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	if ($totalRows_rsGame3_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game2=%s, game2total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['points'], "int"),
							   GetSQLValueString($_POST['game2total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	if ($totalRows_rsGame3_check == 0) {
		  $updateSQL = sprintf("UPDATE users SET game2total=%s WHERE userID=%s",
							   GetSQLValueString($_POST['game2total'], "int"),
							   GetSQLValueString($_POST['userNumber'], "int"));
		
		  mysql_select_db($database_ohiofi, $ohiofi);
		  $Result1 = mysql_query($updateSQL, $ohiofi) or die(mysql_error());
	}
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	if ($totalRows_rsGame3_check == 0) {
		///if ($row_rsScore3['questionNumber'] == $totalRows_rsScore3){
			
					   
	  		$insertSQL = sprintf("INSERT INTO game2responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['entryNumber'], "int"),
						   GetSQLValueString($_SESSION['MM_Username'], "text"),
						   GetSQLValueString($_POST['questionNumber'], "int"),
						   GetSQLValueString($_POST['question1'], "text"),
						   GetSQLValueString($_POST['response1'], "text"),
						   GetSQLValueString($_POST['tally'], "int"));
					   
	  		mysql_select_db($database_ohiofi, $ohiofi);
	 		$Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());
		///}
	}
	header('Location: ' . $_SERVER['PHP_SELF']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	if ($totalRows_rsGame3_check == 0) {
		///if ($row_rsScore3['questionNumber'] == $totalRows_rsScore3){
	  		$insertSQL = sprintf("INSERT INTO game2responses (entryNumber, userName, questionNumber, question, answer, tally) VALUES (%s, %s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['entryNumber'], "int"),
						   GetSQLValueString($_SESSION['MM_Username'], "text"),
						   GetSQLValueString($_POST['questionNumber'], "int"),
						   GetSQLValueString($_POST['question2'], "text"),
						   GetSQLValueString($_POST['response2'], "text"),
						   GetSQLValueString($_POST['tally'], "int"));
	
	  		mysql_select_db($database_ohiofi, $ohiofi);
	  		$Result1 = mysql_query($insertSQL, $ohiofi) or die(mysql_error());
		///}
	}
	header('Location: ' . $_SERVER['PHP_SELF']);
}

$points = $totalRows_rsGame3 + 1;
$currentQuestion = $totalRows_rsScore3 + 1;
//print_r($currentQuestion . "vs" . $rsSurvey['questionNumber'] );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Signal Flow</title>

<link rel="stylesheet" type="text/css" href="musictechwebapps.css" />

</head>
<body>
<?php include("_includes/header.php"); ?>
<div id="signalflowmain">  
    <div id="stage">
        <img id="diagram" src="img/signal_flow_diagram.png">
        
        
        <form id="sfform1" name="sfform1" method="post" action="">
          
          <select name="Question01" id="1" class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform2" name="sfform2" method="post" action="">
          
          <select name="Question02" id="2"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="2">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform3" name="sfform3" method="post" action="">
          
          <select name="Question03" id="3"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="3">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform4" name="sfform4" method="post" action="">
          
          <select name="Question04" id="4"   class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="4">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform5" name="sfform5" method="post" action="">
          
          <select name="Question05" id="5"   class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="5">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform6" name="sfform6" method="post" action="">
          
          <select name="Question06" id="6"   class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="6">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform7" name="sfform7" method="post" action="">
          
          <select name="Question07" id="7"   class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="7">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform8" name="sfform8" method="post" action="">
          
          <select name="Question08" id="8"   class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="8">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform9" name="sfform9" method="post" action="">
          
          <select name="Question09" id="9"   class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="9">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform10" name="sfform10" method="post" action="">
          
          <select name="Question10" id="10"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="10">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform11" name="sfform11" method="post" action="">
          
          <select name="Question11" id="11"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="11">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform12" name="sfform12" method="post" action="">
          
          <select name="Question12" id="12"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="12">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform13" name="sfform13" method="post" action="">
          
          <select name="Question13" id="13"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="13">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform14" name="sfform14" method="post" action="">
          
          <select name="Question14" id="14"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="14">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform15" name="sfform15" method="post" action="">
          
          <select name="Question15" id="15"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="15">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform16" name="sfform16" method="post" action="">
          
          <select name="Question16" id="16"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="16">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform17" name="sfform17" method="post" action="">
          
          <select name="Question17" id="17"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="17">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform18" name="sfform18" method="post" action="">
          
          <select name="Question18" id="18"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="18">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform19" name="sfform19" method="post" action="">
          
          <select name="Question19" id="19"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="-1">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="19">Switching Bank</option>
          </select>
          
        </form>
        
        
        <form id="sfform20" name="sfform20" method="post" action="">
          
          <select name="Question20" id="20"  class="hideMe" onChange="checkAnswer(this.value)">
            <!-- Add items and values to the widget-->
            <option>Please select an item</option>
            <option value="-1">Bus ACN</option>
            <option value="-1">Bus Output Fader</option>
            <option value="-1">Bus/Pan Assign</option>
            <option value="-1">Bus/Tape Switch</option>
            <option value="20">Control Room Volume</option>
            <option value="-1">Direct Out</option>
            <option value="-1">EQ</option>
            <option value="-1">Input Fader</option>
            <option value="-1">Mic/Line Switch</option>
            <option value="-1">Monitor Fader</option>
            <option value="-1">Monitor Pan</option>
            <option value="-1">Pad</option>
            <option value="-1">Post-Input Fader Aux Sends</option>
            <option value="-1">Post-Monitor Fader Aux Sends</option>
            <option value="-1">Pre-Amp</option>
            <option value="-1">Pre-Input Fader Aux Sends</option>
            <option value="-1">Pre-Monitor Fader Aux Sends</option>
            <option value="-1">Stereo ACNs</option>
            <option value="-1">Stereo Master Fader</option>
            <option value="-1">Switching Bank</option>
          </select>
          
        </form>
        
	</div>   
    <div>
    
                <div id="greatJob" class="popup">
                    <h2>
                        <?php $my_array = array(0 => "Great Job!", 1 => "Nicely Done!", 2 => "That's Right!");
                        shuffle($my_array);
                        echo($my_array[0]);
                        ?>               
                    </h2>
                  <form id="form1" name="form1" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question1" id="question1" value=""><input type="hidden" name="response1" id="response1" value=""><input name="tally" type="hidden" value="1" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="points" type="hidden" value="<?=$points ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game2total" type="hidden" value="<?php echo $totalRows_rsScore3; ?>" /><input class="submit button centered" type="submit" name="Submit" id="Submit" value="Submit">
                      <input type="hidden" name="MM_insert" value="form1" />
                      <input type="hidden" name="MM_update" value="form1" />
                  </form>
                </div>
                <div id="tryAgain" class="popup">
                    <h2>Incorrect</h2>
                  <form id="form2" name="form2" action="<?php echo $editFormAction; ?>" method="POST"><input name="entryNumber" type="hidden" value="" /><input name="userName" type="hidden" value="" /><input type="hidden" name="question2" id="question2" value=""><input type="hidden" name="response2" id="response2" value=""><input name="tally" type="hidden" value="0" /><input name="questionNumber" type="hidden" value="<?php echo $currentQuestion; ?>" /><input name="userNumber" type="hidden" value="<?=$row_rsUser["userID"] ?>" /><input name="game2total" type="hidden" value="<?php echo $totalRows_rsScore3; ?>" /><input class="submit button centered" type="submit" name="Submit" id="Submit" value="Submit">
                      <input type="hidden" name="MM_insert" value="form2" />
                      <input type="hidden" name="MM_update" value="form2" />
                    </form>
                </div>
	<br />
    </div> 
            <span id="blank"></span>
            <br />
            
            
        
</div>
    <?php include("_includes/footer.php"); ?>
</body>


<script type="text/javascript">

var question = 1;
var oneChance = 0;

function newQuestion() {
	var randomNumber=Math.floor(Math.random()*20+1);
	if (randomNumber>20) {
		randomNumber=1;
	}
	if (randomNumber<1){
		randomNumber=1;
	}
	question=randomNumber;
	document.form1.question1.value = randomNumber;
	document.form2.question2.value = randomNumber;
}

function checkAnswer(yourAnswer) {
	if (oneChance == 0) {
		document.form1.response1.value = yourAnswer;
		document.form2.response2.value = yourAnswer;
		if (question == yourAnswer) {
			setTimeout('document.getElementById("greatJob").className = "popup popupActive"',300);
		}
		else {
			setTimeout('document.getElementById("tryAgain").className = "popup popupActive"',300);
		}
	}
	oneChance++;
};

function displayQuestion() {
	document.getElementById(question).className = "showMe";
};

newQuestion();
displayQuestion();



</script>
</html>
<?php


mysql_free_result($rsGame3);

mysql_free_result($rsGame3_check);

mysql_free_result($rsScore3);

mysql_free_result($rsUser);

?>
