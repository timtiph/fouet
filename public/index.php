<?php

declare(strict_types=1);
# FRONT CONTROLLER


define('ROOT', str_replace('/public', '', __DIR__));
// echo 'DIR : ' . __DIR__ . ' et ROOT : ' . ROOT . '<br>';

require_once ROOT . '/vendor/autoload.php';


try {
    $app = new Tmoi\Foundation\App();
    $app->render();
} catch (Error $e){
    echo '<br>Problème avec la class App()<br>'.'voir index.php<br>' ;
    echo $e;
}