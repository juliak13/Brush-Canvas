<?php

$db = new SQLite3('./database/artbrush.db');

if (!$db) {
    die("Connection failed");
}

?>