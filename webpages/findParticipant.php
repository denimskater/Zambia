<?php 
    require_once ('db_functions.php');
	$page = $_GET['page']; 
	$limit = $_GET['rows']; 
	$sidx = $_GET['sidx']; 
	$sord = $_GET['sord']; 
	if(!$sidx) $sidx =1; 

	if(isset($_GET["nm_mask"])) $nm_mask = mb_convert_encoding ( $_GET['nm_mask'], 'latin1', 'utf8' ); else $nm_mask = ""; 
	
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }

// calculate the number of rows for the query. We need this for paging the result 
$SQL = "SELECT COUNT(*) AS count from CongoDump c LEFT JOIN Participants d on c.badgeid = d.badgeid  WHERE d.interested != '2' AND d.interested != '3' ";
if ($nm_mask) {
	$SQL .= "AND (c.firstname like '%".$nm_mask."%' OR c.lastname like '%".$nm_mask."%' OR d.pubsname like '%".$nm_mask."%')";
}
$result = mysql_query($SQL);

$row = mysql_fetch_array($result,MYSQL_ASSOC); 
$count = $row['count']; 

// calculate the total pages for the query 
if( $count > 0 ) { 
              $total_pages = ceil($count/$limit); 
} else { 
              $total_pages = 0; 
} 

// if for some reasons the requested page is greater than the total 
// set the requested page to total page 
if ($page > $total_pages) $page=$total_pages;

// calculate the starting position of the rows 
$start = $limit*$page - $limit;

// if for some reasons start position is negative set it to 0 
// typical case is that the user type 0 for the requested page 
if($start <0) $start = 0; 

// the actual query for the grid data 
$SQL = "SELECT c.firstname, c.lastname, c.badgename, c.badgeid, c.email, d.pubsname from CongoDump c LEFT JOIN Participants d on c.badgeid = d.badgeid WHERE d.interested != '2' AND d.interested != '3' ";
if ($nm_mask) {
	$SQL .= "AND (c.firstname like '%".$nm_mask."%' OR c.lastname like '%".$nm_mask."%' OR d.pubsname like '%".$nm_mask."%')";
}
$SQL .="ORDER BY $sidx $sord LIMIT $start , $limit";

/*
 * NOTE: since the tables are latin1 and browsers expect UTF8 (especially when using AJAX)
 * we ask the DB to send the data back as UTF8
 */
$result = mysql_query( $SQL ) or die("Couldn't execute query.".mysql_error()); 

// we should set the appropriate header information
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
              header("Content-type: application/xhtml+xml"); 
} else {
          header("Content-type: text/xml");
}
echo "<?xml version='1.0'?>";
echo "<rows>";
echo "<page>".$page."</page>";
echo "<total>".$total_pages."</total>";
echo "<records>".$count."</records>";

// be sure to put text data in CDATA
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<row id='".$row[badgeid]."'>";      
            echo "<cell><![CDATA[". htmlentities($row[firstname]) ." ". htmlentities($row[lastname]) . "]]></cell>";
            echo "<cell><![CDATA[". htmlentities($row[pubsname])."]]></cell>";
            echo "<cell><![CDATA[". $row[email]."]]></cell>";
            echo "<cell>". $row[badgeid]."</cell>";
echo "</row>";
}
echo "</rows>"; 
?>