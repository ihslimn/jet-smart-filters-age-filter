<?php
/**
 * Plugin Name: JetSmartFilters - Age Filter
 * Plugin URI:  https://crocoblock.com/
 * Description: Allows filtering by age in years using Range filter
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Jet_Smart_Filters_Age_Filter {

	private $base_mask = 'age_filter::';

	public function __construct() {
		add_filter( 'jet-smart-filters/query/final-query', array( $this, 'apply_dates_filter' ), -999 );
	}

	/**
	 * Check if your variable is presented in the query. If yes - replace years number with dates in timestamp
	 */
	public function apply_dates_filter( $query ) {

		if ( empty( $query['meta_query'] ) ) {
			return $query;
		}

		$today = strtotime( 'today midnight' );
		
		foreach ( $query['meta_query'] as $index => $meta_query ) {

			if ( false !== strpos( $meta_query['key'], $this->base_mask ) ) {

				$age_to = $meta_query['value'][1];
				$today_date = DateTime::createFromFormat( 'U', $today );
				$from = $today_date->modify( sprintf( '-%dyears', $age_to+1 ) );
				$date_from = (int) $from->format('U')+1;

				$age_from = $meta_query['value'][0];
				$today_date = DateTime::createFromFormat( 'U', $today );
				$to = $today_date->modify( sprintf( '-%dyears', $age_from ) );
				$date_to = (int) $to->format('U')+1;

				$data = explode( '::', $meta_query['key'] );

				$field = ! empty( $data[1] ) ? $data[1] : false;

				if ( ! $field ) {
					continue;
				}

				$query['meta_query'][ $index ]['key'] = $field;
				$query['meta_query'][ $index ]['value'][0] = $date_from;
				$query['meta_query'][ $index ]['value'][1] = $date_to;

			}
		}

		return $query;

	}

}

new Jet_Smart_Filters_Age_Filter();
