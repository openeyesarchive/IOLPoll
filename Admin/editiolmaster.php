<?php include_once '../HTML.php';?>
<?php include_once 'header.php';?>

<h1>Edit IOL Master</h1>

<?php
if( isset($_POST['id']) &&  isset($_POST['path']) ){
	HTML::updateIOLMaster($_POST);
}
?>

<?php $iol=HTML::getIOLMaster($_GET["id"])?>

<form method="POST" action="editiolmaster.php">
	ID: <input type="text" name="id" value="<?php echo $iol["id"]?>"><br>
	Path: <input type="text" name="path" value="<?php echo $iol["filepath"]?>"><br>
	Notes: <textarea name="notes"><?php echo $iol["notes"]?></textarea><br>
	<input type="submit" value="Submit">
</form>
<?php include_once 'footer.php';?>
