<?php
ob_start();
?>
<div class="container mighty-body">

    <div class="mighty-main-container">
        <div class="col-md-12">
            <h1 class="mighty-home-title">Mighty Media Cleaner</h1>
            <p class="mighty-home-description">Mighty Media Cleaner a product powered by WordPress, brings you<br/> amazing features that just haven't been possible before</p>
        </div>
    </div>

    <?php  require_once("mighty-admin-menu.php");  ?>
</div>
<?php
ob_get_flush();
?>
