<script>
    var AJAX_RESPONSE_DELIMITER = '<?php echo $RESPONSE_DELIMITER; ?>';

    var scriptFolderPath = '<?php echo $BACKEND_DOMAIN_BASE; ?>/scripts';
    var jsFolderPath = '<?php echo $BACKEND_DOMAIN_BASE; ?>/js';
    var styleFolderPath = '<?php echo $BACKEND_DOMAIN_BASE; ?>/layout';

    var userID = <?php echo $_SESSION['u_ID']; ?>;
    var FILE_NAME = (window.location.pathname).split('/').pop();
</script>

<script src="<?php echo $BACKEND_DOMAIN_BASE; ?>/js/base.js"></script>