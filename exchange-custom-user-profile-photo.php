<?php
/**
 * Plugin Name: Exchange Custom User Profile Photo
 * Plugin URI: http://3five.com
 * Description: A simple and effective custom WordPress user profile photo plugin. Based upon the plugin by 3five: contains fix for the Exchange Plugin/Theme project.
 * Author: 3five
 * Author URI: http://3five.com
 * Text Domain: exchange-custom-user-profile-photo
 * Domain Path: /languages/
 * Version: 0.4
 **/

/**
 * This program has been developed for use with the WordPress Software.
 *
 * It is distributed as free software with the intent that it will be
 * useful and does not ship with any WARRANTY.
 *
 * USAGE
 * // Default:
 * This will override the WordPress get_avatar hook
 *
 * // Custom placement:
 * <?php $imgURL = get_cupp_meta( $user_id, $size ); ?>
 * or
 * <img src="<?php echo get_cupp_meta( $user_id, $size ); ?>">
 *
 * Beginner WordPress template editing skill required. Place the above tag in your template and provide the two parameters.
 * @param $user_id    Default: $post->post_author. Will accept any valid user ID passed into this parameter.
 * @param $size       Default: 'thumbnail'. Accepts all default WordPress sizes and any custom sizes made by the add_image_size() function.
 * @return {url}      Use this inside the src attribute of an image tag or where you need to call the image url.
 *
 * Inquiries, suggestions and feedback can be sent to support@3five.com
 *
 * This is plugin is intented for Author, Editor and Admin role post/page authors. Thank you for downloading our plugin.
 * We hope this WordPress plugin meets your needs.
 *
 * Happy coding!
 * - 3five
 *
 * Resources:
 *  • Steven Slack - http://s2web.staging.wpengine.com/226/
 *  • Pippin Williamson - https://gist.github.com/pippinsplugins/29bebb740e09e395dc06
 *  • Mike Jolley - https://gist.github.com/mikejolley/3a3b366cb62661727263#file-gistfile1-php
 *
 */


// Enqueue scripts and styles
add_action( 'admin_enqueue_scripts', 'cupp_enqueue_scripts_styles' );
function cupp_enqueue_scripts_styles() {
    // Register
    wp_register_style( 'cupp_admin_css', plugins_url( 'exchange-custom-user-profile-photo/css/styles.css' ), false, '1.0.0', 'all' );
    wp_register_script( 'cupp_admin_js', plugins_url( 'exchange-custom-user-profile-photo/js/scripts.js' ), array('jquery'), '1.0.0', true );

    // Enqueue
    wp_enqueue_style( 'cupp_admin_css' );
    wp_enqueue_script( 'cupp_admin_js' );
}

// Show the new image field in the user profile page.
add_action( 'show_user_profile', 'cupp_profile_img_fields' );
add_action( 'edit_user_profile', 'cupp_profile_img_fields' );

function cupp_profile_img_fields( $user ) {
    if(!current_user_can('upload_files'))
        return false;

    // vars
    $cupp_url = get_the_author_meta( 'cupp_meta', $user->ID );
    $cupp_upload_url = get_the_author_meta( 'cupp_upload_meta', $user->ID );
    $cupp_upload_edit_url = get_the_author_meta( 'cupp_upload_edit_meta', $user->ID );

    if(!$cupp_upload_url){
        $btn_text = 'Upload New Image';
    } else {
        $cupp_upload_edit_url = get_site_url().get_the_author_meta( 'cupp_upload_edit_meta', $user->ID );
        $btn_text = 'Change Current Image';
    }
    ?>

    <div id="cupp_container">
    <h3><?php _e( 'Custom User Profile Photo', 'exchange-custom-user-profile-photo' ); ?></h3>

    <table class="form-table">

        <tr>
            <th><label for="cupp_meta"><?php _e( 'Profile Photo', 'exchange-custom-user-profile-photo' ); ?></label></th>
            <td>
                <!-- Outputs the image after save -->
                <div id="current_img">
                    <?php if($cupp_upload_url): ?>
                        <img src="<?php echo esc_url( $cupp_upload_url ); ?>" class="cupp-current-img">
                        <div class="edit_options uploaded">
                            <a class="remove_img"><span>Remove</span></a>
                            <a href="<?php echo $cupp_upload_edit_url; ?>" class="edit_img" target="_blank"><span>Edit</span></a>
                        </div>
                    <?php elseif($cupp_url) : ?>
                        <img src="<?php echo esc_url( $cupp_url ); ?>" class="cupp-current-img">
                        <div class="edit_options single">
                            <a class="remove_img"><span>Remove</span></a>
                        </div>
                    <?php else : ?>
                        <img src="<?php echo plugins_url( 'exchange-custom-user-profile-photo/img/placeholder.gif' ); ?>" class="cupp-current-img placeholder">
                    <?php endif; ?>
                </div>

                <!-- Select an option: Upload to WPMU or External URL -->
                <div id="cupp_options">
                    <input type="radio" id="upload_option" name="img_option" value="upload" class="tog" checked>
                    <label for="upload_option">Upload New Image</label><br>
                    <input type="radio" id="external_option" name="img_option" value="external" class="tog">
                    <label for="external_option">Use External URL</label><br>
                </div>

                <!-- Hold the value here if this is a WPMU image -->
                <div id="cupp_upload">
                    <input type="hidden" name="cupp_placeholder_meta" id="cupp_placeholder_meta" value="<?php echo plugins_url( 'exchange-custom-user-profile-photo/img/placeholder.gif' ); ?>" class="hidden" />
                    <input type="hidden" name="cupp_upload_meta" id="cupp_upload_meta" value="<?php echo esc_url_raw( $cupp_upload_url ); ?>" class="hidden" />
                    <input type="hidden" name="cupp_upload_edit_meta" id="cupp_upload_edit_meta" value="<?php echo esc_url_raw( $cupp_upload_edit_url ); ?>" class="hidden" />
                    <input type='button' class="cupp_wpmu_button button-primary" value="<?php _e( $btn_text, 'exchange-custom-user-profile-photo' ); ?>" id="uploadimage"/><br />
                </div>
                <!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
                <div id="cupp_external">
                    <input type="text" name="cupp_meta" id="cupp_meta" value="<?php echo esc_url_raw( $cupp_url ); ?>" class="regular-text" />
                </div>
                <!-- Outputs the save button -->
                <span class="description"><?php _e( 'Upload a custom photo for your user profile or use a URL to a pre-existing photo.', 'exchange-custom-user-profile-photo' ); ?></span>
                <p class="description"><?php _e('Update Profile to save your changes.', 'exchange-custom-user-profile-photo'); ?></p>
            </td>
        </tr>

    </table><!-- end form-table -->
</div> <!-- end #cupp_container -->

    <?php wp_enqueue_media(); // Enqueue the WordPress Media Uploader ?>

<?php }

