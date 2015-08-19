<?php 

// swgge@wrgwerg.rg
// uHKlIAeI8zKJxvLEFRvL

/**$show = false;
$msgClass = 'info';

if(isset($_GET['action']) && $_GET['action'] == 'logout'){
	$show = true;
	$msg = pll__('Jūs atsijungėte');
	$msgClass = 'success';
}

if($show == true){ ?>
<div class="alert alert-<?php echo $msgClass;?>"><?php echo $msg;?></div>
<?php }*/ ?>

<?php 
$flash = new Messages();
echo $flash->display(); 
?>