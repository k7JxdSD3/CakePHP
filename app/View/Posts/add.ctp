<h1>投稿画面</h1>
<?php
echo $this->Form->create('Post');
echo $this->Form->input('title');
echo $this->Form->input('body', array('rows' => '3'));
echo $this->Form->end('投稿する');
?>
<?php
echo $this->Html->link(
	'簡易ブログに戻る',
	array('controller' => 'posts', 'action' => 'index')
);
?>
