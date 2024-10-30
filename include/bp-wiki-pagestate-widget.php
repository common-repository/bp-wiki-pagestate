<?php

/**
 * In this file you should create and register widgets for your component.
 *
 * Widgets should be small, contained functionality that a site administrator can drop into
 * a widget enabled zone (column, sidebar etc)
 *
 * Good examples of suitable widget functionality would be short lists of updates or featured content.
 *
 * For example the friends and groups components have widgets to show the active, newest and most popular
 * of each.
 */

function bp_wiki_pagestate_register_widgets() {
    add_action('widgets_init', create_function('', 'return register_widget("BP_Wiki_Pagestate_Widget");') );
}
add_action( 'plugins_loaded', 'bp_wiki_pagestate_register_widgets' );

class BP_Wiki_Pagestate_Widget extends WP_Widget {

    function bp_wiki_pagestate_widget() {
        parent::WP_Widget( false, $name = __( 'Wiki Page State', 'buddypress' ) );
    }

    function widget( $args, $instance ) {
        global $bp;

        extract( $args );

        echo $before_widget;
        echo $before_title .
                $widget_name .
                $after_title;

        $wiki_page = bp_wiki_get_page_from_slug( $bp->action_variables[0] );
        if($wiki_page){
            //TODO: user/security check
            $current_status=get_post_meta($wiki_page->ID, "wiki_page_state", true);
            if(!$current_status){
                $current_status="Unknown";
            }
            ?>
            <form action="" id="wiki-pagestate-update-form">
                <h4><div id='wiki_pagestate_current'><?php 
                if($current_status == "Working"){
                    echo "<img src='" . WP_PLUGIN_URL . "/bp-wiki-pagestate/include/images/working.png' />";
                }
                else if($current_status == "Broken"){
                    echo "<img src='" . WP_PLUGIN_URL . "/bp-wiki-pagestate/include/images/broken.png' />";
                }
                else{
                    echo("Status:" . $current_status);
                }
                ?></div></h4>
                <select id="new_state" name="new_state">
                    <option><?php echo($current_status);?></option>
                    <option>Unknown</option>
                    <option>Working</option>
                    <option>Broken</option>
                </select>
                <input type="hidden" name="post_id" id="post_id" value = "<?php echo("".$wiki_page->ID); ?>"/>
                <?php wp_nonce_field( 'update_pagestate', '_wpnonce-update-pagestate' ); ?>
                <input type="submit" name="update_pagestate" id="update_pagestate" value = "Update"/>
                <span class="ajax-loader"></span>
            </form>

            <?php
            //echo "$wiki_page->ID-";
            //echo "$wiki_page->post_title";
            //get_post_meta($wiki_page->ID, "wiki_page_state");
            //update_post_meta($wiki_page->ID,"wiki_page_state","Works");
        }
        elseif(BP_GROUPS_SLUG == bp_current_component() && "wiki" == bp_current_action()){
            //TODO: list broken pages
            $group_wiki_page_ids_array = maybe_unserialize( groups_get_groupmeta( $bp->groups->current_group->id, 'bp_wiki_group_wiki_page_ids' ) );

            if ( $group_wiki_page_ids_array == '' ) {
                    $no_pages_found = true;
                    return;
            } else {
                    $no_pages_found = false;
            }

            echo "<h6>Wiki Stats:</h6>";
            echo "<ul><li>".sizeof($group_wiki_page_ids_array)." Pages</li></ul>";
            // For each of those pages, check if current user can view based on group settings/membership
            if(bp_group_is_admin()){
                echo "<h6>Broken Pages:</h6>";
                echo "<ul>";
                foreach ( (array)$group_wiki_page_ids_array as $key => $group_wiki_page_id ) {
                    if ( bp_wiki_can_view_wiki_page( $group_wiki_page_id ) ) {
                        $can_view_any_pages = true;
                        $wiki_page = get_post( $group_wiki_page_id );
                        $page_state = get_post_meta( $group_wiki_page_id , 'wiki_page_state', true);
                        if($page_state == 'Broken'){
                            echo '<li><a href="' . bp_wiki_get_group_page_url( $bp->groups->current_group->id, $group_wiki_page_id ) . '">' . $wiki_page->post_title . '</a></li>';
                        }
                    }
                }
                echo "</ul>";
            }
        }

        
        echo $after_widget;
    }
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        /* This is where you update options for this widget */

        $instance['max_items'] = strip_tags( $new_instance['max_items'] );
        $instance['per_page'] = strip_tags( $new_instance['per_page'] );

        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'max_items' => 200, 'per_page' => 25 ) );
        $per_page = strip_tags( $instance['per_page'] );
        $max_items = strip_tags( $instance['max_items'] );
        ?>

        <p><label for="bp-wiki-pagestate-widget-per-page"><?php _e( 'Number of Items Per Page:', 'bp-wiki-pagestate' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'per_page' ); ?>" name="<?php echo $this->get_field_name( 'per_page' ); ?>" type="text" value="<?php echo attribute_escape( $per_page ); ?>" style="width: 30%" /></label></p>
        <p><label for="bp-wiki-pagestate-widget-max"><?php _e( 'Max items to show:', 'bp-wiki-pagestate' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_items' ); ?>" name="<?php echo $this->get_field_name( 'max_items' ); ?>" type="text" value="<?php echo attribute_escape( $max_items ); ?>" style="width: 30%" /></label></p>
        <?php
    }
}

function bp_wiki_pagestate_update() {
    global $bp;
    check_ajax_referer('update_pagestate');//name of nonce field
    if( $_POST['new_state'] == 'Unknown' ){
        delete_post_meta($_POST['post_id'], "wiki_page_state");
        echo "Status: Unknown";
    }
    else{
        update_post_meta($_POST['post_id'], "wiki_page_state", $_POST['new_state']);
        $current_status=get_post_meta($_POST['post_id'], "wiki_page_state", true);
        if($current_status == "Working"){
            echo "<img src='" . WP_PLUGIN_URL . "/bp-wiki-pagestate/include/images/working.png' />";
        }
        else if($current_status == "Broken"){
            echo "<img src='" . WP_PLUGIN_URL . "/bp-wiki-pagestate/include/images/broken.png' />";
        }
        else{
            echo("Status:" . $current_status);
        }
    }
}
add_action( 'wp_ajax_bp_wiki_pagestate_update', 'bp_wiki_pagestate_update' );

?>