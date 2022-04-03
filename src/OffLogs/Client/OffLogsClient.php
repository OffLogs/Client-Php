<?php

namespace OffLogs\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use OffLogs\LogLevel;
use OffLogs\LogModel;

class OffLogsClient
{
    const ApiUrl = 'https://api.offlogs.com/log/add';

    private string $apiToken;

    private Client $httpClient;

    public function __construct(string $applicationToken, bool $isVerifySsl = true)
    {
        $this->apiToken = $applicationToken;
        if (empty($this->apiToken))
        {
            throw new \Exception('Application Api token can not be empty');
        }
        $this->httpClient = new Client([
            'curl' => [ CURLOPT_SSL_VERIFYPEER => $isVerifySsl ]
        ]);
    }

    /**
     * @param LogModel[] $logs
     * @return void
     */
    public function pushArray(array $logs): bool
    {
        $logsToSend = [];
        /** @var LogModel $log */
        foreach ($logs as $log)
        {
            $logsToSend[] = $log->toArray();
        }
        $headers = [
            'Authorization' => "Bearer $this->apiToken",
        ];
        $request = new Request('POST', self::ApiUrl, $headers);
        $response = $this->httpClient->send($request, [
            RequestOptions::JSON => [ 'logs' => $logsToSend ],
            RequestOptions::ALLOW_REDIRECTS => false
        ]);
        if ($response->getStatusCode() != 200)
        {
            $responseData = $response->getBody();
            throw new \Exception("OffLogs Client. Sending logs error: $responseData");
        }
        return true;
    }

    /**
     * @param string $logLevel
     * @param string $message
     * @param array $properties
     * @return void
     * @throws \Exception
     */
    public function push(string $logLevel, string $message, array $properties = []): bool
    {
        $log = new LogModel($logLevel, $message, $properties);
        return $this->pushOne($log);
    }

    /**
     * @param LogModel $log
     * @return void
     */
    public function pushOne(LogModel $log): bool
    {
        return $this->pushArray([$log]);
    }

    /**
     * @param \Exception $exception
     * @return void
     * @throws \Exception
     */
    public function pushException(\Exception $exception): bool
    {
        $properties = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
        ];
        $log = new LogModel(
            LogLevel::Fatal,
            $exception->getMessage(),
            $properties,
            $this->parseTraces($exception)
        );
        return $this->pushArray([ $log ]);
    }

    private function parseTraces(\Exception $exception): array
    {
        $result = [];
        foreach ($exception->getTrace() as $trace)
        {
            $file = $trace['file'] ?? '';
            $line = $trace['line'] ?? '';
            $function = $trace['function'] ?? '';
            $class = $trace['class'] ?? '';
            $type = $trace['type'] ?? '';
            $result[] = "$file ($line) \n $class$type$function";
        }
        return $result;
    }
}
