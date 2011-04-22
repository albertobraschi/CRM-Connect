<div id="message" class="<?php echo $this->admin->postMessage['class']; ?> fade">
	<p><strong>
		<?php _e($this->admin->postMessage['message']); ?>
	</strong></p>
</div>
<div class="wrap">
	<h2><?php echo $this->title; ?></h2>
	<form method="post" id="crmconnect_<?php echo $this->action; ?>">
		<table class="form-table">
			<tbody>