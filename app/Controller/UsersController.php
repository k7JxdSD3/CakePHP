<?php
//クラスを初めて使用する際に適切にファイルを見つける
App::uses('Appcontroller', 'Controller');
class UsersController extends AppController {
	//コントローラーのアクション前に実行
	public function beforeFilter() {
		//AppControllerからの継承
		parent::beforeFilter();
		//ユーザー自身による登録とログアウト、ログインを許可する
		$this->Auth->allow('add', 'logout', 'login');
		$user = $this->Auth->user();
		//ログイン中add,loginを拒否
		if (!is_null($user)) {
			//ログイン中は登録拒否
			$this->Auth->deny('add', 'login');
			//直リンクでloginを指定した場合
			if (in_array($this->action, array('add', 'login'))) {
				//loginActionの指定になっているのでリダイレクトで回避
				$this->Flash->error(__('無効な操作です'));
				return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
			}
		} else {
			if ($this->action === 'logout') {
			$this->Flash->error(__('loginしていません'));
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
			}
		}
	}
	//ユーザー登録
	public function add() {
		//登録ボタンが押されたら
		if ($this->request->is('post')) {
			//モデル状態の初期化
			$this->User->create();
			//usersテーブルにデータを保存出来たら
			if ($this->User->save($this->request->data)) {
				//__call()は$_SESSIONにメッセージを追加する
				$this->Flash->success(__('ユーザー情報を保存しました'));
				return $this->redirect(array('controller' => 'users', 'action' => 'login'));
			}
			$this->Flash->error(
				__('保存できませんでした、もう一度お試しください')
			);
		}
	}
	//ログイン処理
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->Flash->success(__('Loginしました'));
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Flash->error(__('ログインできませんでした、もう一度お願いします'));
			}
		}
	}
	public function logout() {
		if ($this->Auth->logout()) {
			$this->Flash->success(__('Logoutしました'));
			return $this->redirect($this->Auth->logout());
		}
		$this->Flash->error(__('Logoutできませんでした'));
		return $this->redirect(array('action' => 'index'));
	}
	public function isAuthorized($user) {
		return parent::isAuthorized($user);
	}
}
?>
