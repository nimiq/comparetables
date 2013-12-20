<?php
/**
 * @author nimiq
 * @copyright 2007
 */
 
require_once 'funzioni_fns.php';
require_once 'HTML/Page2.php';
require_once 'HTML/Table.php';


//TODO: nota sulla tabella
$commFinale = "NOTA: la tabella contiene i livelli gruppo e reparto per ogni codice articolo
Il gruppo e il reparto sono da convertire copiandoli dalla tabella ARTST del livello corrente per ogni codice articolo
La chiave univoca logica per la tabella";


//TODO: imposto il nome della tabella in esame
$t_4 = $t04_4;
$t_3 = $t04_3;
$t_2 = $t04_2;
$t_1 = $t04_1;
$t   = $t04;


//Leggo i parametri passati tramite GET
list($da, $nRecordProcessati, $soloNOK) = controllaGet();

//Creo la pagina HTML
$page = new HTML_Page2();
$page->setTitle('CmprTables');
$page->addStyleSheet('Stile.css');
//<style type="text/css" media="screen">@import "/css/homepage/2006/stile_v3.css";</style>

//Aggiungo alla pagina HTML una frase
$page->addBodyContent("<H1>Tabella: $t</H1>");
$card = cardinalita($t_4);
$page->addBodyContent("<H3>Livelli: art, gru, rep<BR>");
$page->addBodyContent("Cardinalita vera: 1352773<BR>");
$page->addBodyContent("Qui sono stati importati solo i primi $card record</H3>");
$nErrori = 0;


//Creo una tabella in cui visualizzare i risultati
$table = new HTML_Table("border='1' bordercolor='#000000' style='border-collapse: collapse; text-align: center; padding-left: 2px; padding-right: 2px;' cellspacing='0'  cellpadding='0'");
$table->setAutoGrow(true);
$nRigaTabella = 0; //Inizializzo il contatore di riga della tabella a 0


//TODO: Colonne della tabella
$table->setCellContents($nRigaTabella, 0, "Tabella in esame");//Parametri: N.Riga, N.Colonna, contenuto
$table->setCellAttributes($nRigaTabella, 0, array('colspan' => "5", 'align' => 'right', 'bgcolor' => 'black', 'style' => 'color: white; font-weight: bold' ));//Parametri: N.Riga, N.Colonna, attributi
$table->setCellContents($nRigaTabella, 6, "ARTST");//Parametri: N.Riga, N.Colonna, contenuto
$table->setCellAttributes($nRigaTabella++, 6, array('colspan' => "5", 'align' => 'left', 'bgcolor' => 'black', 'style' => 'color: white; font-weight: bold' ));//Parametri: N.Riga, N.Colonna, attributi
$colonne = array("liv", "id", "art", "gru", "rep", "", "art", "gru", "rep", "ris", "nota");

//La prima riga è quella con le intestazioni
inserisciRigaTabella( $colonne );


//Eseguo la QUERY PRINCIPALE che estrae tutti i record dalla tabella in esame
$conn = collegaDb();
$query0 = "SELECT TOP $nRecordProcessati id, filacc, numacc, datacc, artacc, gruacc, repacc FROM \"$t_4\" WHERE id >= $da;";
$result0 = odbc_exec($conn, $query0);

	
//Ciclo sui record estratti per un massimo di $nRecordProcessati record
for ($i=0; $i<$nRecordProcessati; $i++) {
	$recordConErrori = false;

	//===== LIVELLO 0 - ORIGINALE
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		if ( ($arrayBuf = odbc_fetch_array($result0)) != null) //se ci sono ancora record nella query principale
			list($id0, $filacc, $numacc, $datacc, $art0, $gru0, $rep0) = array_values( $arrayBuf );	
		else //se non ci sono più record nella query principale
			break;

		//Siccome qusta tabella è stata importata da XLS a MDB (mentre tutte le altre da TXT a MDB) allora stranamente considera i numeri con 1 decimale
		$filacc = intval($filacc);
		$numacc = intval($numacc);
		$datacc = intval($datacc);
		$art0 = intval($art0);
		$gru0 = intval($gru0);
		$rep0 = intval($rep0);

		//Trasformo il codice articolo da numerico a alfabetico di 6 cifre
		$art0 = intval($art0);
		if ($art0<10)
			$art0Str = "00000$art0";
		else if ($art0>=10 && $art0<100)
			$art0Str = "0000$art0";
		else if ($art0>=100 && $art0<1000)
			$art0Str = "000$art0";
		else if ($art0>=1000 && $art0<10000)
			$art0Str = "00$art0";
		else if ($art0>=10000 && $art0<100000)
			$art0Str = "0$art0";
		else
			$art0Str = $art0;

		//b) TABELLA ARTST: estraggo il sottogruppo (che è unico) da livello originale e lo cerco nella tabella di cr dei sottogruppi
		$riuscita = eseguiQuery ( array(	&$artstart0,&$artstgru0,&$artstrep0, &$nota0), 
		              "SELECT 				xyz 			FROM \"$t02_4\" WHERE xyz='$art0Str';");
		
		if (!$riuscita) {
			eseguiQuery ( array(	&$artstart0,&$artstgru0,&$artstrep0, &$nota0),
		              "SELECT 		xyz	 		FROM \"$t03_4\" WHERE xyz='$art0Str';");			
		}
		
		//c) CONTROLLI: nessuno
		$ris = controlla(  array(      array($art0Str, $artstart0), array($gru0, $artstgru0), array($rep0, $artstrep0)       )  );
		
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
		inserisciRigaTabella( array("0-ori", $id0, $art0, $gru0, $rep0, "", $artstart0, $artstgru0, $artstrep0, $ris, $nota0) );
		coloraCelleControlli(array(2,6), "c1");
		coloraCelleControlli(array(3,7), "c2");
		coloraCelleControlli(array(4,8), "c3");
		}
		