// Save the new user CUPP url.
add_action( 'personal_options_update', 'cupp_save_img_meta' );
add_action( 'edit_user_profile_update', 'cupp_save_img_meta' );

function cupp_save_img_meta( $user_id ) {

    if ( !current_user_can( 'upload_files', $user_id ) )
        return false;

    // If the current user can edit Users, allow this.
    update_user_meta( $user_id, 'cupp_meta', $_POST['cupp_meta'] );
    update_user_meta( $user_id, 'cupp_upload_meta', $_POST['cupp_upload_meta'] );
    update_user_meta( $user_id, 'cupp_upload_edit_meta', $_POST['cupp_upload_edit_meta'] );
}

/**
 * Retrieve the appropriate image size
 *
 * @param $user_id    Default: $post->post_author. Will accept any valid user ID passed into this parameter.
 * @param $size       Default: 'thumbnail'. Accepts all default WordPress sizes and any custom sizes made by the add_image_size() function.
 * @return {url}      Use this inside the src attribute of an image tag or where you need to call the image url.
 */
function get_cupp_meta( $user_id, $size ) {
    global $post;

    //allow the user to specify the image size
    if (!$size){
        $size = 'thumbnail'; // Default image size if not specified.
    }
    if(!$user_id || !is_numeric( $user_id ) ){
        // Here we're assuming that the avatar being called is the author of the post.
        // The theory is that when a number is not supplied, this function is being used to
        // get the avatar of a post author using get_avatar() and an email address is supplied
        // for the $id_or_email parameter. We need an integer to get the custom image so we force that here.
        // Also, many themes use get_avatar on the single post pages and pass it the author email address so this
        // acts as a fall back.
        $user_id = $post->post_author;
    }

    // get the custom uploaded image
    $attachment_upload_url = esc_url( get_the_author_meta( 'cupp_upload_meta', $user_id ) );

    // get the external image
    $attachment_ext_url = esc_url( get_the_author_meta( 'cupp_meta', $user_id ) );
    $attachment_url = '';
    $image_url = '';
    if($attachment_upload_url){
        $attachment_url = $attachment_upload_url;

        // grabs the id from the URL using the WordPress function attachment_url_to_postid @since 4.0.0
        $attachment_id = attachment_url_to_postid( $attachment_url );

        // retrieve the thumbnail size of our image
        $image_thumb = wp_get_attachment_image_src( $attachment_id, $size );
        $image_url = $image_thumb[0];

    } elseif($attachment_ext_url) {
        $image_url = $attachment_ext_url;
    }

    if ( empty($image_url) )
        return;

    // return the image thumbnail
    return $image_url;
}

/**
 * WordPress Avatar Filter
 *
 * Replaces the WordPress avatar with your custom photo using the get_avatar hook.
 */
add_filter( 'get_avatar', 'cupp_avatar' , 1 , 5 );

function cupp_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
    $user = false;
    $id = false;

    if ( is_numeric( $id_or_email ) ) {

        $id = (int) $id_or_email;
        $user = get_user_by( 'id' , $id );

    } elseif ( is_object( $id_or_email ) ) {

        if ( ! empty( $id_or_email->user_id ) ) {
            $id = (int) $id_or_email->user_id;
            $user = get_user_by( 'id' , $id );
        }

    } else {
        // $id = (int) $id_or_email;
        $user = get_user_by( 'email', $id_or_email );
    }

    if ( $user && is_object( $user ) ) {

        $custom_avatar = get_cupp_meta($id, 'thumbnail');

        if (isset($custom_avatar) && !empty($custom_avatar)) {
            $avatar = "<img alt='{$alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
        }

    }

    return $avatar;
}

?>
