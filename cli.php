<?php
/**
 * Created by PhpStorm.
 * User: adriar
 * Date: 7/22/15
 * Time: 11:55 AM
 */

if (PHP_SAPI !== 'cli') {
    exit('This script is intended for the command line');
}

include('app/bootstrap.php');

// lets run this thing
$container = \DI\ContainerBuilder::buildDevContainer();

/** @var \IPAddress\Calculator $calc */
$calc = $container->get('\IPAddress\Calculator');
$request = getopt('', ['cidr:']);

if (isset($request['cidr'])) {
    if ($calc->calc($request['cidr']) === true) {
        $range = $calc->getNetworkRange();
        printf("Min Network Range [%s]\n", $range['min']);
        printf("Max Network Range [%s]\n", $range['max']);
        printf("Number of IP addresses:%s\n", $range['hosts']);
    } else {
        $errors = $calc->getErrorMessages();
        printf("%s\n", $errors[0]);
    }
} else {
    echo "No Request received\n";
    echo "Add --cidr <CIDR notation>\n";
}
