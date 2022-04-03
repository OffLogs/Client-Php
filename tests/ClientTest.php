<?php declare(strict_types=1);

use OffLogs\Client\OffLogsClient;
use OffLogs\LogLevel;
use OffLogs\LogModel;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    const TestApiToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9zeXN0ZW0iOiJBcHBsaWNhdGlvbiIsImh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL25hbWVpZGVudGlmaWVyIjoiMjQiLCJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9yc2EiOiJNSUlCSWpBTkJna3Foa2lHOXcwQkFRRUZBQU9DQVE4QU1JSUJDZ0tDQVFFQXorRU1XNS84WGFLMU9NeVJUb2JNeHB0ZTYwQW1OemExSmtUeUd6MytFVVl4ZUdkMVpUSUxEU2NOZ3dUSU1VaXhUVkVKeHhDNnhDMElUK1duVDVIY0xwRkMvcHRhT1UzMlBJanNhV1pUWi9hZGZmcFloQVdPM2t5dWZZekZnOUpqc1pyUHY5M1MwTmhMMDZYWit1d0hXTy9Ca2M1WGFhZXg0aUErczZRd3hJcXVweExxek9SYjByUXhBcEZNU3NuQVdmT2RWSktrQ05la3dMUW8zRnZ3SWpGMEsydEFUa0NrUUpLZDB2VGlMYjk5dXB0dUVveERVdjZVdEdGZmlsb1NyNVhtblBGOUl5SzQ2T2dHOWNUOGU3dlNpTFB6NFZWVVBLN2ZEMWdOSmJnRTZvMTJFZEVSRVBsL2puNVMyTHFHSmJEMWc0Q0Y2TGlpRHdHZmFodVJCd0lEQVFBQiIsImlzcyI6Ik9mZkxvZ3MgQXBwbGljYXRpb24gQVBJIiwiYXVkIjoiT2ZmTG9ncyBBcHBsaWNhdGlvbiBBUEkifQ.JVLhhV0Tk4gukS24iUKJql1BACM8y4xTrrF_66nXi04';

    private OffLogsClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new OffLogsClient(self::TestApiToken, false);
    }

    public function testCannotBeCreatedFromInvalidToken()
    {
        $this->expectException(Exception::class);
        new OffLogsClient('');
    }

    public function testCannotSendIfMessageIsEmpty()
    {
        $this->expectException(Exception::class);

        $this->client->push(LogLevel::Error, '');
    }

    public function testCannotSendIfLogLevelIsIncorrect()
    {
        $this->expectException(Exception::class);

        $this->client->push('Fake', 'message');
    }

    public function testShouldSendInformationLog()
    {
        $success = $this->client->push(LogLevel::Information, 'Test information log');
        $this->assertTrue($success);
    }

    public function testShouldSendErrorLog()
    {
        $success = $this->client->push(LogLevel::Error, 'Test information log');
        $this->assertTrue($success);
    }

    public function testShouldSendFatalLog()
    {
        $success = $this->client->push(LogLevel::Fatal, 'Test information log');
        $this->assertTrue($success);
    }

    public function testShouldSendDebugLog()
    {
        $success = $this->client->push(LogLevel::Debug, 'Test information log');
        $this->assertTrue($success);
    }

    public function testShouldSendWarningLog()
    {
        $success = $this->client->push(LogLevel::Warning, 'Test information log');
        $this->assertTrue($success);
    }

    public function testShouldSendExceptionLog()
    {
        $exception = new Exception('This is exception log');
        $success = $this->client->pushException($exception);
        $this->assertTrue($success);
    }

    public function testShouldSendSeveralLogs()
    {
        $arrayToSend = [];
        for ($i = 0; $i <= 90; $i++)
        {
            $arrayToSend[] = new LogModel(LogLevel::Fatal, "Test array $i");
        }
        $success = $this->client->pushArray($arrayToSend);
        $this->assertTrue($success);
    }

    public function testShouldFailIfToManyLogs()
    {
        $this->expectException(Exception::class);

        $arrayToSend = [];
        for ($i = 0; $i <= 100; $i++)
        {
            $arrayToSend[] = new LogModel(LogLevel::Fatal, "Test array $i");
        }
        $success = $this->client->pushArray($arrayToSend);
        $this->assertTrue($success);
    }
}
