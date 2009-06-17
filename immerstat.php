<?php
/*
Plugin Name: ImmerStat
Plugin URI: http://scompt.com/projects/immerstat
Description: ImmerStat places an image in the header of your admin screen with 
             your current WordPress.com pageview statistics.
Author: Edward Dale
Author URI: http://scompt.com/
Version: 0.6
*/

/**
 * ImmerStat places an image in the header of your admin screen with 
 * your current WordPress.com pageview statistics.
 *
 * LICENSE
 * This file is part of ImmerStat.
 *
 * ImmerStat is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package    ImmerStat
 * @author     Edward Dale <scompt@scompt.com>
 * @copyright  Copyright 2008 Edward Dale
 * @license    http://www.gnu.org/licenses/gpl.txt GPL 2.0
 * @version    $Id$
 * @link       http://www.scompt.com/projects/immerstat
 * @since      0.5
 */
class ImmerStat {
    
    // Whether to regenerate stats
    var $do_load = False;
    
    /**
     * Make sure we've got all we need to run and the run.
     */
    function ImmerStat() {
		add_settings_section('immerstat_setting_section', 'ImmerStat', 
		                     array(&$this, 'settings_section'), 'misc');
		add_settings_field('immerstat_days', 'Number of Days', 
		                   array(&$this, 'settings_field'), 
		                   'misc', 'immerstat_setting_section');
		register_setting('misc', 'immerstat_days');
		add_option('immerstat_days', 30);
        
        if( function_exists('stats_get_csv') && current_user_can( 'manage_options' ) ) {
            $this->load();
        }
    }

	/**
	 * There's not much to say at the top of the ImmerStats section.
	 */
	function settings_section() {
		// NOOP
	}

	/**
	 * Shows the input field for the number of days.
	 */
	function settings_field() {
		$days = get_option('immerstat_days');

		echo "<input name='immerstat_days' id='immerstat_days' type='text'
		value='".$days."' class='code' />";		
	}
    
    /**
     * A filter hook used to pick up on whether new stats were downloaded.
     */
    function enable_load($ret) {
        $this->do_load = True;
        return $ret;
    }

    /**
     * Gets the view count from the WordPress.com Stats plugin and saves it
     * in a form that we can use later in the footer.
     */
    function load() {
        $num_days = get_option('immerstat_days');
		$old_data = get_option('immerstat_data');

        // Use this filter to determine when new stats were actually retrieved
        add_filter('update_option_stats_cache', array(&$this, 'enable_load'));
        $from_wp = stats_get_csv('views', array(     'days'=>$num_days, 
                                                    'limit'=>$num_days, 
                                                'summarize'=>false));
        remove_filter('update_option_stats_cache', array(&$this, 'enable_load'));

        $data = array();
        $max = $min = $from_wp[0]['views'];

        if( $this->do_load || (isset($old_data['days']) && 
                               $old_data['days'] != $num_days) ) {
            foreach( $from_wp as $day ) {
                if( $max<$day['views'] ) $max=$day['views'];
                if( $min>$day['views'] ) $min=$day['views'];
                $data []= $day['views'];
            }

            $stats = array('data'=>implode($data, ','), 'min'=>$min, 
                           'max'=>$max, 'days'=>$num_days, 
                           'current'=>$data[count($data)-1]);
            update_option('immerstat_data', $stats);
        }

		// This filter gets called at a convenient place in the header
        add_filter('favorite_actions', array(&$this, 'show_graph'));
    }

    /**
     * Figure out which colors to use, build the Google Charts API URL,
     * and echo it all out.
     */
    function show_graph($ret) {
    	global $_wp_admin_css_colors;

        // Figure out a good color scheme to use.
    	$scheme_name = get_user_option('admin_color');

    	if ( empty($scheme_name) || !isset($_wp_admin_css_colors[$scheme_name]) ) {
			if( $scheme_name == 'fresh' ) {
	            $colors = array('bg'=>'E4F2FD', 'under'=>'2583AD', 'line'=>'D54E21');
	    	} else { // if( $scheme_name == 'classic' ) {
	            $colors = array('bg'=>'14568A', 'under'=>'CFEBF6', 'line'=>'D54E21');
			}
    	} else {
            $colors = array('bg'=>'FF0000', 'under'=>'00FF00', 'line'=>'0000FF');
            $scheme = $_wp_admin_css_colors[$scheme_name];

			if( count($scheme->colors)>3 ) {
				$colors['bg']    = substr($scheme->colors[0],1);
				$colors['under'] = substr($scheme->colors[1],1);
				$colors['line']  = substr($scheme->colors[2],1);
			}
    	}    	
        $colors = apply_filters('immerstat_colors', $colors);
        
        $stats = get_option('immerstat_data');

		if( $stats['max']==$stats['min']) {
			$current_ratio=0;
		} else {
	        $current_ratio = (float)(($stats['current']-$stats['min'])/($stats['max']-$stats['min']));
		}
        $width = $stats['days']*4;

        $link_args = array('chm'  => "h,{$colors['line']},0,$current_ratio,0.5|B,{$colors['under']},0,0,0",
                           'chco' => $colors['line'],
                           'chf'  => "bg,s,{$colors['bg']}",
                           'chs'  => "{$width}x60",
                           'cht'  => 'ls',
                           'chd'  => "t:{$stats['data']}",
                           'chds' => "{$stats['min']},{$stats['max']}" );
        
        $link_args = apply_filters('immerstat_img_link_args', $link_args);
        $link = add_query_arg($link_args, 'http://chart.apis.google.com/chart?');

		?><div id="immerstat" style="float:left">
		<a href="index.php?page=stats">
		<img title="<?php echo $stats['current'] ?>" 
		     alt="<?php echo $stats['current'] ?>" 
		     height="46" width="<?php echo $width ?>" 
		     src="<?php echo $link ?>" />
		</a></div><?php

		// Don't break the filter
		return $ret;
    }
}

// Get things going, if we're in the admin section.
add_action('admin_init', create_function('', 'new ImmerStat();'));
?>