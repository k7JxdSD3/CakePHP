<div class="users form">
<?php echo $this->Form->create('User'); ?>
<fieldset>
<legend><?php echo __('ユーザーの追加'); ?></legend>
<?php
echo $this->Form->input('username', array(
'between' => 'usernameは50文字以内で入力してください'
));
echo $this->Form->input('mail');
echo $this->Form->input('password', array(
'between' => 'Passwordは5~15文字の半角英数で入力してください'
));
?>
</fieldset>
<?php echo $this->Form->end(__('登録')); ?>
</div>
<?php
	echo $this->Html->link(
		'Loginはこちら',
		array('controller' => 'users', 'action' => 'login')
	);
?>
<br>
<?php
echo $this->Html->link(
	'簡易ブログに戻る',
	array('controller' => 'posts', 'action' => 'index')
);
?>
