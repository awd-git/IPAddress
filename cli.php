<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/22/15
 * Time: 11:55 AM
 */

use IPAddress\Environment\CliOptions;

if (PHP_SAPI !== 'cli') {
    exit('This script is intended for the command line');
}

include('app/bootstrap.php');

try {
    $cliOptions = new CliOptions(getopt(CliOptions::$shortops, CliOptions::$longops));
    $ops = $cliOptions->getValues();
} catch (Exception $e) {
    fwrite(STDOUT, $e->getMessage() . PHP_EOL);
    fwrite(STDOUT, "Add --cidr <CIDR notation>\n");

    return null;
}

// lets run this thing
$container = \DI\ContainerBuilder::buildDevContainer();

/** @var \IPAddress\Calculator $calc */
$calc = $container->get('\IPAddress\Calculator');
if ($calc->calc(CliOptions::getCIDR()) === true) {
    $range = $calc->getNetworkRange();
    printf("Min Network Range [%s]\n", $range['min']);
    printf("Max Network Range [%s]\n", $range['max']);
    printf("Number of IP addresses:%s\n", $range['hosts']);
} else {
    $errors = $calc->getErrorMessages();
    printf("%s\n", $errors[0]);
}

