<?php


class PRWP_MediaInterface
{
    public function __construct(){
        $this->labels = null;
        add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
        /*add_action( 'wp_enqueue_media', function () {
            remove_action( 'admin_footer', 'wp_print_media_templates' );
            add_action( 'admin_footer', array( $this, 'printMediatpl' ) );
        } );*/
        add_filter( 'attachment_fields_to_edit', array( $this, 'printMediatpl' ), 10, 2 );

    }

    function add_metaboxes(){
        add_meta_box('prwp_attachment_info', __('Photo Recognition','prwp'), array( $this, 'prwp_attachment_info' ), 'attachment', 'side', 'low');
    }

    function prwp_attachment_info() {
        echo $this->labelsHtmlSection();

    }

    function printMediatpl( $form_fields, $post ) {

        $form_fields["prwpmedia"]["label"] = __('Photo Recognition','prwp');
        $form_fields["prwpmedia"]["input"] = "html";
        $form_fields["prwpmedia"]["html"] = $this->labelsHtmlSection($post->ID);

        return $form_fields;
    }

    /*function printMediatpl(){
        ob_start();
        wp_print_media_templates();
        $tpl = ob_get_clean();
        // To future-proof a bit, search first for the template and then for the section.
        if ( (( $idx = strpos( $tpl, 'tmpl-attachment-details' ) ) !== false
                || ( $idx = strpos( $tpl, 'tmpl-attachment-details-two-column' ) ) !== false)
            && ( $before_idx = strpos( $tpl, '<div class="attachment-compat">', $idx ) ) !== false ) {
            ob_start();
            ?>
            <label class="setting prwp_attachment_info">
                <span><?php _e('Photo Recognition','prwp'); ?></span>
                <span class="value">
                    <strong><?php _e('Labels','prwp'); ?>:</strong>
                    <div class="prwp-ajax-labelsHtmlUl" data-id="{{ data.id }}">
                    </div>
                </span>
            </label>
            <?php
            $my_section = ob_get_clean();
            $tpl = substr_replace( $tpl, $my_section, $before_idx, 0 );
            //$tpl = $tpl.$my_section;
        }
        echo $tpl;
    }*/

    function labelsHtmlSection($post_id=''){
        if (empty($post_id)) {
            global $post;
            $post_id = $post->ID;
        }

        $this->labels = new PRWP_Labels($post_id);
        ob_start();
        if (!empty($this->labels)){
            echo '<h4>'.__('Labels','prwp').':</h4>';
            echo '<div class="prwp-ajax-labelsHtmlUl" data-id="'.$post_id.'">';
            $this->labels->showLabelsHtmlUl();
            echo '</div>';
        }
        $return = ob_get_clean();
        return $return;
    }

    //	do_action( 'print_media_templates' );
    /* 				<a class="view-attachment" href="{{ data.link }}"><?php _e( 'View attachment page' ); ?></a> */


/*remove_action( 'wp_footer', 'wp_print_media_templates' );
remove_action( 'admin_footer', 'wp_print_media_templates' );
add_action( 'admin_footer', 'my_wp_print_media_templates' );
add_action( 'wp_footer', 'my_wp_print_media_templates' );*/

    /*add_action( 'wp_enqueue_media', function () {
    if ( ! remove_action( 'admin_footer', 'wp_print_media_templates' ) ) {
        error_log("remove_action fail");
    }
    add_action( 'admin_footer', 'my_print_media_templates' );
    } );*/

    /*function my_print_media_templates() {
        $replaces = array(
            '/<option value="center"/' => '<option value="leftsuper">' . esc_attr__('LeftSuper') . '</option>$0',
            '/<option value="none"/' => '<option value="rightsuper">' . esc_attr__('RightSuper') . '</option>$0',
            '/<button class="button" value="center">/' => '<button class="button" value="leftsuper">' . esc_attr__('LeftSuper') . '</button>$0',
            '/<button class="button active" value="none">/' => '<button class="button" value="rightsuper">' . esc_attr__('RightSuper') . '</button>$0',
        );
        ob_start();
        wp_print_media_templates();
        echo preg_replace( array_keys( $replaces ), array_values( $replaces ), ob_get_clean() );
    }*/

    //wp_print_media_templates()
//++++++++++++++++++++++++++++++++++
//http://wordpress.stackexchange.com/questions/215979/wp-media-view-imagedetails-save-settings-as-html5-data-attributes-for-image/217428

}


/*add_action( 'wp_enqueue_media', function () {
    add_action( 'admin_footer', function () {
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                if (wp && wp.media && wp.media.events) {
                    wp.media.events.on( 'editor:image-edit', function (data) {
                        alert(1);
                        data.metadata.my_setting = 'aaaa'; //data.editor.dom.getAttrib( data.image, 'data-my_setting' );
                    } );
                    wp.media.events.on( 'editor:image-update', function (data) {
                        alert(2);
                        data.editor.dom.setAttrib( data.image, 'data-my_setting', data.metadata.my_setting );
                    } );
                }
            });
        </script>
        <?php
    }, 11 );
} );*/



