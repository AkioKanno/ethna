<?php
/**
 *	controller.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/**
 *	����ȥ��饯�饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_Controller
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	string		���ץꥱ�������ID
	 */
	var $appid = 'PHPSTRUTS';

	/**
	 *	@var	string		���ץꥱ�������١����ǥ��쥯�ȥ�
	 */
	var $base = '';

	/**
	 *	@var	string		���ץꥱ�������١���URL
	 */
	var	$url = '';

	/**
	 *	@var	string		���ץꥱ�������DSN(Data Source Name)
	 */
	var $dsn;

	/**
	 *	@var	array		���ץꥱ�������ǥ��쥯�ȥ�
	 */
	var $directory = array(
		'action'		=> 'app/action',
		'etc'			=> 'etc',
		'locale'		=> 'locale',
		'log'			=> 'log',
		'template'		=> 'template',
		'template_c'	=> 'tmp',
	);

	/**
	 *	@var	array		��ĥ������
	 */
	var $ext = array(
		'php'			=> 'php',
		'tpl'			=> 'tpl',
	);

	/**
	 *	@var	array		���饹����
	 */
	var $class = array(
		'config'		=> 'Ethna_Config',
		'logger'		=> 'Ethna_Logger',
		'sql'			=> 'Ethna_AppSQL',
	);

	/**
	 *	@var	string		���Ѹ�������
	 */
	var $language;

	/**
	 *	@var	string		�����ƥ�¦���󥳡��ǥ���
	 */
	var	$system_encoding;

	/**
	 *	@var	string		���饤�����¦���󥳡��ǥ���
	 */
	var	$client_encoding;

	/**
	 *	@var	string		���饤����ȥ�����
	 */
	var $client_type;

	/**
	 *	@var	string	���߼¹���Υ��������̾
	 */
	var	$action_name;

	/**
	 *	@var	array	forward���
	 */
	var $forward = array();

	/**
	 *	@var	array	action���
	 */
	var $action = array();

	/**
	 *	@var	array	soap action���
	 */
	var $soap_action = array();

	/**
	 *	@var	array	���ץꥱ�������ޥ͡��������
	 */
	var	$manager = array();

	/**
	 *	@var	array	smarty modifier���
	 */
	var $smarty_modifier_plugin = array();

	/**
	 *	@var	array	smarty function���
	 */
	var $smarty_function_plugin = array();

	/**
	 *	@var	object	Ethna_Backend	backend���֥�������
	 */
	var $backend;

	/**
	 *	@var	object	Ethna_I18N		i18n���֥�������
	 */
	var $i18n;

	/**
	 *	@var	object	Ethna_ActionError	action error���֥�������
	 */
	var $action_error;

	/**
	 *	@var	object	Ethna_ActionForm	action form���֥�������
	 */
	var $action_form;

	/**
	 *	@var	object	Ethna_Session		���å���󥪥֥�������
	 */
	var $session;

	/**
	 *	@var	object	Ethna_Config		���ꥪ�֥�������
	 */
	var	$config;

	/**
	 *	@var	object	Ethna_Logger		�����֥�������
	 */
	var	$logger;

	/**
	 *	@var	object	Ethna_AppSQL		SQL���֥�������
	 */
	var	$sql;

	/**#@-*/


	/**
	 *	Ethna_Controller���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access		public
	 */
	function Ethna_Controller()
	{
		$GLOBALS['controller'] =& $this;
		$this->base = BASE;

		foreach ($this->directory as $key => $value) {
			if ($value[0] != '/') {
				$this->directory[$key] = $this->base . (empty($this->base) ? '' : '/') . $value;
			}
		}
		$this->i18n =& new Ethna_I18N($this->getDirectory('locale'), $this->getAppId());
		$this->action_form = null;
		list($this->language, $this->system_encoding, $this->client_encoding) = $this->_getDefaultLanguage();
		$this->client_type = $this->_getDefaultClientType();

		// ����ե������ɤ߹���
		$config_class = $this->class['config'];
		$this->config =& new $config_class($this);
		$this->dsn = $this->config->get('dsn');
		$this->url = $this->config->get('url');

		// �����ϳ���
		$logger_class = $this->class['logger'];
		$this->logger =& new $logger_class($this);
		$this->logger->begin();

		// SQL���֥�����������
		$sql_class = $this->class['sql'];
		$this->sql =& new $sql_class($this);

		// Ethna�ޥ͡���������
		$this->_activateEthnaManager();
	}

	/**
	 *	���ץꥱ�������ID���֤�
	 *
	 *	@access	public
	 *	@return	string	���ץꥱ�������ID
	 */
	function getAppId()
	{
		return ucfirst(strtolower($this->appid));
	}

	/**
	 *	DSN���֤�
	 *
	 *	@access	public
	 *	@return	string	DSN
	 */
	function getDSN()
	{
		return $this->dsn;
	}

	/**
	 *	���ץꥱ�������١���URL���֤�
	 *
	 *	@access	public
	 *	@return	string	���ץꥱ�������١���URL
	 */
	function getURL()
	{
		return $this->url;
	}

	/**
	 *	���ץꥱ�������١����ǥ��쥯�ȥ���֤�
	 *
	 *	@access	public
	 *	@return	string	���ץꥱ�������١����ǥ��쥯�ȥ�
	 */
	function getBasedir()
	{
		return $this->base;
	}

	/**
	 *	���饤����ȥ�����/���줫��ƥ�ץ졼�ȥǥ��쥯�ȥ�̾����ꤹ��
	 *
	 *	@access	public
	 *	@return	string	�ƥ�ץ졼�ȥǥ��쥯�ȥ�
	 */
	function getTemplatedir()
	{
		$template = $this->getDirectory('template');
		if ($this->client_type != null && file_exists($template . '/' . $this->client_type)) {
			$template .= '/' . $this->client_type;
		}
		if (file_exists($template . '/' . $this->language)) {
			$template .= '/' . $this->language;
		}

		return $template;
	}

	/**
	 *	���ץꥱ�������ǥ��쥯�ȥ�������֤�
	 *
	 *	@access	public
	 *	@param	string	$key	�ǥ��쥯�ȥ꥿����("tmp", "template"...)
	 *	@return	string	$key���б��������ץꥱ�������ǥ��쥯�ȥ�(���̵꤬������null)
	 */
	function getDirectory($key)
	{
		if (isset($this->directory[$key]) == false) {
			return null;
		}
		return $this->directory[$key];
	}

	/**
	 *	i18n���֥������ȤΥ�������(R)
	 *
	 *	@access	public
	 *	@return	object	Ethna_I18N	i18n���֥�������
	 */
	function &getI18N()
	{
		return $this->i18n;
	}

	/**
	 *	���ꥪ�֥������ȤΥ�������
	 *
	 *	@access	public
	 *	@return	object	Ethna_Config	���ꥪ�֥�������
	 */
	function &getConfig()
	{
		return $this->config;
	}

	/**
	 *	backend���֥������ȤΥ�������
	 *
	 *	@access	public
	 *	@return	object	Ethna_Backend	backend���֥�������
	 */
	function &getBackend()
	{
		return $this->backend;
	}

	/**
	 *	action error���֥������ȤΥ�������
	 *
	 *	@access	public
	 *	@return	object	Ethna_ActionError	action error���֥�������
	 */
	function &getActionError()
	{
		return $this->action_error;
	}

	/**
	 *	action form���֥������ȤΥ�������
	 *
	 *	@access	public
	 *	@return	object	Ethna_ActionForm	action form���֥�������
	 */
	function &getActionForm()
	{
		return $this->action_form;
	}

	/**
	 *	���å���󥪥֥������ȤΥ�������
	 *
	 *	@access	public
	 *	@return	object	Ethna_Session		���å���󥪥֥�������
	 */
	function &getSession()
	{
		return $this->session;
	}

	/**
	 *	�����֥������ȤΥ�������
	 *
	 *	@access	public
	 *	@return	object	Ethna_Logger		�����֥�������
	 */
	function &getLogger()
	{
		return $this->logger;
	}

	/**
	 *	SQL���֥������ȤΥ�������
	 *
	 *	@access	public
	 *	@return	object	Ethna_AppSQL	SQL���֥�������
	 */
	function &getSQL()
	{
		return $this->sql;
	}

	/**
	 *	�ޥ͡�����������֤�
	 *
	 *	@access	public
	 *	@return	array	�ޥ͡��������
	 */
	function getManagerList()
	{
		return $this->manager;
	}

	/**
	 *	�¹����action̾���֤�
	 *
	 *	@access	public
	 *	@return	string	�¹����action̾
	 */
	function getCurrentActionName()
	{
		return $this->action_name;
	}

	/**
	 *	���Ѹ�����������
	 *
	 *	@access	public
	 *	@return	array	���Ѹ���,�����ƥ२�󥳡��ǥ���̾,���饤����ȥ��󥳡��ǥ���̾
	 */
	function getLanguage()
	{
		return array($this->language, $this->system_encoding, $this->client_encoding);
	}

	/**
	 *	���饤����ȥ����פ��������
	 *
	 *	@access	public
	 *	@return	int		���饤����ȥ��������(CLIENT_TYPE_WWW...)
	 */
	function getClientType()
	{
		return $this->client_type;
	}

	/**
	 *	���饤����ȥ����פ����ꤹ��
	 *
	 *	@access	public
	 *	@param	int		$client_type	���饤����ȥ��������(CLIENT_TYPE_WWW...)
	 */
	function setClientType($client_type)
	{
		$this->client_type = $client_type;
	}

	/**
	 *	�ƥ�ץ졼�ȥ��󥸥��������(���ߤ�smarty�Τ��б�)
	 *
	 *	@access	public
	 *	@return	object	Smarty	�ƥ�ץ졼�ȥ��󥸥󥪥֥�������
	 *	@todo	�֥�å��ؿ��ץ饰����(etc)�б�
	 */
	function &getTemplateEngine()
	{
		$smarty =& new Smarty();
		$smarty->template_dir = $this->getTemplatedir();
		$smarty->compile_dir = $this->getDirectory('template_c');

		// default modifiers
		$smarty->register_modifier('number_format', 'smarty_modifier_number_format');
		$smarty->register_modifier('strftime', 'smarty_modifier_strftime');
		$smarty->register_modifier('count', 'smarty_modifier_count');
		$smarty->register_modifier('join', 'smarty_modifier_join');
		$smarty->register_modifier('filter', 'smarty_modifier_filter');
		$smarty->register_modifier('unique', 'smarty_modifier_unique');
		$smarty->register_modifier('wordwrap_i18n', 'smarty_modifier_wordwrap_i18n');
		$smarty->register_modifier('i18n', 'smarty_modifier_i18n');
		$smarty->register_modifier('checkbox', 'smarty_modifier_checkbox');
		$smarty->register_modifier('select', 'smarty_modifier_select');

		// user defined modifiers
		foreach ($this->smarty_modifier_plugin as $modifier) {
			$name = str_replace('smarty_modifier_', '', $modifier);
			$smarty->register_modifier($name, $modifier);
		}

		// default functions
		$smarty->register_function('message', 'smarty_function_message');
		$smarty->register_function('uniqid', 'smarty_function_uniqid');
		$smarty->register_function('select', 'smarty_function_select');
		$smarty->register_function('checkbox_list', 'smarty_function_checkbox_list');

		// user defined functions
		foreach ($this->smarty_function_plugin as $function) {
			$name = str_replace('smarty_function_', '', $function);
			$smarty->register_function($name, $function);
		}

		return $smarty;
	}

	/**
	 *	���ץꥱ�������Υ���ȥ�ݥ����
	 *
	 *	@access	public
	 *	@param	string	$class_name		���ץꥱ������󥳥�ȥ���Υ��饹̾
	 *	@param	mixed	$action_name	����Υ��������̾(��ά��)
	 *	@static
	 */
	function main($class_name, $action_name = "")
	{
		$c =& new $class_name;
		$c->setClientType(CLIENT_TYPE_WWW);
		$c->trigger($action_name);
	}

	/**
	 *	���ޥ�ɥ饤�󥢥ץꥱ�������Υ���ȥ�ݥ����
	 *
	 *	@access	public
	 *	@param	string	$class_name		���ץꥱ������󥳥�ȥ���Υ��饹̾
	 *	@param	string	$action_name	�¹Ԥ��륢�������̾
	 *	@static
	 */
	function main_CLI($class_name, $action_name)
	{
		$c =& new $class_name;
		$c->action[$action_name] = array();
		$c->setClientType(CLIENT_TYPE_WWW);
		$c->trigger($action_name);
	}

	/**
	 *	SOAP���ץꥱ�������Υ���ȥ�ݥ����
	 *
	 *	@access	public
	 *	@param	string	$class_name	���ץꥱ������󥳥�ȥ���Υ��饹̾
	 *	@static
	 */
	function main_SOAP($class_name)
	{
		$c =& new $class_name;
		$c->setClientType(CLIENT_TYPE_SOAP);
		$c->trigger_SOAP();
	}

	/**
	 *	AMF(Flash Remoting)���ץꥱ�������Υ���ȥ�ݥ����
	 *
	 *	@access	public
	 *	@param	string	$class_name	���ץꥱ������󥳥�ȥ���Υ��饹̾
	 *	@static
	 */
	function main_AMF($class_name)
	{
		$c =& new $class_name;
		$c->setClientType(CLIENT_TYPE_AMF);
		$c->trigger_AMF();
	}

	/**
	 *	�ե졼�����ν����򳫻Ϥ���
	 *
	 *	����$default_action_name�����󤬻��ꤵ�줿��硢��������ǻ��ꤵ�줿
	 *	action�ʳ��ϼ����դ��ʤ�(����ʳ���action�����ꤵ�줿��硢�������Ƭ
	 *	�ǻ��ꤵ�줿��������󤬼¹Ԥ����)
	 *
	 *	@access	public
	 *	@param	mixed	$default_action_name	����Υ��������̾
	 *	@return	mixed	0:���ｪλ -1:���顼
	 *	@todo	̤���ݡ��Ȥ�action�����ꤵ�줿���Υ��顼����
	 */
	function trigger($default_action_name = "")
	{
		// action�η���
		$action_name = $this->_getActionName($default_action_name);
		$this->action_name = $action_name;
		$action_obj =& $this->_getAction($action_name);
		if (is_null($action_obj)) {
			// try default action
			if ($default_action_name != "") {
				$action_obj =& $this->_getAction($default_action_name);
			}
			if ($action_obj == null) {
				trigger_error(sprintf("unsupported action [%s]", $action_name), E_USER_ERROR);
				return -1;
			} else {
				$action_name = $default_action_name;
			}
		}

		// action�����include
		$this->_includeActionScript();

		// ��������
		$this->_setLanguage($this->language, $this->system_encoding, $this->client_encoding);

		// ���֥�����������
		$this->action_error =& new Ethna_ActionError();
		$form_name = $this->getActionFormName($action_name);
		$this->action_form =& new $form_name($this);
		$this->session =& new Ethna_Session($this->getAppId(), $this->getDirectory('tmp'), $this->logger);

		// �Хå�����ɽ����¹�
		$backend =& new Ethna_Backend($this);
		$this->backend =& $backend;
		$forward_name = $backend->perform($action_name);

		// forward�������¹�
		if (isset($this->forward[$forward_name]) &&
			isset($this->forward[$forward_name]['preforward_name']) &&
			class_exists($this->forward[$forward_name]['preforward_name'])) {
			$backend->preforward($this->forward[$forward_name]['preforward_name']);
		}

		if ($forward_name != null) {
			if ($this->_forward($forward_name) != 0) {
				return -1;
			}
		}

		return 0;
	}

	/**
	 *  SOAP�ե졼�����ν����򳫻Ϥ���
 	 *
	 *  @access public
	 */
	function trigger_SOAP()
	{
		// action�����include
		$this->_includeActionScript();

		// SOAP����ȥꥯ�饹
		$gg =& new Ethna_SoapGatewayGenerator();
		$script = $gg->generate();
		eval($script);

		// SOAP�ꥯ�����Ƚ���
		$server =& new SoapServer(null, array('uri' => $this->config->get('url')));
		$server->setClass($gg->getClassName());
		$server->handle();
	}

	/**
	 *	AMF(Flash Remoting)�ե졼�����ν����򳫻Ϥ���
	 *
	 *	@access	public
	 */
	function trigger_AMF()
	{
		include_once('ethna/contrib/amfphp/app/Gateway.php');

		$this->action_error =& new Ethna_ActionError();

		// Credential�إå��ǥ��å������������ΤǤ����Ǥ�null������
		$this->session = null;

		$this->_setLanguage($this->language, $this->system_encoding, $this->client_encoding);

		// backend���֥�������
		$backend =& new Ethna_Backend($this);
		$this->backend =& $backend;

		// action�����include
		$this->_includeActionScript();

		// amfphp�˽�����Ѿ�
		$gateway =& new Gateway();
		$gateway->setBaseClassPath('');
		$gateway->service();
	}

	/**
	 *	��̿Ū���顼ȯ�����β��̤�ɽ������
	 *
	 *	��ա��᥽�åɸƤӽФ������Ƥν��������Ǥ����(���Υ᥽�åɤ�exit()����)
	 *
	 *	@access	public
	 */
	function fatal()
	{
		exit(0);
	}

	/**
	 *	���ꤵ�줿action�Υե����९�饹̾���֤�(���֥������Ȥ������ϹԤ�ʤ�)
	 *
	 *	@access	public
	 *	@param	string	$action_name	action̾
	 *	@return	string	action form�Υ��饹̾
	 */
	function getActionFormName($action_name)
	{
		$action_obj =& $this->_getAction($action_name);
		if ($action_obj == null) {
			return null;
		}

		if (class_exists($action_obj['form_name'])) {
			return $action_obj['form_name'];
		} else {
			// fall back to default
			return 'Ethna_ActionForm';
		}
	}

	/**
	 *	���ꤵ�줿action�Υ��饹̾���֤�(���֥������Ȥ������ϹԤ�ʤ�)
	 *
	 *	@access	public
	 *	@param	string	$action_name	action��̾��
	 *	@return	string	action class�Υ��饹̾
	 */
	function getActionClassName($action_name)
	{
		$action_obj =& $this->_getAction($action_name);
		if ($action_obj == null) {
			return null;
		}

		return $action_obj['class_name'];
	}

	/**
	 *	�ե�����ˤ���׵ᤵ�줿action̾���֤�
	 *
	 *	���ץꥱ�������������˱����Ƥ��Υ᥽�åɤ򥪡��С��饤�ɤ��Ʋ�������
	 *	�ǥե���ȤǤ�"action_"�ǻϤޤ�ե������ͤ�"action_"����ʬ����������
	 *	("action_sample"�ʤ�"sample")��action̾�Ȥ��ư����ޤ�
	 *
	 *	@access	protected
	 *	@param	mixed	$default_action_name	����Υ��������̾
	 *	@return	string	�׵ᤵ�줿action��̾��
	 */
	function _getActionName($default_action_name)
	{
		if (isset($_SERVER['REQUEST_METHOD']) == false) {
			return $default_action_name;
		}

		if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0) {
			$http_vars =& $_POST;
		} else {
			$http_vars =& $_GET;
		}

		$action_name = null;
		$fallback_action_name = null;
		foreach ($http_vars as $name => $value) {
			if ($value == "") {
				continue;
			}
			if (strncmp($name, 'action_', 7) == 0) {
				$tmp = substr($name, 7);
				if (preg_match('/_x$/', $name) || preg_match('/_y$/', $name)) {
					$tmp = substr($tmp, 0, strlen($tmp)-2);
				}
				if ($value != "" && $value != "dummy") {
					$action_name = $tmp;
				} else {
					$fallback_action_name = $tmp;
				}
			}
		}

		if ($action_name == null) {
			if ($fallback_action_name == null) {
				$action_name = is_array($default_action_name) ? $default_action_name[0] : $default_action_name;
			} else {
				$action_name = $fallback_action_name;
			}
		}
		if (is_array($default_action_name)) {
			if (in_array($action_name, $default_action_name) == false) {
				return $default_action_name[0];
			}
		}

		$this->logger->log(LOG_DEBUG, 'action_name[%s]', $action_name);

		return $action_name;
	}

	/**
	 *	�ե�����ˤ���׵ᤵ�줿action���б�����������֤�
	 *
	 *	@access	private
	 *	@param	string	$action_name	action��̾��
	 *	@return	array	action���
	 */
	function &_getAction($action_name)
	{
		if ($this->client_type == CLIENT_TYPE_WWW) {
			$action =& $this->action;
		} else if ($this->client_type == CLIENT_TYPE_SOAP) {
			$action =& $this->soap_action;
		}

		if (isset($action[$action_name]) == false) {
			if ($action_name != null) {
				return null;
			}

			return null;
		}

		// ��ά���䴰
		if (isset($action[$action_name]['form_name']) == false) {
			$action[$action_name]['form_name'] = $this->_getDefaultFormClass($action_name);
		}
		if (isset($action[$action_name]['class_name']) == false) {
			$action[$action_name]['class_name'] = $this->_getDefaultActionClass($action_name);
		}

		return $action[$action_name];
	}

	/**
	 *	���ꤵ�줿forward̾���б�������̤���Ϥ���
	 *
	 *	@access	private
	 *	@param	string	$forward_name	Forward̾
	 *	@return	bool	0:���ｪλ -1:���顼
	 */
	function _forward($forward_name)
	{
		$forward_path = $this->_getForwardPath($forward_name);
		$smarty =& $this->getTemplateEngine();

		$form_array =& $this->action_form->getArray();
		$app_array =& $this->action_form->getAppArray();
		$app_ne_array =& $this->action_form->getAppNEArray();
		$smarty->assign_by_ref('form', $form_array);
		$smarty->assign_by_ref('app', $app_array);
		$smarty->assign_by_ref('app_ne', $app_ne_array);
		$smarty->assign_by_ref('errors', Ethna_Util::escapeHtml($this->action_error->getMessageList()));
		if (isset($_SESSION)) {
			$smarty->assign_by_ref('session', Ethna_Util::escapeHtml($_SESSION));
		}
		$smarty->assign('script', basename($_SERVER['PHP_SELF']));
		$smarty->assign('request_uri', htmlspecialchars($_SERVER['REQUEST_URI']));

		// �ǥե���ȥޥ��������
		$this->_setDefaultMacro($smarty);

		$smarty->display($forward_path);

		return 0;
	}

	/**
	 *	forward̾����ƥ�ץ졼�ȥե�����Υѥ�̾���������
	 *
	 *	@access	private
	 *	@param	string	$forward_name	forward̾
	 *	@return	string	�ƥ�ץ졼�ȥե�����Υѥ�̾
	 */
	function _getForwardPath($forward_name)
	{
		$forward_obj = null;

		if (isset($this->forward[$forward_name]) == false) {
			// try default
			$this->forward[$forward_name] = array();
		}
		$forward_obj =& $this->forward[$forward_name];
		if (isset($forward_obj['forward_path']) == false) {
			// ��ά������
			$forward_obj['forward_path'] = $this->_getDefaultForwardPath($forward_name);
		}

		return $forward_obj['forward_path'];
	}

	/**
	 *	���Ѹ�������ꤹ��
	 *
	 *	����ؤγ�ĥ�Τ���Τߤ�¸�ߤ��Ƥ��ޤ������ߤ��ä˥����С��饤�ɤ�ɬ�פϤ���ޤ���
	 *
	 *	@access	protected
	 *	@param	string	$language			�������(LANG_JA, LANG_EN...)
	 *	@param	string	$system_encoding	�����ƥ२�󥳡��ǥ���̾
	 *	@param	string	$client_encoding	���饤����ȥ��󥳡��ǥ���
	 */
	function _setLanguage($language, $system_encoding = null, $client_encoding = null)
	{
		$this->language = $language;
		$this->system_encoding = $system_encoding;
		$this->client_encoding = $client_encoding;

		$this->i18n->setLanguage($language, $system_encoding, $client_encoding);
	}

	/**
	 *	�ǥե���Ⱦ��֤Ǥλ��Ѹ�����������
	 *
	 *	@access	protected
	 *	@return	array	���Ѹ���,�����ƥ२�󥳡��ǥ���̾,���饤����ȥ��󥳡��ǥ���̾
	 */
	function _getDefaultLanguage()
	{
		return array(LANG_JA, null, null);
	}

	/**
	 *	�ǥե���Ⱦ��֤ǤΥ��饤����ȥ����פ��������
	 *
	 *	@access	protected
	 *	@return	int		���饤����ȥ��������(CLIENT_TYPE_WWW...)
	 */
	function _getDefaultClientType()
	{
		return CLIENT_TYPE_WWW;
	}

	/**
	 *	action����ե������include����
	 *
	 *	@access	private
	 */
	function _includeActionScript()
	{
		$ext = "." . $this->ext['php'];
		$ext_len = strlen($ext);
		$action_dir = (empty($this->directory['action']) ? ($this->base . (empty($this->base) ? '' : '/')) : ($this->directory['action'] . "/"));

		$dh = opendir($action_dir);
		if ($dh) {
			while (($file = readdir($dh)) !== false) {
				if (substr($file, -$ext_len, $ext_len) != $ext) {
					continue;
				}
				include_once("$action_dir/$file");
			}
		}
		closedir($dh);
	}

	/**
	 *	���ܻ��Υǥե���ȥޥ�������ꤹ��
	 *
	 *	@access	protected
	 *	@param	object	Smarty	$smarty	�ƥ�ץ졼�ȥ��󥸥󥪥֥�������
	 */
	function _setDefaultMacro(&$smarty)
	{
	}

	/**
	 *	Ethna�ޥ͡���������ꤹ��(���פʾ��϶��Υ᥽�åɤȤ��ƥ����С��饤�ɤ��Ƥ�褤)
	 *
	 *	@access	protected
	 */
	function _activateEthnaManager()
	{
		$base = dirname(dirname(__FILE__));

		if ($this->config->get('debug') == false) {
			return;
		}

		// action����
		$this->action['__ethna_info__'] = array(
			'form_name' =>	'Ethna_Form_Info',
			'class_name' =>	'Ethna_Action_Info',
		);
		$this->action['__ethna_info_do__'] = array(
			'form_name' =>	'Ethna_Form_InfoDo',
			'class_name' =>	'Ethna_Action_InfoDo',
		);

		// forward����
		$forward_obj = array();

		$forward_obj['forward_path'] = sprintf("%s/tpl/info.tpl", $base);
		$forward_obj['preforward_name'] = 'Ethna_Action_Info';
		$this->forward['__ethna_info__'] = $forward_obj;
	}

	/**
	 *	action���б�����ե����९�饹̾����ά���줿���Υǥե���ȥ��饹̾���֤�
	 *
	 *	�ǥե���ȤǤ�[�ץ�������ID]_Form_[���������̾]�Ȥʤ�Τǹ��߱����ƥ����Х饤�ɤ���
	 *
	 *	@access	protected
	 *	@param	string	$action_name	action̾
	 *	@return	string	action form���饹̾
	 */
	function _getDefaultFormClass($action_name)
	{
		return sprintf("%s_%sForm_%s",
			$this->getAppId(),
			$this->getClientType() == CLIENT_TYPE_SOAP ? "S" : "",
			preg_replace('/_(.)/e', "strtoupper('\$1')", ucfirst($action_name))
		);
	}

	/**
	 *	action���б����륢������󥯥饹̾����ά���줿���Υǥե���ȥ��饹̾���֤�
	 *
	 *	�ǥե���ȤǤ�[�ץ�������ID]_Action_[���������̾]�Ȥʤ�Τǹ��߱����ƥ����Х饤�ɤ���
	 *
	 *	@access	protected
	 *	@param	string	$action_name	action̾
	 *	@return	string	action class���饹̾
	 */
	function _getDefaultActionClass($action_name)
	{
		return sprintf("%s_%sAction_%s",
			$this->getAppId(),
			$this->getClientType() == CLIENT_TYPE_SOAP ? "S" : "",
			preg_replace('/_(.)/e', "strtoupper('\$1')", ucfirst($action_name))
		);
	}

	/**
	 *	forward���б�����ƥ�ץ졼�ȥѥ�̾����ά���줿���Υǥե���ȥѥ�̾���֤�
	 *
	 *	�ǥե���ȤǤ�"foo_bar"�Ȥ���forward̾��"foo/bar" + �ƥ�ץ졼�ȳ�ĥ�ҤȤʤ�
	 *	�Τǹ��߱����ƥ����Х饤�ɤ���
	 *
	 *	@access	protected
	 *	@param	string	$forward_name	forward̾
	 *	@return	string	forward�ѥ�̾
	 */
	function _getDefaultForwardPath($forward_name)
	{
		return str_replace('_', '/', $forward_name) . '.' . $this->ext['tpl'];
	}
}
?>
