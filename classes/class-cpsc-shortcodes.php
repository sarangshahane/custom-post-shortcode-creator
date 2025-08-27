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

		add_shortcode( 'cpsc_upcoming_events', array( $this, 'load_upcoming_events_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_shortcode_scripts' ) );
	}

	/**
	 * Workshops Shortcodes
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function load_upcoming_events_shortcode( $atts = array() ) {

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

		// Fetch workshops based on the type
		$workshops = $this->fetch_upcoming_workshops( $atts );
		
		// Fetch courses for all types
		$courses = $this->fetch_tutorlms_courses();
		
		// Combine workshops and courses
		$all_items = array_merge( $workshops, $courses );
		
		// Display combined items
		return $this->display_combined_items( $all_items, $atts );
	}

	/**
	 * Fetch workshops based on type
	 *
	 * @since 1.0.1
	 * @param array $atts Shortcode attributes.
	 * @return array
	 */
	private function fetch_upcoming_workshops( $atts = array() ) {
		$show_date = wp_unslash( boolval( $atts['show_date'] ) );
		$type = strtolower( trim( $atts['type'] ) );
		
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

		$workshops = array();
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();

				$post_image = get_the_post_thumbnail( null, array(300,300), '' ) ? get_the_post_thumbnail( null, array(300,300), '' ) : '<img src="' . CPSC_URL . "assets/images/400x400.jpg" . '">';
				
				$workshop_data = array(
					'type' => 'workshop',
					'image' => $post_image,
					'permalink' => get_the_permalink(),
					'title' => get_the_title(),
					'category' => 'Workshop',
					'date' => $show_date ? get_post_meta( get_the_ID(), '_cpsc_workshop_start_date', true ) : '',
				);
				
				$workshops[] = $workshop_data;
			endwhile;
			wp_reset_postdata();
		endif;

		return $workshops;
	}

	/**
	 * Fetch published TutorLMS courses
	 *
	 * @since 1.0.1
	 * @return array
	 */
	private function fetch_tutorlms_courses() {
		$courses = array();
		
		$args = array(
			'post_type'      => 'courses',
			'posts_per_page' => '-1',
			'post_status'    => 'publish',
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();
				
				$post_image = get_the_post_thumbnail( null, array(300,300), '' ) ? get_the_post_thumbnail( null, array(300,300), '' ) : '<img src="' . CPSC_URL . "assets/images/400x400.jpg" . '">';
				
				$course_data = array(
					'type' => 'course',
					'image' => $post_image,
					'permalink' => get_the_permalink(),
					'title' => get_the_title(),
					'category' => 'Course',
					'date' => '',
				);
				
				$courses[] = $course_data;
			endwhile;
			wp_reset_postdata();
		endif;

		return $courses;
	}

	/**
	 * Display combined workshops and courses
	 *
	 * @since 1.0.1
	 * @param array $items Array of workshops and courses.
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	private function display_combined_items( $items, $atts = array() ) {
		$show_date = wp_unslash( boolval( $atts['show_date'] ) );
		$result = '';
		
		if ( ! empty( $items ) ) :
			$result .= '<ul class="sas-workshops columns-3">';
			foreach ( $items as $item ) :
				$result .= '<li class="sas-workshop type-' . $item['type'] . '">';
				$result .= '<div class="sas-workshop-thumbnail-wrap">';
				$result .= '<a href="' . $item['permalink'] . '" class="sas-workshop-link">' . $item['image'] . '</a>';
				$result .= '</div>';
				$result .= '<div class="sas-workshop-summary-wrap">';
				$result .= '<span class="sas-workshop-category">' . $item['category'] . '</span> ';
				
				if ( $show_date && ! empty( $item['date'] ) ) :
					$formatted_date = date('d-M-Y', strtotime($item['date']));
					$result .= '<span class="sas-workshop__event-date"><span class="dashicons dashicons-calendar-alt"></span>' . $formatted_date . '</span>';
				endif;
				
				$result .= '<a href="' . $item['permalink'] . '" class="sas-workshop__link">';
				$result .= '<h2 class="sas-workshop__title">' . $item['title'] . '</h2>';
				$result .= '</a>';
				$result .= '<a href="' . $item['permalink'] . '" class="sas-workshop__button">Read More</a>';
				$result .= '</div>';
				$result .= '</li>';
			endforeach;
			$result .= '</ul>';
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
