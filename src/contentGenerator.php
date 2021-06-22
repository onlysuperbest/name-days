<?php

class ContentGenerator {
    public function generate($todaysNameDays) {
        $generatedHTML = '<ul class="todays_name_days">';

        foreach ( $todaysNameDays as $singleNameDay ) {
            $generatedHTML .= '<li>' . $singleNameDay . '</li>';
        }

        $generatedHTML .= '</ul>';

        return $generatedHTML;
    }
}
