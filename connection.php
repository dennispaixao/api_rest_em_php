<?php
$conexao = new mysqli("localhost", "root", "senha", "alunos");

if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}