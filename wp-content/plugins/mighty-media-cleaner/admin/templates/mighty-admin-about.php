<?php
ob_start();
?>
<div class="container mighty-body">

    <div class="mighty-main-container">
        <div class="col-md-12">
            <h1 class="mighty-about-title">A Professional Team Product</h1>
            <div class="team-members clearfix">
                <span class="team-member">
                    <img src="<?php echo esc_url(MIGHTY_ADMIN_MEDIA.'dev.png') ?>">
                    <p>Crazy<br/>Developer</p>
                </span>
                <span class="team-member">
                    <img src="<?php echo esc_url(MIGHTY_ADMIN_MEDIA.'pro.png') ?>">
                    <p>Freak<br/>Programmer</p>
                </span>
                <span class="team-member">
                    <img src="<?php echo esc_url(MIGHTY_ADMIN_MEDIA.'des.png') ?>">
                    <p>Insane<br/>Designer</p>
                </span>
            </div>
            <p class="mighty-about-description">Sea ad vidit blandit volutpat. Ut vis illum homero maiorum, cu ius saepe propriae officiis, mea ne magna vivendum qualisque. Feugiat percipit ad his, nam ea noster prompta elaboraret, eu salutatus accommodare pri. Ius ad enim deserunt ullamcorper, eos veri etiam instructior at. Dolor hendrerit vim at, error ridens delenit cum te. Appareat sadipscing at sed.Feugiat percipit ad his, nam ea noster prompta elaboraret, </p>
        </div>
    </div>

    <?php  require_once("mighty-admin-menu.php");  ?>
</div>
<?php
ob_get_flush();
?>
