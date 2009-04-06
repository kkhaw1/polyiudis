<?php

if ( isset($_GET['userid']) ) setcookie("userid", $_GET['userid']);
if ( isset($_GET['username']) ) setcookie("username", $_GET['username']);
if ( isset($_GET['roleid']) ) setcookie("roleid", $_GET['roleid']);

?>

<html>
  <head>
    <title></title>
  </head>
<?php
  $role = $_GET[role];
  $jsCmd = "window.location.href='http://pdc-amd01.poly.edu/~kkhawa01/polyiudis/$role.php';";
  echo "<body onload=\"" . $jsCmd. "\">";
?>
  </body>
</html>
