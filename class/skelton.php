<?php
/**
 *	skelton.php
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

/**
 *	������ȥ��������饹
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@access		public
 *	@package	Ethna
 */
class Ethna_SkeltonGenerator
{
	/**
	 *	�ץ������ȥ�����ȥ����������
	 *
	 *	@access	public
	 *	@param	string	$basedir	�ץ������ȥ١����ǥ��쥯�ȥ�
	 *	@param	string	$id			�ץ�������ID
	 *	@return	bool	true:���� false:����
	 */
	function generateProjectSkelton($basedir, $id)
	{
		$mode = 0755;

		$basedir = sprintf("%s/%s", $basedir, strtolower($id));

		/* �ǥ��쥯�ȥ���� */
		if (is_dir($basedir) == false) {
			if (mkdir($basedir, $mode) == false) {
				return false;
			}
		}
		foreach (array("app", "app/action", "bin", "etc", "lib", "locale", "locale/ja", "locale/ja/LC_MESSAGES", "log", "schema", "template", "template/ja", "tmp", "www") as $dir) {
			$target = "$basedir/$dir";
			if (is_dir($target)) {
				printf("%s already exists -> skipping...\n", $target);
				continue;
			}
			if (mkdir($target, $mode) == false) {
				return false;
			} else {
				printf("proejct sub director created [%s]\n", $target);
			}
		}

		/* ������ȥ�ե�������� */
		$macro['application_id'] = strtoupper($id);
		$macro['project_id'] = ucfirst($id);
		$macro['project_prefix'] = strtolower($id);

		if ($this->_generateFile("www.index.php", "$basedir/www/index.php", $macro) == false ||
			$this->_generateFile("www.info.php", "$basedir/www/info.php", $macro) == false ||
			$this->_generateFile("app.controller.php", sprintf("$basedir/app/%s_controller.php", $macro['project_prefix']), $macro) == false ||
			$this->_generateFile("app.error.php", sprintf("$basedir/app/%s_error.php", $macro['project_prefix']), $macro) == false ||
			$this->_generateFile("app.action.default.php", sprintf("$basedir/app/action/%s.php", $macro['project_prefix']), $macro) == false ||
			$this->_generateFile("app.action.default.php", sprintf("$basedir/app/action/%s.php", $macro['project_prefix']), $macro) == false ||
			$this->_generateFile("etc.ini.php", sprintf("$basedir/etc/%s-ini.php", $macro['project_prefix']), $macro) == false ||
			$this->_generateFile("template.index.tpl", sprintf("$basedir/template/ja/index.tpl"), $macro) == false) {
			return false;
		}

		return true;
	}

	/**
	 *	������ȥ�ե�����˥ޥ����Ŭ�Ѥ��ƥե��������������
	 *
	 *	ethna�饤�֥��Υǥ��쥯�ȥ깽¤���ѹ�����Ƥ��ʤ����Ȥ�����
	 *	�ȤʤäƤ����������
	 *
	 *	@access	private
	 *	@param	string	$skel		������ȥ�ե�����
	 *	@param	string	$entity		�����ե�����̾
	 *	@param	array	$macro		�ִ��ޥ���
	 *	@return	bool	true:���ｪλ false:���顼
	 */
	function _generateFile($skel, $entity, $macro)
	{
		$base = dirname(dirname(__FILE__));
		$rfp = fopen("$base/skel/$skel", "r");
		if ($rfp == null) {
			return false;
		}
		$wfp = fopen($entity, "w");
		if ($wfp == null) {
			fclose($rfp);
			return false;
		}

		for (;;) {
			$s = fread($rfp, 4096);
			if (strlen($s) == 0) {
				break;
			}

			foreach ($macro as $k => $v) {
				$s = preg_replace("/{\\\$$k}/", $v, $s);
			}
			fwrite($wfp, $s);
		}

		fclose($wfp);
		fclose($rfp);

		$st = stat("$base/skel/$skel");
		if (chmod($entity, $st[2]) == false) {
			return false;
		}

		return true;
	}
}
?>
