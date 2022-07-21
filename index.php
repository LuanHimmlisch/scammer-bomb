<?php

use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Amp\Loop;

require __DIR__ . '/vendor/autoload.php';

function getFile($path)
{
    return explode("\n", file_get_contents($path));
}

define('URL', trim(file_get_contents(__DIR__ . '/url.txt')));
define('DOMAINS', getFile(__DIR__ . '/src/domains.txt'));
define('PASSWORDS', getFile(__DIR__ . '/src/passwords.txt'));
define('NAMES', getFile(__DIR__ . '/src/names.txt'));

Loop::run(function () {
    Loop::repeat(100, function () {
        $name = NAMES[random_int(0, count(NAMES) - 1)];
        $domain = DOMAINS[random_int(0, count(DOMAINS) - 1)];
        $password = PASSWORDS[random_int(0, count(PASSWORDS) - 1)];
        $email = strtolower("$name@$domain");
        $client = HttpClientBuilder::buildDefault();
        $sent = json_encode([
            "email" => $email,
            "password" => $password
        ]);

        $request = new Request(URL, "POST");
        $request->setBody($sent);

        $response = yield $client->request($request);

        $status = $response->getStatus();
        $body = yield $response->getBody()->buffer();

        echo "Status: $status. Sent: $sent. Response: $body\n";
    });
});
