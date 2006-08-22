<?php
// vim: foldmethod=marker
/**
 *  Ethna_Generator.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id$
 */

// {{{ Ethna_Generator
/**
 *  ������ȥ��������饹
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_Generator
{
    /**
     *  ������ȥ����������
     *
     *  @access public
     */
    function generate($type)
    {
        $arg_list = func_get_args();
        array_shift($arg_list);

        // tmp controller
        $c =& new Ethna_Controller(GATEWAY_CLI);
        $plugin_manager =& $c->getPlugin();
        $generator =& $plugin_manager->getPlugin('Generator', $type);
        if (Ethna::isError($generator)) {
            return $generator;
        }
        
        // �����ϥץ饰�����¸�Ȥ���
        return call_user_func_array(array($generator, 'generate'), $arg_list);
    }

    /**
     *  ������ȥ��������
     *
     *  @access public
     */
    function remove($type)
    {
        $arg_list = func_get_args();
        array_shift($arg_list);

        // tmp controller
        $c =& new Ethna_Controller(GATEWAY_CLI);
        $plugin_manager =& $c->getPlugin();
        $generator =& $plugin_manager->getPlugin('Generator', $type);
        if (Ethna::isError($generator)) {
            return $generator;
        }
        
        // �����ϥץ饰�����¸�Ȥ���
        return call_user_func_array(array($generator, 'remove'), $arg_list);
    }
}
// }}}
?>
