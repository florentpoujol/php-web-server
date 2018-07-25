<?php

$socket = stream_socket_server('tcp://0.0.0.0:8000', $errno, $errstr);
if (!$socket) {
  echo "Error starting the server: $errstr ($errno)" . PHP_EOL;
  return;
}

echo 'Server started on 0.0.0.0:8000' . PHP_EOL;

require_once 'functions.php';

while ($connection = stream_socket_accept($socket)) {
    /** @var string[] $headerLines */
    $headerLines = [];

	while ($line = fgets($connection)) {
		if ($line === PHP_EOL) {
            // this line is the one between the headers and the body

            // $body = fread($connection, 1024);
            // reading body seems to "hang out"
            // many the function ways for some termination signal from the client ?
            // let's ignore the body for now
			break;
		}

		$headerLines[] = $line;
	}

	echo $headerLines[0] . PHP_EOL;

    // parse headers
    $requestInfos = parse_headers($headerLines);

    echo var_export($requestInfos, true) .PHP_EOL;
    exit;

	fwrite($connection, 'This is the response' . PHP_EOL);
	fclose($connection);
}

fclose($socket);
