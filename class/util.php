<?php
/**
 *	util.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/**
 *	�����Х�桼�ƥ���ƥ��ؿ�: �����顼�ͤ����ǿ�1������Ȥ����֤�
 *
 *	@param	mixed	$v	����Ȥ��ư�����
 *	@return	array	������Ѵ����줿��
 */
function to_array($v)
{
	if (is_array($v)) {
		return $v;
	} else {
		return array($v);
	}
}

/**
 *	�����Х�桼�ƥ���ƥ��ؿ�: ���ꤵ�줿�ե�������ܤ˥��顼�����뤫�ɤ������֤�
 *
 *	@param	string	$name	�ե��������̾
 *	@return	bool	true:���顼ͭ�� false:���顼̵��
 */
function is_error($name)
{
	$c =& $GLOBALS['controller'];

	$action_error =& $c->getActionError();

	return $action_error->isError($name);
}


/**
 *	�桼�ƥ���ƥ����饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_Util
{
	/**
	 *	POST�Υ�ˡ��������å���Ԥ�
	 *
	 *	@access	public
	 *	@return	bool	true:2���ܰʹߤ�POST false:1���ܤ�POST
	 */
	function isDuplicatePost()
	{
		$c =& $GLOBALS['controller'];

		// use raw post data
		if (isset($_POST['uniqid'])) {
			$uniqid = $_POST['uniqid'];
		} else if (isset($_GET['uniqid'])) {
			$uniqid = $_GET['uniqid'];
		} else {
			return false;
		}

		// purge old files
		Etuna_Util::_purgeTmp("uniqid_", 60*60*1);

		$filename = sprintf("%s/uniqid_%s_%s", $c->getDirectory('tmp'), $_SERVER['REMOTE_ADDR'], $uniqid);
		$st = @stat($filename);
		if ($st == false) {
			touch($filename);
			return false;
		}
		if ($st[9] + 60*60*1 < time()) {
			// too old
			return false;
		}

		return true;
	}

	/**
	 *	POST�Υ�ˡ��������å��ե饰�򥯥ꥢ����
	 *
	 *	@access	public
	 *	@return	bool	true:���ｪλ false:���顼
	 */
	function clearDuplicatePost()
	{
		$c =& $GLOBALS['controller'];

		// use raw post data
		if (isset($_POST['uniqid'])) {
			$uniqid = $_POST['uniqid'];
		} else {
			return false;
		}

		$filename = sprintf("%s/uniqid_%s_%s", $c->getDirectory('tmp'), $_SERVER['REMOTE_ADDR'], $uniqid);
		unlink($filename);

		return true;
	}

	/**
	 *	�᡼�륢�ɥ쥹�����������ɤ���������å�����
	 *
	 *	@access	public
	 *	@param	string	$mailaddress	�����å�����᡼�륢�ɥ쥹
	 *	@return	bool	true: �������᡼�륢�ɥ쥹 false: �����ʷ���
	 */
	function isValidMailAddress($mailaddress)
	{
		if (preg_match('/^([a-z0-9_]|\-|\.|\+)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,4}$/i', $mailaddress)) {
			return true;
		}
		return false;
	}

	/**
	 *	CSV���������׽�����Ԥ�
	 *
	 *	@access	public
	 *	@param	string	$csv		�����������оݤ�ʸ����(CSV�γ�����)
	 *	@param	bool	$escape_nl	����ʸ��(\r/\n)�Υ��������ץե饰
	 *	@return	string	CSV���������פ��줿ʸ����
	 */
	function escapeCSV($csv, $escape_nl = false)
	{
		if (preg_match('/[,"\r\n]/', $csv)) {
			if ($escape_nl) {
				$csv = preg_replace('/\r/', "\\r", $csv);
				$csv = preg_replace('/\n/', "\\n", $csv);
			}
			$csv = preg_replace('/"/', "\"\"", $csv);
			$csv = "\"$csv\"";
		}

		return $csv;
	}

	/**
	 *	��������Ǥ�����HTML���������פ����֤�
	 *
	 *	@access	public
	 *	@param	array	$target		HTML�����������оݤȤʤ�����
	 *	@return	array	���������פ��줿����
	 */
	function escapeHtml($target)
	{
		$r = array();
		Ethna_Util::_escapeHtml($target, $r);
		return $r;
	}

	/**
	 *	��������Ǥ�����HTML���������פ����֤�
	 *
	 *	@access	public
	 *	@param	mixed	$vars	HTML�����������оݤȤʤ�����
	 *	@param	mixed	$retval	HTML�����������оݤȤʤ������
	 */
	function _escapeHtml(&$vars, &$retval)
	{
		foreach (array_keys($vars) as $name) {
			if (is_array($vars[$name])) {
				$retval[$name] = array();
				Util::_escapeHtml($vars[$name], $retval[$name]);
			} else {
				$retval[$name] = htmlspecialchars($vars[$name]);
			}
		}
	}

	/**
	 *	Google����󥯥ꥹ�Ȥ��֤�
	 *
	 *	@access	public
	 */
	function getDirectLinkList($total, $offset, $count)
	{
		$direct_link_list = array();

		if ($total == 0) {
			return array();
		}

		// backwards
		$current = $offset - $count;
		while ($current > 0) {
			array_unshift($direct_link_list, $current);
			$current -= $count;
		}
		if ($offset != 0 && $current <= 0) {
			array_unshift($direct_link_list, 0);
		}

		// current
		$backward_count = count($direct_link_list);
		array_push($direct_link_list, $offset);

		// forwards
		$current = $offset + $count;
		for ($i = 0; $i < 10; $i++) {
			if ($current >= $total) {
				break;
			}
			array_push($direct_link_list, $current);
			$current += $count;
		}
		$forward_count = count($direct_link_list) - $backward_count - 1;

		$backward_count -= 4;
		if ($forward_count < 5) {
			$backward_count -= 5 - $forward_count;
		}
		if ($backward_count < 0) {
			$backward_count = 0;
		}

		// add index
		$n = 1;
		$r = array();
		foreach ($direct_link_list as $direct_link) {
			$v = array('offset' => $direct_link, 'index' => $n);
			$r[] = $v;
			$n++;
		}

		return array_splice($r, $backward_count, 10);
	}

	/**
	 *	�������Ǥ�ǯ���֤�
	 *
	 *	@access	public
	 *	@param	int		$t		unix time
	 *	@return	string	����(�����ʾ���null)
	 */
	function getEra($t)
	{
		$tm = localtime($t, true);
		$year = $tm['tm_year'] + 1900;

		if ($year >= 1989) {
			return array('ʿ��', $year - 1988);
		} else if ($year >= 1926) {
			return array('����', $year - 1925);
		}

		return null;
	}

	/**
	 *	getimagesize()���֤����᡼�������פ��б������ĥ�Ҥ��֤�
	 *
	 *	@access	public
	 *	@param	int		$type	getimagesize()�ؿ����֤����᡼��������
	 *	@return	string	$type���б������ĥ��
	 */
	function getImageExtName($type)
	{
		$ext_list = array(
			1	=> 'gif',
			2	=> 'jpg',
			3	=> 'png',
			4	=> 'swf',
			5	=> 'psd',
			6	=> 'bmp',
			7	=> 'tiff',
			8	=> 'tiff',
			9	=> 'jpc',
			10	=> 'jp2',
			11	=> 'jpx',
			12	=> 'jb2',
			13	=> 'swc',
			14	=> 'iff',
			15	=> 'wbmp',
			16	=> 'xbm',
		);

		return @$ext_list[$type];
	}

	/**
	 *	������ʥϥå����ͤ���������
	 *
	 *	@access	public
	 *	@param	int		$length	�ϥå����ͤ�Ĺ��(��64)
	 *	@return	string	�ϥå�����
	 *	@todo	Linux�ʳ��δĶ��б�
	 */
	function getRandom($length = 64)
	{
		$value = "";
		for ($i = 0; $i < 2; $i++) {
			$rx = $tx = 0;
			$fp = fopen('/proc/net/dev', 'r');
			if ($fp != null) {
				$header = true;
				while (feof($fp) === false) {
					$s = fgets($fp, 4096);
					if ($header) {
						$header = false;
						continue;
					}
					$v = preg_split('/[:\s]+/', $s);
					if (is_array($v) && count($v) > 10) {
						$rx += $v[2];
						$tx += $v[10];
					}
				}
			}
			$now = strftime('%Y%m%d %T');
			$time = gettimeofday();
			$v = $now . $time['usec'] . $rx . $tx . rand(0, time());
			$value .= md5($v);
		}

		if ($length < 64) {
			$value = substr($value, 0, $length);
		}
		return $value;
	}

	/**
	 *	1���������m x n�˺ƹ�������
	 *
	 *	@access	public
	 *	@param	array	$array	�����оݤ�1��������
	 *	@param	int		$m		�������ǿ�
	 *	@param	int		$order	$m��X���ȸ�������Y���ȸ�������(0:X�� 1:Y��)
	 *	@return	array	m x n�˺ƹ������줿����
	 */
	function get2dArray($array, $m, $order)
	{
		$r = array();
		
		$n = intval(count($array) / $m);
		if ((count($array) % $m) > 0) {
			$n++;
		}
		for ($i = 0; $i < $n; $i++) {
			$elts = array();
			for ($j = 0; $j < $m; $j++) {
				if ($order == 0) {
					/* ���¤� */
					$key = $i*$m+$j;
				} else {
					/* ���¤� */
					$key = $i+$n*$j;
				}
				if (array_key_exists($key, $array) == false) {
					$array[$key] = null;
				}
				$elts[] = $array[$key];
			}
			$r[] = $elts;
		}

		return $r;
	}

	/**
	 *	�ƥ�ݥ��ǥ��쥯�ȥ�Υե������������
	 *
	 *	@access	public
	 *	@param	string	$prefix		�ե�����Υץ�ե�����
	 *	@param	int		$timeout	����о�����(�á�60*60*1�ʤ�1����)
	 */
	function purgeTmp($prefix, $timeout)
	{
		$c =& $GLOBALS['controller'];

		$dh = opendir($c->getDirectory('tmp'));
		if ($dh) {
			while (($file = readdir($dh)) !== false) {
				if (strncmp($file, $prefix, strlen($prefix)) == 0) {
					$f = $c->getDirectory('tmp') . "/" . $file;
					$st = @stat($f);
					if ($st[9] + $timeout < time()) {
						unlink($f);
					}
				}
			}
			closedir($dh);
		}
	}

	/**
	 *	�ե�������å�����
	 *
	 *	@access	public
	 *	@param	string	$file		��å�����ե�����̾
	 *	@param	int		$mode		��å��⡼��(LOCK_SH, LOCK_EX)
	 *	@param	int		$timeout	��å��Ԥ������ॢ����(�á�0�ʤ�̵��)
	 *	@return	int		��å��ϥ�ɥ�(false�ʤ饨�顼)
	 */
	function lockFile($file, $mode, $timeout = 0)
	{
		$lh = @fopen($file, 'r');
		if ($lh == null) {
			return false;
		}

		$lock_mode = $mode == 'r' ? LOCK_SH : LOCK_EX;

		for ($i = 0; $i < $timeout || $timeout == 0; $i++) {
			$r = flock($lh, $lock_mode | LOCK_NB);
			if ($r == true) {
				break;
			}
			sleep(1);
		}
		if ($timeout > 0 && $i == $timeout) {
			// timed out
			return false;
		}
		@unlink($lock);

		return $lh;
	}

	/**
	 *	�ե�����Υ�å���������
	 *
	 *	@access	public
	 *	@param	int		$lh		��å��ϥ�ɥ�
	 */
	function unlockFile($lh)
	{
		fclose($lh);
	}

	/**
	 *	�Хå��ȥ졼����ե����ޥåȤ����֤�
	 *
	 *	@access	public
	 *	@param	array	$bt		debug_backtrace()�ؿ��Ǽ��������Хå��ȥ졼��
	 *	@return	string	ʸ����˥ե����ޥåȤ��줿�Хå��ȥ졼��
	 */
	function formatBacktrace($bt) 
	{
		$r = "";
		$i = 0;
		foreach ($bt as $elt) {
			$r .= sprintf("[%02d] %s:%d:%s.%s\n", $i, $elt['file'], $elt['line'], isset($elt['class']) ? $elt['class'] : 'global', $elt['function']);
			$i++;

			if (isset($elt['args']) == false || is_array($elt['args']) == false) {
				continue;
			}

			/* �����Υ���� */
			foreach ($elt['args'] as $arg) {
				$r .= Ethna_Util::_formatBacktrace($arg);
			}
		}

		return $r;
	}

	/**
	 *	�Хå��ȥ졼��������ե����ޥåȤ����֤�
	 *
	 *	@access	private
	 *	@param	string	$arg	�Хå��ȥ졼���ΰ���
	 *	@param	int		$level	�Хå��ȥ졼���Υͥ��ȥ�٥�
	 *	@return	string	ʸ����˥ե����ޥåȤ��줿�Хå��ȥ졼��
	 */
	function _formatBacktrace($arg, $level = 0)
	{
		$pad = str_repeat("  ", $level);
		if (is_array($arg)) {
			$r = sprintf("     %s[array] => (\n", $pad);
			if ($level+1 > 4) {
				$r .= sprintf("     %s  *too deep*\n", $pad);
			} else {
				foreach ($arg as $elt) {
					$r .= Ethna_Util::_formatBacktrace($elt, $level+1);
				}
			}
			$r .= sprintf("     %s)\n", $pad);
		} else if (is_object($arg)) {
			$r = sprintf("     %s[object]%s\n", $pad, get_class($arg));
		} else {
			$r = sprintf("     %s[%s]%s\n", $pad, gettype($arg), $arg);
		}

		return $r;
	}
}
?>
