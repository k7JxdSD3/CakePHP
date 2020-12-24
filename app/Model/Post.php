<?php
class Post extends AppModel {
	public $validate = array(
		'title' => array(
			'rule' => 'notBlank'
		),
		'body' => array(
			'rule' => 'notBlank'
		)
	);
	public function isOwnedBy($post, $user) {
		return $this->field('id', array('id' => $post, 'user_id' => $user)) !== false;
	}
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'order' => array('Post.id' => 'ASC')
		)
	);
}
?>
