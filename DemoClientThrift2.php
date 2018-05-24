<?php
/***************************************************************************
 *
 * Copyright (c) 2015 koudai.com, Inc. All Rights Reserved
 *
 **************************************************************************/
require_once __DIR__ . '/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__)) . '/gen-php2';
echo $GEN_DIR . PHP_EOL;

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__ . '/lib');
$loader->register();

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;

require_once $GEN_DIR . '/THBaseService.php';
require_once $GEN_DIR . '/Types.php';

try {
    $socket = new TSocket('192.168.219.131', 9090);
    $socket->setSendTimeout(2000);
    $socket->setRecvTimeout(4000);
    $transport = new TBufferedTransport ($socket);
    $protocol = new TBinaryProtocol ($transport);
    $client = new THBaseServiceClient($protocol);
    $transport->open();

    //HOW TO GET
    $tableName = "test";

    $column_1 = new TColumn();
    $column_1->family = 'cf';
    $column_1->qualifier = 'a';

    $column_2 = new TColumn();
    $column_2->family = 'cf';
    $column_2->qualifier = 'a';

    $columnArray = array($column_1, $column_2);

    $get = new TGet();
    $get->row = 'row1';
    $get->columns = $columnArray;

    $arr = $client->get($tableName, $get);

    $results = $arr->columnValues;
    foreach ($results as $result) {
        $qualifier = (string)$result->qualifier;
        $value = $result->value;
        print_r($qualifier . "\n");
        print_r($value . "\n");
    }

    $transport->close();


} catch (TException $tx) {
    print 'TException: ' . $tx->__toString() . ' Error: ' . $tx->getMessage() . "\n";
}


/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
