<?php
//クラスを初めて使用する際に適切にファイルを見つける
App::uses('Appcontroller', 'Controller');
//メール送信のためのクラス読み込み
App::uses('CakeEmail', 'Network/Email');
class UsersController extends AppController {
	//コントローラーのアクション前に実行
	public function beforeFilter() {
		//AppControllerからの継承
		parent::beforeFilter();
		//ユーザー自身による登録とログアウト、ログインを許可する
		$this->Auth->allow('add', 'logout', 'login', 'password');
		$user = $this->Auth->user();
		//ログイン中add,loginを拒否
		if (!is_null($user)) {
			//ログイン中は登録拒否
			$this->Auth->deny('add', 'login', 'password');
			//直リンクでloginを指定した場合
			if (in_array($this->action, array('add', 'login', 'password'))) {
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
		//URLを押したときの処理
		//if ($this->request->is('get')) {
			if (isset($_GET['key'])) {
				//シークレットキーと時間をもとにデータベースからidを取得
				$user = $this->User->find('first', array(
					'conditions' => array(
						'User.secret_key' => $_GET['key'],
						'User.reset_time >=' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
					),
					'fields' => array('User.id')
				));
				//ViewでUserのidを元に画面を入り変えるために取得
				$this->set('user', $user);
			}
		//}
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
	//パスワードのリセット
	public function password() {
		//URLを押したときの処理
		if ($this->request->is('get')) {
			if (isset($_GET['key'])) {
				//シークレットキーと時間をもとにデータベースからidを取得
				$user = $this->User->find('first', array(
					'conditions' => array(
						'User.secret_key' => $_GET['key'],
						'User.reset_time >=' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
					),
					'fields' => array('User.id')
				));
				////ViewでUserのidを元に画面を入り変えるために取得
				//$this->set('user', $user);
			}
		}
		//ViewでUserのidを元に画面を入り変えるために取得
		if (!empty($user)) {
			$this->set('user', $user);
		}
		//passwordを更新する処理
		if (!empty($this->request->data['User']['password']) && $this->request->is('post')) {
			if (!empty($this->request->data['User']['id'])) {
				//更新するフィールドを指定
				$this->User->id = $this->request->data['User']['id'];
				//password更新とsecret_key,reset_timeの無効か
				$data = array(
					'password' => $this->request->data['User']['password'],
					'reset_time' => null,
					'secret_key' => null
				);
				//データベースへ保存
				if ($this->User->save($data)) {
					$this->Flash->success(__('パスワードの再設定が完了いたしました'));
				}
			} else {
				$this->Flash->error(__('不正なアクセスです。再度お試しください'));
			}
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if (!empty($this->request->data['User']['mail'])) {
				//再発行用URLのメールを送る処理
				$mail = $this->request->data['User']['mail'];
				$this->Flash->success(__('再発行用URLをメールアドレス宛に送信しました'));
				//メールアドレスをもとにデータベースからid,mailを取得
				$user = $this->User->find('first', array(
					'conditions' => array('User.mail' => $mail),
					'fields' => array('User.id', 'User.mail')
				));
				if (!empty($user['User']['id'])) {
					//更新するフィールドを指定
					$this->User->id = $user['User']['id'];
					//シークレットキー生成
					$url = 'https://procir-study.site/Taguchi405/cakephp/users/password?key=';
					$secret_key = md5(uniqid(mt_rand(), true));
					$url .= $secret_key;
					//メールを送信処理
					$message = '下記のURLからパスワードを再設定してください' . "\r\n" . 'URLの有効期限は３０分です' . "\r\n" . $url;
					$email = new CakeEmail();
					$email->from(array('hxh.feitan@gmail.com' => '簡易ブログ'));
					$email->to($mail);
					$email->subject('簡易ブログパスワード再設定案内');
					if ($email->send($message)) {
						$reset_time = date('Y-m-d H:i:s');
						//配列に格納
						$data = array(
							'reset_time' => $reset_time,
							'secret_key' => $secret_key
						);
						//データベースへ保存
						$this->User->save($data);
					}
				}
				//return $this->redirect(array('controller' => 'users', 'action' => 'login'));
			}
		}
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
