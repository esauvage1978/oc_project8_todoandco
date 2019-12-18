<?php

// tests/bootstrap.php
if (isset($_ENV['BOOTSTRAP_FIXTURES_LOAD'])) {
    $command = 'php '.dirname(__DIR__, 1).'/bin/console doctrine:database:drop --force --env=test';
    passthru($command);
    $command = 'php '.dirname(__DIR__, 1).'/bin/console doctrine:database:create --env=test';
    passthru($command);
    $command = 'php '.dirname(__DIR__, 1).'/bin/console doctrine:schema:create --env=test';
    passthru($command);
    // executes the "php bin/console cache:clear" command
    $command = 'php '.dirname(__DIR__, 1).'/bin/console doctrine:fixtures:load -n --env=test';
    passthru($command);
}
require dirname(__DIR__, 1).'/config/bootstrap.php';
