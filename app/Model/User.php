<?php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
class User extends AppModel {
	public $validate = array(
		'username' => array(
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'usernameは必須です'
			),
			'between' => array(
				'rule' =>array('lengthBetween', 1, 50),
				'message' => '50文字以内でお願いします'
			)
		),
		'mail' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'メールアドレスを入力してください'
			),
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'mailは必須です'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => '無効なメールアドレスです'
			)
		),
		'password' => array (
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'passwordは必須です'
			),
			'between' => array(
				'rule' =>array('lengthBetween', 5, 15),
				'message' => '5～15文字でお願いします'
			)
		),
		'image' => array(
			'extension' => array(
				//拡張子の指定
				'rule' => array('extension', array('jpeg', 'jpg', 'gif', 'png')),
				'message' => 'jpg, gif, png のいずれかの拡張子にしてください',
				//空の許可
				'allowEmpty' => true
			),
			'size' => array(
				'rule' => array('fileSize', '<=', '2MB'),
				'message' => '画像のサイズは2MB以下にしてください'
			)
		),
		'comment' => array(
			'maxlength' => array(
				'rule' =>array('maxLength', 255),
				'message' => '255 文字以内でお願いします',
				'allowEmpty' => true
			)
		)
	);
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$passwordHasher = new BlowfishPasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash(
				$this->data[$this->alias]['password']
			);
		}
		return true;
	}
}
