<?php
// vim: foldmethod=marker
/**
 *	Ethna_LogWriter.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// {{{ Ethna_LogWriter
/**
 *	$B%m%0=PNO4pDl%/%i%9(B
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
	 *	@var	string	$B%m%0%"%$%G%s%F%#%F%#J8;zNs(B
	 */
	var	$ident;

	/**
	 *	@var	int		$B%m%0%U%!%7%j%F%#(B
	 */
	var	$facility;

	/**
	 *	@var	int		$B%m%0%*%W%7%g%s(B
	 */
	var	$option;

	/**
	 *	@var	string	$B%m%0%U%!%$%k(B
	 */
	var	$file;

	/**
	 *	@var	bool	$B%P%C%/%H%l!<%9$,<hF@2DG=$+$I$&$+(B
	 */
	var	$have_backtrace;

	/**#@-*/

	/**
	 *	@var	string	$B%m%0%l%Y%kL>%F!<%V%k(B
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
	 *	Ethna_LogWriter$B%/%i%9$N%3%s%9%H%i%/%?(B
	 *
	 *	@access	public
	 *	@param	string	$log_ident		$B%m%0%"%$%G%s%F%#%F%#J8;zNs(B($B%W%m%;%9L>Ey(B)
	 *	@param	int		$log_facility	$B%m%0%U%!%7%j%F%#(B
	 *	@param	string	$log_file		$B%m%0=PNO@h%U%!%$%kL>(B(LOG_FILE$B%*%W%7%g%s$,;XDj$5$l$F$$$k>l9g$N$_(B)
	 *	@param	int		$log_option		$B%m%0%*%W%7%g%s(B(LOG_FILE,LOG_FUNCTION...)
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
	 *	$B%m%0=PNO$r3+;O$9$k(B
	 *
	 *	@access	public
	 */
	function begin()
	{
	}

	/**
	 *	$B%m%0$r=PNO$9$k(B
	 *
	 *	@access	public
	 *	@param	int		$level		$B%m%0%l%Y%k(B(LOG_DEBUG, LOG_NOTICE...)
	 *	@param	string	$message	$B%m%0%a%C%;!<%8(B(+$B0z?t(B)
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
	 *	$B%m%0=PNO$r=*N;$9$k(B
	 *
	 *	@access	public
	 */
	function end()
	{
	}

	/**
	 *	$B%m%0%"%$%G%s%F%#%F%#J8;zNs$r<hF@$9$k(B
	 *
	 *	@access	public
	 *	@return	string	$B%m%0%"%$%G%s%F%#%F%#J8;zNs(B
	 */
	function getIdent()
	{
		return $this->ident;
	}

	/**
	 *	$B%m%0%l%Y%k$rI=<(J8;zNs$KJQ49$9$k(B
	 *
	 *	@access	private
	 *	@param	int		$level	$B%m%0%l%Y%k(B(LOG_DEBUG,LOG_NOTICE...)
	 *	@return	string	$B%m%0%l%Y%kI=<(J8;zNs(B(LOG_DEBUG$B"*(B"DEBUG")
	 */
	function _getLogLevelName($level)
	{
		if (isset($this->level_name_table[$level]) == false) {
			return null;
		}
		return $this->level_name_table[$level];
	}

	/**
	 *	$B%m%0=PNO85$N4X?t$r<hF@$9$k(B
	 *
	 *	@access	private
	 *	@return	string	$B%m%0=PNO85%/%i%9(B/$B%a%=%C%IL>(B("class.method")
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

			// $B%a%=%C%I%9%-%C%W=hM}(B
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
?>
