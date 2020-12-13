<?php
DEFINE('VERSION', 3.7);
function includeHeader($additionalFiles = array())
{
    $addFileList = '';
    if (!empty($additionalFiles) && is_array($additionalFiles)) {
        foreach ($additionalFiles as $singleFile) {
            $addFileList .=
                '<link rel="stylesheet" href="' . $singleFile . '?v=' . VERSION . '">' . "\r\n";
        }
    }

    return '<!doctype html>' . "\r\n"
        . '<html lang="en">' . "\r\n"
        . '<head>' . "\r\n"
        . '<meta charset="utf-8">' . "\r\n"
        . '<title>Battleship</title>' . "\r\n"
        . '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">' . "\r\n"
        . '<link rel="stylesheet" href="css/styles.css?v=' . VERSION . '">' . "\r\n"
        . '<script src="https://kit.fontawesome.com/2055e89e8c.js" crossorigin="anonymous"></script>' . "\r\n"
        . $addFileList
        . '</head>' . "\r\n"
        . '<body>' . "\r\n";
}

function includeFooter($additionalFiles = array()){
    $addFileList = '';
    if (!empty($additionalFiles) && is_array($additionalFiles)) {
        foreach ($additionalFiles as $singleFile) {
            $addFileList .=
                '<script src="' . $singleFile . '?v=' . VERSION . '"></script>' . "\r\n";
        }
    }

    return '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>' . "\r\n"
        . '<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>' . "\r\n"
        . '<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>' . "\r\n"
        . '<script src="js/header.js?v=' . VERSION . '"></script>' . "\r\n"
        . $addFileList
        . '</body>' . "\r\n"
        . '</html>' . "\r\n";
}
?>