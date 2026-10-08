<?php

namespace SMART\Test\Api;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use SMART\Api\Query;

class QueryTest extends TestCase
{
    /** @test */
    public function it_builds_include_lists_without_indexes()
    {
        $this->assertSame('include[]=owner&include[]=groups', Query::build(['include' => ['owner', 'groups']]));
    }

    /** @test */
    public function it_builds_nested_filters()
    {
        $this->assertSame('filter[state]=paid', Query::build(['filter' => ['state' => 'paid']]));
        $this->assertSame('fields[employees][]=id', Query::build(['fields' => ['employees' => ['id']]]));
    }

    /** @test */
    public function it_builds_filter_lists()
    {
        $this->assertSame('filter[name][]=a&filter[name][]=b', Query::build(['filter' => ['name' => ['a', 'b']]]));
    }

    /** @test */
    public function it_converts_booleans()
    {
        $this->assertSame('a=true&b=false', Query::build(['a' => true, 'b' => false]));
    }

    /** @test */
    public function it_skips_nulls()
    {
        $this->assertSame('b=1', Query::build(['a' => null, 'b' => 1, 'c' => ['d' => null]]));
    }

    /** @test */
    public function it_formats_dates()
    {
        $this->assertSame('from=2024-03-05', Query::build(['from' => new DateTime('2024-03-05')]));
        $this->assertSame(
            'from=2024-03-05T13%3A00%3A00%2B00%3A00',
            Query::build(['from' => new DateTime('2024-03-05 13:00:00', new DateTimeZone('UTC'))])
        );
    }

    /** @test */
    public function it_url_encodes_values()
    {
        $this->assertSame('q=a%20b%26c', Query::build(['q' => 'a b&c']));
    }
}
