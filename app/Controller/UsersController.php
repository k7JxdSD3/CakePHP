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
	//ユーザー情報画面
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Flash->error(__('ユーザーが存在しません'));
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
		}
		$this->set('user', $this->User->findById($id));
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
	//ユーザー情報編集
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			$this->Flash->error(__('ユーザーが存在しません'));
			return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			//画像ファイル処理
			$tmp_name = $this->request->data['User']['image']['tmp_name'];
			$image_name = $this->request->data['User']['image']['name'];
			$image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
			//前の画像をデータベースを更新する前に取得
			$previous_image = $this->User->field('image');
			if ($image_name) {
				//画像バリデーション
				if (!in_array($this->request->data['User']['image']['type'],
					array('image/jpeg', 'image/gif', 'image/jpg', 'image/png')
				)) {
					$this->Flash->error(__('ファイル形式が不正です'));
					return $this->redirect(array('action' => 'edit', $this->User->id));
				}
				switch($this->request->data['User']['image']['error']) {
					case 0:
						break;
					case 1:
					case 2:
						$this->Flash->error(__('画像のサイズは2M以下でお願いします'));
						return $this->redirect(array('action' => 'edit', $this->User->id));
				}
				if ($this->request->data['User']['image']['size'] >= 2097153) {
					$this->Flash->error(__('画像のサイズは2M以下でお願いします'));
					return $this->redirect(array('action' => 'edit', $this->User->id));
				}
				$unique_name = uniqid(mt_rand(), true);
				$unique_file = sprintf('%s.%s', $unique_name, $image_extension);
				$this->request->data['User']['image'] = $image_name;
				$path = '../webroot/img/';
				if (!getimagesize($tmp_name)) {
					$this->Flash->error(__('無効なファイルです'));
					return $this->redirect(array('action' => 'edit', $this->User->id));
				}
				//ディレクトリに保存
				move_uploaded_file($tmp_name, $path . $unique_file);
				$this->request->data['User']['image'] = $unique_file;
			} else {
				//ファイルが選択されていない場合
				$this->request->data['User']['image'] = $previous_image;
			}
			//コメント未登録時
			if ($this->request->data['User']['comment'] == '') {
				$this->request->data['User']['comment'] = null;
			}
			//画像をデータベースへ保存
			if ($this->User->save($this->request->data)) {
				//元の画像削除
				if (isset($path, $previous_image) && file_exists($path . $previous_image)) {
					unlink($path . $previous_image);
				}
				$this->Flash->success(__('ユーザー情報を編集しました'));
				return $this->redirect(array('action' => 'view', $this->User->id));
			}
			$this->Flash->error(__('ユーザー情報を編集できませんでした'));
		} else {
			$this->request->data = $this->User->findById($id);
			unset($this->request->data['User']['password']);
		}
		$this->set('user', $this->User->findById($id));
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
		return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
	}
	public function isAuthorized($user) {
		if ($this->action === 'add') {
			return true;
		}
		//登録ユーザーは編集、削除をできるようにする
		if ($this->action === 'edit') {
			$user_id = (int) $this->request->params['pass'][0];
			if ($user_id == $this->Auth->user('id')) {
				return true;
			} else {
				$this->Flash->error(__('無効な操作です'));
				return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
			}
		}
		return parent::isAuthorized($user);
	}
}
?>
