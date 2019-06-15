<?php

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\FrameParser;
use Lisachenko\Protocol\FCGI\Record;
use Lisachenko\Protocol\FCGI\Record\BeginRequest;
use Lisachenko\Protocol\FCGI\Record\Params;
use Lisachenko\Protocol\FCGI\Record\Stdin;

include "vendor/autoload.php";

// Let's connect to the local php-fpm daemon directly
$phpSocket = fsockopen('127.0.0.1', 9001, $errorNumber, $errorString);
$packet    = '';

// Prepare our sequence for querying PHP file
$packet .= new BeginRequest(FCGI::RESPONDER);;
$packet .= new Params(['SCRIPT_FILENAME' => '/var/www/some_file.php']);
$packet .= new Params();
$packet .= new Stdin();

fwrite($phpSocket, $packet);

$response = '';
while ($partialData = fread($phpSocket, 4096)) {
    $response .= $partialData;
    while (FrameParser::hasFrame($response)) {
        $record = FrameParser::parseFrame($response);
        var_dump($record);
    };
};

fclose($phpSocket);
