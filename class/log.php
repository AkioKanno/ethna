<?php
// vim: foldmethod=marker
/**
 *	log.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/**
 *	��ĥ���ץ�ѥƥ�:	�ե��������
 */
define('LOG_FILE', 1 << 16);

/**
 *	��ĥ���ץ�ѥƥ�:	�ؿ�̾ɽ��
 */
define('LOG_FUNCTION', 1 << 17);


// {{{ ethna_error_handler
/**
 *	���顼������Хå��ؿ�
 *
 *	@param	int		$errno		���顼��٥�
 *	@param	string	$errstr		���顼��å�����
 *	@param	string	$errfile	���顼ȯ���ս�Υե�����̾
 *	@param	string	$errline	���顼ȯ���ս�ι��ֹ�
 */
function ethna_error_handler($errno, $errstr, $errfile, $errline)
{
	$c =& $GLOBALS['controller'];

	list($level, $name) = Ethna_Logger::errorLevelToLogLevel($errno);
	if ($errno == E_STRICT) {
		// E_STRICT��ɽ�����ʤ�
		return E_STRICT;
	}

	$logger =& $c->getLogger();
	$logger->log($level, sprintf("[PHP] %s: %s in %s on line %d", $code, $errstr, $errfile, $errline));
}
// }}}

