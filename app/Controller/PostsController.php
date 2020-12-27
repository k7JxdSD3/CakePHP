<?php
class PostsController extends AppController {
	public function index() {
		$this->set('posts', $this->Post->find('all', array(
			'conditions' => array('Post.delete_flg' => 0)
		)));
	}
	public function view($id = null) {
		if (!$id) {
			$this->Flash->error(__('投稿は存在しません'));
			return $this->redirect(array('action' => 'index'));
		}
		$post = $this->Post->findByIdAndDelete_flg($id, 0);
		if (!$post) {
			$this->Flash->error(__('id: %s の投稿は存在しません', h($id)));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('post', $post);
	}
	public function add() {
		if ($this->request->is('post')) {
			$this->request->data['Post']['user_id'] = $this->Auth->user('id');
			$this->Post->create();
			if ($this->Post->save($this->request->data)) {
				$this->Flash->success(__('投稿できました'));
				return $this->redirect(array('action' => 'index'));
			}
			$this->Flash->error(__('投稿できませんでした'));
		}
	}
	public function edit($id = null) {
		if (!$id) {
			$this->Flash->error(__('投稿は存在しません'));
			return $this->redirect(array('action' => 'index'));
		}
		$post = $this->Post->findByIdAndDelete_flg($id, 0);
		if (!$post) {
			$this->Flash->error(__('id: %s の投稿は存在しません', h($id)));
			return $this->redirect(array('action' => 'index'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->Post->id = $id;
			if (!is_null($this->Post->findByIdAndDelete_flg($this->Post->id, 0))) {
				if ($this->Post->save($this->request->data)) {
					$this->Flash->success(__('編集しました'));
					return $this->redirect(array('action' => 'index'));
				}
				$this->Flash->error(__('編集できませんでした'));
			}
		}
		if (!$this->request->data) {
			$this->request->data = $post;
		}
	}
	public function delete($id) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->Post->id = $id;
			$data = array('id' => $this->Post->id, 'delete_flg' => 1);
			if ($this->Post->save($data)) {
				$this->Flash->success(
					__('id: %s の投稿を削除しました', h($id))
				);
			} else {
				$this->Flash->error(
					__('id: %s の投稿は削除できませんでした', h($id))
				);
			}
		}
		return $this->redirect(array('action' => 'index'));
	}
	public function beforeFilter() {
		//AppControllerからの継承
		parent::beforeFilter();
		//ディベロッパーツールによるPOST値改ざん時blackhhole()へ
		$this->Security->blackHoleCallback = 'blackhole';
		//未ログイン時
		$user = $this->Auth->user();
		if (is_null($user)) {
			if ($this->action === 'add') {
				//未ログイン時は投稿拒否
				$this->Flash->error(__('ユーザー登録が必要です'));
				return $this->redirect(array('controller' => 'users', 'action' => 'add'));
			}
			if (in_array($this->action, array('edit', 'delete'))) {
				$this->Flash->error(__('無効な操作です'));
				return $this->redirect(array('action' => 'index'));
			}
		}
	}
	public function blackhole() {
		$this->Flash->error(__('無効な操作です'));
		return $this->redirect(array('action' => 'index'));
	}
	public function isAuthorized($user) {
		if ($this->action === 'add') {
			return true;
		}
		//登録ユーザーは編集、削除をできるようにする
		if (in_array($this->action, array('edit', 'delete'))) {
			$postId = (int) $this->request->params['pass'][0];
			if ($this->Post->isOwnedBy($postId, $user['id'])) {
				return true;
			} else {
				$this->Flash->error(__('無効な操作です'));
				return $this->redirect(array('action' => 'index'));
			}
		}
		return parent::isAuthorized($user);
	}
	//フォーム改ざん対策
	public $components = array('Security');
}
