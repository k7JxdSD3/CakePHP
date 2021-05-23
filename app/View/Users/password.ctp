<div class="users form">
<?php echo $this->Form->create('User'); ?>
<fieldset>
<legend><?php echo __('パスワードの再設定'); ?></legend>
<?php if (!isset($_GET['key'])) : ?>
<?php
echo $this->Form->input('mail', array(
	'between' => '登録されているメールアドレスを入力してください'
));
?>
</fieldset>
<?php echo $this->Form->end(__('送信')); ?>
<?php else : ?>
<?php
echo $this->Form->input('password', array(
	'between' => 'passwordは5～15文字でお願いします'
));
if (!empty($user['User']['id'])) {
	echo $this->Form->hidden('id', array('value' => $user['User']['id']));
}
?>
<?php echo $this->Form->end('再設定する'); ?>
<?php endif; ?>
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
