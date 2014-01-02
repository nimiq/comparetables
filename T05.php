<?php
/**
 * @author nimiq
 * @copyright 2007
 */
 
require_once 'funzioni_fns.php';
require_once 'HTML/Page2.php';
require_once 'HTML/Table.php';


//TODO: nota sulla tabella
$commFinale = "La conversione di questa tabella funziona in 2 modi diversi a seconda che la riga contenga o meno il codice articolo (artmul)
Se il codice articolo e' presente il gruppo e' da convertire copiandolo dalla tabella ARTST del livello corrente
Se il codce articolo non e' presente il gruppo e' da convertire secondo la tabella di cross reference dei gruppi";


//TODO: imposto il nome della tabella in esame
$t_4 = $t05_4;
$t_3 = $t05_3;
$t_2 = $t05_2;
$t_1 = $t05_1;
$t   = $t05;


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
$page->addBodyContent("<H3>Livelli: [art], gru<BR>");
$page->addBodyContent("Cardinalita: $card</H3>");
$nErrori = 0;


//Creo una tabella in cui visualizzare i risultati
$table = new HTML_Table("border='1' bordercolor='#000000' style='border-collapse: collapse; text-align: center; padding-left: 2px; padding-right: 2px;' cellspacing='0'  cellpadding='0'");
$table->setAutoGrow(true);
$nRigaTabella = 0; //Inizializzo il contatore di riga della tabella a 0


//TODO: Colonne della tabella
$table->setCellContents($nRigaTabella, 0, "Tabella in esame");//Parametri: N.Riga, N.Colonna, contenuto
$table->setCellAttributes($nRigaTabella, 0, array('colspan' => "4", 'align' => 'right', 'bgcolor' => 'black', 'style' => 'color: white; font-weight: bold' ));//Parametri: N.Riga, N.Colonna, attributi
$table->setCellContents($nRigaTabella, 5, "ARTST/Tabella CR GRU");//Parametri: N.Riga, N.Colonna, contenuto
$table->setCellAttributes($nRigaTabella++, 5, array('colspan' => "6", 'align' => 'left', 'bgcolor' => 'black', 'style' => 'color: white; font-weight: bold' ));//Parametri: N.Riga, N.Colonna, attributi
$colonne = array("liv", "id", "art", "gru", "", "ARTST.art", "ARTST.gru", "CR.gru-it", "CR.gru-de", "ris", "nota");

//La prima riga è quella con le intestazioni
inserisciRigaTabella( $colonne );


