<?php
require_once('BrainstormCommonCode.php');
$title="All Suggestions";
$showlinks=$_GET["showlinks"];
$_SESSION['return_to_page']="ViewPrecis.php?showlinks=$showlinks";
if ($showlinks=="1") {
  $showlinks=true;
}
elseif ($showlinks="0") {
  $showlinks=false;
}
if (prepare_db()===false) {
  $message="Error connecting to database.";
  RenderError($title,$message);
  exit ();
}
$query = <<<EOD
SELECT 
    sessionid, 
    trackname, 
    null typename, 
    title, 
    CASE
      WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
      ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    estatten,
    pocketprogtext,
    progguiddesc,
    persppartinfo
  FROM
      Sessions,
      Tracks,
      SessionStatuses 
  WHERE
    Sessions.trackid=Tracks.trackid and
    SessionStatuses.statusid=Sessions.statusid and
    SessionStatuses.statusname in ('Edit Me','Brainstorm','Vetted','Assigned','Scheduled') and
    Sessions.invitedguest=0
  ORDER BY
    trackname,
    title
EOD;

if (($result=mysql_query($query,$link))===false) {
  $message="Error retrieving data from database.";
  RenderError($title,$message);
  exit ();
}

brainstorm_header($title);
echo "<P>This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</P>\n";
echo "<P>We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces;";
echo " checking general feasability; finding needed people to present; looking for an appropiate time and location;";
echo " rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</P>\n";
echo "<P>If you want to help, email us at: ";
echo "<A HREF=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</A></P>\n";
echo "<P>This list is sorted by Track and then Title.</P>\n";
RenderPrecis($result,$showlinks);
correct_footer();
exit();
?> 
