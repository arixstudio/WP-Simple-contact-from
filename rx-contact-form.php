<?php
/**
 * Plugin Name: Rx Contact Form
 * Description: A simple contact form plugin for WordPress.
 * Version: 1.0
 * Author: arixstudio
 * Author URI: https://arixstudio.com/
 * Text Domain: rx-contact-form
 * Domain Path: /languages
 */

require_once ABSPATH . 'wp-includes/pluggable.php';


// Define the plugin path and URL
define( 'RX_CONTACT_FORM_PATH', plugin_dir_path( __FILE__ ) );
define( 'RX_CONTACT_FORM_URL', plugin_dir_url( __FILE__ ) );

function rx_contact_init() {
    load_plugin_textdomain( 'rx-contact-form', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('init', 'rx_contact_init');

// Enqueue the necessary scripts and styles
add_action( 'wp_enqueue_scripts', 'rx_contact_form_enqueue_scripts' );
function rx_contact_form_enqueue_scripts() {
    wp_enqueue_style( 'rx-contact-form', RX_CONTACT_FORM_URL . 'css/rx-contact-form.css');
    wp_enqueue_style( 'bootstrap-style', RX_CONTACT_FORM_URL . 'css/bootstrap.min.css');
    wp_enqueue_script( 'rx-contact-form', RX_CONTACT_FORM_URL . 'js/rx-contact-form.js', array( 'jquery' ), '1.0.0', true );
    
    // Define the AJAX URL and nonce
    $ajax_url = admin_url( 'admin-ajax.php' );
    $nonce = wp_create_nonce( 'rx_contact_form_submit' );
    
    // Define the script variables
    $script_vars = array(
        'ajax_url' => $ajax_url,
        'nonce' => $nonce,
    );
    
    // Localize the script variables
    wp_localize_script( 'rx-contact-form','rx_contact_form_params', $script_vars );
}


// Add the shortcode for the contact form
add_shortcode( 'rx_contact_form', 'rx_contact_form_shortcode' );
function rx_contact_form_shortcode() {
    ob_start();
    ?>
    <form id="rx-contact-form" method="post" class="needs-validation" novalidate>
        <div class="form-group mb-3">
            <input type="text" class="form-control" id="rx-contact-form-name" name="rx_contact_form_name" placeholder="<?php _e( 'Name', 'rx-contact-form' ); ?>" required>
            <div class="invalid-feedback"><?php _e( 'Please enter your name.', 'rx-contact-form' ); ?></div>
        </div>
        <div class="form-group mb-3">
            <input type="email" class="form-control" id="rx-contact-form-email" name="rx_contact_form_email" placeholder="<?php _e( 'Email', 'rx-contact-form' ); ?>" required>
            <div class="invalid-feedback"><?php _e( 'Please enter a valid email address.', 'rx-contact-form' ); ?></div>
        </div>
        <div class="form-group mb-3">
            <textarea class="form-control" id="rx-contact-form-message" name="rx_contact_form_message" rows="5" placeholder="<?php _e( 'Message', 'rx-contact-form' ); ?>" required></textarea>
            <div class="invalid-feedback"><?php _e( 'Please enter your message.', 'rx-contact-form' ); ?></div>
        </div>
        <?php wp_nonce_field( 'rx_contact_form_submit', 'rx_contact_form_nonce' ); ?>
        <button type="submit" class="btn btn-secondary"><img src="<?php echo RX_CONTACT_FORM_URL.'/img/arrow.svg' ?>"></button>
    </form>
    <?php
    return ob_get_clean();
}

// Process the form submission via AJAX
add_action( 'wp_ajax_rx_contact_form_submit', 'rx_contact_form_submit' );
add_action( 'wp_ajax_nopriv_rx_contact_form_submit', 'rx_contact_form_submit' );

function rx_contact_form_submit() {
    // Verify the nonce
    if ( ! isset( $_POST['rx_contact_form_nonce'] ) || ! wp_verify_nonce( $_POST['rx_contact_form_nonce'], 'rx_contact_form_submit' ) ) {
        wp_send_json_error( __( 'Nonce verification failed.', 'rx-contact-form' ) );
    }

    // Get the form data
    $name = sanitize_text_field( $_POST['rx_contact_form_name'] );
    $email = sanitize_email( $_POST['rx_contact_form_email'] );
    $message = wp_kses_post( $_POST['rx_contact_form_message'] );

    // Html email template
    $template = '<div style="">
                    <div style="background: #F4F4F4;">
                        <div style="margin: 0; padding: 0; background: #F4F4F4">
                            <table style="width: 0px auto" width="100%" cellspacing="0" cellpadding="10" border="0">
                            <tbody>
                                <tr>
                                <td align="center">
                                    <table style="border: 0; width: 0px auto; max-width: 680px" width="680" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        <tr>
                                        <td style="padding: 15px 0 20px 0; background-color: #FFFFFF; border: 2px solid #E8E8E8;">
                                            <table style="background: #FFFFFF; font-family: Helvetica Neue,Helvetica,Arial,sans-serif" width="680" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                                <tr>
                                                <td width="15"></td>
                                                <td width="650">
                                                    <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                    <tbody>
                                                        <tr>
                                                        <td>
                                                            <div>
                                                            <h2>Petroip webiste</h2>
                                                            <div>
                                                                <div>
                                                                <div style="background-color: #fff; border: 1px solid transparent; border-radius: 4px; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05); margin-bottom: 20px; border-color: #bdbdbd;">
                                                                    <div style="border-top-left-radius: 3px; border-top-right-radius: 3px; padding: 10px 15px; background-color: #5b5b5b; color: #fff">Contact detail</div>
                                                                    <table style="border-collapse: collapse; border-spacing: 0; margin-bottom: 0; width: 100%; background-color: transparent; max-width: 100%">
                                                                    <tbody>
                                                                        <tr>
                                                                        <td style="border-top: 1px solid #ddd; padding: 8px">Name</td>
                                                                        <td style="border-top: 1px solid #ddd; padding: 8px">'.$name.'</td>
                                                                        </tr>
                                                                        <tr>
                                                                        <td style="border-top: 1px solid #ddd; padding: 8px">Email</td>
                                                                        <td style="border-top: 1px solid #ddd; padding: 8px"><a href="mailto:'.$email.'">'.$email.'</a></td>
                                                                        </tr>
                                                                        <tr>
                                                                        <td style="border-top: 1px solid #ddd; padding: 8px">Message</td>
                                                                        <td style="border-top: 1px solid #ddd; padding: 8px">'.$message.'</td>
                                                                        </tr>
                                                                    </tbody>
                                                                    </table>
                                                                </div>
                                                                </div>
                                                            </div>
                                                            </div>
                                                        </td>
                                                        </tr>
                                                    </tbody>
                                                    </table>
                                                </td>
                                                <td width="15"></td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>';

    // Set the email recipient
    $to = get_option( 'admin_email' );

    // Set the email subject
    $subject = sprintf( __( 'New contact form submission from %s', 'rx-contact-form' ), $name );

    // Set the email headers
    $headers = array(
        'From: ' . $name . ' <' . $email . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
        'Content-Type: text/html; charset=UTF-8',
    );

    // Send the email
    if ( wp_mail( $to, $subject, $template, $headers ) ) {
        wp_send_json_success( __( 'Thank you for your message!', 'rx-contact-form' ) );
    } else {
        wp_send_json_error( __( 'Oops, something went wrong. Please try again later.', 'rx-contact-form' ) );
    }

    exit;
}

// Add the settings page for the plugin
add_action( 'admin_menu', 'rx_contact_form_settings_menu' );
function rx_contact_form_settings_menu() {
    add_options_page(
        __( 'Rx Contact Form Settings', 'rx-contact-form' ),
        __( 'Rx Contact Form', 'rx-contact-form' ),
        'manage_options',
        'rx-contact-form-settings',
        'rx_contact_form_settings_page'
    );
}

// Register the plugin settings
add_action( 'admin_init', 'rx_contact_form_register_settings' );
function rx_contact_form_register_settings() {
    register_setting( 'rx_contact_form_settings', 'rx_contact_form_email' );
}

// Define the settings page
function rx_contact_form_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Rx Contact Form Settings', 'rx-contact-form' ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'rx_contact_form_settings' ); ?>
            <?php do_settings_sections( 'rx_contact_form_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e( 'Email Address', 'rx-contact-form' ); ?></th>
                    <td>
                        <input type="email" class="regular-text" name="rx_contact_form_email" value="<?php echo esc_attr( get_option( 'rx_contact_form_email' ) ); ?>">
                        <p class="description"><?php _e( 'Enter the email address where you want to receive contact form submissions.', 'rx-contact-form' ); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Add the plugin settings link to the plugins page
add_filter( 'plugin_action_links_' .__FILE__, 'rx_contact_form_settings_link' );
function rx_contact_form_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=rx-contact-form-settings">' . __( 'Settings', 'rx-contact-form' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}