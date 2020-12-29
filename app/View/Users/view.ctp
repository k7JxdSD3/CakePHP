<h1>ユーザー情報</h1>
<p>Name:<?php echo h($user['User']['username']); ?></p>
<p>Image:
<?php if ($user['User']['image'] === null) : ?>
未登録
<?php else : ?>
<br>
<?php echo $this->Html->image($user['User']['image'], array('alt' => 'image')); ?>
<?php endif; ?>
</p>
<p>Mail:<?php echo h($user['User']['mail']); ?></p>
<p>A comment:
<?php if ($user['User']['comment'] === null) : ?>
未登録
<?php else : ?>
<?php echo h($user['User']['comment']); ?>
<?php endif; ?>
</p>
<!--自身でなければ非表示-->
<p>
<?php if ($user['User']['id'] === $auth->user('id')) : ?>
<?php
echo $this->Html->link(
	'ユーザー情報編集',
	array('controller' => 'users', 'action' => 'edit', $user['User']['id'])
);
?>
</p>
<?php endif; ?>
<p>
<?php
echo $this->Html->link(
	'簡易ブログへ戻る',
	array('controller' => 'posts', 'action' => 'index')
);
?>
</p>
