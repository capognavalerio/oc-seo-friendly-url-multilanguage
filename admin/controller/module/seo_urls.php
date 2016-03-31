<?php

class ControllerModuleSeoUrls extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/seo_urls');

		$this->load->model('localisation/language');
		$this->document->setTitle($this->language->get('heading_title'));

		$language_info = array(
		'heading_title', 'button_save',	'button_cancel', 'button_delete', 'tab_general', 'tab_info', 'entry_status', 'entry_url', 'entry_route', 'text_edit', 'text_enabled', 'text_disabled','button_add','entry_examples','entry_examples_title'
		);

		foreach ($language_info as $language) {
			$data[$language] = $this->language->get($language);
		}

		$data['token'] = $this->session->data['token'];
		$data['languages'] = $this->model_localisation_language->getLanguages();

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

 		if (isset($this->error['folder'])) {
			$data['error_folder'] = $this->error['folder'];
		} else {
			$data['error_folder'] = '';
		}

		$data['breadcrumbs'] = array();

 		$data['breadcrumbs'][] = array(
     		'text'      => $this->language->get('text_home'),
     		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
    		'separator' => false
 		);

 		$data['breadcrumbs'][] = array(
     		'text'      => $this->language->get('text_module'),
     		'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
    		'separator' => ' :: '
 		);

 		$data['breadcrumbs'][] = array(
     		'text'      => $this->language->get('heading_title'),
     		'href'      => $this->url->link('module/seo_urls', 'token=' . $this->session->data['token'], 'SSL'),
    		'separator' => ' :: '
 		);

		$data['action'] = $this->url->link('module/seo_urls', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$seo_urls_db = $this->db->query("SELECT * FROM ". DB_PREFIX ."url_alias WHERE query LIKE 'route=%'");

		$data['seo_urls'] = array();

		foreach ($seo_urls_db->rows as $seo_url) {
			$url_route = explode('route=', $seo_url['query']);

			if (isset($url_route[1])) {
				$url_route = $url_route[1];
			} else {
				$url_route = $url_route[0];
			}

			$data['seo_urls'][$seo_url['query']]['keywords'][] = array(
				'keyword' => $seo_url['keyword'],
				'language' => $this->model_localisation_language->getLanguage($seo_url['language_id'])
			);

			$url_delete = '&query=' . $url_route;
			$url_delete = $this->url->link('module/seo_urls/delete', 'token=' . $this->session->data['token'] . $url_delete, 'SSL');

			$data['seo_urls'][$seo_url['query']]['delete'] = $url_delete;
			$data['seo_urls'][$seo_url['query']]['route'] = $url_route;
		}

		// RENDER
		$data['edit_add'] = $this->url->link('module/seo_urls/add', 'token=' . $this->session->data['token'], 'SSL');
		$data['edit_url'] = $this->url->link('module/seo_urls/edit', 'token=' . $this->session->data['token'], 'SSL');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/seo_urls.tpl', $data));
	}

	public function add() {
		$this->load->language('module/seo_urls');

		$json = array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('seo_urls', $this->request->post);

			$route = $this->request->post['route'];

			$seo_url_route = 'route='.trim($route['route']);

			/**
			 * $key is language_id
			 * $value is SEO Friendly Url
			 */

			foreach ($route['url'] as $key => $value) {
				$seo_url_url = trim($value);

				$this->db->query("INSERT INTO ". DB_PREFIX ."url_alias SET query = '". $this->db->escape($seo_url_route) ."', keyword = '". $this->db->escape($seo_url_url) ."', language_id = '" . (int)$key . "'");
			}

			if($this->isXHR()) {
				$json['success'] = $this->language->get('text_success');
			} else {
				$this->session->data['success'] = $this->language->get('text_success');
				$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
		} else {
			if($this->isXHR()) {
				$json['error'] = $this->error;
			}
		}

		if($this->isXHR()) {
			echo json_encode($json);
		} else {
			$this->index();
		}
	}

	public function edit() {
		$this->load->language('module/seo_urls');

		$json = array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('seo_urls', $this->request->post);

			$route = $this->request->post['route'];

			$seo_url_route = 'route='.trim($route['route']);

			/**
			 * $key is language_id
			 * $value is SEO Friendly Url
			 */
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = '" . $this->db->escape($seo_url_route) . "'");

			foreach ($route['url'] as $key => $value) {
				$seo_url_url = trim($value);

				$this->db->query("INSERT INTO ". DB_PREFIX ."url_alias SET query = '". $this->db->escape($seo_url_route) ."', keyword = '". $this->db->escape($seo_url_url) ."', language_id = '" . (int)$key . "'");
			}

			if($this->isXHR()) {
				$json['success'] = $this->language->get('text_success');
			} else {
				$this->session->data['success'] = $this->language->get('text_success');
				$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}

		if($this->isXHR()) {
			echo json_encode($json);
		} else {
			$this->index();
		}
	}

	public function delete() {
		$this->load->language('module/seo_urls');

		if (isset($this->request->get['query']) && $this->validateDelete()) {
			$query = $this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'route=" . $this->db->escape($this->request->get['query']) . "'");
			$this->session->data['success'] = $this->language->get('text_success_delete');
			$this->response->redirect($this->url->link('module/seo_urls', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->index();
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/seo_urls')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$route = $this->request->post['route'];

		if (isset($route['route'])) {
			if (empty($route['route'])) {
			$this->error['warning'] = $this->language->get('error_url_route');
			}
		}

		if (isset($route['route'])) {
			$query = $this->db->query("SELECT * FROM ".DB_PREFIX."url_alias WHERE query = 'route=".$this->db->escape($route['route'])."'");
			if($query->num_rows) {
				$this->error['warning'] = $this->language->get('error_route');
			}
		}

		if (isset($route['url'])) {
			if (empty($route['url'])) {
			$this->error['warning'] = $this->language->get('error_url_route');
			}
		}

		if (isset($route['url'])) {
			foreach ($route['url'] as $language_id => $value) {
				$query = $this->db->query("SELECT * FROM ".DB_PREFIX."url_alias WHERE keyword='".$this->db->escape($value)."' AND language_id = '" . (int)$language_id . "'");

				if ($query->num_rows) {
					$this->error['warning'][$language_id] = $this->language->get('error_keyword');
				}
			}
		}


		return !$this->error;
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'module/seo_urls')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

	private function isXHR() {
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
}
?>
