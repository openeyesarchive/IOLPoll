<?php include_once '../HTML.php';?>
<?php include_once 'header.php';?>

<h1>Edit IOL Master</h1>

<?php
if( isset($_POST['id']) &&  isset($_POST['path']) ){
	HTML::UpdateIOLMaster($_POST);
}
?>

<?php $iol=HTML::GetIOLMaster($_GET["id"])?>

<form method="POST" action="editiolmaster.php">
	ID: <input type="text" name="id" value="<?=$iol["id"]?>"><br>
	Path: <input type="text" name="path" value="<?=$iol["filepath"]?>"><br>
	Notes: <textarea name="notes"><?=$iol["notes"]?></textarea><br>
	<input type="submit" value="Submit">
</form>
<?php include_once 'footer.php';?>
