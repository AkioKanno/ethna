<?php
/**
 *	config.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/**
 *	���ꥯ�饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_Config
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	object	Ethna_Controller	controller���֥�������
	 */
	var	$controller;
	
	/**
	 *	@var	array	��������
	 */
	var	$config = null;

	/**#@-*/


	/**
	 *	Ethna_Config���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	object	Ethna_Controller	&$controller	controller���֥�������
	 */
	function Ethna_Config(&$controller)
	{
		$this->controller =& $controller;

		// ����ե�������ɤ߹���
		if ($this->_getConfig() == false) {
			// ���λ����Ǥ�logging���Ͻ���ʤ�
			$fp = fopen("php://stderr", "r");
			fputs($fp, sprintf("error occured while reading config file(s)\n"));
			fclose($fp);
			$this->controller->fatal();
		}
	}

	/**
	 *	�����ͤؤΥ�������(R)
	 *
	 *	@access	public
	 *	@param	string	$key	�������̾
	 *	@return	string	������
	 */
	function get($key)
	{
		if (isset($this->config[$key]) == false) {
			return null;
		}
		return $this->config[$key];
	}

	/**
	 *	�����ͤؤΥ�������(W)
	 *
	 *	@access	public
	 *	@param	string	$key	�������̾
	 *	@param	string	$value	������
	 */
	function set($key, $value)
	{
		$this->config[$key] = $value;
	}

	/**
	 *	����ե�����򹹿�����
	 *
	 *	@access	public
	 *	@return	bool	true:���ｪλ false:���顼
	 */
	function update()
	{
		return $this->_setConfig();
	}

	/**
	 *	����ե�������ɤ߹���
	 *
	 *	@access	private
	 *	@return	bool	true:���ｪλ false:���顼
	 */
	function _getConfig()
	{
		$config = array();
		$file = $this->_getConfigFile();
		if (file_exists($file)) {
			$lh = Ethna_Util::lockFile($file, 'r');
			if ($lh == false) {
				return false;
			}

			include($file);

			Ethna_Util::unlockFile($lh);
		}

		// �ǥե����������
		if (isset($config['url']) == false) {
			$config['url'] = sprintf("http://%s", $_SERVER['HTTP_HOST']);
		}
		if (isset($config['dsn']) == false) {
			$config['dsn'] = "";
		}
		if (isset($config['log_facility']) == false) {
			$config['log_facility'] = "";
		}
		if (isset($config['log_level']) == false) {
			$config['log_level'] = "";
		}
		if (isset($config['log_option']) == false) {
			$config['log_option'] = "";
		}

		$this->config = $config;

		return true;
	}

	/**
	 *	����ե�����˽񤭹���
	 *
	 *	@access	private
	 *	@return	bool	true:���ｪλ false:���顼
	 */
	function _setConfig()
	{
		$file = $this->_getConfigFile();

		$lh = Ethna_Util::lockFile($file, 'w');
		if ($lh == false) {
			return false;
		}

		$fp = fopen($file, 'w');
		if ($fp == null) {
			return false;
		}
		fwrite($fp, "<?php\n");
		fwrite($fp, sprintf("/*\n * %s\n *\n * update: %s\n */\n", basename($file), strftime('%Y/%m/%d %H:%M:%S')));
		fwrite($fp, "\$config = array(\n");
		foreach ($this->config as $key => $value) {
			fputs($fp, "\t'$key' => '$value',\n");
		}
		fwrite($fp, ");\n?>\n");
		fclose($fp);

		Ethna_Util::unlockFile($lh);

		return true;
	}

	/**
	 *	����ե�����̾���������
	 *
	 *	@access	private
	 *	@return	string	����ե�����ؤΥե�ѥ�̾
	 */
	function _getConfigFile()
	{
		return $this->controller->getDirectory('etc') . '/' . strtolower($this->controller->getAppId()) . '-ini.php';
	}
}
?>
