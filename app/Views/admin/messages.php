<?php  
  $session = \Config\Services::session();
?>

<div class="col-xs-10 col-xs-offset-1 ">

<?php if(Null !== session()->get("errors")) : ?>

	<div class="alert alert-danger alert-dismissible" role="alert"  style="padding: 8px 35px 8px 14px;">
		<button type="button" class="close" data-dismiss="alert"
			aria-label="Close">&times;</button>
	     <?php echo session("errors"); ?>
    </div>
	
<?php endif; ?>

<?php if(Null !== session()->get("messages")): ?>
   <div class="alert alert-success alert-dismissible" role="alert" style="padding: 8px 35px 8px 14px;">
	<button type="button" class="close" data-dismiss="alert"
		aria-label="Close">&times;</button>
	<?php echo session("messages"); ?>
   </div>
<?php endif; ?>

<?php if(Null !== session()->get("success")): ?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<?php echo session("success"); ?>
	</div>
<?php endif; ?>

</div>
