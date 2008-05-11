<?php
/*
Plugin Name: ImmerStat
Plugin URI: http://scompt.com/projects/immerstat
Description: Replaces the Flash-based WordPress.com stats display on the dashboard with an ever-present .PNG in the top-right corner.
Author: Edward Dale
Author URI: http://scompt.com/
Version: 0.5
*/

class ImmerStat {
    /**
     * Make sure we've got all we need to run and the run.
     */
    function ImmerStat() {
        if( function_exists('stats_get_csv') && current_user_can( 'manage_options' ) ) {
            $this->load();
        }
    }

    /**
     * Gets the view count from the WordPress.com Stats plugin and saves it
     * in a form that we can use later in the footer.
     *
     * TODO: If the stats haven't changed, don't do all this stuff.
     */
    function load() {
        $num_days = apply_filters('immerstat_days', 30);
        $from_wp = stats_get_csv('views', array('days'=>$num_days, 'limit'=>$num_days, 'summarize'=>false));
        $data = array();
        $max = $min = $from_wp[0]['views'];
        foreach( $from_wp as $day ) {
            if( $max<$day['views'] ) $max=$day['views'];
            if( $min>$day['views'] ) $min=$day['views'];
            $data []= $day['views'];
        }

        $stats = array('data'=>implode($data, ','), 'min'=>$min, 'max'=>$max, 'days'=>$num_days, 'current'=>$data[count($data)-1]);
        update_option('immerstat_data', $stats);

        add_action('admin_footer', array(&$this, 'footer'));
        add_filter( 'wp_dashboard_widgets', array(&$this,'remove_dashboard_widget'), 11, 1);
    }

    /**
     * Removes the WordPress.com Stats widget from the dashboard.
     */
    function remove_dashboard_widget($widgets) {
    	array_splice($widgets, array_search( "dashboard_stats", $widgets), 1 );
        return $widgets;
    }

    /**
     * Figure out which colors to use, build the Google Charts API URL,
     * and echo it all out.
     */
    function footer() {
    	global $_wp_admin_css_colors;

        // Figure out a good color scheme to use.
    	$scheme_name = get_user_option('admin_color');
    	if ( empty($scheme_name) || !isset($_wp_admin_css_colors[$scheme_name]) || $scheme_name =='fresh' ) {
            $colors = array('bg'=>'E4F2FD', 'under'=>'2583AD', 'line'=>'D54E21');
    	} else if( $scheme_name == 'classic' ) {
            $colors = array('bg'=>'14568A', 'under'=>'CFEBF6', 'line'=>'D54E21');
    	} else {
            $colors = array('bg'=>'FF0000', 'under'=>'00FF00', 'line'=>'0000FF');
            $scheme = $_wp_admin_css_colors[$scheme_name];
            for( $i=0; $i<3; $i++ ) {
                if( isset( $scheme->colors[$i])) $colors[$i] = substr($scheme->colors[$i],1);
            }
    	}    	
        $colors = apply_filters('immerstat_colors', $colors);
        
        $stats = get_option('immerstat_data');
        $current_ratio = (float)(($stats['current']-$stats['min'])/($stats['max']-$stats['min']));
        $width = $stats['days']*4;

        $link_args = array('chm'  => "h,{$colors['line']},0,$current_ratio,0.5|B,{$colors['under']},0,0,0",
                           'chco' => $colors['line'],
                           'chf'  => "bg,s,{$colors['bg']}",
                           'chs'  => "{$width}x60",
                           'cht'  => 'ls',
                           'chd'  => "t:{$stats['data']}",
                           'chds' => "{$stats['min']},{$stats['max']}" );
        
        $link_args = apply_filters('immerstat_img_link_args', $link_args);
        $link = add_query_arg($link_args, 'http://chart.apis.google.com/chart?')        
?>
<script type="text/javascript">
    var img = jQuery('<a href="index.php?page=stats"><img title="<?php echo $stats['current'] ?>" alt="<?php echo $stats['current'] ?>" style="position:absolute;top:35px;right:20px;" height="60" width="<?php echo $width ?>" src="<?php echo $link ?>" /></a>');
    jQuery('#wphead').append(img);
</script>
<?php
    }
}

// Get things going, if we're in the admin section.
add_action('admin_init', create_function('', 'new ImmerStat();'));
?>