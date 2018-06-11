<?php
$result=(isset($_SESSION['mc-scan']))?$_SESSION['mc-scan']:array();
if(isset($_SESSION['mc-scan'])){
    unset($_SESSION['mc-scan']);
}else{
    $url = admin_url().'admin.php?page=mighty-mc-dashboard';
    echo '<script type="text/javascript">
            window.location.href = "'.$url.'";
            </script>';
}

ob_start();


?>
<div class="container mighty-body">
    <div class="row mighty-main-container">
        <div class="row mighty-result-header">
            <div class="col-lg-4 file-counter"><?php echo(count($result)); ?> <?php _e('Files Available','mc-cleaner'); ?></div>
            <div class="col-lg-1 buttons"><div class="mighty-button transparent result-select-all"><?php _e('Select All','mc-cleaner'); ?></div></div>
            <div class="col-lg-1 buttons"><div class="mighty-button transparent result-delete-media"><?php _e('Remove','mc-cleaner'); ?></div></div>

            <div class="col-lg-3 additional"><?php _e('Found in Just','mc-cleaner'); ?> <?php echo round($_GET['time']/1000); ?> <?php _e('Seconds','mc-cleaner'); ?></div>
        </div>
        <div class="result-container row">
            <?php
            $counter=0;
            $uploadeddirectory="false";
            $imageid="000";
            $uploaded_file=get_option("mighty-uploaded-file");

            foreach($result as $id => $url)
            {
                $upload_dir = wp_upload_dir();
                $uppload_url=$upload_dir['baseurl'];
                $url = $uppload_url.'/'.$url;
                $result[$id] = $url;


                if(strpos($id,"ir-")<0 || strpos($id,"ir-")==false ){

                    //Get Meta Data of each image (small thumbnail url , default thumbnail url , file address , file size and file name,file ext)
                    $smallurl=wp_get_attachment_image_src ($id,'medium')[0];
                    $thumbnailurl=wp_get_attachment_image_src ($id,'large')[0];
                    $fileaddress=get_attached_file( $id );
                    if(!file_exists($fileaddress)){
                        continue;
                    }
                    $filesize=filesize($fileaddress);
                    $filename=$fullname=basename($fileaddress);

                    //Filter file name if its too long
                    if(strlen($filename)>20){
                        $filename=substr($filename,0,15)."...";
                    }
                    //Filter file name for popup image if its too long
                    if(strlen($fullname)>40){
                        $fullname=substr($fullname,0,37)."...";
                    }
                    $filetype=wp_check_filetype( $fileaddress) ;
                    $filesize=round( (($filesize/1024)/1024),2);
                    $counter=($counter % 7);
                    $imageid=$id;
                    $counter++;
                }else{
                    $pos=strpos($uploaded_file,$url);
                    if($pos===0 || $pos!=false){
                        continue;
                    }
                    $uploadeddirectory="true";
                    $url = str_replace(" ", "%20", $url);
                    $smallurl=$thumbnailurl=$url;
                    $fileaddress=$url;
                    $filetype=wp_check_filetype( $fileaddress) ;
                    @$fileContent = file_get_contents($fileaddress);
                    if($fileContent === false){
                        $fileContent = '';
                    }
                    $filesize=strlen($fileContent);
                    $filesize=round( (($filesize/1024)/1024),2);
                    $filename=$fullname=basename($fileaddress);

                    if(strlen($filename)>20){
                        $filename=substr($filename,0,15)."...";
                    }
                    //Filter file name for popup image if its too long
                    if(strlen($fullname)>40){
                        $fullname=substr($fullname,0,37)."...";
                    }
                    $counter++;
                }



                if($counter>0 && $counter!=6 && $counter!=7){
                    $class="not-edge";
                }
                else{
                    $class="edge";
                }


            ?>

                <div data-counter="<?php echo $counter; ?>" class="single-result <?php echo $class; ?>" data-id="<?php echo(esc_attr($imageid )); ?>" data-url="<?php echo(esc_attr($url )); ?>" data-physical-path="<?php echo(esc_attr($fileaddress)); ?>"  data-is-uploaded-directory="<?php echo($uploadeddirectory); ?>">
                    <a class="image-container" id="popup-<?php echo $counter; ?>">
                        <div class="lazy-load result-thumbnail" data-original="<?php echo($smallurl); ?>"  style="background-image:url(<?php echo MIGHTY_MEDIA_IMG.'img-place-holder.png' ?>);"></div>
                        <div class="overlay"></div>
                        <div class="check-mark"></div>
                    </a>
                    <div class="file-name"><a class="open-popup " href="#popupme--<?php echo(esc_attr($counter . "-" . $imageid )); ?>"><?php echo(esc_attr($filename)); ?></a></div>
                    <div id="popupme--<?php echo(esc_attr($counter . "-" . $imageid )); ?>" class="mfp-hide mighty-popup">
                        <div class="full-image-container" style="background-image:url(<?php echo(esc_attr($thumbnailurl)); ?>);"></div>
                        <div class="full-file-url">URL   <input type="text" disabled value="<?php echo(esc_attr($url)); ?>"></div>
                        <div class="full-file-name"><?php _e('File Name','mc-cleaner'); ?> : <?php echo($fullname); ?></div>
                        <div class="full-file-size"><?php _e('File Type','mc-cleaner'); ?> : <?php echo(strtoupper(esc_attr($filetype['ext']))); ?></div>
                        <div class="full-file-type"><?php _e('File Size','mc-cleaner'); ?> : <?php echo(esc_attr($filesize)." MB"); ?></div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <?php
        require_once("mighty-admin-menu.php");
        ?>
    </div>
</div>
<?php
ob_get_flush();
?>