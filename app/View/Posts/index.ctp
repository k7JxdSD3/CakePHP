<h1>簡易ブログ</h1>
<p><?php
echo $this->Html->link(
	'投稿はこちら',
	array('controller' => 'posts', 'action' => 'add')
);
?></p>
<br>
<?php if ($auth->loggedIn()) : ?>
<?php echo h($auth->user('username')); ?> さん、こんにちは！
<?php
	echo $this->Html->link(
		'Logoutはこちら',
		array('controller' => 'users', 'action' => 'logout')
	);
?>
<?php else : ?>
<?php
	echo $this->Html->link(
		'新規会員登録',
		array('controller' => 'users', 'action' => 'add')
	);
?>
<br>
<?php
echo $this->Html->link(
	'Loginはこちら',
	array('controller' => 'users', 'action' => 'login')
);
?>
<?php endif; ?>
<table>
<tr>
<th>Id</th>
<th>name</th>
<th>Title</th>
<th>body</th>
<th>Action</th>
<th>Created</th>
</tr>
<!-- ここから、$posts配列をループして、投稿記事の情報を表示 -->
<?php foreach ($posts as $post): ?>
<tr>
<td><?php echo $post['Post']['id']; ?></td>
<td><?php echo $post['User']['username']; ?></td>
<td>
<?php
echo $this->Html->link(
	mb_substr($post['Post']['title'], 0, 10),
	array('controller' => 'posts', 'action' => 'view', $post['Post']['id'])
);
?>
</td>
<td><?php echo mb_substr($post['Post']['body'], 0, 15); ?></td>
<td>
<?php if ($post['Post']['user_id'] === $auth->user('id')) : ?>
<?php
echo $this->Form->postLink(
	'削除',
	array('controller' => 'posts', 'action' => 'delete', $post['Post']['id']),
	array('confirm' => '削除してよろしいですか?')
);
?>
|
<?php
echo $this->Html->link(
	'編集',
	array('controller' => 'posts', 'action' => 'edit', $post['Post']['id'])
);
?>
<?php endif; ?>
</td>
<td><?php echo $post['Post']['created']; ?></td>
</tr>
<?php endforeach; ?>
<?php unset($post); ?>
</table>
