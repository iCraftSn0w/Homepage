<?php

/*
 * Blocks WP add-ons
 *
 * Created by: Pinegrow.com
 *
 */

/* Custom customizer control */
if( ! class_exists('WP_BlocksContentDropdown_Customize_Control')):

    class WP_BlocksContentDropdown_Customize_Control extends WP_Customize_Control {
        public $type = 'blocks_content_dropdown';

        public function render_content() {
            $dropdown = wp_dropdown_pages(
                array(
                    'name'              => '_customize-dropdown-blocks-content-' . $this->id,
                    'echo'              => 0,
                    'show_option_none'  => __( '&mdash; Select &mdash;' ),
                    'option_none_value' => '0',
                    'selected'          => $this->value(),
                    'post_type'         => 'blocks_content'
                )
            );

            // Hackily add in the data link parameter.
            $dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

            printf(
                '<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
                $this->label,
                $dropdown
            );
        }
    }
endif;
?>