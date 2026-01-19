<?php
// Snippet Name: Cuenta MeBook Unificada
// Shortcode: [mebook_perfil]

add_shortcode('mebook_perfil', 'mebook_control_total_cuenta');

function mebook_control_total_cuenta() {
    global $wpdb;

    // PROCESAR LOGIN
    if ( isset($_POST['mebook_login_submit']) ) {
        $creds = array(
            'user_login'    => sanitize_text_field($_POST['log_user']),
            'user_password' => $_POST['log_pass'],
            'remember'      => true
        );
        $user = wp_signon( $creds, false );
        if ( !is_wp_error($user) ) {
            echo '<script>window.location.reload();</script>';
            exit;
        }
    }

    // RENDERIZADO
    if (!is_user_logged_in()) {
        // Formulario de Login/Registro
        return '<div>Formulario de Login...</div>';
    } else {
        // Dashboard del Usuario
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $mis_libros = $wpdb->get_results("SELECT * FROM libros WHERE usuario_id = $user_id");
        
        return '<div>Bienvenido ' . esc_html($current_user->display_name) . '</div>';
    }
}
