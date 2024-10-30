/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready( function() {
    // Put your JS in here, and it will run after the DOM has loaded.
    jQuery('#wiki-pagestate-update-form input#update_pagestate').click(function(){
        jQuery('.ajax-loader').toggle();
        jQuery('#update_pagestate').attr({'disabled': true});
        
        jQuery.post( ajaxurl, {
            action: 'bp_wiki_pagestate_update',
            'cookie': encodeURIComponent(document.cookie),
            '_wpnonce': jQuery("input#_wpnonce-update-pagestate").val(),
            'post_id': jQuery("input[name='post_id']").val(),
            'new_state': jQuery("select[name='new_state']").val()
        },
        function(response) {
                jQuery('.ajax-loader').toggle();
                jQuery('#wiki_pagestate_current').html(response);
                jQuery('#update_pagestate').attr({'disabled': false});
                //jQuery('#update_pagestate').attr({'value': 'Updated'});
                //jQuery('#wiki-pagestate-update-form').append(response);
                //jQuery(response).appendTo('#wiki-pagestate-update-form');
            } );


    });

});

