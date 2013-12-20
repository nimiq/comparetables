<?php
/**
 * @author nimiq
 * @copyright 2007
 */
 
require_once 'funzioni_fns.php';
require_once 'HTML/Page2.php';
require_once 'HTML/Table.php';


//TODO: nota sulla tabella
$commFinale = "NOTA: c'e una corrispondenza 1 a 1 tra i record delle tabelle nelle varie fasi di conversione
La chiave univoca logica per la tabella e': (filacc, numacc, datacc, artacc)
Quindi basta controllare il numero di record nelle varie tabelle per la verifica inversa
PERO' HO IMPORTATO SOLO I PRIMI 10000 RECORD, per cui questo controllo perde di significato
Ho controllato pero che nel DB le tabelle 04 dopo i vari livelli di conversione avevano la stessa cardinalita";


//TODO: imposto il nome della tabella in esame
$t_4 = $t04_4;
$t_3 = $t04_3;
$t_2 = $t04_2;
$t_1 = $t04_1;
$t   = $t04;


//Creo la pagina HTML
$page = new HTML_Page2();
$page->setTitle('CmprTables');


//Aggiungo alla pagina HTML una frase
$page->addBodyContent("<H1>Tabella: $t</H1>");

$conn = collegaDb();

//Cardinalità della tabella 4
$query4 = "SELECT COUNT(*) FROM \"$t_4\";"; 
list($n4) = array_values( odbc_fetch_array(  odbc_exec($conn, $query4)  ) );
$page->addBodyContent("Cardinalita $t_4: $n4<BR>");
	
//Cardinalità della tabella 3
$query3 = "SELECT COUNT(*) FROM \"$t_3\";"; 
list($n3) = array_values( odbc_fetch_array(  odbc_exec($conn, $query3)  ) );
$page->addBodyContent("Cardinalita $t_3: $n3<BR>");
	
//Cardinalità della tabella 3
$query2 = "SELECT COUNT(*) FROM \"$t_2\";"; 
list($n2) = array_values( odbc_fetch_array(  odbc_exec($conn, $query2)  ) );
$page->addBodyContent("Cardinalita $t_2: $n2<BR>");
	
//Cardinalità della tabella 1
$query1 = "SELECT COUNT(*) FROM \"$t_1\";"; 
list($n1) = array_values( odbc_fetch_array(  odbc_exec($conn, $query1)  ) );
$page->addBodyContent("Cardinalita $t_1: $n1<BR>");
	
//Cardinalità della tabella finale
$queryf = "SELECT COUNT(*) FROM \"$t\";"; 
list($nf) = array_values( odbc_fetch_array(  odbc_exec($conn, $queryf)  ) );
$page->addBodyContent("Cardinalita $t: $nf<BR><BR>");

$page->addBodyContent("RISULTATO: ");
if ($n4 == $n3 && $n4 == $n2 && $n4 == $n1 && $n4 == $nf) {
	$page->addBodyContent("<B>OK!!</B>");
} else {
	$page->addBodyContent("<B>NOOOOOOOOOOK!!</B>");
}
	


//Infine aggiungo la tabella alla pagina
$page->addBodyContent("<P><PRE>$commFinale</PRE></P>");
$page->addBodyContent("<p><a href='index.html'>[Index]</a></p>");
$page->display();

odbc_close($conn);




?>