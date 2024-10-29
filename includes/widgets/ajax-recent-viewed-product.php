<?php
/**
 * Ajax Recent Viewed Product widget
 *
 * @package Baltic_Kit
 */
namespace BalticKit\Widgets;

if ( ! defined( 'ABSPATH' ) ) exit;

class Ajax_Recent_Viewed_Product extends \WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 */
	public function __construct() {

		$this->defaults = [
			'title'	=> '',
			'count'	=> 5,
		];

		$widget_ops = array(
			'classname'   => 'baltic-kit-ajax-recent-viewed-products hide',
			'description' => esc_html__( 'Display a list of a customer\'s recently viewed products via ajax.', 'baltic-kit' ),
			'customize_selective_refresh' => true,
		);

		$control_ops = array(
			'id_base' => 'baltic-kit-ajax-recent-viewed-products',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct(
			'baltic-kit-ajax-recent-viewed-products',
			esc_html__( '+ Ajax Recent Viewed Product', 'baltic-kit' ),
			$widget_ops,
			$control_ops
		);

		// Set cookies
		add_action( 'template_redirect', array( $this, 'track_product_view' ), 20 );

	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title']; // WPCS: prefix ok.
		}

		echo '<div class="baltic-kit__recently-viewed loading" data-count="'. absint( $instance['count'] ) .'"></div>';


		echo $args['after_widget'];

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'baltic-kit' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Number of products to show', 'baltic-kit' ); ?>:</label>
			<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" value="<?php echo esc_attr( $instance['count'] ); ?>" class="alignright tiny-text" step="1" min="1" size="4" />
		</p>

		<hr class="div" />

		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$new_instance['title'] = strip_tags( $new_instance['title'] );
		$new_instance['count'] = (int) $new_instance['count'];

		return $new_instance;

	}

	/**
	 * Track product views.
	 */
	function track_product_view() {

		if ( ! is_singular( 'product' ) || ! is_active_widget( false, false, 'baltic-kit-ajax-recent-viewed-products', true ) ) {
			return;
		}

		global $post;

		if ( empty( $_COOKIE['baltic_kit_recently_viewed_products'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = (array) explode( '|', $_COOKIE['baltic_kit_recently_viewed_products'] );
		}

		// Unset if already in viewed products list.
		$keys = array_flip( $viewed_products );
		if ( isset( $keys[ $post->ID ] ) ) {
			unset( $viewed_products[ $keys[ $post->ID ] ] );
		}

		$viewed_products[] = $post->ID;

		if ( count( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only.
		wc_setcookie( 'baltic_kit_recently_viewed_products', implode( '|', $viewed_products ) );

	}

}
