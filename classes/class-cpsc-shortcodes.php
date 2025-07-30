<?php
/**
 * Cpsc Frontend.
 *
 * @package Cpsc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Cpsc_Frontend.
 */
class Cpsc_Frontend {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		add_shortcode( 'workshops', array( $this, 'load_workshop_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_shortcode_scripts' ) );
	}

	/**
	 * Workshops Shortcodes
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function load_workshop_shortcode( $atts = array() ) {

		if ( is_admin() ) {
			return;
		}

		$atts = shortcode_atts(
			array(
				'type' => 'all', // all, upcoming, past, today, tomorrow
				'show_date' => true,
			),
			$atts,
			'workshops'
		);

		$type = strtolower( trim( $atts['type'] ) );
		$show_date = wp_unslash( boolval( $atts['show_date'] ) );

		$today = date( 'Y-m-d' );
		$tomorrow = date( 'Y-m-d', strtotime( '+1 day' ) );

		$meta_query = array();

		// Filter by event type
		switch ( $type ) {
			case 'upcoming':
				$meta_query[] = array(
					'key'     => '_cpsc_workshop_start_date',
					'value'   => $today,
					'compare' => '>=',
					'type'    => 'DATE',
				);
				break;
			case 'past':
				$meta_query[] = array(
					'key'     => '_cpsc_workshop_start_date',
					'value'   => $today,
					'compare' => '<',
					'type'    => 'DATE',
				);
				break;
			case 'today':
				$meta_query[] = array(
					'key'     => '_cpsc_workshop_start_date',
					'value'   => $today,
					'compare' => '=',
					'type'    => 'DATE',
				);
				break;
			case 'tomorrow':
				$meta_query[] = array(
					'key'     => '_cpsc_workshop_start_date',
					'value'   => $tomorrow,
					'compare' => '=',
					'type'    => 'DATE',
				);
				break;
			case 'all':
			default:
				// No meta_query for all
				break;
		}

		$args = array(
			'post_type'      => 'workshop',
			'posts_per_page' => '-1',
			'post_status'    => 'publish',
		);

		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query;
		}

		$result = '';
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			$result .= '<ul class="sas-workshops columns-3">';
			while ( $query->have_posts() ) :
				$query->the_post();

				$post_image = get_the_post_thumbnail( null, array(300,300), '' ) ? get_the_post_thumbnail( null, array(300,300), '' ) : '<img src="' . CPSC_URL . "assets/images/400x400.jpg" . '">';
				$result .=  '<li class="sas-workshop type-workshop">';
				$result .=  '<div class="sas-workshop-thumbnail-wrap">';
				$result .=  '<a href="' . get_the_permalink() . '" class="sas-workshop-link">' . $post_image . '</a>';
				$result .=  '</div>';
				$result .=  '<div class="sas-workshop-summary-wrap">';
				$result .=  '<span class="sas-workshop-category">Workshop</span> ';
				if( $show_date ){
					$start_date = get_post_meta( get_the_ID(), '_cpsc_workshop_start_date', true );
					$formatted_date = date('d-M-Y', strtotime($start_date));

					$result .=  '<span class="sas-workshop__event-date"><span class="dashicons dashicons-calendar-alt"></span>' . $formatted_date . '</span>';
				}
				$result .=  '<a href="' . get_the_permalink() . '" class="sas-workshop__link">';
				$result .=  '<h2 class="sas-workshop__title">' . get_the_title() . '</h2>';
				$result .=  '</a>';
				$result .=  '<a href="' . get_the_permalink() . '" class="sas-workshop__button">Read More</a>';
				$result .=  '</div>';
				$result .=  '</li>';
			endwhile;
			$result .= '</ul>';
			wp_reset_postdata();
		endif;

		return $result;
	}

	
	/**
	 * Shortcode Scripts & Styles.
	 *
	 * @since 1.0.0
	 */
	public function load_shortcode_scripts() {

		wp_enqueue_style( 'cpsc-shortcode', CPSC_URL . 'assets/css/workshop-grid.css', array(), CPSC_VER );
	}
}

/**
 *  Prepare if class 'Cpsc_Frontend' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
Cpsc_Frontend::get_instance();
