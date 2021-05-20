<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

function get_transient( $transient ) {
    return null;
}

function set_transient( $transient, $value, $expiration ) {
    // Intentionally left blank
}

function delete_transient( $transient ) {
    // Intentionally left blank
}

function wp_remote_get( $url, $args = array() ) {
    return null;
}

function is_wp_error() {
    return false;
}

function add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
    // Intentionally left blank
}

function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
    // Intentionally left blank
}

function wp_timezone() {
    return new DateTimeZone( date_default_timezone_get() );
}

final class nameDaysTest extends TestCase
{
    public function testClassLoaded(): void
    {
        $this->assertTrue( class_exists( 'NameDaysProvider' ) );
    }

    public function testSingleName() {
        $stub = $this->getMockBuilder( NameDaysProvider::class )->setMethods(['getRemoteContent'])->getMock();

        $stub->method( 'getRemoteContent' )->willReturn( array( 'body' => '<p class="vardadieniai"><a>Name 1</a></p>' ) );

        $this->assertSame( '<ul class="todays_name_days"><li>Name 1</li></ul>', $stub->processTodayNameDaysShortcode( array() ) );
    }

    public function testMultipleNames() {
        $stub = $this->getMockBuilder( NameDaysProvider::class )->setMethods(['getRemoteContent'])->getMock();

        $stub->method( 'getRemoteContent' )->willReturn( array( 'body' => '<p class="vardadieniai"><a>Name 1</a><a>Name 2</a><a>Name 3</a></p>' ) );

        $this->assertSame( '<ul class="todays_name_days"><li>Name 1</li><li>Name 2</li><li>Name 3</li></ul>', $stub->processTodayNameDaysShortcode( array() ) );
    }

    public function testHTMLWithAttributes() {
        $stub = $this->getMockBuilder( NameDaysProvider::class )->setMethods(['getRemoteContent'])->getMock();

        $stub->method( 'getRemoteContent' )->willReturn(
            array( 'body' => '<p aria-label="true" class="vardadieniai" title="Some Text"><a href="https://www.example.com">Name 1</a><a href="#">Name 2</a><a>Name 3</a></p>' )
        );

        $this->assertSame( '<ul class="todays_name_days"><li>Name 1</li><li>Name 2</li><li>Name 3</li></ul>', $stub->processTodayNameDaysShortcode( array() ) );
    }
}
