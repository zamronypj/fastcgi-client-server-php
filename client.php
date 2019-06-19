<?php

use Protocol\FCGI;
use Protocol\FCGI\FrameParser;
use Protocol\FCGI\Record;
use Protocol\FCGI\Record\BeginRequest;
use Protocol\FCGI\Record\Params;
use Protocol\FCGI\Record\Stdin;

include "vendor/autoload.php";

if (count($argv) < 2) {
    echo "Usage : php client.php [hostname] [port]\n";
    die();
}

$hostName = $argv[0];
$port = $argv[1];

//Open connection to a FastCGI server
$phpSocket = fsockopen($hostName, $port, $errorNumber, $errorString);
$packet    = '';

// Prepare our sequence for querying PHP file
$packet .= new BeginRequest(FCGI::RESPONDER);
$packet .= new Params([
    'SCRIPT_FILENAME' => '/var/www/some_file.php',
    'REMOTE_ADDR' => '127.0.0.1',
    'REQUEST_URI' => '/',
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
