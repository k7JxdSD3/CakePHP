<h1>編集画面</h1>
<?php
echo $this->Form->create('Post');
echo $this->Form->input('title', array(
'between' => 'Titleは50文字以内で入力してください'
));
echo $this->Form->input('body', array(
'rows' => '3',
'between' => 'Bodyは255文字以内で入力してください'
));
echo $this->Form->input('id', array('type' => 'hidden'));
echo $this->Form->end('編集する');
?>
<?php
echo $this->Html->link(
	'簡易ブログに戻る',
	array('controller' => 'posts', 'action' => 'index')
);
?>
