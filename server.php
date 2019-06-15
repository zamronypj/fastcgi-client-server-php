<?php 

use Lisachenko\Protocol\FCGI;
use Lisachenko\Protocol\FCGI\FrameParser;
use Lisachenko\Protocol\FCGI\Record;
use Lisachenko\Protocol\FCGI\Record\BeginRequest;
use Lisachenko\Protocol\FCGI\Record\Params;
use Lisachenko\Protocol\FCGI\Record\Stdin;

include "vendor/autoload.php";

$server = stream_socket_server("tcp://127.0.0.1:9001" , $errorNumber, $errorString);

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
