<h1>ユーザー情報編集</h1>
<p>画像とコメントを編集できます</p>
<?php
echo $this->Form->create('User',array('type' => 'file'));
echo $this->Form->input('image', array(
	'label' => '画像アップロード',
	'accept' => 'image/jpeg, image/png, image/gif',
	'type' => 'file',
	'between' => '2MB以下 jpg, gif, png のいずれかの拡張子でおねがいします'
));
echo $this->Form->input('comment', array('between' => 'commentは255文字以内でお願いします'));
echo $this->Form->end('編集する');
?>
<p>
<?php
echo $this->Html->link('ユーザー情報へ戻る', array('controller' => 'users', 'action' => 'view', $user['User']['id']));
?>
</p>
<p>
<?php
echo $this->Html->link('簡易ブログへ戻る', array('controller' => 'posts', 'action' => 'index'));
?>
</p>
