<?php
// zakonczenie sesji
session_start();
session_unset();
session_destroy();
header("Location: index.html");
exit();
?>