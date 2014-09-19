<?php
/**
 * @author nimiq
 * @copyright 2007
 */
 
require_once 'funzioni_fns.php';
require_once 'HTML/Page2.php';
require_once 'HTML/Table.php';


//TODO: nota sulla tabella
$commFinale = "NOTA: La tabella contiene tutti gli articoli del secondo tipo (titoli)
La 'chiave univoca' e' il campo arart (codice articolo)";


//TODO: imposto il nome della tabella in esame
$t_4 = $t03_4;
$t_3 = $t03_3;
$t_2 = $t03_2;
$t_1 = $t03_1;
$t   = $t03;


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
$page->addBodyContent("<H3>Livelli: art, stg, gru, rep, set<BR>");
$page->addBodyContent("Cardinalita: $card</H3>");

$nErrori = 0;


//Creo una tabella in cui visualizzare i risultati
$table = new HTML_Table("border='1' bordercolor='#000000' style='border-collapse: collapse; text-align: center; padding-left: 2px; padding-right: 2px;' cellspacing='0'  cellpadding='0'");
$table->setAutoGrow(true);
$nRigaTabella = 0; //Inizializzo il contatore di riga della tabella a 0


//TODO: Colonne della tabella
$table->setCellContents($nRigaTabella, 0, "Tabella in esame");//Parametri: N.Riga, N.Colonna, contenuto
$table->setCellAttributes($nRigaTabella, 0, array('colspan' => "7", 'align' => 'right', 'bgcolor' => 'black', 'style' => 'color: white; font-weight: bold' ));//Parametri: N.Riga, N.Colonna, attributi
$table->setCellContents($nRigaTabella, 8, "Tabelle CR");//Parametri: N.Riga, N.Colonna, contenuto
$table->setCellAttributes($nRigaTabella++, 8, array('colspan' => "5", 'align' => 'left', 'bgcolor' => 'black', 'style' => 'color: white; font-weight: bold' ));//Parametri: N.Riga, N.Colonna, attributi
$colonne = array("liv", "id", "art", "stg", "gru", "rep", "set", "", "id-it", "id-de", "padre", "ris", "nota");

//La prima riga � quella con le intestazioni
inserisciRigaTabella( $colonne );


