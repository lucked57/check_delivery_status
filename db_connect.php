<?php


$mysqli = new mysqli('d78806.mysql.zonevs.eu','d78806sa263893','6dMXKjqfBVF269824','d78806sd300806'); // хост, логин, пароль, имя бд





if(mysqli_connect_errno())
{
    echo 'Ошибка в подключении к базе данных ('.mysqli_connect_errno().'): '. mysqli_connect_error();
    exit();
}
$mysqli->query ("SET NAMES UTF8");

if ($mysqli->connect_errno) {
    die('Ошибка соединения: ' . $mysqli->connect_errno);
}
