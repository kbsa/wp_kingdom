<?php
$upload_dir = wp_upload_dir();
$uppload_url=$upload_dir['baseurl'];
$upload_dir = $upload_dir['basedir'];
$backup = $files = array();
$files = array();
if(is_dir($upload_dir."/mighty-mc/backups")){
    $files=scandir($upload_dir."/mighty-mc/backups");
}
$rootaddress=$uppload_url."/mighty-mc/backups";
foreach($files as $file){
    if(strpos($file,".zip"))
    {
        $backup[$file]=date(" | m/d/Y | H:i:s.",filectime($upload_dir."/mighty-mc/backups/".$file));
    }
}




ob_start();
?>

<div class="container mighty-body">
    <div class="row mighty-main-container">

        <div class="row backup-firstrow">
            <div class="mighty-backup-header col-lg-9"><?php _e('Click to Create a New Back Up File','mc-cleaner'); ?></div>

            <div class="mighty-backup-header-button col-lg-1">
                <div class="mighty-button transparent">
                    <span class="progress"></span>
                    <?php _e('Backup','mc-cleaner'); ?>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="mighty-backup-subheader col-lg-8"><?php _e('Backup Files','mc-cleaner'); ?></div>
        </div>


        <div class="row backup-list">
            <?php
            if(!count($backup)){ ?>
            <p class="no-file-found"><?php _e('NO BACKUP FILE FOUND !','mc-cleaner'); ?></p>
                <?php
            }else{
            $counter=0;
            foreach($backup as $key => $value){
                $counter++;
                $filename=strtoupper(substr($key,0,strpos($key,".zip")));
                ?>
                <div class="col-lg-12 file-container">
                    <div class="col-lg-7 backup-file-name">
                        <span class="file-number"><?php echo(esc_attr($counter)); ?></span>
                        <?php echo("MC-Backup ".esc_attr($value)); ?>
                    </div>
                    <div class="col-lg-1">
                        <div class="mighty-button transparent">
                            <a class="mighty-download-zip" href="<?php echo(esc_attr($rootaddress)."/". esc_attr($key)); ?>" target="_blank"><?php _e('Download','mc-cleaner'); ?></a>
                        </div>
                    </div>
                    <div class="col-lg-1 ">
                        <div class="mighty-restore-backup mighty-button transparent" data-file-name="<?php echo(esc_attr($key)); ?>">
                            <?php _e('Restore','mc-cleaner'); ?>
                        </div>
                    </div>
                    <div class="col-lg-1 ">
                        <div class="mighty-remove-backup mighty-button transparent" data-file-name="<?php echo(esc_attr($key)); ?>">
                            <?php _e('Remove','mc-cleaner'); ?>
                        </div>
                    </div>
                </div>

                <?php
            }}
            ?>
        </div>



    </div>



</div>


<?php
require_once("mighty-admin-menu.php");
?>

<?php
ob_get_flush();
?>