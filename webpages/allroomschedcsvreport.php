<?php
// Copyright (c) 2015-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
$ConStartDatim = CON_START_DATIM; // make it a variable so it can be substituted
$query = "SET group_concat_max_len=25000";
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
mysqli_free_result($result);
$query = <<<EOD
SELECT
            R.roomname,
            R.function,
            DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time', 
            CASE
                WHEN HOUR(S.duration) < 1 THEN CONCAT(DATE_FORMAT(S.duration,'%i'),'min')
                WHEN MINUTE(S.duration)=0 THEN CONCAT(DATE_FORMAT(S.duration,'%k'),'hr')
                ELSE CONCAT(DATE_FORMAT(S.duration,'%k'),'hr ',DATE_FORMAT(S.duration,'%i'),'min')
                END AS 'duration',
            T.Trackname,
            S.sessionid,
            S.title,
            GROUP_CONCAT(CONCAT(P.pubsname,' (',P.badgeid,')') SEPARATOR '; ') AS 'Participants' 
    FROM
            Sessions S
       JOIN Schedule SCH USING (sessionid)
       JOIN Rooms R USING (roomid)
  LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
  LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  LEFT JOIN Tracks T ON T.trackid=S.trackid
    GROUP BY
            SCH.scheduleid 
    ORDER BY
            R.roomname, SCH.starttime
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
if (mysqli_num_rows($result) == 0) {
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $title = "Send CSV file of Program Packet Merge";
    staff_header($title);
    $message = "Report returned no records.";
    echo "<p>" . $message . "\n";
    staff_footer();
    exit();
}
header('Content-disposition: attachment; filename=allroomsched.csv');
header('Content-type: text/csv');
echo "Room Name, Room Function, Start Time, Duration, Track, Session ID, Title, Participants\n";
while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    $betweenValues = false;
    foreach ($row as $value) {
        if ($betweenValues) echo ",";
        if (strpos($value, "\"") !== false) {
            $value = str_replace("\"", "\"\"", $value);
            echo "\"$value\"";
        } elseif (strpos($value, ",") !== false or strpos($value, "\n") !== false) {
            echo "\"$value\"";
        } else {
            echo $value;
        }
        $betweenValues = true;
    }
    echo "\n";
}
mysqli_free_result($result);
exit();
?>