// {{{ Ethna_Logger
/**
 *	���������饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_Logger extends Ethna_AppManager
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	array	���ե�����ƥ�����
	 */
	var $log_facility_list = array(
		'auth' => array('name' => 'LOG_AUTH'),
		'authpriv' => array('name' => 'LOG_AUTHPRIV'),
		'cron' => array('name' => 'LOG_CRON'),
		'daemon' => array('name' => 'LOG_DAEMON'),
		'kern' => array('name' => 'LOG_KERN'),
		'local0' => array('name' => 'LOG_LOCAL0'),
		'local1' => array('name' => 'LOG_LOCAL1'),
		'local2' => array('name' => 'LOG_LOCAL2'),
		'local3' => array('name' => 'LOG_LOCAL3'),
		'local4' => array('name' => 'LOG_LOCAL4'),
		'local5' => array('name' => 'LOG_LOCAL5'),
		'local6' => array('name' => 'LOG_LOCAL6'),
		'local7' => array('name' => 'LOG_LOCAL7'),
		'lpr' => array('name' => 'LOG_LPR'),
		'mail' => array('name' => 'LOG_MAIL'),
		'news' => array('name' => 'LOG_NEWS'),
		'syslog' => array('name' => 'LOG_SYSLOG'),
		'user' => array('name' => 'LOG_USER'),
		'uucp' => array('name' => 'LOG_UUCP'),
		'file' => array('name' => 'LOG_FILE'),
	);

	/**
	 *	@var	array	����٥����
	 */
	var $log_level_list = array(
		'emerg' => array('name' => 'LOG_EMERG'),
		'alert' => array('name' => 'LOG_ALERT'),
		'crit' => array('name' => 'LOG_CRIT'),
		'err' => array('name' => 'LOG_ERR'),
		'warning' => array('name' => 'LOG_WARNING'),
		'notice' => array('name' => 'LOG_NOTICE'),
		'info' => array('name' => 'LOG_INFO'),
		'debug' => array('name' => 'LOG_DEBUG'),
	);

	/**
	 *	@var	array	�����ץ�������
	 */
	var $log_option_list = array(
		'pid' => array('name' => 'PIDɽ��', 'value' => LOG_PID),
		'function' => array('name' => '�ؿ�̾ɽ��', 'value' => LOG_FUNCTION),
	);

	/**
	 *	@var	array	����٥�ơ��֥�
	 */
	var $level_table = array(
		LOG_EMERG	=> 7,
		LOG_ALERT	=> 6,
		LOG_CRIT	=> 5,
		LOG_ERR		=> 4,
		LOG_WARNING	=> 3,
		LOG_NOTICE	=> 2,
		LOG_INFO	=> 1,
		LOG_DEBUG	=> 0,
	);

	/**
	 *	@var	object	Ethna_Controller	controller���֥�������
	 */
	var	$controller;

	/**
	 *	@var	int		����٥�
	 */
	var $level;

	/**
	 *	@var	int		���顼�ȥ�٥�
	 */
	var $alert_level;

	/**
	 *	@var	string	���顼�ȥ᡼�륢�ɥ쥹
	 */
	var $alert_mailaddress;

	/**
	 *	@var	string	��å������ե��륿(����)
	 */
	var $message_filter_do;

	/**
	 *	@var	string	��å������ե��륿(̵��)
	 */
	var $message_filter_ignore;

	/**
	 *	@var	object	Ethna_LogWriter	�����ϥ��֥�������
	 */
	var	$writer;

	/**#@-*/
	
	/**
	 *	Ethna_Logger���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	object	Ethna_Controller	$controller	controller���֥�������
	 */
	function Ethna_Logger(&$controller)
	{
		$this->controller =& $controller;
		$config =& $controller->getConfig();
		
		// ������μ���
		$this->level = $this->_parseLogLevel($config->get('log_level'));
		if (is_null($this->level)) {
			// ̤����ʤ�LOG_WARNING
			$this->level = LOG_WARNING;
		}
		$facility = $this->_parseLogFacility($config->get('log_facility'));
		$file = sprintf('%s/%s.log', $controller->getDirectory('log'), strtolower($controller->getAppid()));
		list($this->alert_mailaddress, $this->alert_level, $option) = $this->_parseLogOption($config->get('log_option'));
		$this->message_filter_do = $config->get('log_filter_do');
		$this->message_filter_ignore = $config->get('log_filter_ignore');

		if ($facility == LOG_FILE) {
			$writer_class = "Ethna_LogWriter_File";
		} else if (is_null($facility)) {
			$writer_class = "Ethna_LogWriter";
		} else {
			$writer_class = "Ethna_LogWriter_Syslog";
		}
		$this->writer =& new $writer_class($controller->getAppId(), $facility, $file, $option);

		set_error_handler("ethna_error_handler");
	}

	/**
	 *	PHP���顼��٥�����٥���Ѵ�����
	 *
	 *	@access	public
	 *	@param	int		$errno	PHP���顼��٥�
	 *	@return	array	����٥�(LOG_NOTICE,...), ���顼��٥�ɽ��̾("E_NOTICE"...)
	 *	@static
	 */
	function errorLevelToLogLevel($errno)
	{
		switch ($errno) {
		case E_ERROR:			$code = "E_ERROR"; $level = LOG_ERR; break;
		case E_WARNING:			$code = "E_WARNING"; $level = LOG_WARNING; break;
		case E_PARSE:			$code = "E_PARSE"; $level = LOG_CRIT; break;
		case E_NOTICE:			$code = "E_NOTICE"; $level = LOG_NOTICE; break;
		case E_USER_ERROR:		$code = "E_USER_ERROR"; $level = LOG_ERR; break;
		case E_USER_WARNING:	$code = "E_USER_WARNING"; $level = LOG_WARNING; break;
		case E_USER_NOTICE:		$code = "E_USER_NOTICE"; $level = LOG_NOTICE; break;
		case E_STRICT:			$code = "E_STRING"; $level = LOG_NOTICE; return;
		default:				$code = "E_UNKNOWN"; $level = LOG_DEBUG; break;
		}
		return array($level, $code);
	}

	/**
	 *	�����Ϥ򳫻Ϥ���
	 *
	 *	@access	public
	 */
	function begin()
	{
		$this->writer->begin();
	}

	/**
	 *	������Ϥ���
	 *
	 *	@access	public
	 *	@param	int		$level		����٥�(LOG_DEBUG, LOG_NOTICE...)
	 *	@param	string	$message	����å�����(+����)
	 */
	function log($level, $message)
	{
		// ����å������ե��륿(��٥�ե��륿��ͥ�褹��)
		$r = $this->_isMessageMask($message);
		if ($r === false) {
			return;
		}

		// ����٥�ե��륿
		if ($r !== true && $this->_isLevelMask($this->level, $level)) {
			return;
		}

		// ������
		$args = func_get_args();
		if (count($args) > 2) {
			array_splice($args, 0, 2);
			$message = vsprintf($message, $args);
		}
		$output = $this->writer->log($level, $message);

		// ���顼�Ƚ���
		if ($this->_isLevelMask($this->alert_level, $level) == false) {
			if ($this->alert_mailaddress) {
				$this->_alertMail($output);
			}
			$this->_alert($output);
		}
	}

	/**
	 *	�����Ϥ�λ����
	 *
	 *	@access	public
	 */
	function end()
	{
		$this->writer->end();
	}

	/**
	 *	�����ץ����(����ե�������)����Ϥ���
	 *
	 *	@access	private
	 *	@param	string	$string	�����ץ����(����ե�������)
	 *	@return	array	���Ϥ��줿����ե�������(���顼�����Υ᡼�륢�ɥ쥹, ���顼���оݥ���٥�, �����ץ����)
	 */
	function _parseLogOption($string)
	{
		$alert_mailaddress = null;
		$alert_level = null;
		$option = null;
		$elts = explode(',', $string);
		foreach ($elts as $elt) {
			if (strncmp($elt, 'alert_mailaddress:', 18) == 0) {
				$alert_mailaddress = substr($elt, 18);
			} else if (strncmp($elt, 'alert_level:', 12) == 0) {
				$alert_level = $this->_ParseLogLevel(substr($elt, 12));
			} else if ($elt == 'pid') {
				$option |= LOG_PID;
			} else if ($elt == 'function') {
				$option |= LOG_FUNCTION;
			}
		}

		return array($alert_mailaddress, $alert_level, $option);
	}

	/**
	 *	���顼�Ƚ���(DB��³���顼�������η�³������ʥ��顼ȯ��)��Ԥ�
	 *
	 *	@access	protected
	 *	@param	$message	����å�����
	 */
	function _alert($message)
	{
		$this->controller->fatal();
	}

	/**
	 *	���顼�ȥ᡼�����������
	 *
	 *	@access	protected
	 *	@param	string	$message	����å�����
	 *	@return	int		0:���ｪλ
	 */
	function _alertMail($message)
	{
		restore_error_handler();

		// �إå�
		$header = "Mime-Version: 1.0\n";
		$header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
		$header .= "X-Alert: " . $this->writer->getIdent();
		$subject = sprintf("[%s] alert (%s%s)\n", $this->writer->getIdent(), substr($message, 0, 12), strlen($message) > 12 ? "..." : "");
		
		// ��ʸ
		$mail = sprintf("--- [log message] ---\n%s\n\n", $message);
		if (function_exists("debug_backtrace")) {
			$bt = debug_backtrace();
			$mail .= sprintf("--- [backtrace] ---\n%s\n", Util::FormatBacktrace($bt));
		}

		mail($this->alert_mailaddress, $subject, mb_convert_encoding($mail, "ISO-2022-JP", "EUC-JP"), $header);

		set_error_handler("ethna_error_handler");

		return 0;
	}

	/**
	 *	����å������Υޥ��������å���Ԥ�
	 *
	 *	@access	private
	 *	@param	string	$message	����å�����
	 *	@return	mixed	true:�������� false:����̵�� null:�����å�
	 */
	function _isMessageMask($message)
	{
		$regexp_do = sprintf("/%s/", $this->message_filter_do);
		$regexp_ignore = sprintf("/%s/", $this->message_filter_ignore);

		if ($this->message_filter_do && preg_match($regexp_do, $message)) {
			return true;
		}
		if ($this->message_filter_ignore && preg_match($regexp_ignore, $message)) {
			return false;
		}
		return null;
	}

	/**
	 *	����٥�Υޥ��������å���Ԥ�
	 *
	 *	@access	private
	 *	@param	int		$src	����٥�ޥ���
	 *	@param	int		$dst	����٥�
	 *	@return	bool	true:���Ͱʲ� false:���Ͱʾ�
	 */
	function _isLevelMask($src, $dst)
	{
		// �Τ�ʤ���٥�ʤ���Ϥ��ʤ�
		if (isset($this->level_table[$src]) == false || isset($this->level_table[$dst]) == false) {
			return true;
		}

		if ($this->level_table[$dst] >= $this->level_table[$src]) {
			return false;
		}

		return true;
	}

	/**
	 *	���ե�����ƥ�(����ե�������)����Ϥ���
	 *
	 *	@access	private
	 *	@param	string	$facility	���ե�����ƥ�(����ե�������)
	 *	@return	int		���ե�����ƥ�(LOG_LOCAL0, LOG_FILE...)
	 */
	function _parseLogFacility($facility)
	{
		$facility_map_table = array(
			'auth'		=> LOG_AUTH,
			'authpriv'	=> LOG_AUTHPRIV,
			'cron'		=> LOG_CRON,
			'daemon'	=> LOG_DAEMON,
			'kern'		=> LOG_KERN,
			'local0'	=> LOG_LOCAL0,
			'local1'	=> LOG_LOCAL1,
			'local2'	=> LOG_LOCAL2,
			'local3'	=> LOG_LOCAL3,
			'local4'	=> LOG_LOCAL4,
			'local5'	=> LOG_LOCAL5,
			'local6'	=> LOG_LOCAL6,
			'local7'	=> LOG_LOCAL7,
			'lpr'		=> LOG_LPR,
			'mail'		=> LOG_MAIL,
			'news'		=> LOG_NEWS,
			'syslog'	=> LOG_SYSLOG,
			'user'		=> LOG_USER,
			'uucp'		=> LOG_UUCP,
			'file'		=> LOG_FILE,
		);
		if (isset($facility_map_table[strtolower($facility)]) == false) {
			return null;
		}
		return $facility_map_table[strtolower($facility)];
	}

	/**
	 *	����٥�(����ե�������)����Ϥ���
	 *
	 *	@access	private
	 *	@param	string	$level	����٥�(����ե�������)
	 *	@return	int		����٥�(LOG_DEBUG, LOG_NOTICE...)
	 */
	function _parseLogLevel($level)
	{
		$level_map_table = array(
			'emerg'		=> LOG_EMERG,
			'alert'		=> LOG_ALERT,
			'crit'		=> LOG_CRIT,
			'err'		=> LOG_ERR,
			'warning'	=> LOG_WARNING,
			'notice'	=> LOG_NOTICE,
			'info'		=> LOG_INFO,
			'debug'		=> LOG_DEBUG,
		);
		if (isset($level_map_table[strtolower($level)]) == false) {
			return null;
		}
		return $level_map_table[strtolower($level)];
	}
}
// }}}

