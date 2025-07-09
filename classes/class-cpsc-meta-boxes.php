<?php
/**
 * Cpsc Meta Boxes.
 *
 * @package custom-post-shortcode-creator
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Cpsc_Meta_Boxes.
 */
class Cpsc_Meta_Boxes {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Meta box ID for workshop details.
	 *
	 * @var string
	 */
	private $meta_box_id = 'cpsc_workshop_details';

	/**
	 * Meta field names.
	 *
	 * @var array
	 */
	private $meta_fields = array(
		'workshop_start_date' => '_cpsc_workshop_start_date',
		'workshop_start_time' => '_cpsc_workshop_start_time',
		'workshop_end_time' => '_cpsc_workshop_end_time',
		'workshop_location_type' => '_cpsc_workshop_location_type',
		'workshop_location_address' => '_cpsc_workshop_location_address',
	);

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
		// Add meta box to workshop post type.
		add_action( 'add_meta_boxes', array( $this, 'add_workshop_meta_box' ) );

		// Save meta box data.
		add_action( 'save_post', array( $this, 'save_workshop_meta_box_data' ) );

		// Enqueue admin scripts and styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Add meta box to workshop post type.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_workshop_meta_box() {
		// Add meta box only for workshop post type.
		add_meta_box(
			$this->meta_box_id, // Meta box ID.
			__( 'Workshop Details', 'custom-post-shortcode-creator' ), // Meta box title.
			array( $this, 'render_workshop_meta_box' ), // Callback function.
			'workshop', // Post type.
			'normal', // Context.
			'default' // Priority - below main content area.
		);
	}

	/**
	 * Render workshop meta box content.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The post object.
	 * @return void
	 */
	public function render_workshop_meta_box( $post ) {
		// Add nonce for security.
		wp_nonce_field( 'cpsc_workshop_meta_box', 'cpsc_workshop_meta_box_nonce' );

		// Get existing meta values.
		$workshop_start_date = get_post_meta( $post->ID, $this->meta_fields['workshop_start_date'], true );
		$workshop_start_time = get_post_meta( $post->ID, $this->meta_fields['workshop_start_time'], true );
		$workshop_end_time = get_post_meta( $post->ID, $this->meta_fields['workshop_end_time'], true );
		$location_type = get_post_meta( $post->ID, $this->meta_fields['workshop_location_type'], true );
		$location_address = get_post_meta( $post->ID, $this->meta_fields['workshop_location_address'], true );

		// Set default values if empty.
		$location_type = ! empty( $location_type ) ? $location_type : 'online';
		?>
		<div class="cpsc-meta-fields">
			<div class="cpsc-field-group">
				<label for="<?php echo esc_attr( $this->meta_fields['workshop_start_date'] ); ?>">
					<?php esc_html_e( 'Workshop Date', 'custom-post-shortcode-creator' ); ?>
				</label>
				<input 
					type="date" 
					id="<?php echo esc_attr( $this->meta_fields['workshop_start_date'] ); ?>"
					name="<?php echo esc_attr( $this->meta_fields['workshop_start_date'] ); ?>"
					value="<?php echo esc_attr( $workshop_start_date ); ?>"
					class="regular-text"
					required
				/>
				<p class="description">
					<?php esc_html_e( 'Select the date for the workshop.', 'custom-post-shortcode-creator' ); ?>
				</p>
			</div>

			<div class="cpsc-field-group">
				<label><?php esc_html_e( 'Workshop Time', 'custom-post-shortcode-creator' ); ?></label>
				<div class="cpsc-time-fields">
					<div class="form-field">
						<label for="<?php echo esc_attr( $this->meta_fields['workshop_start_time'] ); ?>">
							<?php esc_html_e( 'Start Time', 'custom-post-shortcode-creator' ); ?>
						</label>
						<input 
							type="time" 
							id="<?php echo esc_attr( $this->meta_fields['workshop_start_time'] ); ?>"
							name="<?php echo esc_attr( $this->meta_fields['workshop_start_time'] ); ?>"
							value="<?php echo esc_attr( $workshop_start_time ); ?>"
							required
						/>
					</div>
					<div class="form-field">
						<label for="<?php echo esc_attr( $this->meta_fields['workshop_end_time'] ); ?>">
							<?php esc_html_e( 'End Time', 'custom-post-shortcode-creator' ); ?>
						</label>
						<input 
							type="time" 
							id="<?php echo esc_attr( $this->meta_fields['workshop_end_time'] ); ?>"
							name="<?php echo esc_attr( $this->meta_fields['workshop_end_time'] ); ?>"
							value="<?php echo esc_attr( $workshop_end_time ); ?>"
							required
						/>
					</div>
				</div>
				<p class="description">
					<?php esc_html_e( 'Select the start and end times for the workshop.', 'custom-post-shortcode-creator' ); ?>
				</p>
			</div>

			<div class="cpsc-field-group">
				<label for="<?php echo esc_attr( $this->meta_fields['workshop_location_type'] ); ?>">
					<?php esc_html_e( 'Location Type', 'custom-post-shortcode-creator' ); ?>
				</label>
				<select 
					id="<?php echo esc_attr( $this->meta_fields['workshop_location_type'] ); ?>"
					name="<?php echo esc_attr( $this->meta_fields['workshop_location_type'] ); ?>"
					class="cpsc-location-type-select"
				>
					<option value="online" <?php selected( $location_type, 'online' ); ?>>
						<?php esc_html_e( 'Online (Via Zoom)', 'custom-post-shortcode-creator' ); ?>
					</option>
					<option value="offline" <?php selected( $location_type, 'offline' ); ?>>
						<?php esc_html_e( 'Offline', 'custom-post-shortcode-creator' ); ?>
					</option>
				</select>
				<p class="description">
					<?php esc_html_e( 'Choose whether the workshop will be conducted online or offline.', 'custom-post-shortcode-creator' ); ?>
				</p>
			</div>

			<div class="cpsc-field-group" id="cpsc-location-address-row" style="<?php echo ( 'offline' === $location_type ) ? 'display: block;' : 'display: none;'; ?>">
				<label for="<?php echo esc_attr( $this->meta_fields['workshop_location_address'] ); ?>">
					<?php esc_html_e( 'Location Address', 'custom-post-shortcode-creator' ); ?>
				</label>
				<textarea 
					id="<?php echo esc_attr( $this->meta_fields['workshop_location_address'] ); ?>"
					name="<?php echo esc_attr( $this->meta_fields['workshop_location_address'] ); ?>"
					rows="3"
					cols="50"
					class="large-text"
					placeholder="<?php esc_attr_e( 'Enter the workshop location address...', 'custom-post-shortcode-creator' ); ?>"
				><?php echo esc_textarea( $location_address ); ?></textarea>
				<p class="description">
					<?php esc_html_e( 'Enter the physical address where the workshop will be held.', 'custom-post-shortcode-creator' ); ?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Save workshop meta box data.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 * @return void
	 */
	public function save_workshop_meta_box_data( $post_id ) {
		// Check if nonce is valid.
		if ( ! isset( $_POST['cpsc_workshop_meta_box_nonce'] ) || 
			 ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cpsc_workshop_meta_box_nonce'] ) ), 'cpsc_workshop_meta_box' ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check if our custom fields are set.
		if ( ! isset( $_POST[ $this->meta_fields['workshop_start_date'] ] ) ) {
			return;
		}

		// Sanitize and save workshop start date.
		$workshop_start_date = sanitize_text_field( wp_unslash( $_POST[ $this->meta_fields['workshop_start_date'] ] ) );
		if ( ! empty( $workshop_start_date ) ) {
			update_post_meta( $post_id, $this->meta_fields['workshop_start_date'], $workshop_start_date );
		} else {
			delete_post_meta( $post_id, $this->meta_fields['workshop_start_date'] );
		}

		// Sanitize and save workshop start time.
		$workshop_start_time = sanitize_text_field( wp_unslash( $_POST[ $this->meta_fields['workshop_start_time'] ] ) );
		if ( ! empty( $workshop_start_time ) ) {
			update_post_meta( $post_id, $this->meta_fields['workshop_start_time'], $workshop_start_time );
		} else {
			delete_post_meta( $post_id, $this->meta_fields['workshop_start_time'] );
		}

		// Sanitize and save workshop end time.
		$workshop_end_time = sanitize_text_field( wp_unslash( $_POST[ $this->meta_fields['workshop_end_time'] ] ) );
		if ( ! empty( $workshop_end_time ) ) {
			update_post_meta( $post_id, $this->meta_fields['workshop_end_time'], $workshop_end_time );
		} else {
			delete_post_meta( $post_id, $this->meta_fields['workshop_end_time'] );
		}

		// Sanitize and save location type.
		$location_type = sanitize_text_field( wp_unslash( $_POST[ $this->meta_fields['workshop_location_type'] ] ) );
		if ( in_array( $location_type, array( 'online', 'offline' ), true ) ) {
			update_post_meta( $post_id, $this->meta_fields['workshop_location_type'], $location_type );
		}

		// Sanitize and save location address (only if offline).
		if ( 'offline' === $location_type && isset( $_POST[ $this->meta_fields['workshop_location_address'] ] ) ) {
			$location_address = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_fields['workshop_location_address'] ] ) );
			update_post_meta( $post_id, $this->meta_fields['workshop_location_address'], $location_address );
		} else {
			// Remove location address if not offline.
			delete_post_meta( $post_id, $this->meta_fields['workshop_location_address'] );
		}
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since 1.0.0
	 * @param string $hook The current admin page.
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook ) {
		// Only enqueue on post.php and post-new.php for workshop post type.
		global $post_type;
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || 'workshop' !== $post_type ) {
			return;
		}

		// Enqueue custom CSS for meta box styling.
		wp_enqueue_style(
			'cpsc-meta-boxes',
			CPSC_URL . 'assets/css/meta-boxes.css',
			array(),
			CPSC_VER
		);

		// Enqueue minimal JavaScript for modern WordPress compatibility.
		wp_enqueue_script(
			'cpsc-meta-boxes',
			CPSC_URL . 'assets/js/meta-boxes.js',
			array( 'jquery' ),
			CPSC_VER,
			true
		);
	}



	/**
	 * Get workshop start date.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 * @return string|false The workshop start date or false if not set.
	 */
	public function get_workshop_start_date( $post_id ) {
		return get_post_meta( $post_id, $this->meta_fields['workshop_start_date'], true );
	}

	/**
	 * Get workshop start time.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 * @return string|false The workshop start time or false if not set.
	 */
	public function get_workshop_start_time( $post_id ) {
		return get_post_meta( $post_id, $this->meta_fields['workshop_start_time'], true );
	}

	/**
	 * Get workshop end time.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 * @return string|false The workshop end time or false if not set.
	 */
	public function get_workshop_end_time( $post_id ) {
		return get_post_meta( $post_id, $this->meta_fields['workshop_end_time'], true );
	}

	/**
	 * Get workshop datetime as combined string.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 * @return string|false The combined datetime string or false if not set.
	 */
	public function get_workshop_datetime( $post_id ) {
		$start_date = $this->get_workshop_start_date( $post_id );
		$start_time = $this->get_workshop_start_time( $post_id );
		
		if ( ! empty( $start_date ) && ! empty( $start_time ) ) {
			return $start_date . 'T' . $start_time;
		}
		
		return false;
	}

	/**
	 * Get workshop location type.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 * @return string The workshop location type.
	 */
	public function get_workshop_location_type( $post_id ) {
		$location_type = get_post_meta( $post_id, $this->meta_fields['workshop_location_type'], true );
		return ! empty( $location_type ) ? $location_type : 'online';
	}

	/**
	 * Get workshop location address.
	 *
	 * @since 1.0.0
	 * @param int $post_id The post ID.
	 * @return string The workshop location address.
	 */
	public function get_workshop_location_address( $post_id ) {
		return get_post_meta( $post_id, $this->meta_fields['workshop_location_address'], true );
	}
}

/**
 *  Prepare if class 'Cpsc_Meta_Boxes' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
Cpsc_Meta_Boxes::get_instance(); 