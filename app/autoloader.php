<?php
spl_autoload_register(function ($class_name) {
    $folders = [
        'Controller/',
        'Model/',
        'Model/Interface',
        'Model/Manager',
        'Service/',
        'utils/',
    ];

    foreach ($folders as $folder){
        if(file_exists('./../app/' . $folder . $class_name.'.php')){
            require_once $folder.$class_name.'.php';
            return;
        }
    }

});