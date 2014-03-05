<?php include_once '../HTML.php';?>
<?php include_once 'header.php';?>
<?php
if( isset($_POST['id']) &&  isset($_POST['path']) ){
	HTML::postNewIOLMaster($_POST);
}
?>
<h1>Add IOL Master</h1>

<form method="POST" action="addiolmaster.php">
	ID: <input type="text" name="id"><br>
	Path: <input type="text" name="path"><br>
	Notes: <textarea name="notes"></textarea><br>
	<input type="submit" value="Submit">
</form>
<?php include_once 'footer.php';?>
