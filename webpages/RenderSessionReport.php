<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function RenderSessionReport($result) {
    global $title;
    $title = "Session Report";
    require_once('StaffCommonCode.php');
    staff_header($title);
    ?>
    <p> Here are the results of your search. The report includes Session id, track, title, duration, estimated
        attendance, pocket program text, notes for prospective participants.
    <table class="table table-condensed table-hover">
        <?php
        while (list($sessionid, $trackname, $title, $duration, $estatten, $pocketprogtext, $persppartinfo) = mysqli_fetch_array($result, MYSQLI_NUM)) {
            echo "        <tr>\n";
            echo "            <td rowspan=3 class=\"border0000\" id=\"sessidtcell\">
<a href=\"EditSession.php?id=" . $sessionid . "\"><b>" . $sessionid . "</a>&nbsp;&nbsp;</td>\n";

            echo "            <td class=\"border0000\"><b>" . $trackname . "</td>\n";
            echo "            <td class=\"border0000\"><b>" . htmlspecialchars($title, ENT_NOQUOTES) . "</td>\n";
            echo "            <td class=\"border0000\"><b>" . $duration . " hr</td>\n";
            echo "            <td rowspan=3 class=\"border0000\">" . $estatten . "&nbsp;&nbsp;</td>\n";
            echo "            </tr>\n";
            echo "        <tr><td colspan=3 class=\"border0010\">" . htmlspecialchars($pocketprogtext, ENT_NOQUOTES) . "</td></tr>\n";
            echo "        <tr><td colspan=3 class=\"border0000\">" . htmlspecialchars($persppartinfo, ENT_NOQUOTES) . "</td></tr>\n";
            echo "        <tr><td colspan=5 class=\"border0020\">&nbsp;</td></tr>\n";
        }
        mysqli_free_result($result);
        ?>
    </table>
    <?php
    staff_footer();
}
?>
