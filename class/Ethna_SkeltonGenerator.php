<?php
// vim: foldmethod=marker
/**
 *  Ethna_SkeltonGenerator.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id$
 */

// {{{ Ethna_SkeltonGenerator
/**
 *  ������ȥ��������饹
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_SkeltonGenerator
{
    /**
     *  �ץ������ȥ�����ȥ����������
     *
     *  @access public
     *  @param  string  $basedir    �ץ������ȥ١����ǥ��쥯�ȥ�
     *  @param  string  $id         �ץ�������ID
     *  @return bool    true:���� false:����
     */
    function generateProjectSkelton($basedir, $id)
    {
        $dir_list = array(
            array("app", 0755),
            array("app/action", 0755),
            array("app/action_cli", 0755),
            array("app/action_xmlrpc", 0755),
            array("app/filter", 0755),
            array("app/plugin", 0755),
            array("app/plugin/Filter", 0755),
            array("app/plugin/Validator", 0755),
            array("app/view", 0755),
            array("bin", 0755),
            array("etc", 0755),
            array("lib", 0755),
            array("locale", 0755),
            array("locale/ja", 0755),
            array("locale/ja/LC_MESSAGES", 0755),
            array("log", 0777),
            array("schema", 0755),
            array("skel", 0755),
            array("template", 0755),
            array("template/ja", 0755),
            array("tmp", 0777),
            array("www", 0755),
            array("www/css", 0755),
            array("www/js", 0755),
        );

        $r = Ethna_Controller::checkAppId($id);
        if (Ethna::isError($r)) {
            return $r;
        }

        $basedir = sprintf("%s/%s", $basedir, strtolower($id));

        // �ǥ��쥯�ȥ����
        if (is_dir($basedir) == false) {
            // confirm
            printf("creating directory ($basedir) [y/n]: ");
            flush();
            $fp = fopen("php://stdin", "r");
            $r = trim(fgets($fp, 128));
            fclose($fp);
            if (strtolower($r) != 'y') {
                return Ethna::raiseError('aborted by user');
            }

            if (mkdir($basedir, 0775) == false) {
                return Ethna::raiseError('directory creation failed');
            }
        }
        foreach ($dir_list as $dir) {
            $mode = $dir[1];
            $dir = $dir[0];
            $target = "$basedir/$dir";
            if (is_dir($target)) {
                printf("%s already exists -> skipping...\n", $target);
                continue;
            }
            if (mkdir($target, $mode) == false) {
                return Ethna::raiseError('directory creation failed');
            } else {
                printf("project sub directory created [%s]\n", $target);
            }
            if (chmod($target, $mode) == false) {
                return Ethna::raiseError('chmod failed');
            }
        }

        // ������ȥ�ե��������
        $macro['application_id'] = strtoupper($id);
        $macro['project_id'] = ucfirst($id);
        $macro['project_prefix'] = strtolower($id);
        $macro['basedir'] = realpath($basedir);

        $macro['action_class'] = '{$action_class}';
        $macro['action_form'] = '{$action_form}';
        $macro['action_name'] = '{$action_name}';
        $macro['action_path'] = '{$action_path}';
        $macro['forward_name'] = '{$forward_name}';
        $macro['view_name'] = '{$view_name}';
        $macro['view_path'] = '{$view_path}';

        $user_macro = $this->_getUserMacro();
        $default_macro = $macro;
        $macro = array_merge($macro, $user_macro);

        // the longest if? :)
        if ($this->_generateFile("www.index.php", "$basedir/www/index.php", $macro) == false ||
            $this->_generateFile("www.info.php", "$basedir/www/info.php", $macro) == false ||
            $this->_generateFile("www.unittest.php", "$basedir/www/unittest.php", $macro) == false ||
            $this->_generateFile("www.xmlrpc.php", "$basedir/www/xmlrpc.php", $macro) == false ||
            $this->_generateFile("www.css.ethna.css", "$basedir/www/css/ethna.css", $macro) == false ||
            $this->_generateFile("dot.ethna", "$basedir/.ethna", $macro) == false ||
            $this->_generateFile("app.controller.php", sprintf("$basedir/app/%s_Controller.php", $macro['project_id']), $macro) == false ||
            $this->_generateFile("app.error.php", sprintf("$basedir/app/%s_Error.php", $macro['project_id']), $macro) == false ||
            $this->_generateFile("app.action.default.php", "$basedir/app/action/Index.php", $macro) == false ||
            $this->_generateFile("app.plugin.filter.default.php", sprintf("$basedir/app/plugin/Filter/%s_Plugin_Filter_ExecutionTime.php", $macro['project_id']), $macro) == false ||
            $this->_generateFile("app.view.default.php", "$basedir/app/view/Index.php", $macro) == false ||
            $this->_generateFile("app.unittest.php", sprintf("$basedir/app/%s_UnitTestManager.php", $macro['project_id']), $macro) == false ||
            $this->_generateFile("etc.ini.php", sprintf("$basedir/etc/%s-ini.php", $macro['project_prefix']), $macro) == false ||
            $this->_generateFile("skel.action.php", sprintf("$basedir/skel/skel.action.php"), $default_macro) == false ||
            $this->_generateFile("skel.action_cli.php", sprintf("$basedir/skel/skel.action_cli.php"), $default_macro) == false ||
            $this->_generateFile("skel.action_test.php", sprintf("$basedir/skel/skel.action_test.php"), $default_macro) == false ||
            $this->_generateFile("skel.app_object.php", sprintf("$basedir/skel/skel.app_object.php"), $default_macro) == false ||
            $this->_generateFile("skel.cli.php", sprintf("$basedir/skel/skel.cli.php"), $default_macro) == false ||
            $this->_generateFile("skel.view.php", sprintf("$basedir/skel/skel.view.php"), $default_macro) == false ||
            $this->_generateFile("skel.template.tpl", sprintf("$basedir/skel/skel.template.tpl"), $default_macro) == false ||
            $this->_generateFile("skel.view_test.php", sprintf("$basedir/skel/skel.view_test.php"), $default_macro) == false ||
            $this->_generateFile("template.index.tpl", sprintf("$basedir/template/ja/index.tpl"), $default_macro) == false) {
            return Ethna::raiseError('generating files failed');
        }

        return true;
    }

    /**
     *  ���������Υ�����ȥ����������
     *
     *  @access public
     *  @param  string  $action_name    ���������̾
     *  @param  string  $app_dir        �ץ������ȥǥ��쥯�ȥ�
     *  @param  int     $gateway        �����ȥ�����
     *  @return bool    true:���� false:����
     */
    function generateActionSkelton($action_name, $app_dir, $gateway = GATEWAY_WWW)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }

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

        Ethna_Handle::mkdir(dirname("$action_dir$action_path"), 0755);

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

    /**
     *  �ӥ塼�Υ�����ȥ����������
     *
     *  @access public
     *  @param  string  $forward_name   ���������̾
     *  @param  string  $app_dir        �ץ������ȥǥ��쥯�ȥ�
     *  @return bool    true:���� false:����
     */
    function generateViewSkelton($forward_name, $app_dir)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }

        $view_dir = $c->getViewdir();
        $view_class = $c->getDefaultViewClass($forward_name, false);
        $view_path = $c->getDefaultViewPath($forward_name, false);

        $macro = array();
        $macro['project_id'] = $c->getAppId();
        $macro['forward_name'] = $forward_name;
        $macro['view_class'] = $view_class;
        $macro['view_path'] = $view_path;

        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        Ethna_Handle::mkdir(dirname("$view_dir/$view_path"), 0755);

        if (file_exists("$view_dir$view_path")) {
            printf("file [%s] already exists -> skip\n", "$view_dir$view_path");
        } else if ($this->_generateFile("skel.view.php", "$view_dir$view_path", $macro) == false) {
            printf("[warning] file creation failed [%s]\n", "$view_dir$view_path");
        } else {
            printf("view script(s) successfully created [%s]\n", "$view_dir$view_path");
        }
    }

    /**
     *  CLI����ȥ�ݥ���ȤΥ�����ȥ����������
     *
     *  @access public
     *  @param  string  $forward_name   ���������̾
     *  @param  string  $app_dir        �ץ������ȥǥ��쥯�ȥ�
     *  @return bool    true:���� false:����
     */
    function generateCliSkelton($action_name, $app_dir)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }

        $app_dir = $c->getDirectory('app');
        $bin_dir = $c->getDirectory('bin');
        $cli_file = sprintf("%s/%s.%s", $bin_dir, $action_name, $c->getExt('php'));

        $macro = array();
        $macro['project_id'] = $c->getAppId();
        $macro['action_name'] = $action_name;
        $macro['dir_app'] = $app_dir;
        $macro['dir_bin'] = $bin_dir;

        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        if (file_exists($cli_file)) {
            printf("file [%s] already exists -> skip\n", $cli_file);
        } else if ($this->_generateFile("skel.cli.php", $cli_file, $macro) == false) {
            printf("[warning] file creation failed [%s]\n", $cli_file);
        } else {
            printf("action script(s) successfully created [%s]\n", $cli_file);
        }
    }

    /**
     *  �ƥ�ץ졼�ȤΥ�����ȥ����������
     *
     *  @access public
     *  @param  string  $forward_name   ���������̾
     *  @param  string  $app_dir        �ץ������ȥǥ��쥯�ȥ�
     *  @return bool    true:���� false:����
     */
    function generateTemplateSkelton($forward_name, $app_dir)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }

        $tpl_dir = $c->getTemplatedir();
        if ($tpl_dir{strlen($tpl_dir)-1} != '/') {
            $tpl_dir .= '/';
        }
        $tpl_path = $c->getDefaultForwardPath($forward_name);

        $macro = array();
        // add '_' for tpl and no user macro for tpl
        $macro['_project_id'] = $c->getAppId();

        Ethna_Handle::mkdir(dirname("$tpl_dir/$tpl_path"), 0755);

        if (file_exists("$tpl_dir$tpl_path")) {
            printf("file [%s] already exists -> skip\n", "$tpl_dir$tpl_path");
        } else if ($this->_generateFile("skel.template.tpl", "$tpl_dir$tpl_path", $macro) == false) {
            printf("[warning] file creation failed [%s]\n", "$tpl_dir$tpl_path");
        } else {
            printf("template file(s) successfully created [%s]\n", "$tpl_dir$tpl_path");
        }
    }

    /**
     *  ���ץꥱ������󥪥֥������ȤΥ�����ȥ����������
     *
     *  @access public
     *  @param  string  $table_name     �ơ��֥�̾
     *  @param  string  $app_dir        �ץ������ȥǥ��쥯�ȥ�
     *  @return bool    true:���� false:����
     */
    function generateAppObjectSkelton($table_name, $app_dir)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }

        $table_id = preg_replace('/_(.)/e', "strtoupper('\$1')", ucfirst($table_name));

        $app_dir = $c->getDirectory('app');
        $app_path = ucfirst($c->getAppId()) . '_' . $table_id .'.php';

        $macro = array();
        $macro['project_id'] = $c->getAppId();
        $macro['app_path'] = $app_path;
        $macro['app_object'] = ucfirst($c->getAppId()) . '_' . $table_id;

        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        $path = "$app_dir/$app_path";
        Ethna_Handle::mkdir(dirname($path), 0755);
        if (file_exists($path)) {
            printf("file [%s] already exists -> skip\n", $path);
        } else if ($this->_generateFile("skel.app_object.php", $path, $macro) == false) {
            printf("[warning] file creation failed [%s]\n", $path);
        } else {
            printf("app-object script(s) successfully created [%s]\n", $path);
        }
    }

    /**
     *  ���ץꥱ�������ޥ͡�����Υ�����ȥ����������
     *
     *  @access public
     *  @param  string  $manager_name    ���ץꥱ�������ޥ͡���̾
     *  @param  string  $app_dir        �ץ������ȥǥ��쥯�ȥ�
     *  @return bool    true:���� false:����
     */
    function generateAppManagerSkelton($manager_name, $app_dir)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }

        $manager_id = preg_replace('/_(.)/e', "strtoupper('\$1')", ucfirst($manager_name));

        $app_dir = $c->getDirectory('app');
        $app_path = ucfirst($c->getAppId()) . '_' . $manager_id .'Manager.php';

        $macro = array();
        $macro['project_id'] = $c->getAppId();
        $macro['app_path'] = $app_path;
        $macro['app_manager'] = ucfirst($c->getAppId()) . '_' . $manager_id;

        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        $path = "$app_dir/$app_path";
        Ethna_Handle::mkdir(dirname($path), 0755);
        if (file_exists($path)) {
            printf("file [%s] already exists -> skip\n", $path);
        } else if ($this->_generateFile("skel.app_manager.php", $path, $macro) == false) {
            printf("[warning] file creation failed [%s]\n", $path);
        } else {
            printf("app-manager script(s) successfully created [%s]\n", $path);
        }
    }

    /**
     *  ����������ѥƥ��ȤΥ�����ȥ����������
     *
     *  @access public
     *  @param  string  $action_name    ���������̾
     *  @param  string  $app_dir        �ץ������ȥǥ��쥯�ȥ�
     *  @return bool    true:���� false:����
     */
    function generateActionTestSkelton($action_name, $app_dir, $gateway = GATEWAY_WWW)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }

        $action_dir = $c->getActiondir($gateway);
        $action_class = $c->getDefaultActionClass($action_name, false);
        $action_form = $c->getDefaultFormClass($action_name, false);
        $action_path = $c->getDefaultActionPath($action_name . "Test", false);

        $macro = array();
        $macro['project_id'] = $c->getAppId();
        $macro['action_name'] = $action_name;
        $macro['action_class'] = $action_class;
        $macro['action_form'] = $action_form;
        $macro['action_path'] = $action_path;

        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        Ethna_Handle::mkdir(dirname("$action_dir$action_path"), 0755);

        if (file_exists("$action_dir$action_path")) {
            printf("file [%s] aleady exists -> skip\n", "$action_dir$action_path");
        } else if ($this->_generateFile("skel.action_test.php", "$action_dir$action_path", $macro) == false) {
            printf("[warning] file creation failed [%s]\n", "$action_dir$action_path");
        } else {
            printf("action test(s) successfully created [%s]\n", "$action_dir$action_path");
        }
    }

    /**
     *  �ӥ塼�ѥƥ��ȤΥ�����ȥ����������
     *
     *  @access public
     *  @param  string  $forward_name   ���������̾
     *  @return bool    true:���� false:����
     */
    function generateViewTestSkelton($forward_name, $app_dir)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }

        $view_dir = $c->getViewdir();
        $view_class = $c->getDefaultViewClass($forward_name, false);
        $view_path = $c->getDefaultViewPath($forward_name . "Test", false);

        $macro = array();
        $macro['project_id'] = $c->getAppId();
        $macro['forward_name'] = $forward_name;
        $macro['view_class'] = $view_class;
        $macro['view_path'] = $view_path;

        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        Ethna_Handle::mkdir(dirname("$view_dir/$view_path"), 0755);

        if (file_exists("$view_dir$view_path")) {
            printf("file [%s] aleady exists -> skip\n", "$view_dir$view_path");
        } else if ($this->_generateFile("skel.view_test.php", "$view_dir$view_path", $macro) == false) {
            printf("[warning] file creation failed [%s]\n", "$view_dir$view_path");
        } else {
            printf("view test(s) successfully created [%s]\n", "$view_dir$view_path");
        }
    }

    /**
     *  �ץ饰�������������
     *
     *  @access public
     *  @param  string  $type       �ץ饰�����$type
     *  @param  string  $name       �ץ饰�����$name
     *  @return bool    true:���� false:����
     */
    function generatePlugin($type, $name, $app_dir)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }
        $appid = $c->getAppId();
        $plugin =& $c->getPlugin();

        list($class, $plugin_dir, $plugin_path) = $plugin->getPluginNaming($type, $name, $appid);

        $macro = array();
        $macro['project_id'] = $appid;
        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        Ethna_Handle::mkdir(dirname("$plugin_dir/$plugin_path"), 0755);

        if (file_exists("$plugin_dir/$plugin_path")) {
            printf("file [%s] already exists -> skip\n", "$plugin_dir/$plugin_path");
        } else if ($this->_generateFile("skel.plugin.{$type}_{$name}.php", "$plugin_dir/$plugin_path", $macro) == false) {
            printf("[warning] file creation failed [%s]\n", "$plugin_dir/$plugin_path");
        } else {
            printf("plugin script(s) successfully created [%s]\n", "$plugin_dir/$plugin_path");
        }
    }

    /**
     *  �ץ饰�����ä�
     *  TODO: ��äȤ������ˤ�ͤ���
     *
     *  @access public
     *  @param  string  $type       �ץ饰�����$type
     *  @param  string  $name       �ץ饰�����$name
     *  @return bool    true:���� false:����
     */
    function deletePlugin($type, $name, $app_dir)
    {
        // get application controller
        $c =& Ethna_Handle::getAppController($app_dir);
        if (Ethna::isError($c)) {
            return $c;
        }
        $appid = $c->getAppId();
        $plugin =& $c->getPlugin();

        list($class, $plugin_dir, $plugin_path) = $plugin->getPluginNaming($type, $name, $appid);

        $macro = array();
        $macro['project_id'] = $appid;
        $user_macro = $this->_getUserMacro();
        $macro = array_merge($macro, $user_macro);

        if (file_exists("$plugin_dir/$plugin_path")) {
            unlink("$plugin_dir/$plugin_path");
            printf("file [%s] successfully unlinked\n", "$plugin_dir/$plugin_path");
        } else {
            printf("file [%s] not found\n", "$plugin_dir/$plugin_path");
        }
    }

    /**
     *  ������ȥ�ե�����˥ޥ����Ŭ�Ѥ��ƥե��������������
     *
     *  ethna�饤�֥��Υǥ��쥯�ȥ깽¤���ѹ�����Ƥ��ʤ����Ȥ�����
     *  �ȤʤäƤ����������
     *
     *  @access private
     *  @param  string  $skel       ������ȥ�ե�����
     *  @param  string  $entity     �����ե�����̾
     *  @param  array   $macro      �ִ��ޥ���
     *  @return bool    true:���ｪλ false:���顼
     */
    function _generateFile($skel, $entity, $macro)
    {
        $base = null;

        if (file_exists($entity)) {
            printf("file [%s] already exists -> skip\n", $entity);
            return true;
        }
        $c =& Ethna_Controller::getInstance();
        if (is_object($c)) {
            $base = $c->getBasedir();
            if (file_exists("$base/skel/$skel") == false) {
                $base = null;
            }
        }
        if (is_null($base)) {
            $base = dirname(dirname(__FILE__));
        }

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

        printf("file generated [%s -> %s]\n", $skel, $entity);

        return true;
    }

    /**
     *  �桼������Υޥ�������ꤹ��(~/.ethna)
     *
     *  @access private
     */
    function _getUserMacro()
    {
        if (isset($_SERVER['USERPROFILE']) && is_dir($_SERVER['USERPROFILE'])) {
            $home = $_SERVER['USERPROFILE'];
        } else {
            $home = $_SERVER['HOME'];
        }

        if (is_file("$home/.ethna") == false) {
            return array();
        }

        $user_macro = parse_ini_file("$home/.ethna");
        return $user_macro;
    }
}
// }}}
?>
