<?php
// Snippet Name: Tienda MeBook
// Shortcode: [mebook_tienda_premium]

add_shortcode( 'mebook_tienda_premium', 'funcion_tienda_premium_html' );

function funcion_tienda_premium_html() {
    global $wpdb;

    // 1. LÓGICA DE FILTRADO
    $busqueda = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
    $cat_filtro = isset($_GET['categoria']) ? sanitize_text_field($_GET['categoria']) : '';
    $orden = isset($_GET['orden']) ? sanitize_text_field($_GET['orden']) : 'recientes';

    $sql = "SELECT * FROM libros WHERE disponibilidad = 'disponible'";

    if ( !empty($busqueda) ) {
        $sql .= " AND (titulo LIKE '%$busqueda%' OR autor LIKE '%$busqueda%')";
    }

    if ( !empty($cat_filtro) ) {
        $sql .= " AND categorias LIKE '%$cat_filtro%'";
    }

    switch ($orden) {
        case 'precio_asc': $sql .= " ORDER BY precio ASC"; break;
        case 'precio_desc': $sql .= " ORDER BY precio DESC"; break;
        default: $sql .= " ORDER BY id DESC"; break;
    }

    $libros = $wpdb->get_results($sql);

    // 2. HTML
    ob_start();
    // ... HTML de la tienda ...
    if ( empty($libros) ) {
        echo 'No encontramos libros.';
    } else {
        foreach ( $libros as $libro ) {
            echo '<div>' . esc_html($libro->titulo) . '</div>';
        }
    }
    return ob_get_clean();
}