/*		
NOTA: qs query "SELECT artacc, gruacc, repacc, filacc, numacc, datacc FROM MCG_ALB.ACCME00F_4 WHERE artacc='50006' AND filacc=37 AND numacc=25339 AND datacc=20020214" ci impiega
15 sec, quindi tutta la pagina è letissima perchè per altro ha 1mln di record
*/


	//===== LIVELLO 4 - STG
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(		&$id4,	&$art4,	&$gru4,	&$rep4,	&$nota4), //Variabili in cui memorizzare il risultato della query
		        		    "SELECT		xyz 		FROM \"$t_3\" WHERE xyz=$art0 AND xyz=$filacc AND xyz=$numacc AND datacc=$datacc"); //Query da eseguire
		
		//Siccome qusta tabella è stata importata da XLS a MDB (mentre tutte le altre da TXT a MDB) allora stranamente considera i numeri con 1 decimale
		$art4 = intval($art4);
		$gru4 = intval($gru4);
		$rep4 = intval($rep4);

		//b) TABELLA ARTST: estraggo il gruppo (che è unico) dal livello 4 e lo cerco nella tabella di cr dei gruppi
		$riuscita = eseguiQuery ( array(	&$artstart4,&$artstgru4,&$artstrep4, &$nota4), 
		              "SELECT 				xyz 			FROM \"$t02_3\" WHERE xyz='$art0Str';",
					  $ok);
		
		if (!$riuscita) {
			eseguiQuery ( array(	&$artstart4,&$artstgru4,&$artstrep4, &$nota4),
		              "SELECT 		xyz 			FROM \"$t03_3\" WHERE xyz='$art0Str';",
					  $ok);			
		}

		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr
		$ris = controlla(  array(      array($art0Str, $artstart4), array($gru4, $artstgru4), array($rep4, $artstrep4)       )  );
	
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {		
		inserisciRigaTabella( array("4-stg", $id4, $art4, $gru4, $rep4, "", $artstart4, $artstgru4, $artstrep4, $ris, $nota4) );
		coloraCelleControlli(array(2,6), "c1");
		coloraCelleControlli(array(3,7), "c4");
		coloraCelleControlli(array(4,8), "c5");
		}


	//===== LIVELLO 3 - GRU
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(		&$id3,	&$art3,	&$gru3,	&$rep3,	&$nota3), //Variabili in cui memorizzare il risultato della query
		        		    "SELECT		xyz 		FROM \"$t_2\" WHERE xyz=$art0 AND xyz=$filacc AND xyz=$numacc AND xyz=$datacc"); //Query da eseguire

		//Siccome qusta tabella è stata importata da XLS a MDB (mentre tutte le altre da TXT a MDB) allora stranamente considera i numeri con 1 decimale
		$art3 = intval($art3);
		$gru3 = intval($gru3);
		$rep3 = intval($rep3);

		//b) TABELLA artst: estraggo il reparto (che è unico) dal livello 3 e lo cerco nella tabella di cr dei reparti
		$riuscita = eseguiQuery ( array(	&$artstart3,&$artstgru3,&$artstrep3, &$nota3), 
		              "SELECT 				xyz 			FROM \"$t02_2\" WHERE xyz='$art0Str';",
					  $ok);
		
		if (!$riuscita) {
			eseguiQuery ( array(	&$artstart3,&$artstgru3,&$artstrep3, &$nota3),
		              "SELECT 		xyz 			FROM \"$t03_2\" WHERE xyz='$art0Str';",
					  $ok);			
		}
		             
		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr
		$ris = controlla(  array(      array($art0Str, $artstart3), array($gru3, $artstgru3), array($rep3, $artstrep3)       )  );

		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {		
		inserisciRigaTabella( array("3-gru", $id3, $art3, $gru3, $rep3, "", $artstart3, $artstgru3, $artstrep3, $ris, $nota3) );
		coloraCelleControlli(array(2,6), "c1");
		coloraCelleControlli(array(3,7), "c6");
		coloraCelleControlli(array(4,8), "c7");
		}


	//===== LIVELLO 2 - REP		
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(		&$id2,	&$art2,	&$gru2,	&$rep2,	&$nota2), //Variabili in cui memorizzare il risultato della query
		        		    "SELECT		xyz 		FROM \"$t_1\" WHERE xyz=$art0 AND xyz=$filacc AND xyz=$numacc AND xyz=$datacc"); //Query da eseguire

		//Siccome qusta tabella è stata importata da XLS a MDB (mentre tutte le altre da TXT a MDB) allora stranamente considera i numeri con 1 decimale
		$art2 = intval($art2);
		$gru2 = intval($gru2);
		$rep2 = intval($rep2);

		//b) TABELLA artst: Estraggo il settore (che è unico) dal livello 2 e lo cerco nella tabella di cr dei settori
		$riuscita = eseguiQuery ( array(	&$artstart2,&$artstgru2,&$artstrep2, &$nota2), 
		              "SELECT 				xyz 			FROM \"$t02_1\" WHERE xyz='$art0Str';",
					  $ok);
		
		if (!$riuscita) {
			eseguiQuery ( array(	&$artstart2,&$artstgru2,&$artstrep2, &$nota2),
		              "SELECT 		xyz 			FROM \"$t03_1\" WHERE xyz='$art0Str';",
					  $ok);			
		}
		             
		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr
		$ris = controlla(  array(      array($art0Str, $artstart2), array($gru2, $gru3, $artstgru2), array($rep2, $artstrep2)       )  );
		
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
		inserisciRigaTabella( array("2-rep", $id2, $art2, $gru2, $rep2, "", $artstart2, $artstgru2, $artstrep2, $ris, $nota2) );
		coloraCelleControlli(array(2,6), "c1");
		coloraCelleControlli(array(3,7), "c4");
		coloraCelleControlli(array(4,8), "c6");	
		}


	//===== LIVELLO 1 - SET
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(		&$id1,	&$art1,	&$gru1,	&$rep1,	&$nota1), //Variabili in cui memorizzare il risultato della query
		        		    "SELECT		xyz 		FROM \"$t\" WHERE xyz=$art0 AND xyz=$filacc AND xyz=$numacc AND xyz=$datacc"); //Query da eseguire

		//Siccome qusta tabella è stata importata da XLS a MDB (mentre tutte le altre da TXT a MDB) allora stranamente considera i numeri con 1 decimale
		$art1 = intval($art1);
		$gru1 = intval($gru1);
		$rep1 = intval($rep1);

		//b) TABELLA artst: nulla da fare
		$riuscita = eseguiQuery ( array(	&$artstart1,&$artstgru1,&$artstrep1, &$nota1), 
		              "SELECT 				xyz 			FROM \"$t02\" WHERE xyz='$art0Str';",
					  $ok);
		
		if (!$riuscita) {
			eseguiQuery ( array(	&$artstart1,&$artstgru1,&$artstrep1, &$nota1),
		              "SELECT 		xyz 			FROM \"$t03\" WHERE xyzxyz='$art0Str';",
					  $ok);			
		}
		
		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr		
		$ris = controlla(  array(      array($art0Str, $artstart1), array($gru1, $gru2, $artstgru1), array($rep1, $rep2, $artstrep1)       )  );
		             
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {		
		inserisciRigaTabella( array("1-set", $id1, $art1, $gru1, $rep1, "", $artstart1, $artstgru1, $artstrep1, $ris, $nota1) );
		coloraCelleControlli(array(2,6), "c1");
		coloraCelleControlli(array(3,7), "c4");
		coloraCelleControlli(array(4,8), "c6");			
		}


	if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
	//Inserisco una riga vuota
	$nTotColonne = $table->getColCount();
	//$table->setCellAttributes($nRigaTabella++, 0, array('colspan' => "$nTotColonne", 'bgcolor' => 'black', 'height' => '1', 'style' => 'font-size: 1mm' ));//Parametri: N.Riga, N.Colonna, attributi
	inserisciRigaTabella( $colonne );
	$table->updateRowAttributes($nRigaTabella-1, "align='center' valign='center' bgcolor='gray' style='color: white; font-weight: bold'");
	}

	//Sommo 1 agli errori se è il caso
	if ($recordConErrori)
		$nErrori++;
}


//Coloro la prima riga di intestazione
$table->updateRowAttributes(1, "align='center' valign='center' bgcolor='gray' style='color: white; font-weight: bold'");

//Coloro e allineo la colonna con le note

$table->updateColAttributes(($table->getColCount())-1, "class='cnote'");

//Coloro la colonna di separazione tra Tabella in esame e Tabella di CR
$table->updateColAttributes(5, "bgcolor='black'");

//Infine aggiungo la tabella alla pagina
$page->addBodyContent($table->toHTML());
$page->addBodyContent("<H3>Num. errori: $nErrori</H3>");
$page->addBodyContent("<P><PRE>$commFinale</PRE></P>");
$page->addBodyContent("<p><a href='T04inv.php'>[Controllo inverso]</a><br>");
$page->addBodyContent("<a href='index.html'>[Index]</a></p>");
$page->display();

odbc_close($conn);




?>