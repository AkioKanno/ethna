<?php
/**
 *	generate_project_skelton.php
 *
 *	usage: /path/to/php generate_project_skelton.php [project_basedir] [project_id]
 *
 *	@author		Masaki Fujimoto <fujimoto@php.net>
 *	@license	http://www.opensource.org/licenses/bsd-license.php The BSD License
 *	@package	Ethna
 *	@version	$Id$
 */

// �ǥ��쥯�ȥ�Ķ�����
if (!defined('PATH_SEPARATOR')) {
	if (OS_WINDOWS) {
		/** include_path���ѥ졼��(Windows) */
		define('PATH_SEPARATOR', ';');
	} else {
		/** include_path���ѥ졼��(Unix) */
		define('PATH_SEPARATOR', ':');
	}
}
$base = dirname(dirname(dirname(__FILE__)));
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . "$base");

include_once('Ethna/Ethna.php');

// ���������������å�
if ($_SERVER['argc'] != 3) {
	ethna_generate_project_skelton_usage();
	exit(1);
}
$project_basedir = $_SERVER['argv'][1];
$project_id = $_SERVER['argv'][2];

$sg = new Ethna_SkeltonGenerator();
if ($sg->generateProjectSkelton($project_basedir, $project_id)) {
	printf("\nproject skelton for [%s] is successfully generated at [%s]\n\n", $project_id, $project_basedir);
} else {
	printf("\nerror occurred while generating skelton. please see also error messages given above\n\n");
}

/**
 *	���ޥ�ɥ饤��إ�פ�ɽ������
 */
function ethna_generate_project_skelton_usage()
{
	printf("usage: /path/to/php generate_project_skelton.php [project_basedir] [project_id]\n\n");
}
?>
