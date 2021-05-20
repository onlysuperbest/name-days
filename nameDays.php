<?php

/*
	Plugin Name: Today's Name Days
	Plugin URI: https://github.com/onlysuperbest/name-days
	Description: This plugins allows you to display today's name days in any post or widget.
	Author: Dymytriy
	Author URI: https://github.com/onlysuperbest
	Text Domain: name-days
	Version: 1.0
	License: GNU General Public License v2 or later
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

require_once( dirname(__FILE__) . '/src/nameDaysProvider.php' );

$name_days_provider = new NameDaysProvider();
