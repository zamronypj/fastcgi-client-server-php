<?php

use Protocol\FCGI;
use Protocol\FCGI\FrameParser;
use Protocol\FCGI\Record;
use Protocol\FCGI\Record\BeginRequest;
use Protocol\FCGI\Record\Params;
use Protocol\FCGI\Record\Stdin;

include "vendor/autoload.php";

$server = stream_socket_server("tcp://127.0.0.1:20477" , $errorNumber, $errorString);

// Just take the first one request and process it
$phpSocket = stream_socket_accept($server);

$response = '';
while ($partialData = fread($phpSocket, 4096)) {
    $response .= $partialData;
    while (FrameParser::hasFrame($response)) {
        $record = FrameParser::parseFrame($response);
        var_dump($record);
    };
};

// We don't respond correctly here, it's a task for your application

fclose($phpSocket);
fclose($server);
