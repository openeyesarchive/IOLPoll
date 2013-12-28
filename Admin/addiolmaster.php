<?php include_once '../HTML.php';?>
<?php
if( isset($_POST['id']) &&  isset($_POST['path']) ){
	HTML::PostNewIOLMaster($_POST);
}
?>
<h1>Add IOL Master</h1>

<form method="POST" action="addiolmaster.php">
	ID: <input type="text" name="id"><br>
	Path: <input type="text" name="path"><br>
	Notes: <input type="text" name="path"><br>
	<input type="submit" value="Submit">
</form>
