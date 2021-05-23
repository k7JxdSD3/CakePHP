<div class="users form">
<?php echo $this->Flash->render('auth'); ?>
<?php echo $this->Form->create('User'); ?>
<fieldset>
<legend>
<?php echo __('メール、パスワードを入力してください'); ?>
</legend>
<?php
echo $this->Form->input('mail');
echo $this->Form->input('password');
?>
</fieldset>
<?php echo $this->Form->end(__('Login')); ?>
<?php
echo $this->Html->link(
	'パスワードを忘れた方はこちら',
	array('controller' => 'users', 'action' => 'password')
);
?>
</div>
<?php
echo $this->Html->link(
	'新規会員登録',
	array('controller' => 'users', 'action' => 'add')
);
?>
<br>
<?php
echo $this->Html->link(
	'簡易ブログに戻る',
	array('controller' => 'posts', 'action' => 'index')
);
?>
