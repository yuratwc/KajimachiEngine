<?php

namespace kajimachi;

date_default_timezone_set(TIMEZONE);

function include_dir($dir)
{
    $files = glob($dir);
    foreach($files as $file)
    {
        require_once($file);
    }
}
