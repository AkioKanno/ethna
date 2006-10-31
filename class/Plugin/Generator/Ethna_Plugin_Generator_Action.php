<?php
// vim: foldmethod=marker
/**
 *  Ethna_Plugin_Generator_Action.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id$
 */

// {{{ Ethna_Plugin_Generator_Action
/**
 *  ������ȥ��������饹
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Plugin_Generator_Action extends Ethna_Plugin_Generator
{
    /**
     *  ���������Υ�����ȥ����������
     *
     *  @access public
     *  @param  string  $action_name    ���������̾
     *  @param  string  $app_dir        �ץ������ȥǥ��쥯�ȥ�
     *  @param  int     $gateway        �����ȥ�����
     *  @return bool    true:���� false:����
     */
    function generate($action_name, $app_dir, $gateway = GATEWAY_WWW)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }
        $this->ctl =& $c;

        $action_dir = $c->getActiondir($gateway);
        $action_class = $c->getDefaultActionClass($action_name, $gateway);
        $action_form = $c->getDefaultFormClass($action_name, $gateway);
        $action_path = $c->getDefaultActionPath($action_name);

        $macro = array();
        $macro['project_id'] = $c->getAppId();
        $macro['action_name'] = $action_name;
        $macro['action_class'] = $action_class;
        $macro['action_form'] = $action_form;
        $macro['action_path'] = $action_path;

        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        Ethna_Util::mkdir(dirname("$action_dir$action_path"), 0755);

        switch ($gateway) {
        case GATEWAY_WWW:
            $skelton = "skel.action.php";
            break;
        case GATEWAY_CLI:
            $skelton = "skel.action_cli.php";
            break;
        case GATEWAY_XMLRPC:
            $skelton = "skel.action_xmlrpc.php";
            break;
        }

        if (file_exists("$action_dir$action_path")) {
            printf("file [%s] already exists -> skip\n", "$action_dir$action_path");
        } else if ($this->_generateFile($skelton, "$action_dir$action_path", $macro) == false) {
            printf("[warning] file creation failed [%s]\n", "$action_dir$action_path");
        } else {
            printf("action script(s) successfully created [%s]\n", "$action_dir$action_path");
        }
    }
}
// }}}
?>
