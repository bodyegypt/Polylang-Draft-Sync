<?php
/**
 * Plugin Name: Polylang Draft Sync
 * Description: Automatically synchronizes draft status across all language versions of a post when any language version is set to draft.
 * Version: 1.2
 * Author: Abdalla Bayoumi
 * Author URI: https://abdallabayoumi.com
 * Text Domain: polylang-draft-sync
 */

// Prevent direct access to the plugin file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Polylang_Draft_Sync {
    
    // Array to store error messages
    private $error_log = array();

    /**
     * Constructor: Sets up the plugin by hooking into WordPress
     */
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Initialize the plugin
     * Checks for Polylang and sets up necessary hooks
     */
    public function init() {
        if ( ! $this->is_polylang_active() ) {
            add_action( 'admin_notices', array( $this, 'polylang_missing_notice' ) );
            return;
        }

        add_action( 'transition_post_status', array( $this, 'sync_draft_status' ), 10, 3 );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        add_action( 'shutdown', array( $this, 'log_errors' ) );
    }

    /**
     * Check if Polylang is active and required functions exist
     *
     * @return bool True if Polylang is active, false otherwise
     */
    public function is_polylang_active() {
        return function_exists( 'pll_languages_list' ) && function_exists( 'pll_get_post_translations' ) && function_exists( 'pll_get_post_language' );
    }

    /**
     * Display admin notice if Polylang is not active
     */
    public function polylang_missing_notice() {
        echo '<div class="error"><p>' . __( 'Polylang Draft Sync requires Polylang to be installed and activated.', 'polylang-draft-sync' ) . '</p></div>';
    }

    /**
     * Synchronize draft status across all translations of a post
     *
     * @param string $new_status New post status
     * @param string $old_status Old post status
     * @param WP_Post $post Post object
     */
    public function sync_draft_status( $new_status, $old_status, $post ) {
        // Only proceed if the status is changing to or from draft
        if ( ! in_array( 'draft', array( $new_status, $old_status ) ) ) {
            return;
        }

        // Get all translations of the post
        $translations = pll_get_post_translations( $post->ID );
        
        if ( empty( $translations ) ) {
            $this->log_error( "No translations found for post {$post->ID}" );
            return;
        }

        $sync_to_status = ( $new_status === 'draft' ) ? 'draft' : $old_status;

        foreach ( $translations as $lang => $translation_id ) {
            if ( $translation_id != $post->ID ) {
                $this->update_post_status( $translation_id, $sync_to_status );
            }
        }

        // Add appropriate admin notice based on the status change
        if ( $new_status === 'draft' ) {
            $this->add_admin_notice( sprintf( 
                __( 'All translations of post %s have been set to draft.', 'polylang-draft-sync' ), 
                $post->ID 
            ) );
        } elseif ( $old_status === 'draft' ) {
            $this->add_admin_notice( sprintf( 
                __( 'All translations of post %s have been restored from draft.', 'polylang-draft-sync' ), 
                $post->ID 
            ) );
        }
    }

    /**
     * Update the status of a given post
     *
     * @param int $post_id ID of the post to update
     * @param string $new_status New status to set
     */
    private function update_post_status( $post_id, $new_status ) {
        $post = get_post( $post_id );
        if ( ! $post ) {
            $this->log_error( "Post {$post_id} not found" );
            return;
        }

        if ( $post->post_status === $new_status ) {
            $this->log_error( "Post {$post_id} is already in {$new_status} status" );
            return;
        }

        $updated_post = wp_update_post( array(
            'ID' => $post_id,
            'post_status' => $new_status
        ), true );

        if ( is_wp_error( $updated_post ) ) {
            $this->log_error( "Failed to update post {$post_id} to status {$new_status}. Error: " . $updated_post->get_error_message() );
        } else {
            $this->log_error( "Successfully updated post {$post_id} to status {$new_status}" );
        }
    }

    /**
     * Log an error message
     *
     * @param string $message Error message to log
     */
    private function log_error( $message ) {
        $this->error_log[] = $message;
    }

    /**
     * Write all logged errors to the error log
     */
    public function log_errors() {
        if ( ! empty( $this->error_log ) ) {
            error_log( "Polylang Draft Sync Errors:\n" . implode( "\n", $this->error_log ) );
        }
    }

    /**
     * Add an admin notice to be displayed
     *
     * @param string $message Notice message
     */
    private function add_admin_notice( $message ) {
        $notices = get_option( 'polylang_draft_sync_notices', array() );
        $notices[] = $message;
        update_option( 'polylang_draft_sync_notices', $notices );
    }

    /**
     * Display admin notices and errors
     */
    public function admin_notices() {
        $notices = get_option( 'polylang_draft_sync_notices', array() );
        foreach ( $notices as $notice ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $notice ) . '</p></div>';
        }
        delete_option( 'polylang_draft_sync_notices' );

        if ( ! empty( $this->error_log ) ) {
            echo '<div class="error"><p>' . __( 'Polylang Draft Sync encountered errors. Please check the error log for details.', 'polylang-draft-sync' ) . '</p></div>';
        }
    }
}

// Instantiate the plugin
new Polylang_Draft_Sync();