//Eseguo la QUERY PRINCIPALE che estrae tutti i record dalla tabella in esame
$conn = collegaDb();
$query0 = "SELECT TOP $nRecordProcessati id, arart, araugr, aragr, arabtv, arabt FROM \"$t_4\" WHERE id >= $da;";
$result0 = odbc_exec($conn, $query0);

	
//Ciclo sui record estratti per un massimo di $nRecordProcessati record
for ($i=0; $i<$nRecordProcessati; $i++) {
	$recordConErrori = false;

	//===== LIVELLO 0 - ORIGINALE
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		if ( ($arrayBuf = odbc_fetch_array($result0)) != null) //se ci sono ancora record nella query principale
			list($id0, $art0, $stg0, $gru0, $rep0, $set0) = array_values( $arrayBuf );	
		else //se non ci sono pi� record nella query principale
			break;

		//b) TABELLA CR: estraggo il sottogruppo (che � unico) da livello originale e lo cerco nella tabella di cr dei sottogruppi
		eseguiQuery ( array(	&$stgit, &$stgde, 	&$stgpadit, &$nota0), 
		              "select 	xyz 	from \"$tCrStg\" where xyz=$stg0;");
		
		//c) CONTROLLI: nessuno
		$ris = controlla(  array(      array($stg0, $stgit)      )  );
		
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
			inserisciRigaTabella( array("0-ori", $id0, $art0, $stg0, $gru0, $rep0, $set0, "", $stgit, $stgde, $stgpadit, $ris, $nota0) );
			coloraCelleControlli(array(3,8), "c1");
			coloraCelleControlli(array(9), "c2");
			coloraCelleControlli(array(10), "c3");
		}
		

	//===== LIVELLO 4 - STG
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(		&$id4,	&$art4,	&$stg4,	&$gru4,	&$rep4,	&$set4, &$nota4), //Variabili in cui memorizzare il risultato della query
		        		    "select		xyz from \"$t_3\" where xyz='$art0';"); //Query da eseguire
		
		//b) TABELLA CR: estraggo il gruppo (che � unico) dal livello 4 e lo cerco nella tabella di cr dei gruppi
		eseguiQuery( array(		&$gruit,	&$grude,	&$grupadit, &$nota4),
		             "select 	xyz 	from \"$tCrGru\" where xyz=$gru4;",
		             $ok);

		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr
		$ris = controlla(  array(      array($stg4, $stgde),  array($gru4, $stgpadit, $gruit)      )  );
	
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
			inserisciRigaTabella( array("4-stg", $id4, $art4, $stg4, $gru4, $rep4, $set4, "", $gruit, $grude, $grupadit, $ris, $nota4) );
			coloraCelleControlli(array(3), "c2");
			coloraCelleControlli(array(4,8), "c3");
			coloraCelleControlli(array(9), "c4");
			coloraCelleControlli(array(10), "c5");
		}


	//===== LIVELLO 3 - GRU
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(	&$id3,	&$art3, &$stg3, &$gru3, &$rep3, &$set3, &$nota3),
							"select xyz	from \"$t_2\" where xyz='$art0';");

		//b) TABELLA CR: estraggo il reparto (che � unico) dal livello 3 e lo cerco nella tabella di cr dei reparti
		eseguiQuery( array(		&$repit,	&$repde,	&$reppadit, &$nota3),
		             "select 	xyz 	from \"$tCrRep\" where xyz=$rep3;",
		             $ok);
		             
		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr
		$ris = controlla(  array(      array($stg3, $stgde),  array($gru3, $grude),  array($rep3, $grupadit, $repit)      )  );

		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
			inserisciRigaTabella( array("3-gru", $id3, $art3, $stg3, $gru3, $rep3, $set3, "", $repit, $repde, $reppadit, $ris, $nota3) );
			coloraCelleControlli(array(3), "c2");
			coloraCelleControlli(array(4), "c4");
			coloraCelleControlli(array(5,8), "c5");
			coloraCelleControlli(array(9), "c6");		
			coloraCelleControlli(array(10), "c7");		
		}


	//===== LIVELLO 2 - REP		
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(	&$id2,	&$art2, &$stg2, &$gru2, &$rep2, &$set2, &$nota2),
							"select xyz	from \"$t_1\" where xyz='$art0';");

		//b) TABELLA CR: Estraggo il settore (che � unico) dal livello 2 e lo cerco nella tabella di cr dei settori
		eseguiQuery(	array(	&$setit,	&$setde, &$nota2),
						"select xyz		 from \"$tCrSet\" where xyz=$set2;",
						$ok);

		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr
		$ris = controlla(  array(      array($stg2, $stgde),  array($gru2, $grude),  array($rep2, $repde),  array($set2, $reppadit, $setit)      )  );	
		
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
			inserisciRigaTabella( array("2-rep", $id2, $art2, $stg2, $gru2, $rep2, $set2, "", $setit, $setde, "", $ris, $nota2) );
			coloraCelleControlli(array(3), "c2");
			coloraCelleControlli(array(4), "c4");
			coloraCelleControlli(array(5), "c6");
			coloraCelleControlli(array(6,8), "c7");		
			coloraCelleControlli(array(9), "c8");		
			coloraCelleControlli(array(10), "cdis");
		}		


	//===== LIVELLO 1 - SET
		//a) TABELLA IN ESAME: estraggo i dati (id, articolo, stg, gru, rep, set)
		$ok = eseguiQuery( 	array(	&$id1,	&$art1, &$stg1, &$gru1, &$rep1, &$set1, &$nota1),
							"select xyz	from \"$t\" where xyz='$art0';");
		
		//b) TABELLA CR: nulla da fare
		
		//c) CONTROLLI: eseguo i controlli incrociati tra Tabella in esame e Tabella Cr		
		$ris = controlla(  array(      array($stg1, $stgde),  array($gru1, $grude),  array($rep1, $repde),  array($set1, $setde)      )  );		
	
		//d) STAMPA: stampo la riga
		if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
			inserisciRigaTabella( array("1-set", $id1, $art1, $stg1, $gru1, $rep1, $set1, "", "", "", "", $ris, $nota1) );
			coloraCelleControlli(array(3), "c2");
			coloraCelleControlli(array(4), "c4");
			coloraCelleControlli(array(5), "c6");
			coloraCelleControlli(array(6), "c8");		
			coloraCelleControlli(array(8,9,10), "cdis");
		}		
		


	if ( !$soloNOK || ($soloNOK && $recordConErrori) ) {
		//Inserisco una riga vuota
		$nTotColonne = $table->getColCount();
		//$table->setCellAttributes($nRigaTabella++, 0, array('colspan' => "$nTotColonne", 'bgcolor' => 'black', 'height' => '1', 'style' => 'font-size: 1mm' ));//Parametri: N.Riga, N.Colonna, attributi
		inserisciRigaTabella( $colonne );
		$table->updateRowAttributes($nRigaTabella-1, "align='center' valign='center' bgcolor='gray' style='color: white; font-weight: bold'");
	}

	//Sommo 1 agli errori se � il caso
	if ($recordConErrori)
		$nErrori++;
}


//Coloro la prima riga di intestazione
$table->updateRowAttributes(1, "align='center' valign='center' bgcolor='gray' style='color: white; font-weight: bold'");

//Coloro e allineo la colonna con le note
$table->updateColAttributes(($table->getColCount())-1, "class='cnote'");

//Coloro la colonna di separazione tra Tabella in esame e Tabella di CR
$table->updateColAttributes(7, "bgcolor='black'");

//Infine aggiungo la tabella alla pagina
$page->addBodyContent($table->toHTML());
$page->addBodyContent("<H3>Num. errori: $nErrori</H3>");
$page->addBodyContent("<P><PRE>$commFinale</PRE></P>");
$page->addBodyContent("<p><a href='T03inv.php'>[Controllo inverso]</a><br>");
$page->addBodyContent("<a href='index.html'>[Index]</a></p>");
$page->display();

odbc_close($conn);




?>