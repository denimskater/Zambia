<?php 
    require_once ('db_functions.php');

    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }

$id = $_GET["id"];

$args = explode('_', $id);

$SQL  = "SELECT postmail, french, other_fr, language_fr, english, other_en, language_en ";
$SQL .= "FROM ".PARTICIPANT_SOURCE.".rawdata WHERE mbox LIKE '".$args[0]."' AND  message_number LIKE '" . $args[1] . "'";
$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());

// we should set the appropriate header information
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
              header("Content-type: application/xhtml+xml;charset=latin-1"); 
} else {
          header("Content-type: text/xml;charset=latin-1");
}
echo "<?xml version='1.0' encoding='latin-1'?>";

echo "<rows>";
// be sure to put text data in CDATA
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
	echo "<row>";
	echo "<cell><![CDATA[". htmlentities($row[postmail])."]]></cell>\n";
	echo "<cell>". $row[french]."</cell>";
	echo "<cell>". $row[other_fr]."</cell>";
	echo "<cell><![CDATA[". htmlentities($row[language_fr])."]]></cell>";
	echo "<cell>". $row[english]."</cell>";
	echo "<cell>". $row[other_en]."</cell>";
	echo "<cell><![CDATA[". htmlentities($row[language_en])."]]></cell>";
	echo "</row>";
}
echo "</rows>";				
?>