//Eseguo la QUERY PRINCIPALE che estrae tutti i record dalla tabella in esame
$conn = collegaDb();
$query0 = "SELECT TOP $nRecordProcessati id, artmul, grumul, codfil, codmul FROM \"$t_4\" WHERE id >= $da;";
$result0 = odbc_exec($conn, $query0);

	
//Ciclo sui record estratti per un massimo di $nRecordProcessati record
for ($i=0; $i<$nRecordProcessati; $i++) {
	$recordConErrori = false;
	$cercaInArtst = false;

	//===== LIVELLO 0 - ORIGINALE
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		if ( ($arrayBuf = odbc_fetch_array($result0)) != null) //se ci sono ancora record nella query principale
			list($id0, $art0, $gru0, $codfil, $codmul) = array_values( $arrayBuf );	
		else //se non ci sono più record nella query principale
			break;

		if ($art0 != 0)
			$cercaInArtst = true;

		if ($cercaInArtst) {
			//Trasformo il codice articolo da numerico a alfabetico di 6 cifre
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
			
			//b) TABELLA ARTST: estraggo il gruppo
			$riuscita = eseguiQuery ( array(	&$artstart0,&$artstgru0, &$nota0), 
			              "SELECT 				xyz			FROM \"$t02_4\" WHERE xyz='$art0Str';");
		
			if (!$riuscita) {
				eseguiQuery ( array(	&$artstart0,&$artstgru0, &$nota0),
		        	      "SELECT 		xyz			FROM \"$t03_4\" WHERE xyz='$art0Str';");			
			}
		
			//c) CONTROLLI
			$ris = controlla(  array(      array($art0Str, $artstart0), array($gru0, $artstgru0)       )  );
		
			//d) STAMPA: stampo la riga
			if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
				inserisciRigaTabella( array("0-ori", $id0, $art0, $gru0, "", $artstart0, $artstgru0, "", "", $ris, $nota0) );
				coloraCelleControlli(array(2,5), "c1");
				coloraCelleControlli(array(3,6), "c2");
				coloraCelleControlli(array(7,8), "cdis");
			}
		} else {
			//b)Al livello ORIG non devo fare niente			
			
			//c) CONTROLLI
		
			//d) STAMPA: stampo la riga
			if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
				inserisciRigaTabella( array("0-ori", $id0, $art0, $gru0, "", "", "", "", "", "", "") );
				coloraCelleControlli(array(2), "c1");
				coloraCelleControlli(array(3), "c2");
				coloraCelleControlli(array(5,6,7,8,9), "cdis");
			}
			
		}
		

	//===== LIVELLO 4 - STG
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(		&$id4,	&$art4,	&$gru4,	&$nota4), //Variabili in cui memorizzare il risultato della query
		        		    "SELECT		xyz 		FROM \"$t_3\" WHERE xyz=$art0 AND xyz=$codfil AND xyz=$codmul;"); //Query da eseguire
		
		if ($cercaInArtst) {
			
			//b) TABELLA ARTST: estraggo il gruppo
			$riuscita = eseguiQuery ( array(	&$artstart4,&$artstgru4, &$nota4), 
			              "SELECT 				xyz			FROM \"$t02_3\" WHERE xyz='$art0Str';");
		
			if (!$riuscita) {
				eseguiQuery ( array(	&$artstart4,&$artstgru4, &$nota4),
		        	      "SELECT 		xyz			FROM \"$t03_3\" WHERE xyz='$art0Str';");			
			}
		
			//c) CONTROLLI
			$ris = controlla(  array(      array($art0Str, $artstart4), array($gru4, $artstgru4)       )  );
		
			//d) STAMPA: stampo la riga
			if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
				inserisciRigaTabella( array("4-stg", $id4, $art4, $gru4, "", $artstart4, $artstgru4, "", "", $ris, $nota4) );
				coloraCelleControlli(array(2,5), "c1");
				coloraCelleControlli(array(3,6), "c2");
				coloraCelleControlli(array(7,8), "cdis");
			}
		} else {
			//b)Al livello STG devo pescare il gruppo it e de da CD gru	
			eseguiQuery( array(		&$gruit,	&$grude,	&$grupadit, &$nota4),
			             "select 	xyz 	from \"$tCrGru\" where xyz=$gru4;",
			             $ok);

			//c) CONTROLLI
			$ris = controlla(  array(      array($gru4, $gruit)       )  );
					
			//d) STAMPA: stampo la riga
			if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
				inserisciRigaTabella( array("4-stg", $id4, $art4, $gru4, "", "", "", $gruit, $grude, $ris, $nota4) );
				coloraCelleControlli(array(2), "c1");
				coloraCelleControlli(array(3), "c2");
				coloraCelleControlli(array(7), "c2");
				coloraCelleControlli(array(8), "c3");
				coloraCelleControlli(array(5,6), "cdis");
			}
			
		}


	//===== LIVELLO 3 - GRU
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(		&$id3,	&$art3,	&$gru3,	&$nota3), //Variabili in cui memorizzare il risultato della query
		        		    "SELECT		xyz 		FROM \"$t_2\" WHERE xyz=$art0 AND xyz=$codfil AND xyz=$codmul;"); //Query da eseguire
		
		if ($cercaInArtst) {
			
			//b) TABELLA ARTST: estraggo il gruppo
			$riuscita = eseguiQuery ( array(	&$artstart3,&$artstgru3, &$nota3), 
			              "SELECT 				xyz			FROM \"$t02_2\" WHERE xyz='$art0Str';");
		
			if (!$riuscita) {
				eseguiQuery ( array(	&$artstart3,&$artstgru3, &$nota3),
		        	      "SELECT 		xyz			FROM \"$t03_2\" WHERE xyz='$art0Str';");			
			}
		
			//c) CONTROLLI
			$ris = controlla(  array(      array($art0Str, $artstart3), array($gru3, $artstgru3)       )  );
		
			//d) STAMPA: stampo la riga
			if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
				inserisciRigaTabella( array("3-gru", $id3, $art3, $gru3, "", $artstart3, $artstgru3, "", "", $ris, $nota3) );
				coloraCelleControlli(array(2,5), "c1");
				coloraCelleControlli(array(3,6), "c3");
				coloraCelleControlli(array(7,8), "cdis");
			}
		} else {
			//b)Dal livello GRU in poi non devo fare nulla

			//c) CONTROLLI
			$ris = controlla(  array(      array($gru3, $grude)       )  );
					
			//d) STAMPA: stampo la riga
			if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
				inserisciRigaTabella( array("3-gru", $id3, $art3, $gru3, "", "", "", "", "", $ris, $nota3) );
				coloraCelleControlli(array(2), "c1");
				coloraCelleControlli(array(3), "c3");
				coloraCelleControlli(array(5,6,7,8), "cdis");
			}
			
		}


	//===== LIVELLO 2 - REP		
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(		&$id2,	&$art2,	&$gru2,	&$nota2), //Variabili in cui memorizzare il risultato della query
		        		    "SELECT		xyz 		FROM \"$t_1\" WHERE xyz=$art0 AND xyz=$codfil AND xyz=$codmul;"); //Query da eseguire

		//b) TABELLA artst: Estraggo il settore (che è unico) dal livello 2 e lo cerco nella tabella di cr dei settori
		             
		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr
		$ris = controlla(  array(      array($art2, $art3), array($gru2, $gru3)       )  );
		
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {		
			inserisciRigaTabella( array("2-rep", $id2, $art2, $gru2, "", "", "", "", "", $ris, $nota2) );
			coloraCelleControlli(array(2), "c1");
			coloraCelleControlli(array(3), "c3");
			coloraCelleControlli(array(5,6,7,8), "cdis");		
		}


	//===== LIVELLO 1 - SET
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(		&$id1,	&$art1,	&$gru1,	&$nota1), //Variabili in cui memorizzare il risultato della query
		        		    "SELECT		xyz 		FROM \"$t\" WHERE xyz=$art0 AND xyz=$codfil AND xyz=$codmul;"); //Query da eseguire

		//b) TABELLA artst: nulla da fare

		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr		
		$ris = controlla(  array(      array($art1, $art2), array($gru1, $gru2)       )  );
		             
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {		
			inserisciRigaTabella( array("1-set", $id1, $art1, $gru1, "", "", "", "", "", $ris, $nota1) );
			coloraCelleControlli(array(2), "c1");
			coloraCelleControlli(array(3), "c3");
			coloraCelleControlli(array(5,6,7,8), "cdis");		
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
$table->updateColAttributes($nTotColonne-1, "class='cnote'");

//Coloro la colonna di separazione tra Tabella in esame e Tabella di CR
$table->updateColAttributes(4, "bgcolor='black'");

//Infine aggiungo la tabella alla pagina
$page->addBodyContent($table->toHTML());
$page->addBodyContent("<H3>Num. errori: $nErrori</H3>");
$page->addBodyContent("<P><PRE>$commFinale</PRE></P>");
$page->addBodyContent("<p><a href='T05inv.php'>[Controllo inverso]</a><br>");
$page->addBodyContent("<a href='index.html'>[Index]</a></p>");
$page->display();

odbc_close($conn);




?>