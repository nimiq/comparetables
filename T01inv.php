<?php
/**
 * @author nimiq
 * @copyright 2007
 */
 
require_once 'funzioni_fns.php';
require_once 'HTML/Page2.php';
require_once 'HTML/Table.php';


//TODO: nota sulla tabella
$commFinale = "NOTA: controllo che la tabella dopo il livello di conversione x contenga solo livelli x tedeschi,
cioe livelli x presenti nella tabella di CR del livello x nella colonna id de";


//TODO: imposto il nome della tabella in esame
$t_4 = $t01_4;
$t_3 = $t01_3;
$t_2 = $t01_2;
$t_1 = $t01_1;
$t   = $t01;

//Creo la pagina HTML
$page = new HTML_Page2();
$page->setTitle('CmprTables');


//Aggiungo alla pagina HTML una frase
$page->addBodyContent("<H1>Tabella: $t</H1>");



//********** PRIMA TABELLA: $t_3 **************************************************************************
$page->addBodyContent("<H3>Tabella: $t_3</H3>");
//Creo una tabella in cui visualizzare i risultati
$table = new HTML_Table("border='1' bordercolor='#000000' style='border-collapse: collapse; text-align: center; padding-left: 2px; padding-right: 2px;' cellspacing='0'  cellpadding='0'");
$table->setAutoGrow(true);
$nRigaTabella = 0; //Inizializzo il contatore di riga della tabella a 0

//TODO: Colonne della tabella
//La prima riga è quella con le intestazioni
$colonne = array("#", "id", "stato", "stg", "gru", "rep", "set");
inserisciRigaTabella( $colonne );


$conn = collegaDb();

