<?php include_once '../HTML.php';?>

<h1>Delete IOL Master</h1>

<?php
if( isset($_POST['id']) ){
	HTML::DeleteIOLMaster($_POST['id']);
}
?>
<?php $iol=HTML::GetIOLMaster($_GET["id"])?>

<form method="POST" action="deleteiolmaster.php">
	<input type="checkbox" name="id" value="<?=$iol['id']?>">Confirm delete IOL Master (<?=$iol['id']?>)<br>
	<input type="submit" value="Submit">
</form>
