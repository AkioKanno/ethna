<?php
// vim: foldmethod=marker
/**
 *	Ethna_Controller.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_Controller
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
	var $appid = 'ETHNA';

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
		'filter'		=> 'app/filter',
		'locale'		=> 'locale',
		'log'			=> 'log',
		'plugins'		=> array(),
		'template'		=> 'template',
		'template_c'	=> 'tmp',
		'tmp'			=> 'tmp',
		'view'			=> 'app/view',
	);

	/**
	 *	@var	array		DB�����������
	 */
	var	$db = array(
		''				=> DB_TYPE_RW,
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
		'db'			=> 'Ethna_DB',
		'logger'		=> 'Ethna_Logger',
		'sql'			=> 'Ethna_AppSQL',
	);

	/**
	 *	@var	array		�ե��륿����
	 */
	var $filter = array(
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
	 *	@var	array	smarty prefilter���
	 */
	var $smarty_prefilter_plugin = array();

	/**
	 *	@var	array	smarty postfilter���
	 */
	var $smarty_postfilter_plugin = array();

	/**
	 *	@var	array	smarty outputfilter���
	 */
	var $smarty_outputfilter_plugin = array();

	/**
	 *	@var	object	Ethna_Backend	backend���֥�������
	 */
	var $backend = null;

	/**
	 *	@var	object	Ethna_I18N		i18n���֥�������
	 */
	var $i18n = null;

	/**
	 *	@var	object	Ethna_ActionError	��������󥨥顼���֥�������
	 */
	var $action_error = null;

	/**
	 *	@var	object	Ethna_ActionForm	���������ե����४�֥�������
	 */
	var $action_form = null;

	/**
	 *	@var	object	Ethna_Session		���å���󥪥֥�������
	 */
	var $session = null;

	/**
	 *	@var	object	Ethna_Config		���ꥪ�֥�������
	 */
	var	$config = null;

	/**
	 *	@var	object	Ethna_Logger		�����֥�������
	 */
	var	$logger = null;

	/**
	 *	@var	object	Ethna_AppSQL		SQL���֥�������
	 */
	var	$sql = null;

	/**
	 *	@var	array	�ե��륿����������(Ethna_Filter���֥������Ȥ�����)
	 */
	var	$filter_chain = array();

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

		// ���顼�ϥ�ɥ������
		Ethna::setErrorCallback(array(&$this, 'handleError'));

		// �ǥ��쥯�ȥ�̾������(���Хѥ�->���Хѥ�)
		foreach ($this->directory as $key => $value) {
			if ($key == 'plugins') {
				// Smarty�ץ饰����ǥ��쥯�ȥ������ǻ��ꤹ��
				$tmp = array(SMARTY_DIR . 'plugins');
				foreach (to_array($value) as $elt) {
					if ($elt{0} != '/') {
						$tmp[] = $this->base . (empty($this->base) ? '' : '/') . $elt;
					}
				}
				$this->directory[$key] = $tmp;
			} else {
				if ($value{0} != '/') {
					$this->directory[$key] = $this->base . (empty($this->base) ? '' : '/') . $value;
				}
			}
		}
		$this->i18n =& new Ethna_I18N($this->getDirectory('locale'), $this->getAppId());
		list($this->language, $this->system_encoding, $this->client_encoding) = $this->_getDefaultLanguage();
		$this->client_type = $this->_getDefaultClientType();

		// ����ե������ɤ߹���
		$config_class = $this->class['config'];
		$this->config =& new $config_class($this);
		$this->dsn = $this->_prepareDSN();
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
	 *	(���ߥ����ƥ��֤�)����ȥ���Υ��󥹥��󥹤��֤�
	 *
	 *	@access	public
	 *	@return	object	Ethna_Controller	����ȥ���Υ��󥹥���
	 *	@static
	 */
	function &getInstance()
	{
		if (isset($GLOBALS['controller'])) {
			return $GLOBALS['controller'];
		} else {
			return null;
		}
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
	 *	@param	string	$type	DB����
	 *	@return	string	DSN
	 */
	function getDSN($type = "")
	{
		if (isset($this->dsn[$type]) == false) {
			return null;
		}
		return $this->dsn[$type];
	}

	/**
	 *	DSN�λ�³��³������֤�
	 *
	 *	@access	public
	 *	@param	string	$type	DB����
	 *	@return	bool	true:persistent false:non-persistent(���뤤������̵��)
	 */
	function getDSN_persistent($type = "")
	{
		$key = sprintf("dsn%s_persistent", $type == "" ? "" : "_$type");
		$dsn_persistent = $this->config->get($key);
		if (is_null($dsn_persistent)) {
			return false;
		}
		return $dsn_persistent;
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

		// �����̥ǥ��쥯�ȥ�
		if (file_exists($template . '/' . $this->language)) {
			$template .= '/' . $this->language;
		}

		// ���饤������̥ǥ��쥯�ȥ�(if we need)
		if ($this->client_type == CLIENT_TYPE_MOBILE_AU && file_exists($template . '/au')) {
			$template .= '/au';
		}

		return $template;
	}

	/**
	 *	���������ǥ��쥯�ȥ�̾����ꤹ��
	 *
	 *	@access	public
	 *	@return	string	���������ǥ��쥯�ȥ�
	 */
	function getActiondir()
	{
		return (empty($this->directory['action']) ? ($this->base . (empty($this->base) ? '' : '/')) : ($this->directory['action'] . "/"));
	}

	/**
	 *	�ӥ塼�ǥ��쥯�ȥ�̾����ꤹ��
	 *
	 *	@access	public
	 *	@return	string	���������ǥ��쥯�ȥ�
	 */
	function getViewdir()
	{
		return (empty($this->directory['view']) ? ($this->base . (empty($this->base) ? '' : '/')) : ($this->directory['view'] . "/"));
	}

	/**
	 *	���ץꥱ������󥯥饹������֤�
	 *
	 *	@access	public
	 *	@param	string	$key	���饹������("config", "db", "logger", "sql")
	 *	@return	string	$key���б��������饹̾(���꤬�ʤ�����null)
	 */
	function getClass($key)
	{
		if (isset($this->class[$key]) == false) {
			return null;
		}
		return $this->class[$key];
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
	 *	DB������֤�
	 *
	 *	@access	public
	 *	@param	string	$key	DB������("r", ...)
	 *	@return	string	$key���б�����DB�������(���̵꤬������null)
	 */
	function getDB($key)
	{
		if (isset($this->db[$key]) == false) {
			return null;
		}
		return $this->db[$key];
	}

	/**
	 *	���ץꥱ��������ĥ��������֤�
	 *
	 *	@access	public
	 *	@param	string	$key	��ĥ�ҥ�����("php", "tpl"...)
	 *	@return	string	$key���б�������ĥ��(���̵꤬������null)
	 */
	function getExt($key)
	{
		if (isset($this->ext[$key]) == false) {
			return null;
		}
		return $this->ext[$key];
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
	 *	��������󥨥顼���֥������ȤΥ�������
	 *
	 *	@access	public
	 *	@return	object	Ethna_ActionError	��������󥨥顼���֥�������
	 */
	function &getActionError()
	{
		return $this->action_error;
	}

	/**
	 *	���������ե�����form���֥������ȤΥ�������
	 *
	 *	@access	public
	 *	@return	object	Ethna_ActionForm	���������ե�����form���֥�������
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
	 *	�¹���Υ��������̾���֤�
	 *
	 *	@access	public
	 *	@return	string	�¹���Υ��������̾
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
		$smarty->compile_dir = $this->getDirectory('template_c') . '/tpl_' . md5($smarty->template_dir);
		if (@is_dir($smarty->compile_dir) == false) {
			mkdir($smarty->compile_dir, 0755);
		}
		$smarty->plugins_dir = $this->getDirectory('plugins');

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
		$smarty->register_modifier('form_value', 'smarty_modifier_form_value');

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

		// user defined prefilters
		foreach ($this->smarty_prefilter_plugin as $prefilter) {
			$smarty->register_prefilter($prefilter);
		}

		// user defined postfilters
		foreach ($this->smarty_postfilter_plugin as $postfilter) {
			$smarty->register_postfilter($postfilter);
		}

		// user defined outputfilters
		foreach ($this->smarty_outputfilter_plugin as $outputfilter) {
			$smarty->register_outputfilter($outputfilter);
		}

		$this->_setDefaultTemplateEngine($smarty);

		return $smarty;
	}

	/**
	 *	���ץꥱ�������Υ���ȥ�ݥ����
	 *
	 *	@access	public
	 *	@param	string	$class_name		���ץꥱ������󥳥�ȥ���Υ��饹̾
	 *	@param	mixed	$action_name	����Υ��������̾(��ά��)
	 *	@param	mixed	$fallback_action_name	��������󤬷���Ǥ��ʤ��ä����˼¹Ԥ���륢�������̾(��ά��)
	 *	@static
	 */
	function main($class_name, $action_name = "", $fallback_action_name = "")
	{
		$c =& new $class_name;
		$c->trigger('www', $action_name, $fallback_action_name);
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
		$c->trigger('www', $action_name);
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
		$c->trigger('soap');
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
		$c->trigger('amf');
	}

	/**
	 *	�ե졼�����ν����򳫻Ϥ���
	 *
	 *	@access	public
	 *	@param	strint	$type					����������(WWW/SOAP/AMF)
	 *	@param	mixed	$default_action_name	����Υ��������̾
	 *	@param	mixed	$fallback_action_name	���������̾������Ǥ��ʤ��ä����˼¹Ԥ���륢�������̾
	 *	@return	mixed	0:���ｪλ Ethna_Error:���顼
	 */
	function trigger($type, $default_action_name = "", $fallback_action_name = "")
	{
		// �ե��륿��������
		$this->_createFilterChain();

		// prefilter
		for ($i = 0; $i < count($this->filter_chain); $i++) {
			$this->filter_chain[$i]->prefilter();
		}

		// trigger
		if ($type == 'www') {
			$this->_trigger($default_action_name, $fallback_action_name);
		} else if ($type == 'soap') {
			$this->_trigger_SOAP();
		} else if ($type == 'amf') {
			$this->_trigger_AMF();
		}

		// postfilter
		for ($i = count($this->filter_chain) - 1; $i >= 0; $i--) {
			$this->filter_chain[$i]->postfilter();
		}
	}

	/**
	 *	�ե졼�����ν�����¹Ԥ���(WWW)
	 *
	 *	����$default_action_name�����󤬻��ꤵ�줿��硢��������ǻ��ꤵ�줿
	 *	���������ʳ��ϼ����դ��ʤ�(����ʳ��Υ�������󤬻��ꤵ�줿��硢
	 *	�������Ƭ�ǻ��ꤵ�줿��������󤬼¹Ԥ����)
	 *
	 *	@access	private
	 *	@param	mixed	$default_action_name	����Υ��������̾
	 *	@param	mixed	$fallback_action_name	���������̾������Ǥ��ʤ��ä����˼¹Ԥ���륢�������̾
	 *	@return	mixed	0:���ｪλ Ethna_Error:���顼
	 */
	function _trigger($default_action_name = "", $fallback_action_name = "")
	{
		// ���������̾�μ���
		$action_name = $this->_getActionName($default_action_name, $fallback_action_name);

		// �������������μ���
		$action_obj =& $this->_getAction($action_name);
		if (is_null($action_obj)) {
			if ($fallback_action_name != "") {
				$this->logger->log(LOG_DEBUG, 'undefined action [%s] -> try fallback action [%s]', $action_name, $fallback_action_name);
				$action_obj =& $this->_getAction($fallback_action_name);
			}
			if ($action_obj == null) {
				return Ethna::raiseError(E_APP_UNDEFINED_ACTION, "undefined action [%s]", $action_name);
			} else {
				$action_name = $fallback_action_name;
			}
		}
		$this->action_name = $action_name;

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

		// ����ȥ�������������ꤹ��(���ץ����)
		$forward_name = $this->_sortForward($action_name, $forward_name);

		if ($forward_name != null) {
			$view_name = $this->getViewClassName($forward_name);
			$view_class =& new $view_name($this->backend, $forward_name, $this->_getForwardPath($forward_name));
			$view_class->preforward();
			// �����ߴ�����:(
			if (is_subclass_of($view_class, 'Ethna_ViewClass') == false) {
				$view_class =& new Ethna_ViewClass($this->backend, $forward_name, $this->_getForwardPath($forward_name));
			}
			$view_class->forward();
		}

		return 0;
	}

	/**
	 *  SOAP�ե졼�����ν�����¹Ԥ���
	 *
	 *	(experimental)
 	 *
	 *  @access private
	 */
	function _trigger_SOAP()
	{
		// ��������󥹥���ץȤ򥤥󥯥롼��
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
	 *	AMF(Flash Remoting)�ե졼�����ν�����¹Ԥ���
	 *
	 *	(experimental)
	 *
	 *	@access	public
	 */
	function _trigger_AMF()
	{
		include_once('ethna/contrib/amfphp/app/Gateway.php');

		$this->action_error =& new Ethna_ActionError();

		// Credential�إå��ǥ��å������������ΤǤ����Ǥ�null������
		$this->session = null;

		$this->_setLanguage($this->language, $this->system_encoding, $this->client_encoding);

		// backend���֥�������
		$backend =& new Ethna_Backend($this);
		$this->backend =& $backend;

		// ��������󥹥���ץȤ򥤥󥯥롼��
		$this->_includeActionScript();

		// amfphp�˽�����Ѿ�
		$gateway =& new Gateway();
		$gateway->setBaseClassPath('');
		$gateway->service();
	}

	/**
	 *	���顼�ϥ�ɥ�
	 *
	 *	���顼ȯ�������ɲý�����Ԥ��������Ϥ��Υ᥽�åɤ򥪡��С��饤�ɤ���
	 *	(���顼�ȥ᡼���������ݥǥե���ȤǤϥ����ϻ��˥��顼�ȥ᡼��
	 *	����������뤬�����顼ȯ�������̤˥��顼�ȥ᡼��򤳤�������
	 *	�����뤳�Ȥ��ǽ)
	 *
	 *	@access	public
	 *	@param	object	Ethna_Error		���顼���֥�������
	 */
	function handleError(&$error)
	{
		// ������
		list ($log_level, $dummy) = $this->logger->errorLevelToLogLevel($error->getLevel());
		$message = $error->getMessage();
		$this->logger->log($log_level, sprintf("[APP(%d)] %s", $error->getCode(), $message));
	}

	/**
	 *	���顼��å��������������
	 *
	 *	@access	public
	 *	@param	int		$code		���顼������
	 *	@return	string	���顼��å�����
	 */
	function getErrorMessage($code)
	{
		$message_list =& $GLOBALS['_Ethna_error_message_list'];
		for ($i = count($message_list)-1; $i >= 0; $i--) {
			if (array_key_exists($code, $message_list[$i])) {
				return $message_list[$i][$code];
			}
		}
		return null;
	}

	/**
	 *	���ꤵ�줿���������Υե����९�饹̾���֤�(���֥������Ȥ������ϹԤ�ʤ�)
	 *
	 *	@access	public
	 *	@param	string	$action_name	���������̾
	 *	@return	string	���������Υե����९�饹̾
	 */
	function getActionFormName($action_name)
	{
		$action_obj =& $this->_getAction($action_name);
		if (is_null($action_obj)) {
			return null;
		}

		return $action_obj['form_name'];
	}

	/**
	 *	���ꤵ�줿���������Υ��饹̾���֤�(���֥������Ȥ������ϹԤ�ʤ�)
	 *
	 *	@access	public
	 *	@param	string	$action_name	����������̾��
	 *	@return	string	���������Υ��饹̾
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
	 *	���ꤵ�줿����̾���б�����ӥ塼���饹̾���֤�(���֥������Ȥ������ϹԤ�ʤ�)
	 *
	 *	@access	public
	 *	@param	string	$forward_name	�������̾��
	 *	@return	string	view class�Υ��饹̾
	 */
	function getViewClassName($forward_name)
	{
		if ($forward_name == null) {
			return null;
		}

		if (isset($this->forward[$forward_name])) {
			$forward_obj = $this->forward[$forward_name];
		} else {
			$forward_obj = array();
		}

		if (isset($forward_obj['view_name'])) {
			$class_name = $forward_obj['view_name'];
			if (class_exists($class_name)) {
				return $class_name;
			}
		} else {
			$class_name = null;
		}

		// view�Υ��󥯥롼��
		$this->_includeViewScript($forward_obj, $forward_name);

		if (is_null($class_name) == false && class_exists($class_name)) {
			return $class_name;
		} else if (is_null($class_name) == false) {
			$this->logger->log(LOG_WARNING, 'stated view class is not defined [%s] -> try default', $class_name);
		}

		$class_name = $this->getDefaultViewClass($forward_name);
		if (class_exists($class_name)) {
			return $class_name;
		} else {
			$this->logger->log(LOG_DEBUG, 'view class is not defined for [%s] -> use default [%s]', $forward_name, 'Ethna_ViewClass');
			return 'Ethna_ViewClass';
		}
	}

	/**
	 *	�����������б�����ե����९�饹̾����ά���줿���Υǥե���ȥ��饹̾���֤�
	 *
	 *	�ǥե���ȤǤ�[�ץ�������ID]_Form_[���������̾]�Ȥʤ�Τǹ��߱����ƥ����Х饤�ɤ���
	 *
	 *	@access	public
	 *	@param	string	$action_name	���������̾
	 *	@param	bool	$fallback		���饤����ȼ��̤ˤ��fallback on/off
	 *	@return	string	���������ե�����̾
	 */
	function getDefaultFormClass($action_name, $fallback = true)
	{
		$postfix = preg_replace('/_(.)/e', "strtoupper('\$1')", ucfirst($action_name));

		$r = null;
		if ($this->getClientType() == CLIENT_TYPE_SOAP) {
			$r = sprintf("%s_SOAPForm_%s", $this->getAppId(), $postfix);
		} else if ($this->getClientType() == CLIENT_TYPE_MOBILE_AU) {
			$tmp = sprintf("%s_MobileAUForm_%s", $this->getAppId(), $postfix);
			if ($fallback == false || class_exists($tmp)) {
				$r = $tmp;
			}
		}

		if ($r == null) {
			$r = sprintf("%s_Form_%s", $this->getAppId(), $postfix);
		}
		$this->logger->log(LOG_DEBUG, "default action class [%s]", $r);
		return $r;
	}

	/**
	 *	�����������б�����ե�����ѥ�̾����ά���줿���Υǥե���ȥѥ�̾���֤�
	 *
	 *	�ǥե���ȤǤ�_getDefaultActionPath()��Ʊ����̤��֤�(1�ե������
	 *	��������󥯥饹�ȥե����९�饹�����Ҥ����)�Τǡ����ߤ˱�����
	 *	�����С��饤�ɤ���
	 *
	 *	@access	public
	 *	@param	string	$action_name	���������̾
	 *	@param	bool	$fallback		���饤����ȼ��̤ˤ��fallback on/off
	 *	@return	string	form class���������륹����ץȤΥѥ�̾
	 */
	function getDefaultFormPath($action_name, $fallback = true)
	{
		return $this->getDefaultActionPath($action_name, $fallback);
	}

	/**
	 *	�����������б����륢������󥯥饹̾����ά���줿���Υǥե���ȥ��饹̾���֤�
	 *
	 *	�ǥե���ȤǤ�[�ץ�������ID]_Action_[���������̾]�Ȥʤ�Τǹ��߱����ƥ����Х饤�ɤ���
	 *
	 *	@access	public
	 *	@param	string	$action_name	���������̾
	 *	@param	bool	$fallback		���饤����ȼ��̤ˤ��fallback on/off
	 *	@return	string	��������󥯥饹̾
	 */
	function getDefaultActionClass($action_name, $fallback = true)
	{
		$postfix = preg_replace('/_(.)/e', "strtoupper('\$1')", ucfirst($action_name));

		$r = null;
		if ($this->getClientType() == CLIENT_TYPE_SOAP) {
			$r = sprintf("%s_SOAPAction_%s", $this->getAppId(), $postfix);
		} else if ($this->getClientType() == CLIENT_TYPE_MOBILE_AU) {
			$tmp = sprintf("%s_MobileAUAction_%s", $this->getAppId(), $postfix);
			if ($fallback == false || class_exists($tmp)) {
				$r = $tmp;
			}
		}

		if ($r == null) {
			$r = sprintf("%s_Action_%s", $this->getAppId(), $postfix);
		}
		$this->logger->log(LOG_DEBUG, "default action class [%s]", $r);
		return $r;
	}

	/**
	 *	�����������б����륢�������ѥ�̾����ά���줿���Υǥե���ȥѥ�̾���֤�
	 *
	 *	�ǥե���ȤǤ�"foo_bar" -> "/Foo/Bar.php"�Ȥʤ�Τǹ��߱����ƥ����С��饤�ɤ���
	 *
	 *	@access	public
	 *	@param	string	$action_name	���������̾
	 *	@param	bool	$fallback		���饤����ȼ��̤ˤ��fallback on/off
	 *	@return	string	��������󥯥饹���������륹����ץȤΥѥ�̾
	 */
	function getDefaultActionPath($action_name, $fallback = true)
	{
		$default_path = preg_replace('/_(.)/e', "'/' . strtoupper('\$1')", ucfirst($action_name)) . '.' . $this->getExt('php');
		$action_dir = $this->getActiondir();

		if ($this->getClientType() == CLIENT_TYPE_SOAP) {
			$r = 'SOAP/' . $r;
		} else if ($this->getClientType() == CLIENT_TYPE_MOBILE_AU) {
			$r = 'MobileAU/' . $r;
		} else {
			$r = $default_path;
		}

		if ($fallback && file_exists($action_dir . $r) == false && $r != $default_path) {
			$this->logger->log(LOG_DEBUG, 'client_type specific file not found [%s] -> try defualt', $r);
			$r = $default_path;
		}

		$this->logger->log(LOG_DEBUG, "default action path [%s]", $r);
		return $r;
	}

	/**
	 *	����̾���б�����ӥ塼���饹̾����ά���줿���Υǥե���ȥ��饹̾���֤�
	 *
	 *	�ǥե���ȤǤ�[�ץ�������ID]_View_[����̾]�Ȥʤ�Τǹ��߱����ƥ����Х饤�ɤ���
	 *
	 *	@access	public
	 *	@param	string	$forward_name	forward̾
	 *	@param	bool	$fallback		���饤����ȼ��̤ˤ��fallback on/off
	 *	@return	string	view class���饹̾
	 */
	function getDefaultViewClass($forward_name, $fallback = true)
	{
		$postfix = preg_replace('/_(.)/e', "strtoupper('\$1')", ucfirst($forward_name));

		$r = null;
		if ($this->getClientType() == CLIENT_TYPE_MOBILE_AU) {
			$tmp = sprintf("%s_MobileAUView_%s", $this->getAppId(), $postfix);
			if ($fallback == false || class_exists($tmp)) {
				$r = $tmp;
			}
		}

		if ($r == null) {
			$r = sprintf("%s_View_%s", $this->getAppId(), $postfix);
		}
		$this->logger->log(LOG_DEBUG, "default view class [%s]", $r);
		return $r;
	}

	/**
	 *	����̾���б�����ӥ塼�ѥ�̾����ά���줿���Υǥե���ȥѥ�̾���֤�
	 *
	 *	�ǥե���ȤǤ�"foo_bar" -> "/Foo/Bar.php"�Ȥʤ�Τǹ��߱����ƥ����С��饤�ɤ���
	 *
	 *	@access	public
	 *	@param	string	$forward_name	forward̾
	 *	@param	bool	$fallback		���饤����ȼ��̤ˤ��fallback on/off
	 *	@return	string	view class���������륹����ץȤΥѥ�̾
	 */
	function getDefaultViewPath($forward_name, $fallback = true)
	{
		$default_path = preg_replace('/_(.)/e', "'/' . strtoupper('\$1')", ucfirst($forward_name)) . '.' . $this->getExt('php');
		$view_dir = $this->getViewdir();

		if ($this->getClientType() == CLIENT_TYPE_MOBILE_AU) {
			$r = 'MobileAU/' . $r;
		} else {
			$r = $default_path;
		}

		if ($fallback && file_exists($view_dir . $r) == false && $r != $default_path) {
			$this->logger->log(LOG_DEBUG, 'client_type specific file not found [%s] -> try defualt', $r);
			$r = $default_path;
		}

		$this->logger->log(LOG_DEBUG, "default view path [%s]", $r);
		return $r;
	}

	/**
	 *	�¹Ԥ��륢�������̾���֤�
	 *
	 *	@access	private
	 *	@param	mixed	$default_action_name	����Υ��������̾
	 *	@return	string	�¹Ԥ��륢�������̾
	 */
	function _getActionName($default_action_name, $fallback_action_name)
	{
		// �ե����फ���׵ᤵ�줿���������̾���������
		$form_action_name = $this->_getActionName_Form();
		$form_action_name = preg_replace('/[^a-z0-9\-_]+/i', '', $form_action_name);
		$this->logger->log(LOG_DEBUG, 'form_action_name[%s]', $form_action_name);

		// �ե����फ��λ��̵꤬�����ϥ���ȥ�ݥ���Ȥ˻��ꤵ�줿�ǥե�����ͤ����Ѥ���
		if ($form_action_name == "" && count($default_action_name) > 0) {
			$tmp = is_array($default_action_name) ? $default_action_name[0] : $default_action_name;
			if ($tmp{strlen($tmp)-1} == '*') {
				$tmp = substr($tmp, 0, -1);
			}
			$this->logger->log(LOG_DEBUG, '-> default_action_name[%s]', $tmp);
			$action_name = $tmp;
		} else {
			$action_name = $form_action_name;
		}

		// ����ȥ�ݥ���Ȥ����󤬻��ꤵ��Ƥ�����ϻ���ʳ��Υ��������̾�ϵ��ݤ���
		if (is_array($default_action_name)) {
			if ($this->_isAcceptableActionName($action_name, $default_action_name) == false) {
				// ����ʳ��Υ��������̾�ǹ�ä�����$fallback_action_name(or �ǥե����)
				$tmp = $fallback_action_name != "" ? $fallback_action_name : $default_action_name[0];
				if ($tmp{strlen($tmp)-1} == '*') {
					$tmp = substr($tmp, 0, -1);
				}
				$this->logger->log(LOG_DEBUG, '-> fallback_action_name[%s]', $tmp);
				$action_name = $tmp;
			}
		}

		$this->logger->log(LOG_DEBUG, '<<< action_name[%s] >>>', $action_name);

		return $action_name;
	}

	/**
	 *	�ե�����ˤ���׵ᤵ�줿���������̾���֤�
	 *
	 *	���ץꥱ�������������˱����Ƥ��Υ᥽�åɤ򥪡��С��饤�ɤ��Ʋ�������
	 *	�ǥե���ȤǤ�"action_"�ǻϤޤ�ե������ͤ�"action_"����ʬ����������
	 *	("action_sample"�ʤ�"sample")�����������̾�Ȥ��ư����ޤ�
	 *
	 *	@access	protected
	 *	@return	string	�ե�����ˤ���׵ᤵ�줿action��̾��
	 */
	function _getActionName_Form()
	{
		if (isset($_SERVER['REQUEST_METHOD']) == false) {
			return $default_action_name;
		}

		if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0) {
			$http_vars =& $_POST;
		} else {
			$http_vars =& $_GET;
		}

		// �ե������ͤ���ꥯ�����Ȥ��줿���������̾���������
		$action_name = $sub_action_name = null;
		foreach ($http_vars as $name => $value) {
			if ($value == "" || strncmp($name, 'action_', 7) != 0) {
				continue;
			}

			$tmp = substr($name, 7);

			// type="image"�б�
			if (preg_match('/_x$/', $name) || preg_match('/_y$/', $name)) {
				$tmp = substr($tmp, 0, strlen($tmp)-2);
			}

			// value="dummy"�ȤʤäƤ����Τ�ͥ���٤򲼤���
			if ($value == "dummy") {
				$sub_action_name = $tmp;
			} else {
				$action_name = $tmp;
			}
		}
		if ($action_name == null) {
			$action_name = $sub_action_name;
		}

		return $action_name;
	}

	/**
	 *	�ե�����ˤ���׵ᤵ�줿���������̾���б�����������֤�
	 *
	 *	@access	private
	 *	@param	string	$action_name	���������̾
	 *	@return	array	������������
	 */
	function &_getAction($action_name)
	{
		if ($this->client_type == CLIENT_TYPE_SOAP) {
			$action =& $this->soap_action;
		} else {
			$action =& $this->action;
		}

		$action_obj = array();
		if (isset($action[$action_name])) {
			$action_obj = $action[$action_name];
			if (isset($action_obj['inspect']) && $action_obj['inspect']) {
				return $action_obj;
			}
		} else {
			$this->logger->log(LOG_DEBUG, "action [%s] is not defined -> try default", $action_name);
		}

		// ��������󥹥���ץȤΥ��󥯥롼��
		$this->_includeActionScript($action_obj, $action_name);

		// ��ά�ͤ�����
		if (isset($action_obj['class_name']) == false) {
			$action_obj['class_name'] = $this->getDefaultActionClass($action_name);
		}

		if (isset($action_obj['form_name']) == false) {
			$action_obj['form_name'] = $this->getDefaultFormClass($action_name);
		} else {
			// �������ꤵ�줿�ե����९�饹���������Ƥ��ʤ����Ϸٹ�
			$this->logger->log(LOG_WARNING, 'stated form class is not defined [%s]', $action_obj['form_name']);
		}

		// ɬ�׾��γ�ǧ
		if (class_exists($action_obj['class_name']) == false) {
			$this->logger->log(LOG_WARNING, 'action class is not defined [%s]', $action_obj['class_name']);
			return null;
		}
		if (class_exists($action_obj['form_name']) == false) {
			// �ե����९�饹��̤����Ǥ��ɤ�
			$this->logger->log(LOG_DEBUG, 'form class is not defined [%s] -> falling back to default [%s]', $action_obj['form_name'], 'Ethna_ActionForm');
			$action_obj['form_name'] = 'Ethna_ActionForm';
		}

		$action_obj['inspect'] = true;
		$action[$action_name] = $action_obj;
		return $action[$action_name];
	}

	/**
	 *	���������̾�ȥ�������󥯥饹���������ͤ˴�Ť������������ꤹ��
	 *
	 *	@access	protected
	 *	@param	string	$action_name	���������̾
	 *	@param	string	$retval			��������󥯥饹����������
	 *	@return	string	������
	 */
	function _sortForward($action_name, $retval)
	{
		return $retval;
	}

	/**
	 *	����̾����ƥ�ץ졼�ȥե�����Υѥ�̾���������
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
		if (is_null($GLOBALS['_Ethna_client_type']) == false) {
			return $GLOBALS['_Ethna_client_type'];
		}
		return CLIENT_TYPE_WWW;
	}

	/**
	 *	��������󥹥���ץȤ򥤥󥯥롼�ɤ���
	 *
	 *	�����������󥯥롼�ɤ����ե�����˥��饹���������������Ƥ��뤫�ɤ������ݾڤ��ʤ�
	 *
	 *	@access	private
	 *	@param	array	$action_obj		������������
	 *	@param	string	$action_name	���������̾
	 */
	function _includeActionScript($action_obj, $action_name)
	{
		$class_path = $form_path = null;

		$action_dir = $this->getActiondir();

		// class_path°�������å�
		if (isset($action_obj['class_path'])) {
			if (file_exists($action_dir . $action_obj['class_path']) == false) {
				$this->logger->log(LOG_WARNING, 'class_path file not found [%s] -> try default', $action_obj['class_path']);
			} else {
				include_once($action_dir . $action_obj['class_path']);
				$class_path = $action_obj['class_path'];
			}
		}

		// �ǥե���ȥ����å�
		if (is_null($class_path)) {
			$class_path = $this->getDefaultActionPath($action_name);
			if (file_exists($action_dir . $class_path)) {
				include_once($action_dir . $class_path);
			} else {
				$this->logger->log(LOG_DEBUG, 'default action file not found [%s] -> try all files', $class_path);
				$class_path = null;
			}
		}
		
		// ���ե����륤�󥯥롼��
		if (is_null($class_path)) {
			$this->_includeDirectory($this->getActiondir());
			return;
		}

		// form_path°�������å�
		if (isset($action_obj['form_path'])) {
			if ($action_obj['form_path'] == $class_path) {
				return;
			}
			if (file_exists($action_dir . $action_obj['form_path']) == false) {
				$this->logger->log(LOG_WARNING, 'form_path file not found [%s] -> try default', $action_obj['form_path']);
			} else {
				include_once($action_dir . $action_obj['form_path']);
				$form_path = $action_obj['form_path'];
			}
		}

		// �ǥե���ȥ����å�
		if (is_null($form_path)) {
			$form_path = $this->getDefaultFormPath($action_name);
			if ($form_path == $class_path) {
				return;
			}
			if (file_exists($action_dir . $form_path)) {
				include_once($action_dir . $form_path);
			} else {
				$this->logger->log(LOG_DEBUG, 'default form file not found [%s] -> maybe falling back to default form class', $form_path);
			}
		}
	}

	/**
	 *	�ӥ塼������ץȤ򥤥󥯥롼�ɤ���
	 *
	 *	�����������󥯥롼�ɤ����ե�����˥��饹���������������Ƥ��뤫�ɤ������ݾڤ��ʤ�
	 *
	 *	@access	private
	 *	@param	array	$forward_obj	�������
	 *	@param	string	$forward_name	����̾
	 */
	function _includeViewScript($forward_obj, $forward_name)
	{
		$view_dir = $this->getViewdir();

		// view_path°�������å�
		if (isset($action_obj['view_path'])) {
			if (file_exists($view_dir . $forward_obj['view_path']) == false) {
				$this->logger->log(LOG_WARNING, 'view_path file not found [%s] -> try default', $forward_obj['view_path']);
			} else {
				include_once($action_dir . $forward_obj['view_path']);
				return;
			}
		}

		// �ǥե���ȥ����å�
		$view_path = $this->getDefaultViewPath($forward_name);
		if (file_exists($view_dir . $view_path)) {
			include_once($view_dir . $view_path);
			return;
		} else {
			$this->logger->log(LOG_DEBUG, 'default view file not found [%s]', $view_path);
			$view_path = null;
		}
	}

	/**
	 *	�ǥ��쥯�ȥ�ʲ������ƤΥ�����ץȤ򥤥󥯥롼�ɤ���
	 *
	 *	@access	private
	 */
	function _includeDirectory($dir)
	{
		$ext = "." . $this->ext['php'];
		$ext_len = strlen($ext);

		if (is_dir($dir) == false) {
			return;
		}

		$dh = opendir($dir);
		if ($dh) {
			while (($file = readdir($dh)) !== false) {
				if ($file != '.' && $file != '..' && is_dir("$dir/$file")) {
					$this->_includeDirectory("$dir/$file");
				}
				if (substr($file, -$ext_len, $ext_len) != $ext) {
					continue;
				}
				include_once("$dir/$file");
			}
		}
		closedir($dh);
	}

	/**
	 *  �ƥ�ץ졼�ȥ��󥸥�Υǥե���Ⱦ��֤����ꤹ��
	 *
	 *  @access protected
	 *  @param  object  Smarty  $smarty �ƥ�ץ졼�ȥ��󥸥󥪥֥�������
	 */
	function _setDefaultTemplateEngine(&$smarty)
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
		$forward_obj['view_name'] = 'Ethna_Action_Info';
		$this->forward['__ethna_info__'] = $forward_obj;
	}

	/**
	 *	����̾���б�����ƥ�ץ졼�ȥѥ�̾����ά���줿���Υǥե���ȥѥ�̾���֤�
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

	/**
	 *	����ե������DSN���������Ѥ���ǡ�����ƹ��ۤ���(���졼�֥�������ʬ����)
	 *
	 *	DSN�������ˡ(�ǥե����:����ե�����)���Ѥ��������Ϥ����򥪡��С��饤�ɤ���
	 *
	 *	@access	protected
	 *	@return	array	DSN���
	 */
	function _prepareDSN()
	{
		$r = array();

		foreach ($this->db as $key => $value) {
			$config_key = "dsn";
			if ($key != "") {
				$config_key .= "_$key";
			}
			$dsn = $this->config->get($config_key);
			if (is_array($dsn)) {
				// ����1�ĤˤĤ�ʣ��DSN���������Ƥ�����ϥ�������ʬ��
				$dsn = $this->_selectDSN($key, $dsn);
			}
			$r[$key] = $dsn;
		}
		return $r;
	}

	/**
	 *	DSN�Υ�������ʬ����Ԥ�
	 *	
	 *	���졼�֥����Фؤο�ʬ������(�ǥե����:������)���ѹ����������Ϥ��Υ᥽�åɤ򥪡��С��饤�ɤ���
	 *
	 *	@access	protected
	 *	@param	string	$type		DB����
	 *	@param	array	$dsn_list	DSN����
	 *	@return	string	���򤵤줿DSN
	 */
	function _selectDSN($type, $dsn_list)
	{
		// �ǥե����:������
		list($usec, $sec) = explode(' ', microtime());
		mt_srand($sec + ((float) $usec * 100000));
		$n = mt_rand(0, count($dsn_list)-1);
		
		return $dsn_list[$n];
	}

	/**
	 *	���������̾���¹Ե��Ĥ���Ƥ����Τ��ɤ������֤�
	 *
	 *	@access	private
	 *	@param	string	$action_name			�ꥯ�����Ȥ��줿���������̾
	 *	@param	array	$default_action_name	���Ĥ���Ƥ��륢�������̾
	 *	@return	bool	true:���� false:�Ե���
	 */
	function _isAcceptableActionName($action_name, $default_action_name)
	{
		foreach (to_array($default_action_name) as $name) {
			if ($action_name == $name) {
				return true;
			} else if ($name{strlen($name)-1} == '*') {
				if (strncmp($action_name, substr($name, 0, -1), strlen($name)-1) == 0) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 *	�ե��륿�������������������
	 *
	 *	@access	private
	 */
	function _createFilterChain()
	{
		$this->filter_chain = array();
		foreach ($this->filter as $filter) {
			$file = sprintf("%s/%s.%s", $this->getDirectory('filter'), $filter, $this->getExt('php'));
			if (file_exists($file)) {
				include_once($file);
			}
			if (class_exists($filter)) {
				$this->filter_chain[] =& new $filter($this);
			}
		}
	}
}
// }}}
?>
