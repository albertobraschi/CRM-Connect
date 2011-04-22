<?php
switch ($this->field['type']){
	case 'text':
		?>
		<tr valign="top">
			<th scope="row"><?php echo $this->field['title']; ?></th>
			<td><input type="text" name="<?php echo $this->field['name']; ?>" value="<?php echo ($this->data[$this->field['name']]) ? $this->data[$this->field['name']] : ($_REQUEST[$this->field['name']] ? $_REQUEST[$this->field['name']] : $this->field['default']); ?>" style="width:150px" /></td>
		</tr>
		<?php
		break;
	case 'long-text':
		?>
		<tr valign="top">
			<th scope="row"><?php echo $this->field['title']; ?></th>
			<td><input type="text" name="<?php echo $this->field['name']; ?>" value="<?php echo ($this->data[$this->field['name']]) ? $this->data[$this->field['name']] : ($_REQUEST[$this->field['name']] ? $_REQUEST[$this->field['name']] : $this->field['default']); ?>" style="width:400px" /></td>
		</tr>
		<?php
		break;
	case 'password':
		?>
		<tr valign="top">
			<th scope="row"><?php echo $this->field['title']; ?></th>
			<td><input type="password" name="<?php echo $this->field['name']; ?>" value="<?php echo ($this->data[$this->field['name']]) ? $this->data[$this->field['name']] : ($_REQUEST[$this->field['name']] ? $_REQUEST[$this->field['name']] : $this->field['default']); ?>" style="width:150px" /></td>
		</tr>
		<?php
		break;
	case 'select':
		sort($this->field['options']);
		?>
		<tr valign="top">
			<th scope="row"><?php echo $this->field['title']; ?></th>
			<td>
			    <select name="<?php echo $this->field['name']; ?>" value="<?php echo ($this->data[$this->field['name']]) ? $this->data[$this->field['name']] : $this->field['default']; ?>">
				<option></option>
				<?php foreach($this->field['options'] as $option): ?>
				<option value="<?php echo $option; ?>"<?php echo (($this->data[$this->field['name']])==$option || $_REQUEST[$this->field['name']]==$option) ? ' selected="selected"' : ''; ?>><?php echo $option; ?></option>
				<?php endforeach; ?>
			    </select>
			</td>
		</tr>
		<?php
		break;
}
?>