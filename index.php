<?php
if (PHP_SAPI !== 'cli') {
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

use Hth\ImageUrlProcessor;
use Hth\ZbarCliReader;
use Hth\StdoutLogger;
use Hth\InputManager;
use Aws\Sdk;
use Aws\Middleware;
use Psr\Http\Message\RequestInterface;

$logger = new StdoutLogger();
$reader = new ZbarCliReader($logger);
$imageProcessor = new ImageUrlProcessor($reader);

$inputManager = new InputManager($argv);

if ($inputManager->hasInputErrors()) {
    echo implode("\n", $inputManager->getErrorList()) . "\n";
    exit(1);
}

try {
    $resultList = $imageProcessor->process($inputManager->getFileUrl());
} catch (\Exception $e) {
    $resultList = [];
}

echo 'Barcode recognition result' . "\n";
var_dump($resultList);

$sdk = new Sdk([
    'region'   => $inputManager->getAwsRegion(),
    'version'  => 'latest',
]);

$lambdaClient = $sdk->createLambda([
    'credentials' => [
        'key'    => $inputManager->getAwsKey(),
        'secret' => $inputManager->getAwsSecret(),
    ],
]);

$args = [
    'FunctionName'    => $inputManager->getLambdaFunctionName(),
    'InvocationType'  => 'Event',
    //'LogType'       => 'string',
    //'ClientContext' => 'string',
    'Payload'         => json_encode($resultList),
    'Qualifier'       => $inputManager->getLambdaFunctionQualifier(),
];

try {
    $command = $lambdaClient->getCommand('Invoke', $args);
    $traceId = $inputManager->getFunctionTraceId();
    if ($traceId) {
        $command->getHandlerList()->appendBuild(
            Middleware::mapRequest(function (RequestInterface $request) use ($traceId) {
                // Return a new request with the added header
                return $request->withHeader('X-Amzn-Trace-Id', $traceId);
            }),
            'add-header'
        );
    }
    $lambdaClient->execute($command);
} catch (\Exception $e) {
    echo $e->getMessage() . "\n" . $e->getTraceAsString();
    exit(1);
}


