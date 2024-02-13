<?php

// shorcode [user_dash_table_subs_script]


// user table
function user_dashboard_subs_table($atts){
$user_query = new WP_User_Query(array('role' => 'subscriber'));
?>
<table class="form-table">
    <thead>
        <th><?php echo _e('Username','venn'); ?></th>
        <th><?php echo _e('Email','venn'); ?></th>
        <th><?php echo _e('Role','venn'); ?></th>
        <th><?php echo _e('Current Subscription','venn'); ?></th>
        <th><?php echo _e('Status','venn'); ?></th>
    </thead>
    <tbody>
            <?php if(!empty($user_query->results)){ foreach ($user_query->results as $key => $value){  ?>
            <tr>
                <td><?php echo esc_html($value->user_login); ?></td>
                <td><?php echo esc_html($value->user_email); ?></td>
                <td><?php echo _e('Subscriber','venn'); ?></td>
                <td></td>
                <td>
                    <?php  $status=get_user_meta($value->ID,'account_status',true); ?>
                    <select id="venn_status_role" data-id="<?php echo $value->ID; ?>" data-username="<?php echo $value->user_login; ?>" data-email="<?php echo $value->user_email; ?>">
                        <?php if($status=='approved'){ $active="selected"; }else{ $active=''; }  ?>
                        <option value="inactive">Inactive</option>
                        <option value="approved" <?php echo $active; ?>>Active</option>
                    </select>
                </td>
            </tr>
        <?php } } ?>
    </tbody>
     <tfoot>
        <th><?php echo _e('Username','venn'); ?></th>
        <th><?php echo _e('Email','venn'); ?></th>
        <th><?php echo _e('Role','venn'); ?></th>
        <th><?php echo _e('Current Subscription','venn'); ?></th>
        <th><?php echo _e('Status','venn'); ?></th>
    </tfoot>
</table>

<?php	
}
add_shortcode('user_dashboard_subs','user_dashboard_subs_table');

function venn_subscribe_table_action_fn(){
 $status=isset($_POST['status']) ? $_POST['status'] : "";
 $user=isset($_POST['user']) ? $_POST['user'] : "";
 update_user_meta($user,'account_status',$status);
	if($status=='approved'){
		wpven_mail_custom_approved($_POST);
	}else{
		wpven_mail_custom_reject($_POST);
	}
	
 wp_die();
}
add_action('wp_ajax_venn_subscribe_table_action','venn_subscribe_table_action_fn');
add_action('wp_ajax_nopriv_venn_subscribe_table_action','venn_subscribe_table_action_fn');

function user_dash_table_subs_script(){
?>
<script type="text/javascript">
    (function($){
        $(document).on("change","#venn_status_role",function(){
            let status=$(this).val();
            let user=$(this).data("id");
			let username=$(this).data("username");
			let email=$(this).data("email");
            let vpost=$.post('<?php echo admin_url("admin-ajax.php"); ?>',{"username":username,"email":email,"status":status,"user":user,"action":"venn_subscribe_table_action"});
                vpost.done(function(data){
                    window.location.reload();
                });
        });
    })(jQuery);
</script>
<?php
}
add_action('wp_footer', 'user_dash_table_subs_script');

// active status email
function wpven_mail_custom_approved($param){
$to = $param['email'];
$subject = 'Your account at vennclothing is now active';
$body = '<div style="max-width: 560px; padding: 20px; background: #ffffff; border-radius: 5px; margin: 40px auto; font-family: Open Sans,Helvetica,Arial; font-size: 15px; color: #666;">
<div style="color: #444444; font-weight: normal;">
<div style="text-align: center; font-weight: 600; font-size: 26px; padding: 10px 0; border-bottom: solid 3px #eeeeee;">VENN</div>
<div style="clear: both;"></div>
</div>
<div style="padding: 0 30px 30px 30px; border-bottom: 3px solid #eeeeee;">
<div style="padding: 30px 0; font-size: 24px; text-align: center; line-height: 40px;">Thank you for signing up! Your account is now approved.</div>
<div style="padding: 10px 0 50px 0; text-align: center;"><a style="background: #555555; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 3px; letter-spacing: 0.3px;" href="https://vennclothing.com/login/">Login to our site</a></div>
<div style="padding: 0 0 15px 0;">
<div style="background: #eee; color: #444; padding: 12px 15px; border-radius: 3px; font-weight: bold; font-size: 16px;">Account Information</div>
<div style="padding: 10px 15px 0 15px; color: #333;"><span style="color: #999;">Your account e-mail:</span> <span style="font-weight: bold;">'.$param['email'].'</span></div>
<div style="padding: 10px 15px 0 15px; color: #333;"><span style="color: #999;">Your account username:</span> <span style="font-weight: bold;">'.$param['username'].'</span></div>
</div>
</div>
<div style="color: #999; padding: 20px 30px;">
<div>Thank you!</div>
<div>The <a style="color: #3ba1da; text-decoration: none;" href="https://vennclothing.com/">VENN</a> Team</div>
</div>
</div>';
$headers = array('Content-Type: text/html; charset=UTF-8','From: vennclothing <wordpress@vennclothing.com>');

wp_mail( $to, $subject, $body, $headers );

}

// inactive status email
function wpven_mail_custom_reject($param){
$to = $param['email'];
$subject = 'Your account has been Inactive';
$body = '<div style="max-width: 560px;padding: 20px;background: #ffffff;border-radius: 5px;margin:40px auto;font-family: Open Sans,Helvetica,Arial;font-size: 15px;color: #666;">

    <div style="color: #444444;font-weight: normal;">
        <div style="text-align: center;font-weight:600;font-size:26px;padding: 10px 0;border-bottom: solid 3px #eeeeee;">vennclothing</div>

        <div style="clear:both"></div>
    </div>

    <div style="padding: 0 30px 30px 30px;border-bottom: 3px solid #eeeeee;">

        <div style="padding: 30px 0;font-size: 24px;text-align: center;line-height: 40px;">Your account is now Inactive.</div>

        <div style="padding: 15px;background: #eee;border-radius: 3px;text-align: center;">If you want your account to be Inactive, please <a href="mailto:wordpress@vennclothing.com" style="color: #3ba1da;text-decoration: none">contact us</a>.</div>

    </div>

    <div style="color: #999;padding: 20px 30px">

        <div style="">Thank you!</div>
        <div style="">The <a href="https://vennclothing.com/" style="color: #3ba1da;text-decoration: none;">vennclothing</a> Team</div>

    </div>

</div>';
$headers = array('Content-Type: text/html; charset=UTF-8','From: vennclothing <wordpress@vennclothing.com>');

wp_mail( $to, $subject, $body, $headers );

}