// {{{ Ethna_LogWriter
/**
 *	�����ϴ��쥯�饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_LogWriter
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	string	�������ǥ�ƥ��ƥ�ʸ����
	 */
	var	$ident;

	/**
	 *	@var	int		���ե�����ƥ�
	 */
	var	$facility;

	/**
	 *	@var	int		�����ץ����
	 */
	var	$option;

	/**
	 *	@var	string	���ե�����
	 */
	var	$file;

	/**
	 *	@var	bool	�Хå��ȥ졼����������ǽ���ɤ���
	 */
	var	$have_backtrace;

	/**#@-*/

	/**
	 *	@var	string	����٥�̾�ơ��֥�
	 */
	var	$level_name_table = array(
		LOG_EMERG	=> 'EMERG',
		LOG_ALERT	=> 'ALERT',
		LOG_CRIT	=> 'CRIT',
		LOG_ERR		=> 'ERR',
		LOG_WARNING	=> 'WARNING',
		LOG_NOTICE	=> 'NOTICE',
		LOG_INFO	=> 'INFO',
		LOG_DEBUG	=> 'DEBUG',
	);

	/**
	 *	Ethna_LogWriter���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	string	$log_ident		�������ǥ�ƥ��ƥ�ʸ����(�ץ���̾��)
	 *	@param	int		$log_facility	���ե�����ƥ�
	 *	@param	string	$log_file		��������ե�����̾(LOG_FILE���ץ���󤬻��ꤵ��Ƥ�����Τ�)
	 *	@param	int		$log_option		�����ץ����(LOG_FILE,LOG_FUNCTION...)
	 */
	function Ethna_LogWriter($log_ident, $log_facility, $log_file, $log_option)
	{
		$this->ident = $log_ident;
		$this->facility = $log_facility;
		$this->option = $log_option;
		$this->file = $log_file;
		$this->have_backtrace = function_exists('debug_backtrace');
	}

	/**
	 *	�����Ϥ򳫻Ϥ���
	 *
	 *	@access	public
	 */
	function begin()
	{
	}

	/**
	 *	������Ϥ���
	 *
	 *	@access	public
	 *	@param	int		$level		����٥�(LOG_DEBUG, LOG_NOTICE...)
	 *	@param	string	$message	����å�����(+����)
	 */
	function log($level, $message)
	{
		$prefix = strftime('%Y/%m/%d %H:%M:%S ') . $this->ident;
		if ($this->option & LOG_PID) {
			$prefix .= sprintf('[%d]', getmypid());
		}
		$prefix .= sprintf('(%s): ', $this->_getLogLevelName($level));
		if ($this->option & LOG_FUNCTION) {
			$function = $this->_getFunctionName();
			if ($function) {
				$prefix .= sprintf('%s: ', $function);
			}
		}
		printf($prefix . $message . "\n");

		return $prefix . $message;
	}

	/**
	 *	�����Ϥ�λ����
	 *
	 *	@access	public
	 */
	function end()
	{
	}

	/**
	 *	�������ǥ�ƥ��ƥ�ʸ������������
	 *
	 *	@access	public
	 *	@return	string	�������ǥ�ƥ��ƥ�ʸ����
	 */
	function getIdent()
	{
		return $this->ident;
	}

	/**
	 *	����٥��ɽ��ʸ������Ѵ�����
	 *
	 *	@access	private
	 *	@param	int		$level	����٥�(LOG_DEBUG,LOG_NOTICE...)
	 *	@return	string	����٥�ɽ��ʸ����(LOG_DEBUG��"DEBUG")
	 */
	function _getLogLevelName($level)
	{
		if (isset($this->level_name_table[$level]) == false) {
			return null;
		}
		return $this->level_name_table[$level];
	}

	/**
	 *	�����ϸ��δؿ����������
	 *
	 *	@access	private
	 *	@return	string	�����ϸ����饹/�᥽�å�̾("class.method")
	 */
	function _getFunctionName()
	{
		$skip_method_list = array(
			array('ethna', 'raise*'),
			array('ethna_logger', null),
			array('ethna_logwriter_*', null),
			array('ethna_error', null),
			array('ethna_apperror', null),
			array('ethna_actionerror', null),
			array('ethna_backend', 'log'),
			array(null, 'ethna_error_handler'),
			array(null, 'trigger_error'),
		);

		if ($this->have_backtrace == false) {
			return null;
		}

		$bt = debug_backtrace();
		$i = 0;
		while ($i < count($bt)) {
			if (isset($bt[$i]['class']) == false) {
				$bt[$i]['class'] = null;
			}
			$skip = false;

			// �᥽�åɥ����å׽���
			foreach ($skip_method_list as $method) {
				$class = $function = true;
				if ($method[0] != null) {
					if (preg_match('/\*$/', $method[0])) {
						$n = strncasecmp($bt[$i]['class'], $method[0], strlen($method[0])-1);
					} else {
						$n = strcasecmp($bt[$i]['class'], $method[0]);
					}
					$class = $n == 0 ? true : false;
				}
				if ($method[1] != null) {
					if (preg_match('/\*$/', $method[1])) {
						$n = strncasecmp($bt[$i]['function'], $method[1], strlen($method[1])-1);
					} else {
						$n = strcasecmp($bt[$i]['function'], $method[1]);
					}
					$function = $n == 0 ? true : false;
				}
				if ($class && $function) {
					$skip = true;
					break;
				}
			}

			if ($skip) {
				$i++;
			} else {
				break;
			}
		}

		return sprintf("%s.%s", isset($bt[$i]['class']) ? $bt[$i]['class'] : 'global', $bt[$i]['function']);
	}
}
// }}}

