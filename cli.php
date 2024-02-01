<?php
require_once 'Configuration/config.php';

$cliFile = sprintf('%s/Cli/%s.php', __DIR__, $argv[1]);
if (!file_exists($cliFile)) {
   throw new Exception(sprintf("'%s' cliFile n'existe pas", $cliFile));
}

require_once $cliFile;