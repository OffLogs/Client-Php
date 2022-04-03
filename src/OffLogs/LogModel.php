<?php

namespace OffLogs;

/**
 * Class LogModel
 * @package OffLogs
 *
 * @property string $level
 * @property string $message
 * @property array $traces
 * @property array $properties
 */
class LogModel
{
    public $level;

    public $message;

    public $traces = [];

    public $properties = [];

    /**
     * @throws \Exception
     */
    public function __construct(string $level, string $message, array $properties = [], array $traces = [])
    {
        $this->level = $level;
        $this->message = $message;
        if (empty($this->message))
        {
            $this->throwError('The message can\'t be empty');
        }
        if (!LogLevel::isCorrect($this->level))
        {
            $logLevels = print_r(LogLevel::$levels, true);
            $this->throwError("Log level is incorrect. It's can be: $logLevels");
        }
        $this->properties = $properties;
        $this->traces = $traces;
    }

    /**
     * @param string $message
     * @throws \Exception
     */
    private function throwError(string $message)
    {
        throw new \Exception("OffLogs Client. $message");
    }

    public function toArray(): array
    {
        $result = [
            'level' => $this->level,
            'message' => $this->message,
            'timestamp' => date('Y-m-d\TH:i:sp')
        ];
        if (count($this->traces)) {
            $result['traces'] = $this->traces;
        }
        if (count($this->properties)) {
            $result['properties'] = $this->properties;
        }
        return $result;
    }
}