// {{{ Ethna_LogWriter_File
/**
 *	�����ϥ��饹(File)
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_LogWriter_File extends Ethna_LogWriter
{
	/**#@+
	 *	@access	private
	 */

	/**
	 *	@var	int		���ե�����ϥ�ɥ�
	 */
	var	$fp;

	/**#@-*/

	/**
	 *	Ethna_LogWriter_File���饹�Υ��󥹥ȥ饯��
	 *
	 *	@access	public
	 *	@param	string	$log_ident		�������ǥ�ƥ��ƥ�ʸ����(�ץ���̾��)
	 *	@param	int		$log_facility	���ե�����ƥ�
	 *	@param	string	$log_file		��������ե�����̾(LOG_FILE���ץ���󤬻��ꤵ��Ƥ�����Τ�)
	 *	@param	int		$log_option		�����ץ����(LOG_FILE,LOG_FUNCTION...)
	 */
	function Ethna_LogWriter_File($log_ident, $log_facility, $log_file, $log_option)
	{
		parent::Ethna_LogWriter($log_ident, $log_facility, $log_file, $log_option);
		$this->fp = null;
	}

	/**
	 *	�����Ϥ򳫻Ϥ���
	 *
	 *	@access	public
	 */
	function begin()
	{
		$this->fp = fopen($this->file, 'a');
	}

	/**
	 *	������Ϥ���
	 *
	 *	@access	public
	 *	@param	int		$level		����٥�(LOG_DEBUG, LOG_NOTICE...)
	 *	@param	string	$message	����å�����(+����)
	 */
	function log($level, $message)
	{
		if ($this->fp == null) {
			return;
		}

		$prefix = strftime('%Y/%m/%d %H:%M:%S ') . $this->ident;
		if ($this->option & LOG_PID) {
			$prefix .= sprintf('[%d]', getmypid());
		}
		$prefix .= sprintf('(%s): ', $this->_getLogLevelName($level));
		if ($this->option & LOG_FUNCTION) {
			$function = $this->_getFunctionName();
			if ($function) {
				$prefix .= sprintf('%s: ', $function);
			}
		}
		fwrite($this->fp, $prefix . $message . "\n");

		return $prefix . $message;
	}

	/**
	 *	�����Ϥ�λ����
	 *
	 *	@access	public
	 */
	function end()
	{
		if ($this->fp) {
			fclose($this->fp);
			$this->fp = null;
		}
	}
}
// }}}

// {{{ Ethna_LogWriter_Syslog
/**
 *	�����ϥ��饹(Syslog)
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_LogWriter_Syslog extends Ethna_LogWriter
{
	/**
	 *	�����Ϥ򳫻Ϥ���
	 *
	 *	@access	public
	 */
	function begin()
	{
		// syslog�ѥ��ץ����Τߤ����
		$option = $this->option & (LOG_PID);

		openlog($this->ident, $option, $this->facility);
	}

	/**
	 *	������Ϥ���
	 *
	 *	@access	public
	 *	@param	int		$level		����٥�(LOG_DEBUG, LOG_NOTICE...)
	 *	@param	string	$message	����å�����(+����)
	 */
	function log($level, $message)
	{
		$prefix = sprintf('%s: ', $this->_getLogLevelName($level));
		if ($this->option & LOG_FUNCTION) {
			$function = $this->_getFunctionName();
			if ($function) {
				$prefix .= sprintf('%s: ', $function);
			}
		}
		syslog($level, $prefix . $message);

		return $prefix . $message;
	}

	/**
	 *	�����Ϥ�λ����
	 *
	 *	@access	public
	 */
	function end()
	{
		closelog();
	}
}
// }}}
?>
