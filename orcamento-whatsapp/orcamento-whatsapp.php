<?php
/*
Plugin Name: Orçamentos por WhatsApp
Description: Adiciona um botão para enviar mensagem no WhatsApp em cada post.
Version: 1.0
Author: Guilherme Afonso [guilhermeafonso.dev.br]
*/

function enqueue_plugin_styles() {
    wp_enqueue_style('orcamento-whatsapp-style', plugins_url('orcamento-whatsapp/orcamento-whatsapp.css'));
}
add_action('wp_enqueue_scripts', 'enqueue_plugin_styles');

// Adiciona uma guia de configurações para o plugin
function orcamento_button_settings_page() {
    add_menu_page(
        'Orçamento',
        'Orçamento',
        'manage_options',
        'orcamento-button',
        'orcamento_button_settings_page_content',
        'dashicons-money-alt',
        99
    );
    
}

add_action('admin_menu', 'orcamento_button_settings_page');

// Conteúdo da página de configurações
function orcamento_button_settings_page_content() {
    ?>
    <div class="wrap">
        <h1>Configurações do Orçamento WhatsApp</h1>
        <form method="post" action="options.php">
            <?php settings_fields('orcamento_button_settings'); ?>
            <?php do_settings_sections('orcamento-button'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Campos de configurações e registro
function orcamento_button_settings_init() {
    add_settings_section(
        'orcamento_button_settings_section',
        'PREENCHA NO FORMATO: BR DDD NÚMERO, COMO DESCRITO NO CAMPO',
        '',
        'orcamento-button'
    );

    add_settings_field(
        'orcamento_button_phone_number',
        'Número de telefone do WhatsApp',
        'orcamento_button_phone_number_callback',
        'orcamento-button',
        'orcamento_button_settings_section'
    );

    register_setting(
        'orcamento_button_settings',
        'orcamento_button_phone_number'
    );
}

add_action('admin_init', 'orcamento_button_settings_init');

// Callback para o campo do número de telefone
function orcamento_button_phone_number_callback() {
    $phone_number = get_option('orcamento_button_phone_number');
    ?>
    <input type="text" name="orcamento_button_phone_number" value="<?php echo esc_attr($phone_number); ?>" placeholder="Ex.: 5500999999999" />
    <?php
}

// Shortcode para exibir o botão do WhatsApp
function solicitar_orcamento_shortcode($atts) {
    $phone_number = get_option('orcamento_button_phone_number');

    if (!empty($phone_number)) {
        $atts = shortcode_atts(array(
            'message' => 'Olá!+Gostaria+de+fazer+o+orçamento+deste+produto.',
        ), $atts);

        $post_link = get_permalink();
        $message = urlencode($atts['message'] . ' ' . $post_link);
        $whatsapp_url = 'https://wa.me/' . $phone_number . '?text=' . $message;

        $orcamento_button = '<a class="orcamento-button" href="' . $whatsapp_url . '" target="_blank"><i class="fab fa-whatsapp"></i> Solicitar um orçamento</a>';

        return '<div class="orcamento-button-container">' . $orcamento_button . '</div>';
    } else {
        return '';
    }
}

add_shortcode('solicitar_orcamento', 'solicitar_orcamento_shortcode');

?>