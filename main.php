<?php
/**
 * Plugin Name: Advent of Code PHP Plugin
 * Plugin URI: https://github.com/bv1998
 * Description: This is a plugin that will allow you to add your advent of code to your site and run the code based on a shortcode
 * Version: 1.0
 * Author: Bryan Vernon
 * Author URI: https://github.com/bv1998
 * License: GPL3
 */
 
/*  Copyright 2023  BRYAN VERNON  bryan.vernon9@icloud.com
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as 
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function create_advent_post_type(){
    register_post_type('advent_code_days',
        array(
            'labels'=> array(
                'name' => __('Advent Of Code Days'),
                'singular_name'=> __('Advent of Code Day')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'advent_of_code_day'),
            'supports' => array('title', 'editor'),
        )
        );
}
add_action( 'init', 'create_advent_post_type');

function advent_shortcode($atts){
    $attributes = shortcode_atts(array(
            'year' => date('Y'),
            'day' => 1,
        ), $atts);
        
    $year = intval($attributes['year']);
    $day = intval($attributes['day']);

    $args = array(
        'post_type' => 'advent_code_days',
        'meta_query' => array(
            array(
                'key' => 'year',
                'value' => $year,
                'compare' => '=',
            ),
            array(
                'key' => 'day',
                'value' => $day,
                'compare' => '=',
            ),
        ),
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            the_content();
        }
        wp_reset_postdata();
        return run_advent_code('x',ob_get_clean());
        // return ob_get_clean();
    } else {
        return 'Custom post not found for the specified year and day.';
    }
}
add_shortcode('advent_shortcode', 'advent_shortcode');


function run_advent_code($dataset, $code){
    $code = '$myfile = "' . $dataset . '";' . $code;
    $i = null;
    ob_start();
    eval($code);
    $output = ob_get_clean();
    return $output;
}



