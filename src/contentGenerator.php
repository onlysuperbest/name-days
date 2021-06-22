<?php

class ContentGenerator {
    public function generate($todaysNameDays) {
        $generatedHTML = '';

        if ($this->arrayReadyToIterate($todaysNameDays)) {
            $generatedHTML = '<ul class="todays_name_days">';

            foreach ( $todaysNameDays as $singleNameDay ) {
                $generatedHTML .= '<li>' . $singleNameDay . '</li>';
            }

            $generatedHTML .= '</ul>';
        }

        return $generatedHTML;
    }

    private function arrayReadyToIterate($todaysNameDays) {
        if (is_array($todaysNameDays) ) {
            return true;
        }

        if (count($todaysNameDays) > 0) {
            return true;
        }

        return false;
    }
}
