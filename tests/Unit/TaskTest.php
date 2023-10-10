<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    // when testing API endpoints, we are inherently 
    // testing a larger piece of functionality that involves
    // HTTP request handling, database interactions, and
    // authentication/authorization. This kind of testing is typically
    // categorized as integration or feature testing.
    // thats why i have not written any test here. 
}
