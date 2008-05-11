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
    function ImmerStat() {
        $this->loaded();
    }

    function loaded() {
        if( function_exists('stats_get_csv') ) {
            $bob = stats_get_csv('views', array('days'=>'30', 'limit'=>'30', 'summarize'=>false));
            $data = array();
            $max = 0;
            foreach( $bob as $day ) {
                if( $max<$day['views'] ) $max=$day['views'];
                $data []= $day['views'];
            }

            $bob = implode($data, ',');
            $joe = array('data'=>$bob, 'max'=>$max, 'current'=>$data[count($data)-1]);
            update_option('scompt_bob', $joe);

            add_action('admin_footer', array(&$this, 'footer'));
            add_filter( 'wp_dashboard_widgets', array(&$this,'remove_dashboard_widget'), 11, 1);
        }
    }

    function remove_dashboard_widget($widgets) {
    	array_splice($widgets, array_search( "dashboard_stats", $widgets), 1 );
        return $widgets;
    }

    function footer() {
    	global $_wp_admin_css_colors;

    	$scheme_name = get_user_option('admin_color');
    	if ( empty($scheme_name) || !isset($_wp_admin_css_colors[$scheme_name]) )
    		$scheme_name = 'fresh';
        $scheme = $_wp_admin_css_colors[$scheme_name];

        $colors = array('FF0000', '00FF00', '0000FF', 'FFFF00'); // defaults
        for( $i=0; $i<4; $i++ ) {
            if( isset( $scheme->colors[$i])) $colors[$i] = substr($scheme->colors[$i],1);
        }
        $joe = get_option('scompt_bob');
        $asdf = (float)$joe['current']/$joe['max'];
        $link = "http://chart.apis.google.com/chart?chm=h,{$colors[3]},0,$asdf,0.5|B,{$colors[0]},0,0,0&chco={$colors[1]}&chf=bg,s,{$colors[2]}&chs=120x60&amp;cht=ls&amp;chd=t:{$joe['data']}&amp;chds=0,{$joe['max']}";
    ?>
    <script type="text/javascript">
    var img = jQuery('<a href="index.php?page=stats"><img title="<?php echo $joe['current'] ?>" alt="<?php echo $joe['current'] ?>" style="position:absolute;top:30px;right:20px;" height="60" width="120" src="<?php echo $link ?>" /></a>');
    jQuery('#wphead').append(img);
    </script>
    <?php
    }
}

add_action('admin_init', create_function('', 'new ImmerStat();'));
?>