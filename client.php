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

$hostName = $argv[1];
$port = (int) $argv[2];

echo "Sending request to $hostName:$port ...\n";

//Open connection to a FastCGI server
$phpSocket = @fsockopen($hostName, $port, $errorNumber, $errorString);

if (!$phpSocket) {
    echo "$errorString [$errorNumber]\n";
    die();
}

$packet    = '';

// Prepare our sequence for querying PHP file
$packet .= new BeginRequest(FCGI::RESPONDER);
$packet .= new Params([
    'SCRIPT_FILENAME' => '/var/www/some_file.php',
    'SCRIPT_NAME' => 'some_file.php',
    'REMOTE_ADDR' => '192.168.2.1',
    'REMOTE_PORT' => '20797',
    'SERVER_ADDR' => '127.0.0.1',
    'SERVER_PORT' => '20797',
    'REQUEST_URI' => '/',
    'QUERY_STRING' => 'test=ok&value=nice',
    'HTTP_USER_AGENT' => 'PHP fcgi client',
]);
$packet .= new Params(['REQUEST_METHOD' => 'GET']);
$packet .= new Params(['DOCUMENT_ROOT' => 'public']);
$packet .= new Params(['REQUEST_SCHEME' => 'http']);
$packet .= new Params(['CONTENT_TYPE' => 'text/html']);
$packet .= new Params();
$packet .= new Stdin();

fwrite($phpSocket, $packet);

$response = '';
while ($partialData = fread($phpSocket, 4096)) {
    $response .= $partialData;
    while (FrameParser::hasFrame($response)) {
        $record = FrameParser::parseFrame($response);
        echo $record;
    };
};

fclose($phpSocket);
