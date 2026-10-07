<?php

declare(strict_types=1);

namespace SMART\Test\NestPension;

use NestPension\Environment\Environment;
use PHPUnit\Framework\TestCase;

class NestEnvironmentTest extends TestCase
{
    protected function setUp(): void
    {
        Environment::reset();
    }

    public function testSandboxUsesNestPtfWebServices(): void
    {
        $env = Environment::getInstance();
        $env->setToSandbox();

        $this->assertSame('https://netews.nestpensions.org.uk', $env->getBaseUrl());
    }

    public function testLiveUsesNestWebServices(): void
    {
        $env = Environment::getInstance();
        $env->setToLive();

        $this->assertSame('https://ws.nestpensions.org.uk', $env->getBaseUrl());
    }

    public function testBaseUrlCanBeOverriddenAndCleared(): void
    {
        $env = Environment::getInstance();
        $env->setBaseUrl('https://example.test/nest/');

        $this->assertSame('https://example.test/nest', $env->getBaseUrl());

        $env->setBaseUrl('');

        $this->assertSame(Environment::SANDBOX_BASE_URL, $env->getBaseUrl());
    }

    public function testProxyIsOptional(): void
    {
        $env = Environment::getInstance();

        $this->assertNull($env->getProxy());

        $env->setProxy('http://user:pass@203.0.113.10:3128');
        $this->assertSame('http://user:pass@203.0.113.10:3128', $env->getProxy());

        $env->setProxy(null);
        $this->assertNull($env->getProxy());
    }
}