$query0 = "SELECT rastat, rastgr, ragrup, rarepa, rasett FROM \"$t_3\" WHERE rastgr NOT IN
(SELECT xyz FROM \"$tCrStg\") ORDER BY xyz ASC";
$result0 = odbc_exec($conn, $query0);

	
//Ciclo su tutti i record estratti
$n = 1;
while (true) {
	
	//Estraggo i risultati e interrompo se sono all'ultimo record
	if ( ($array0 = odbc_fetch_array($result0)) != null) //se ci sono ancora record nella query principale
		list($stato0, $stg0, $gru0, $rep0, $set0) = array_values( $array0 );	
	else //se non ci sono più record nella query principale
		break;
	
	//$set0 estratto da $t è in forma stringa mentre quello in $tCrSet è in forma numerica, quindi trasformo $set0 in numero intero
	//$set0 = intval($set0);
	inserisciRigaTabella( array($n, "", $stato0, $stg0, $gru0, $rep0, $set0) );
	//$page->addBodyContent("$n, $id0, $stato0, $stg0, $gru0, $rep0, $set0<BR>");
	$n++;
	
}
//Coloro la colonna controllata
$table->updateColAttributes(3, "bgcolor='gray'" );

//Coloro la prima riga di intestazione
$table->updateRowAttributes(0, "align='center' valign='center' bgcolor='gray' style='color: white; font-weight: bold'");

//Infine aggiungo la tabella alla pagina
$page->addBodyContent($table->toHTML());










//********** PRIMA TABELLA: $t_2 **************************************************************************
$page->addBodyContent("<H3>Tabella: $t_2</H3>");
//Creo una tabella in cui visualizzare i risultati
$table = new HTML_Table("border='1' bordercolor='#000000' style='border-collapse: collapse; text-align: center; padding-left: 2px; padding-right: 2px;' cellspacing='0'  cellpadding='0'");
$table->setAutoGrow(true);
$nRigaTabella = 0; //Inizializzo il contatore di riga della tabella a 0

//TODO: Colonne della tabella
//La prima riga è quella con le intestazioni
$colonne = array("#", "id", "stato", "stg", "gru", "rep", "set");
inserisciRigaTabella( $colonne );


$conn = collegaDb();

$query0 = "SELECT xyz, xyz, xyz, xyz, xyz FROM \"$t_2\" WHERE ragrup NOT IN
(SELECT f7cote FROM \"$tCrGru\") ORDER BY rarepa ASC";
$result0 = odbc_exec($conn, $query0);

	
//Ciclo su tutti i record estratti
$n = 1;
while (true) {
	
	//Estraggo i risultati e interrompo se sono all'ultimo record
	if ( ($array0 = odbc_fetch_array($result0)) != null) //se ci sono ancora record nella query principale
		list($stato0, $stg0, $gru0, $rep0, $set0) = array_values( $array0 );	
	else //se non ci sono più record nella query principale
		break;
	
	//$set0 estratto da $t è in forma stringa mentre quello in $tCrSet è in forma numerica, quindi trasformo $set0 in numero intero
	//$set0 = intval($set0);
	inserisciRigaTabella( array($n, "", $stato0, $stg0, $gru0, $rep0, $set0) );
	//$page->addBodyContent("$n, $id0, $stato0, $stg0, $gru0, $rep0, $set0<BR>");
	$n++;
	
}
//Coloro la colonna controllata
$table->updateColAttributes(4, "bgcolor='gray'" );

//Coloro la prima riga di intestazione
$table->updateRowAttributes(0, "align='center' valign='center' bgcolor='gray' style='color: white; font-weight: bold'");

//Infine aggiungo la tabella alla pagina
$page->addBodyContent($table->toHTML());










//********** PRIMA TABELLA: $t_1 **************************************************************************
$page->addBodyContent("<H3>Tabella: $t_1</H3>");
//Creo una tabella in cui visualizzare i risultati
$table = new HTML_Table("border='1' bordercolor='#000000' style='border-collapse: collapse; text-align: center; padding-left: 2px; padding-right: 2px;' cellspacing='0'  cellpadding='0'");
$table->setAutoGrow(true);
$nRigaTabella = 0; //Inizializzo il contatore di riga della tabella a 0

//TODO: Colonne della tabella
//La prima riga è quella con le intestazioni
$colonne = array("#", "id", "stato", "stg", "gru", "rep", "set");
inserisciRigaTabella( $colonne );


$conn = collegaDb();

$query0 = "SELECT xyz, xyz, xyz, xyz, xyz FROM \"$t_1\" WHERE xyz NOT IN
(SELECT xyz FROM \"$tCrRep\") ORDER BY rasett ASC";
$result0 = odbc_exec($conn, $query0);

	
//Ciclo su tutti i record estratti
$n = 1;
while (true) {
	
	//Estraggo i risultati e interrompo se sono all'ultimo record
	if ( ($array0 = odbc_fetch_array($result0)) != null) //se ci sono ancora record nella query principale
		list($stato0, $stg0, $gru0, $rep0, $set0) = array_values( $array0 );	
	else //se non ci sono più record nella query principale
		break;
	
	//$set0 estratto da $t è in forma stringa mentre quello in $tCrSet è in forma numerica, quindi trasformo $set0 in numero intero
	//$set0 = intval($set0);
	inserisciRigaTabella( array($n, "", $stato0, $stg0, $gru0, $rep0, $set0) );
	//$page->addBodyContent("$n, $id0, $stato0, $stg0, $gru0, $rep0, $set0<BR>");
	$n++;
	
}
//Coloro la colonna controllata
$table->updateColAttributes(5, "bgcolor='gray'" );

//Coloro la prima riga di intestazione
$table->updateRowAttributes(0, "align='center' valign='center' bgcolor='gray' style='color: white; font-weight: bold'");

//Infine aggiungo la tabella alla pagina
$page->addBodyContent($table->toHTML());










//********** PRIMA TABELLA: $t_1 **************************************************************************
$page->addBodyContent("<H3>Tabella: $t</H3>");
//Creo una tabella in cui visualizzare i risultati
$table = new HTML_Table("border='1' bordercolor='#000000' style='border-collapse: collapse; text-align: center; padding-left: 2px; padding-right: 2px;' cellspacing='0'  cellpadding='0'");
$table->setAutoGrow(true);
$nRigaTabella = 0; //Inizializzo il contatore di riga della tabella a 0

//TODO: Colonne della tabella
//La prima riga è quella con le intestazioni
$colonne = array("#", "id", "stato", "stg", "gru", "rep", "set");
inserisciRigaTabella( $colonne );


$conn = collegaDb();

$query0 = "SELECT xyz FROM \"$t\" WHERE xyz NOT IN
(SELECT f5cote FROM \"$tCrSet\")";
$result0 = odbc_exec($conn, $query0);

	
//Ciclo su tutti i record estratti
$n = 1;
while (true) {
	
	//Estraggo i risultati e interrompo se sono all'ultimo record
	if ( ($array0 = odbc_fetch_array($result0)) != null) //se ci sono ancora record nella query principale
		list($stato0, $stg0, $gru0, $rep0, $set0) = array_values( $array0 );	
	else //se non ci sono più record nella query principale
		break;
	
	//$set0 estratto da $t è in forma stringa mentre quello in $tCrSet è in forma numerica, quindi trasformo $set0 in numero intero
	//$set0 = intval($set0);
	inserisciRigaTabella( array($n, "", $stato0, $stg0, $gru0, $rep0, $set0) );
	//$page->addBodyContent("$n, $id0, $stato0, $stg0, $gru0, $rep0, $set0<BR>");
	$n++;
	
}
//Coloro la colonna controllata
$table->updateColAttributes(6, "bgcolor='gray'" );

//Coloro la prima riga di intestazione
$table->updateRowAttributes(0, "align='center' valign='center' bgcolor='gray' style='color: white; font-weight: bold'");

//Infine aggiungo la tabella alla pagina
$page->addBodyContent($table->toHTML());









//Infine aggiungo la tabella alla pagina
$page->addBodyContent("<P><PRE>$commFinale</PRE></P>");
$page->addBodyContent("<p><a href='index.html'>[Index]</a></p>");
$page->display();

odbc_close($conn);




?>