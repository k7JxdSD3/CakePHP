<?php
class PostsController extends AppController {
	public $helpers = array('Html', 'Form', 'Flash');
	public $components = array('Flash');
	public function index() {
		$this->set('posts', $this->Post->find('all', array(
			'conditions' => array('Post.delete_flg' => 0)
		)));
	}
	public function view($id = null) {
		if (!$id) {
			throw new NotFoundException(__('無効な投稿です'));
		}
		$post = $this->Post->findByIdAndDelete_flg($id, 0);
		if (!$post) {
			throw new NotFoundException(__('無効な投稿です'));
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
			throw new NotFoundException(__('無効な投稿です'));
		}
		$post = $this->Post->findByIdAndDelete_flg($id, 0);
		if (!$post) {
			throw new NotFoundException(__('無効な投稿です'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->Post->id = $id;
			if ($this->Post->save($this->request->data)) {
				$this->Flash->success(__('編集しました'));
				return $this->redirect(array('action' => 'index'));
			}
			$this->Flash->error(__('編集できませんでした'));
		}
		if (!$this->request->data) {
			$this->request->data = $post;
		}
	}
	public function delete($id) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}
		$data = array('id' => $id, 'delete_flg' => 1);
		if ($this->Post->save($data)) {
			$this->Flash->success(
				__('id: %s の投稿を削除しました', h($id))
			);
		} else {
			$this->Flash->error(
				__('id: %s の投稿は削除できませんでした', h($id))
			);
		}
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
			}
		}
		return parent::isAuthorized($user);
	}
}
