<?php

namespace SMART\Test\Api;

use PHPUnit\Framework\TestCase;
use SMART\Api\Hosts;
use SMART\Exceptions\InvalidVariableValueException;

class HostsTest extends TestCase
{
    /** @test */
    public function it_resolves_api_urls_per_environment()
    {
        $this->assertSame('https://api.dev.autoenrolment.co.uk', Hosts::url('dev'));
        $this->assertSame('https://api.sandbox.autoenrolment.co.uk', Hosts::url('sandbox'));
        $this->assertSame('https://api.autoenrolment.co.uk', Hosts::url('live'));
    }

    /** @test */
    public function it_resolves_other_services()
    {
        $this->assertSame('https://id.sandbox.autoenrolment.co.uk', Hosts::url('sandbox', Hosts::IDENTITY));
        $this->assertSame('https://account-claiming.autoenrolment.co.uk', Hosts::url('live', Hosts::ACCOUNT_CLAIMING));
    }

    /** @test */
    public function it_swaps_the_service_of_a_custom_base_url()
    {
        $this->assertSame('https://id.proxy.example.com', Hosts::swapService('https://api.proxy.example.com/', Hosts::IDENTITY));
    }

    /** @test */
    public function it_rejects_invalid_environments()
    {
        $this->expectException(InvalidVariableValueException::class);

        Hosts::url('staging');
    }
}
