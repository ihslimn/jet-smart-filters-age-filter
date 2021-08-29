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

		foreach ( $query['meta_query'] as $index => $meta_query ) {

			if ( false !== strpos( $meta_query['key'], $this->base_mask ) ) {

				$today = strtotime( 'today midnight' );

				$from = DateTime::createFromFormat( 'U', $today );
				$from->modify( sprintf( '-%dyears', $meta_query['value'][1]+1 ) );
				$from = (int) $from->format('U')-1;

				$to = DateTime::createFromFormat( 'U', $today );
				$to->modify( sprintf( '-%dyears', $meta_query['value'][0] ) );
				$to = (int) $to->format('U')+1;

				$data = explode( '::', $meta_query['key'] );

				$field = ! empty( $data[1] ) ? $data[1] : false;

				$query['meta_query'][ $index ]['key'] = $field;
				$query['meta_query'][ $index ]['value'][1] = $to;
				$query['meta_query'][ $index ]['value'][0] = $from;

			}
		}

		return $query;

	}

}

new Jet_Smart_Filters_Age_Filter();
