<?php

/**
 * @author nimiq
 * @copyright 2007
 */


//TODO: Tempo di esecuzione della query
set_time_limit(900);

//TODO: Memoria massima
ini_set("memory_limit","500M");


//TODO: Nascondo gli eventuali warning
error_reporting (E_ALL); //Mostra tutto
//error_reporting (E_ERROR); //Mostra solo gli errori gravi





function collegaDb() {
	$conn = odbc_connect("3. XYZ-Export-FINALE","","");
	
	if ($conn == false) {
		echo "Problemi di connessione ad DB tramite ODBC!!";
		exit();		
	} else
		return $conn;

	//turns off autocommit
	//odbc_autocommit($conn,FALSE);	
}




function cardinalita($tab, $where = '') {
	$conn = collegaDb();
	
	$query0 = "SELECT count(*) FROM \"$tab\" $where";
	list($card) = array_values( odbc_fetch_array(odbc_exec($conn, $query0)) );
	
	odbc_close($conn);
	
	return $card;
}




function controllaGet() {
	if ( (isset($_GET['da']) && isset($_GET['n'])) &&
	      ($_GET['da']!='' && $_GET['n']!='')          ) {
	    
	    if ( isset($_GET['solonok']) ) {  //solonok=ON
			return array($_GET['da'], $_GET['n'], true);	    
	    } else {
			return array($_GET['da'], $_GET['n'], false);
		}
	} else {
		echo "Non trovo i parametri 'da' e 'a' nel GET!!";
		exit();
	}
	
}




function controlla($arrayEsterno) {
	global $nRigaTabella;
	global $table;
	global $recordConErrori;
	global $soloNOK;
	
	$ok = true;

	foreach ($arrayEsterno as $arrayInterno) {
		foreach ($arrayInterno as $valore) {

			if (strcasecmp($valore, '')==0) {
				$ok = false;
			} else if (substr($valore, 0, 1)=='0') {
				$arrayInterno[0] = intval($arrayInterno[0]);
				$valore = intval($valore);
				
				if ($arrayInterno[0] != $valore)
					$ok = false;
			} else {
				if ( strcasecmp(trim($arrayInterno[0]), trim($valore))!=0 )
					$ok = false;
			}
		}
	}

	if ($ok) {
		$table->updateCellAttributes($nRigaTabella, $table->getColCount()-2, array('bgcolor' => "green"));
		return "OK";
	} else {
		$table->updateCellAttributes($nRigaTabella, $table->getColCount()-2, array('bgcolor' => "red"));
		$recordConErrori = true;
		return "NOOOOOOOOOK";
	}
}









function controllaDescr($str1, $str2){
	global $nRigaTabella;
	global $table;
	global $recordConErrori;
	
	$ok = true;

	if ( strcasecmp($str1, $str2)!=0 )
		$ok = false;

	if ($ok) {
		$table->updateCellAttributes($nRigaTabella, $table->getColCount()-2, array('bgcolor' => "green"));
		return "OK";
	} else {
		$table->updateCellAttributes($nRigaTabella, $table->getColCount()-2, array('bgcolor' => "red"));
		$recordConErrori = true;
		return "NOOOOOOOOOK";
	}
	
}











function coloraCelleControlli($array, $classe) {
	global $nRigaTabella;
	global $table;
	
	foreach ($array as $value)
		$table->updateCellAttributes($nRigaTabella-1, $value, array('class' => $classe) );

}




function inserisciRigaTabella($array) {
	global $nRigaTabella;
	global $table;
	
	$nColonna = 0;
	foreach ($array as $value) {
		$table->setCellContents($nRigaTabella, $nColonna++, $value);//Parametri: N.Riga, N.Colonna, contenuto
	}
	$nRigaTabella++;
}













function eseguiQuery($array, $query, $ok = true) {
	global $conn;
	
	if ($ok == false) {
			for ($i=0; $i<sizeof($array)-1; $i++)
				$array[$i] = "";
			//$array[$i] = ""; //questa è la nota
			return;

	} else {
	
		//1) ESEGUO E CONTROLLO LA VALIDITA' DELLA QUERY
			//Eseguo la query e memorizzo il risultato
			@$result = odbc_exec($conn, $query); 

			//Se ha restituito false c'è qualche problema => Query errata
			if ($result == false) {
				////echo "<H3>La query:<BR>$query</BR>presenta un errore!</H3>";
				////exit();
				for ($i=0; $i<sizeof($array)-1; $i++)
					$array[$i] = "";
				$array[$i] = "La query:<BR>$query<BR>presenta un errore!!";
				return false;
			}
	
		//2) ESTRAGGO I RISULTATI
			//Estraggo la prima riga sotto forma di array
			$arrayResult = odbc_fetch_array($result, 1);

			//Se ha restituito false significa che non c'è nessuna riga => 0 risultati
			if ($arrayResult == false) {
				for ($i=0; $i<sizeof($array)-1; $i++)
					$array[$i] = "";
				$array[$i] = "La query:<BR>$query<BR>Ha restituito 0 record!!";
				return false;
			}
		
			//Provo a estrarre la seconda riga, se c'è è un errore: la query doveva restituire solo 1 riga	
			$riga2 = odbc_fetch_array($result, 2);
			if ($riga2 != false) {
				for ($i=0; $i<sizeof($array)-1; $i++)
					$array[$i] = "";
				$array[$i] = "La query:<BR>$query<BR>Ha restituito piu di 1 record!!";
				return false;
			}


			//Controllo che la dimensione dei 2 array (l'array passato come parametro e l'array di risposta della query) sia uguale e 
			if ( sizeof($array)-1 == sizeof($arrayResult) ) {
				//$array = array_values($arrayResult); //non funziona
				//$array = $arrayResult; //non funziona
				for ($i=0; $i<sizeof($array)-1; $i++)
					$array[$i] = array_shift($arrayResult);
				$array[$i] = "";
			}
			return true;
	}

}







/* FUNZIONI DI GESTIONE DEL LOG
 *  Apertura del log
 *  Chiusura del log
 *  Scrittura nel log
 */
$handleLog;

function apriLog($nomeFile) {
	global 	$handleLog;
		
	$dataAttuale = date("Y-m-d H.i.s");
	$fileLog = "$dataAttuale $nomeFile.log";
	$handleLog = fopen($fileLog, 'w');
	fwrite($handleLog, "$dataAttuale\r\n\r\n"); //Scrivo data e ora corrente nel file di log
}

function chiudiLog() {
	global 	$handleLog;
		
	fclose($handleLog);	
}

function scriviLog($stringa) {
	global 	$handleLog;
		
	fwrite($handleLog, $stringa);
}


?>