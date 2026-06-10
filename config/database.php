<?php

$qmanager = new mysqli("localhost", "root", "", "qm");

if ($qmanager->connect_error) {
    die("Erro de conexão");
}
