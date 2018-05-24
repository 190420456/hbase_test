<?php
/***************************************************************************
 * https://mshk.top/2014/08/php-golang-thrift-hbase/
 * Copyright (c) 2015 koudai.com, Inc. All Rights Reserved
 *
 **************************************************************************/
require_once __DIR__ . '/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__)) . '/gen-php1';
echo $GEN_DIR . PHP_EOL;

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__ . '/lib');
$loader->register();

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;
use Hbase\HbaseClient;
use Hbase\ColumnDescriptor;

require_once $GEN_DIR . '/Hbase.php';
require_once $GEN_DIR . '/Types.php';

try {
    $socket = new TSocket('192.168.219.131', 9090);
    $socket->setSendTimeout(2000);
    $socket->setRecvTimeout(4000);
    $transport = new TBufferedTransport ($socket);
    $protocol = new TBinaryProtocol ($transport);
    $client = new HbaseClient($protocol);
    $transport->open();

    for ($i = 0; $i < 10000; $i++) {
        $tables = $client->getTableNames();
        foreach ($tables as $name) {
            echo("  found: {$name}\n");
        }
    }


    $descriptors = $client->getColumnDescriptors($tables[0]);
    asort($descriptors);
    foreach ($descriptors as $col) {
        echo("\tcolumn: {$col->name}, maxVer: {$col->maxVersions}\n");
    }


} catch (TException $tx) {
    print 'TException: ' . $tx->__toString() . ' Error: ' . $tx->getMessage() . "\n";
}


function createTable($client)
{
    $columns = array(
        new ColumnDescriptor(array(
            'name' => 'id',
            'maxVersions' => 10
        )),
        new ColumnDescriptor(array(
            'name' => 'name:'
        )),
        new ColumnDescriptor(array(
            'name' => 'score:'
        ))
    );
    $client->createTable("test2", $columns);
}


/* vim: set expandtab ts=4 sw=4 sts=4 tw=100: */
?>
