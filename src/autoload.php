<?php

spl_autoload_register(function($class){
    $c = explode('\\', $class);
    if($c[0] == 'karms' & $c[1] == 'TripleTriad') {
        require  __DIR__ .  "/{$c[2]}.php";
    }
}, true);