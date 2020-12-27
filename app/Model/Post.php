<?php
class Post extends AppModel {
	public $validate = array(
		'title' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'titleは必須です'
			),
			'between' => array(
				'rule' =>array('lengthBetween', 1, 50),
				'message' => '50文字以内でお願いします'
			)
		),
		'body' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'titleは必須です'
			),
			'between' => array(
				'rule' =>array('lengthBetween', 1, 255),
				'message' => '255文字以内でお願いします'
			)
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
