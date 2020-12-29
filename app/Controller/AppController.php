<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	//ヘルパーの読み込み
	public $helpers = array('Html', 'Form', 'Flash');
	public $components = array(
		'DebugKit.Toolbar' => array('panels' => array('history' => false)),
		'Security',
		'Flash',
		'Auth' => array(
			//ログイン後のページ指定
			'loginRedirect' => array(
				'controller' => 'posts',
				'action' => 'index'
			),
			//ログイン後のページ指定
			'logoutRedirect' => array(
				'controller' => 'posts',
				'action' => 'index'
			),
			//POSTされたデータの認証
			'authenticate' => array(
				'Form' => array(
					'passwordHasher' => 'Blowfish',
					'fields' => array(
						//認証をmailに変更
						'username' => 'mail',
						'password' => 'password'
					)
				)
			),
			'authorize' => array('Controller'),
			//認証されていない時のリダイレクト先
			'loginAction' => array(
				'controller' => 'users',
				'action' => 'add'
			),
		)
	);
	//ログインしていなくてもindex,viewを見れるようにする
	public function beforeFilter() {
		$this->Auth->allow('index', 'view');
		$this->set('auth', $this->Auth);
		//ディベロッパーツールによるPOST値改ざん時blackhhole()へ
		$this->Security->blackHoleCallback = 'blackhole';
	}
	public function blackhole() {
		$this->Flash->error(__('無効な操作です'));
		return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
	}
	public function isAuthorized($user) {
		return false;
	}
}
