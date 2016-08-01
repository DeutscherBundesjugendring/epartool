<?php

spl_autoload_register(function ($className)
{
    $filename = "src/" . str_replace('\\', '/', $className) . ".php";
    if (file_exists($filename)) {
        require($filename);
        return true;
    }

    return false;
});
