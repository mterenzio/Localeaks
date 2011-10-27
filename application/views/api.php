<?php
//echo json_encode($orgs);
foreach ($orgs as $org) {
echo "<option value=\"".$org->org_id."\">".$org->org_name."</option>";

}
?>