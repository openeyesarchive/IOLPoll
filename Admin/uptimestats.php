<?php include_once '../HTML.php';?>
<?php include_once 'header.php';?>

    <h1>Uptime Stats <?=($_GET["id"])?></h1>

<?php echo HTML::uptimeStats($_GET["id"])?>
<?php include_once 'footer.php';?>