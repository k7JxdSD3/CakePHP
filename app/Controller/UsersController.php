<?php
App::uses('Appcontroller', 'Controller');
class UsersController extends AppController {
	//コントローラーのアクション前に実行
	public function beforeFilter() {
		parent::beforeFilter();
		//ユーザー自身による登録とログアウトを許可する
		$this->Auth->allow('add', 'logout');
		$user = $this->Auth->user();
		//ログイン中add,loginを拒否
		if (!is_null($user)) {
			//ログイン中は登録拒否
			$this->Auth->deny('add');
			//loginActionの指定になっているのでリダイレクトで回避
			if ($this->action === 'login') {
				return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
			}
		}
	}
	//データベースから情報を取得？set()する必要があるのか？
	public function index() {
		//find()を使用した場合デフォルトでjoinが行われる
		//$recursive = -1でテーブル単体を検索できる、０はデフォルト
		$this->User->recursive = 0;
	}
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('ユーザーが存在しません'));
		}
		//idが'$id'番目のデータを取して$userに格納
		$this->set('user', $this->User->findById($id));
	}
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('ユーザー情報を保存しました'));
				return $this->redirect(array('controller' => 'users', 'action' => 'login'));
			}
			$this->Flash->error(
				__('保存できませんでした、もう一度お試しください')
			);
		}
	}
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('無効なユーザーです'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Flash->success(__('ユーザー情報を編集しました'));
				return $this->redirect(array('action' => 'index'));
			}
			$this->Flash->error(
				__('編集できませんでした、もう一度お試しください')
			);
		} else {
			$this->request->data = $this->User->findById($id);
			unset($this->request->data['User']['password']);
		}
	}
	public function delete($id = null) {
		$this->request->allowMethod('post');
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('無効なユーザーです'));
		}
		if ($this->User->delete()) {
			$this->Flash->success(__('ユーザー情報を削除しました'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->Flash->error(__('ユーザー情報を削除できませんでした'));
		return $this->redirect(array('action' => 'index'));
	}
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
