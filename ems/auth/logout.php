<?php
session_start();

/* UNSET ALL SESSION VARIABLES */
session_unset();

/* DESTROY SESSION */
session_destroy();

/* REDIRECT TO LOGIN PAGE */
header("Location: ../index.php");
exit;
