<?php $this->display("admin_header.tpl.php"); ?>
<tr>
	<td class="crmconnect_field_block">
		<h3>Form</h3>
		<ul class="crmconnect_form_fields connectedSortable">
			<li id="username" class="required">Username <?php echo $this->admin->username_select; ?></li>
			<li id="email" class="required">Email <?php echo $this->admin->email_select; ?></li>
			<?php echo $this->admin->selected_fields; ?>
		</ul>
	</td>
	<td class="crmconnect_field_block">
		<h3>Available Fields</h3>
		<ul class="crmconnect_module_fields connectedSortable">
			<?php echo $this->admin->avail_fields; ?>
		</ul>
	</td>
</tr>
<tr>
	<td colspan="2"><input type="hidden" id="crmconnect_fields" name="crmconnect_fields" value='' /></td>
</tr>
<?php $this->display("admin_footer.tpl.php"); ?>