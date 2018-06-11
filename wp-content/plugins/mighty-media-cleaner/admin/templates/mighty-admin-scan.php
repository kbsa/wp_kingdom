<?php

// Get Scan mode
$mode = (isset($_GET['mode']))?$_GET['mode']:'full';
$mode = ($mode == 'custom')?'custom':'full';

$results = array();
// Get Results
if('full' == $mode){
    $plugin_core = new Mighty_MC_Core();
    $items = $plugin_core->media_from_media_library();
}elseif('custom' == $mode){
    $items = (isset($_GET['items']))?explode(',',$_GET['items']):array();
    $files = array();
    foreach ( $items as $media ) {
        $files[$media] = wp_get_attachment_url( $media );
    }
    $items = $files;
}
wp_enqueue_script( "mighty-media-cleaner-core");
wp_localize_script( "mighty-media-cleaner-core",'mighty_core',  array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'items' => json_encode($items),
    'resultURL' => admin_url( 'admin.php?page=mighty-mc-media-result')
) );


?>
<div class="container mighty-body">
    <div class="row mighty-main-container">


            <div class="scan-page-title"><?php _e('See How Mighty Works','mc-cleaner'); ?></div>

        <div class="clearfix"></div>
        <div class="col-lg-12 tip-container">
            <div class="col-lg-5 tool-tip-text">
                <div class="col-lg-12 tool-tip-icon">
                    <img class="tool-tip-icon1 tool-tip-image" src="<?php echo (MIGHTY_ADMIN_MEDIA); ?>icon1.png">
                    <img class="tool-tip-icon2 tool-tip-image" src="<?php echo (MIGHTY_ADMIN_MEDIA); ?>icon2.png">
                    <img class="tool-tip-icon3 tool-tip-image" src="<?php echo (MIGHTY_ADMIN_MEDIA); ?>icon3.png">
                </div>
                <div class="col-lg-12 tool-tip-description">
                    <img class="tool-tip-description1 tool-tip-image" src="<?php echo (MIGHTY_ADMIN_MEDIA); ?>description1.png">
                    <img class="tool-tip-description2 tool-tip-image" src="<?php echo (MIGHTY_ADMIN_MEDIA); ?>description2.png">
                    <img class="tool-tip-description3 tool-tip-image" src="<?php echo (MIGHTY_ADMIN_MEDIA); ?>description3.png">
                </div>
            </div>
            <div class="col-lg-6 tool-tip-image">
                <img class="image image-1" src="<?php echo (MIGHTY_ADMIN_MEDIA); ?>tooltip1.png">
                <img class="image image-2" src="<?php echo (MIGHTY_ADMIN_MEDIA); ?>tooltip2.png">
                <img class="image image-3" src="<?php echo (MIGHTY_ADMIN_MEDIA); ?>tooltip3.png">
            </div>
        </div>


            <div class="progress-bar-container" data-percentage="20">
                <div class="percentage-container"><span>0%</span></div>
                <div class="bar-container"><div class="back-bor"></div></div>
            </div>




    </div>
</div>
<?php
require_once("mighty-admin-menu.php");

