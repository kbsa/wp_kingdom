<?php
ob_start();
?>
<div class="container mighty-home-icon-container">
    <div class="menu-table">
        <ul>
            <li class="mighty-home-icons"><a class="mighty-home-icon-link" href="<?php echo esc_url(admin_url( 'admin.php?page=mighty-mc-media-scan')); ?>"><div class="mighty-home-icon auto-scan"></div><p class="mighty-home-icon-label"><?php _e('Auto Scan','mc-cleaner'); ?></p></a></li>
            <li class="mighty-home-icons"><a class="mighty-home-icon-link" href="<?php echo esc_url(admin_url( 'upload.php')); ?>"><div class="mighty-home-icon manual-scan"></div><p class="mighty-home-icon-label"><?php _e('Manual Scan','mc-cleaner'); ?></p></a></li>
            <li class="mighty-home-icons"><a class="mighty-home-icon-link" href="<?php echo esc_url(admin_url( 'admin.php?page=mighty-mc-media-backup')); ?>"><div class="mighty-home-icon backup"></div><p class="mighty-home-icon-label"><?php _e('Backup','mc-cleaner'); ?></p></a></li>
            <li class="mighty-home-icons"><a class="mighty-home-icon-link" href="<?php echo esc_url(admin_url( 'admin.php?page=mighty-mc-media-about')); ?>"><div class="mighty-home-icon about"></div><p class="mighty-home-icon-label"><?php _e('About','mc-cleaner'); ?></p></a></li>
        </ul>
    </div>
</div>
<?php
ob_get_flush();
?>



