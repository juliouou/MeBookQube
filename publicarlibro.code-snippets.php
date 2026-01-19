<?php
// Snippet Name: Publicar Libro MeBook
// Shortcode: [mebook_publicar_premium]

add_shortcode( 'mebook_publicar_premium', 'funcion_publicar_libro_blindado' );

function funcion_publicar_libro_blindado() {
    
    // 1. SEGURIDAD
    if ( !is_user_logged_in() ) {
        $url_login = home_url('/mebook_perfil/'); 
        return '<script>
            if(confirm("⚠️ Para vender, primero necesitas identificarte.\n\n¿Quieres ir a crear tu cuenta ahora?")) {
                window.location.href = "' . $url_login . '";
            } else {
                window.history.back();
            }
        </script>';
    }

    $mensaje_estado = '';

    // 2. PROCESAMIENTO
    if ( isset($_POST['titulo_libro']) && wp_verify_nonce($_POST['mebook_publicar_nonce'], 'publicar_libro_accion') ) {
        
        global $wpdb;
        $current_user = wp_get_current_user();
        $errores = [];

        // Sanitización
        $titulo = sanitize_text_field($_POST['titulo_libro']);
        $autor  = sanitize_text_field($_POST['autor_libro']);
        $precio = floatval($_POST['precio_libro']);
        $estado = sanitize_text_field($_POST['estado_libro']);
        $desc   = sanitize_textarea_field($_POST['descripcion_libro']);
        $cats_raw = isset($_POST['cats_libro']) ? $_POST['cats_libro'] : array();

        // Validaciones
        if ( empty($titulo) ) $errores[] = "El Título es obligatorio.";
        if ( empty($autor) )  $errores[] = "El Autor es obligatorio.";
        if ( $precio <= 0 )   $errores[] = "El precio debe ser mayor a 0.";
        if ( empty($cats_raw) ) $errores[] = "Debes seleccionar al menos una categoría.";

        if ( empty($_FILES['portada_libro']['name']) ) {
            $errores[] = "La foto es obligatoria.";
        }

        // Guardar si no hay errores
        if ( count($errores) == 0 ) {
            if ( !function_exists('wp_handle_upload') ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
            $uploadedfile = $_FILES['portada_libro'];
            $movefile = wp_handle_upload( $uploadedfile, array( 'test_form' => false ) );

            if ( $movefile && !isset( $movefile['error'] ) ) {
                $imagen_url = $movefile['url'];
                $cats_str = implode(', ', array_map('sanitize_text_field', $cats_raw));
                
                $wpdb->insert('libros', array(
                    'usuario_id'     => $current_user->ID,
                    'titulo'         => $titulo,
                    'autor'          => $autor,
                    'categorias'     => $cats_str,
                    'descripcion'    => $desc,
                    'estado'         => $estado,
                    'precio'         => $precio,
                    'imagen'         => $imagen_url,
                    'disponibilidad' => 'disponible',
                    'fecha_publicacion' => current_time('mysql')
                ));
                
                echo '<script>window.location.href="'.home_url('/tienda').'";</script>';
                exit;
            }
        }
    }
    
    // 3. HTML (Resumido para análisis)
    return '<form>...</form>'; 
}
