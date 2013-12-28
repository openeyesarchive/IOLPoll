<?php include_once '../HTML.php';?>

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
	Notes: <input type="text" name="path" value="<?=$iol["notes"]?>"><br>
	<input type="submit" value="Submit">
</form>
