<?php
/**
 *  {$project_id}_Plugin_Urlhandler_Default.php
 *
 *  @author     {$author}
 *  @package    {$project_id}
 *  @version    $Id$
 */

Ethna_Plugin::includeEthnaPlugin('Urlhandler', 'Default');

/**
 *  ��������󥲡��ȥ������ץ饰����
 *  �����ˤϥ��ץ������(action_map�ʤ�)�򵭽Ҥ��Ƥ���������
 *
 *  @author     {$author}
 *  @access     public
 *  @package    {$project_id}
 */
class {$project_id}_Plugin_Urlhandler_Default extends Ethna_Plugin_Urlhandler_Default
{
    /** @var    array   ���������ޥåԥ� */
    var $action_map = array(
        /*
        'user'   => array(
            'profile_view' => array(
                'path'          => '',
                'path_regexp'   => '/^(\d+)$/',
                'path_ext'      => array(
                    'user_id' => array('input_filter' => '_filter'),
                ),
            ),
        ),
        */
    );

    /*
    // {{{
    function _getPath_User()
    {
        return array('/user/', array());
    }

    function _normalizeRequest_User($http_vars)
    {
        return $http_vars;
    }

    function _filter($user_id)
    {
        return (int) $user_id;
    }
    // }}}
    */
}

?>
