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
    public function testMainClassLoaded(): void
    {
        $this->assertTrue( class_exists( 'NameDaysProvider' ) );
    }

    public function testCotnentGeneratorLoaded(): void
    {
        $this->assertTrue( class_exists( 'ContentGenerator' ) );
    }

    public function testSingleName() {
        $contentGenerator = new ContentGenerator();

        $stub = $this->getMockBuilder( NameDaysProvider::class )->setConstructorArgs(array($contentGenerator))->setMethods(['getRemoteContent'])->getMock();

        $stub->method( 'getRemoteContent' )->willReturn( array( 'body' => '<p class="vardadieniai"><a>Name 1</a></p>' ) );

        $this->assertSame( '<ul class="todays_name_days"><li>Name 1</li></ul>', $stub->processTodayNameDaysShortcode( array() ) );
    }

    public function testMultipleNames() {
        $contentGenerator = new ContentGenerator();

        $stub = $this->getMockBuilder( NameDaysProvider::class )->setConstructorArgs(array($contentGenerator))->setMethods(['getRemoteContent'])->getMock($contentGenerator);

        $stub->method( 'getRemoteContent' )->willReturn( array( 'body' => '<p class="vardadieniai"><a>Name 1</a><a>Name 2</a><a>Name 3</a></p>' ) );

        $this->assertSame( '<ul class="todays_name_days"><li>Name 1</li><li>Name 2</li><li>Name 3</li></ul>', $stub->processTodayNameDaysShortcode( array() ) );
    }

    public function testHTMLWithAttributes() {
        $contentGenerator = new ContentGenerator();

        $stub = $this->getMockBuilder( NameDaysProvider::class )->setConstructorArgs(array($contentGenerator))->setMethods(['getRemoteContent'])->getMock($contentGenerator);

        $stub->method( 'getRemoteContent' )->willReturn(
            array( 'body' => '<p aria-label="true" class="vardadieniai" title="Some Text"><a href="https://www.example.com">Name 1</a><a href="#">Name 2</a><a>Name 3</a></p>' )
        );

        $this->assertSame( '<ul class="todays_name_days"><li>Name 1</li><li>Name 2</li><li>Name 3</li></ul>', $stub->processTodayNameDaysShortcode( array() ) );
    }

    public function testContentGenerator() {
        $contentGenerator = new ContentGenerator();

        $sampleData = array( 'Name 1', 'Name 2', 'Name 3' );

        $this->assertSame( '<ul class="todays_name_days"><li>Name 1</li><li>Name 2</li><li>Name 3</li></ul>', $contentGenerator->generate( $sampleData ) );
    }
}
