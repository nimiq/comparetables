<?php
/**
 * @author nimiq
 * @copyright 2007
 */
 
require_once 'funzioni_fns.php';
require_once 'HTML/Page2.php';
require_once 'HTML/Table.php';
	

$conn = collegaDb();					 
$query0 = "SELECT TOP 10 id, artacc, xyz, repacc FROM \"04 XYZ\" where artacc=50006";

$result0 = odbc_exec($conn, $query0);


$arrayBuf = odbc_fetch_array($result0);
print_r($arrayBuf);

echo "<BR>";
echo "<P>";
echo date("Y-m-d H:i:s");
echo "</P>";


apriLog("provaaaa");
scriviLog("1");
scriviLog("1");
scriviLog("1");
chiudiLog();


odbc_close($conn);




?>