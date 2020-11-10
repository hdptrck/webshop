<?php
require('./conf/db.conf.php');

spl_autoload_register(function ($class_name) {
    include('class/'.$class_name.'.php');
});

function dnd($var)
{
    die(var_dump($var));
}