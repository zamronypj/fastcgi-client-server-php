<?php

use Protocol\FCGI;
use Protocol\FCGI\FrameParser;
use Protocol\FCGI\Record;
use Protocol\FCGI\Record\BeginRequest;
use Protocol\FCGI\Record\Params;
use Protocol\FCGI\Record\Stdin;

include "vendor/autoload.php";

// Let's connect to the local php-fpm daemon directly
$phpSocket = fsockopen('127.0.0.1', 20477, $errorNumber, $errorString);
$packet    = '';

// Prepare our sequence for querying PHP file
$packet .= new BeginRequest(FCGI::RESPONDER);
$packet .= new Params([
    'SCRIPT_FILENAME' => '/var/www/some_file.php',
    'REQUEST_URI' => '/assa/asdads',
    'REQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URIREQUEST_URI' => '/assa/asdads',
]);
$packet .= new Params(['REQUEST_METHOD' => 'GET']);
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
