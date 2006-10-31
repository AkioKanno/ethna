<?php
// vim: foldmethod=marker
/**
 *  Ethna_Plugin_Generator_Template.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id$
 */

// {{{ Ethna_Plugin_Generator_Template
/**
 *  ������ȥ��������饹
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Generator_Template extends Ethna_Plugin_Generator
{
    /**
     *  �ƥ�ץ졼�ȤΥ�����ȥ����������
     *
     *  @access public
     *  @param  string  $forward_name   ���������̾
     *  @param  string  $app_dir        �ץ������ȥǥ��쥯�ȥ�
     *  @return bool    true:���� false:����
     */
    function generate($forward_name, $app_dir, $skel_file = null)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }
        $this->ctl =& $c;

        $tpl_dir = $c->getTemplatedir();
        if ($tpl_dir{strlen($tpl_dir)-1} != '/') {
            $tpl_dir .= '/';
        }
        $tpl_path = $c->getDefaultForwardPath($forward_name);

        // skel_file
        if ($skel_file === null) {
            $skel_file = "skel.template.tpl";
        }

        $macro = array();
        // add '_' for tpl and no user macro for tpl
        $macro['_project_id'] = $c->getAppId();

        Ethna_Util::mkdir(dirname("$tpl_dir/$tpl_path"), 0755);

        if (file_exists("$tpl_dir$tpl_path")) {
            printf("file [%s] already exists -> skip\n", "$tpl_dir$tpl_path");
        } else if ($this->_generateFile($skel_file, "$tpl_dir$tpl_path", $macro) == false) {
            printf("[warning] file creation failed [%s]\n", "$tpl_dir$tpl_path");
        } else {
            printf("template file(s) successfully created [%s]\n", "$tpl_dir$tpl_path");
        }
    }
}
// }}}
?>
