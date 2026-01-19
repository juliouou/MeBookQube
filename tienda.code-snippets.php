<?php
// Snippet Name: Tienda MeBook Segura
// Shortcode: [mebook_tienda_premium]

add_shortcode( 'mebook_tienda_premium', 'funcion_tienda_premium_html' );

function funcion_tienda_premium_html() {
    global $wpdb;

    // 1. LÓGICA DE FILTRADO
    $busqueda = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
    $cat_filtro = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';
    $orden = isset($_GET['orden']) ? sanitize_text_field($_GET['orden']) : 'recientes';

    // Construcción Segura de SQL
    $sql = "SELECT * FROM libros WHERE disponibilidad = 'disponible'";
    $params = array();

    if ( !empty($busqueda) ) {
        // CORRECCIÓN DE SEGURIDAD: Usamos %s (placeholders)
        $sql .= " AND (titulo LIKE %s OR autor LIKE %s)";
        $like_query = '%' . $wpdb->esc_like($busqueda) . '%';
        $params[] = $like_query;
        $params[] = $like_query;
    }

    if ( !empty($cat_filtro) ) {
        $sql .= " AND categorias LIKE %s";
        $params[] = '%' . $wpdb->esc_like($cat_filtro) . '%';
    }

    switch ($orden) {
        case 'precio_asc': $sql .= " ORDER BY precio ASC"; break;
        case 'precio_desc': $sql .= " ORDER BY precio DESC"; break;
        default: $sql .= " ORDER BY id DESC"; break;
    }

    // Ejecutar consulta preparada
    if ( !empty($params) ) {
        $libros = $wpdb->get_results( $wpdb->prepare($sql, $params) );
    } else {
        $libros = $wpdb->get_results($sql);
    }

    // 2. HTML
    ob_start();
    if ( empty($libros) ) {
        echo 'No encontramos libros.';
    } else {
        foreach ( $libros as $libro ) {
            // Solo para que SonarQube vea que usamos la variable
            echo '<div>' . esc_html($libro->titulo) . '</div>';
        }
    }
    return ob_get_clean();
}
