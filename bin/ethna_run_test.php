<?php
/**
 *  ethna_run_test.php
 *
 *  Ethna Test Runner
 *
 *  @author     Kazuhiro Hosoi <hosoi@gree.co.jp>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id$
 */

/** ���ץꥱ�������١����ǥ��쥯�ȥ� */
define('BASE', dirname(dirname(__FILE__)));

// include_path������(���ץꥱ�������ǥ��쥯�ȥ���ɲ�)
$app = BASE . "/app";
$lib = BASE . "/lib";

/** Ethna��Ϣ���饹�Υ��󥯥롼�� */
include_once('Ethna/Ethna.php');

/** SimpleTest�Υ��󥯥롼�� */
include_once('simpletest/unit_tester.php');
include_once('simpletest/reporter.php');

/** �ƥ��ȥ�����������ǥ��쥯�ȥ� */
$test_dir = ETHNA_BASE . '/test';

$test = &new GroupTest('Ethna All tests');

// �ƥ��ȥ������Υե�����ꥹ�Ȥ����
$file_list = getFileList($test_dir);

// �ƥ��ȥ���������Ͽ
foreach ($file_list as $file) {
	$test->addTestFile($file);
}

// ��̤򥳥ޥ�ɥ饤��˽���
$test->run(new TextReporter());

function getFileList($dir_path) {
	$file_list = array();
    if ($dir = opendir($dir_path)) {
        while($file_path = readdir($dir)) {
            $full_path = $dir_path . '/'. $file_path;
            if (is_file($full_path)){
            	// �ƥ��ȥ������Υե�����Τ��ɤ߹���
                if (preg_match('/^(Ethna_)(.*)(_Test.php)$/',$file_path,$matches)) {
                    $file_list[] = $full_path;
                }
            // ���֥ǥ��쥯�ȥ꤬������ϡ��Ƶ�Ū���ɤ߹��ࡥ
            // "."�ǻϤޤ�ǥ��쥯�ȥ���ɤ߹��ޤʤ�.
            } else if (is_dir($full_path) && !preg_match('/^\./',$file_path,$matches)) {
                $file_list = array_merge($file_list,getFileList($full_path));
            }
        }
        closedir($dir);
    }
    return $file_list;
}
?>
