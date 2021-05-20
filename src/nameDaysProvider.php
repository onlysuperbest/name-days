<?php

// We will store cached result in a WordPress transient for a period
// of one day, but given in seconds
define( 'NAME_DAYS_EXPIRATION', 24 * 60 * 60 );

class NameDaysProvider {
    public function __construct() {
        $this->installActionsAndFilters();
    }

    protected function installActionsAndFilters() {
        add_action( 'init', array( $this, 'initActionHandler' ) );

        // Following is added to allow using shortcodes in regular WordPress widgets
        add_filter( 'widget_text', 'do_shortcode' ) ;
    }

    public function initActionHandler() {
        add_shortcode( 'today_name_days', array( $this, 'processTodayNameDaysShortcode' ) );
    }

    public function processTodayNameDaysShortcode( $atts, $content = null ) {
        if ( !$this->libXMLModuleAvailable() ) {
            return 'Sorry, but this plugin requires <strong>php dom</strong> extension to be installed and enabled. Please consult with your hosting provider.';
        }

        $todaysNameDays = $this->getTodaysNameDaysAndCheckCache();

        if ( empty( $todaysNameDays ) ) {
            $todaysNameDays = $this->getTodaysNameDaysFromProvider();
        }

        return $this->generateTodaysNameDaysHTML( $todaysNameDays );
    }

    protected function libXMLModuleAvailable() {
        if ( class_exists( 'DOMDocument' ) ) {
            return true;
        }

        return false;
    }

    protected function getTodaysNameDaysAndCheckCache() {
        $todaysNameDays = get_transient( 'todays_name_days' );
        $todaysCheckTime = get_transient( 'todays_check_time' );

        if ( $this->areTodaysNameDaysValid( $todaysNameDays, $todaysCheckTime ) ) {
            return $todaysNameDays;
        } else {
            $this->invalidateTodaysNameDaysCache();
        }

        return null;
    }

    protected function areTodaysNameDaysValid( $todaysNameDays, $todaysCheckTime ) {
        if ( empty( $todaysNameDays ) ) {
            return false;
        }

        if ( empty( $todaysCheckTime ) ) {
            return false;
        }

        return $this->todayAndCheckTimeAreTheSameDay( $todaysCheckTime );
    }

    protected function todayAndCheckTimeAreTheSameDay( $todaysCheckTime ) {
        $nowTimestamp = time();

        if ( $this->timestampToDayYearFormat( $nowTimestamp ) === $this->timestampToDayYearFormat( $todaysCheckTime ) ) {
            return true;
        }

        return false;
    }

    protected function timestampToDayYearFormat( $timestamp ) {
        $date = new DateTime();
        $date->setTimezone( wp_timezone() );
        $date->setTimestamp( $timestamp );
        return $date->format( 'z-Y' );
    }

    protected function storeTodaysNameDaysToCache( $todaysNameDays ) {
        set_transient( 'todays_name_days', $todaysNameDays, NAME_DAYS_EXPIRATION );

        // We set validity day also, so if the expiration crosses to the next day,
        // we can invalidate outdated result from yesterday
        set_transient( 'todays_check_time', time(), NAME_DAYS_EXPIRATION );
    }

    protected function invalidateTodaysNameDaysCache() {
        delete_transient( 'todays_name_days' );
        delete_transient( 'todays_check_time' );
    }

    protected function getTodaysNameDaysFromProvider() {
        $response = $this->getRemoteContent( 'https://day.lt/' );

        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
            $pageContent = $response['body'];

            if ( !empty( $pageContent ) ) {
                return $this->processProviderResponseAndGetDays( $pageContent );
            }
        }

        // We were not able to fetch data from the provider due to an unknown reason
        return null;
    }

    protected function getRemoteContent( $url ) {
        return wp_remote_get( $url );
    }

    protected function processProviderResponseAndGetDays( $pageContent ) {
        $providerContentDOM = new DOMDocument( '1.0', 'UTF-8' );

        // Instruct the DOM parser to not throw exceptions
        $providerContentDOM->strictErrorChecking = false;

        // '@' is added intentionally to disable warnings which may be generated
        // in case if the source HTML contains errors
        @$providerContentDOM->loadHTML( $pageContent );

        $paragraphs = $providerContentDOM->getElementsByTagName( 'p' );

        return $this->processParagraphsAndFindNames( $paragraphs );
    }

    protected function processParagraphsAndFindNames( $paragraphs ) {
        $todaysNameDays = array();

        foreach ( $paragraphs as $singleParagraph ) {
            if ( $this->isParagraphContainsNames( $singleParagraph ) ) {
                $aTagsWithName = $singleParagraph->getElementsByTagName( 'a' );

                foreach( $aTagsWithName as $singleATag ) {
                    $todaysNameDays[] = trim( $singleATag->nodeValue );
                }
            }
        }

        $this->storeTodaysNameDaysToCache( $todaysNameDays );

        return $todaysNameDays;
    }

    protected function isParagraphContainsNames( $singleParagraph ) {
        return stripos( $singleParagraph->getAttribute( 'class' ), 'vardadieniai' ) !== false;
    }

    protected function generateTodaysNameDaysHTML( $todaysNameDays ) {
        if ( empty( $todaysNameDays ) || ( ! is_array( $todaysNameDays ) ) ) {
            return '<p>Sorry, there is no data to display at the moment.</p>';
        }

        $generatedHTML = '<ul class="todays_name_days">';

        foreach ( $todaysNameDays as $singleNameDay ) {
            $generatedHTML .= '<li>' . $singleNameDay . '</li>';
        }

        $generatedHTML .= '</ul>';

        return $generatedHTML;
    }
}
