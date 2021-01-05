<?php
/**
 * Faith Blog Theme Info
 *
 * @package Blog_Starter
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function faith_blog_customizer_theme_info( $wp_customize ) {
	
    $wp_customize->add_section( 'theme_info', array(
		'title'       => __( 'Demo & Documentation' , 'faith-blog' ),
		'priority'    => 6,
	) );
    
    /** Important Links */
	$wp_customize->add_setting( 'faith_blog_theme_info',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    
    $theme_info = '<p>';
	$theme_info .= sprintf( __( 'Demo Link: %1$sClick here.%2$s', 'faith-blog' ),  '<a href="' . esc_url( 'http://blogstarter.theimran.com/demoes/' ) . '" target="_blank">', '</a>' );
    $theme_info .= '</p><p>';

    $theme_info .= sprintf( __( 'Documentation Link: %1$sClick here.%2$s', 'faith-blog' ),  '<a href="' . esc_url( 'http://documentation.theimran.com/blog-starter' ) . '" target="_blank">', '</a>' );
     $theme_info .= '</p><p class="one-click-demo-import">';
    $theme_info .= sprintf( __( 'One Click Demo Import Only Available With Pro Version: %1$sView Details.%2$s', 'faith-blog' ),  '<a href="' . esc_url( 'https://theimran.com/themes/wordpress-theme/blog-starter-pro-personal-blog-wordpress-theme/' ) . '" target="_blank">', '</a>' );
    $theme_info .= '</p>';
	
	$wp_customize->add_control( new Faith_Blog_Note_Control( $wp_customize,
        'faith_blog_theme_info', 
            array(
                'section'     => 'theme_info',
                'description' => $theme_info
            )
        )
    );
    
}
add_action( 'customize_register', 'faith_blog_customizer_theme_info' );

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class faith_blog_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		require get_template_directory() . '/inc/upgradetopro/section-pro.php';

		// Register custom section types.
		$manager->register_section_type( 'Faith_Blog_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new Faith_Blog_Customize_Section_Pro(
				$manager,
				'upgradetopro',
				array(
					'priority'       => 1,
					'pro_text' => esc_html__( 'Faith Blog - Upgrade To Pro', 'faith-blog' ),
					'pro_url'  => 'https://theimran.com/themes/wordpress-theme/blog-starter-pro-personal-blog-wordpress-theme/',
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'faith-blog-customize-controls', get_theme_file_uri( '/inc/upgradetopro/customize-controls.js' ), array( 'customize-controls' ) );

		wp_enqueue_style( 'faith-blog-customize-controls', get_theme_file_uri( '/inc/upgradetopro/customize-controls.css' ));
	}
}

// Doing this customizer thang!
faith_blog_Customize::get_instance();
