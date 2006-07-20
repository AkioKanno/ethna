<?php
// vim: foldmethod=marker
/**
 *  Ethna_ViewClass.php
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @license    http://www.opensource.org/licenses/bsd-license.php The BSD License
 *  @package    Ethna
 *  @version    $Id$
 */

// {{{ Ethna_ViewClass
/**
 *  view���饹
 *
 *  @author     Masaki Fujimoto <fujimoto@php.net>
 *  @access     public
 *  @package    Ethna
 */
class Ethna_ViewClass
{
    /**#@+
     *  @access private
     */

    /** @var    object  Ethna_Backend       backend���֥������� */
    var $backend;

    /** @var    object  Ethna_Config        ���ꥪ�֥�������    */
    var $config;

    /** @var    object  Ethna_I18N          i18n���֥������� */
    var $i18n;

    /** @var    object  Ethna_ActionError   ��������󥨥顼���֥������� */
    var $action_error;

    /** @var    object  Ethna_ActionError   ��������󥨥顼���֥�������(��ά��) */
    var $ae;

    /** @var    object  Ethna_ActionForm    ���������ե����४�֥������� */
    var $action_form;

    /** @var    object  Ethna_ActionForm    ���������ե����४�֥�������(��ά��) */
    var $af;

    /** @var    array   ���������ե����४�֥�������(helper) */
    var $helper_action_form = array();

    /** @var    object  Ethna_Session       ���å���󥪥֥������� */
    var $session;

    /** @var    string  ����̾ */
    var $forward_name;

    /** @var    string  ������ƥ�ץ졼�ȥե�����̾ */
    var $forward_path;

    /**#@-*/

    /**
     *  Ethna_ViewClass�Υ��󥹥ȥ饯��
     *
     *  @access public
     *  @param  object  Ethna_Backend   $backend    backend���֥�������
     *  @param  string  $forward_name   �ӥ塼�˴�Ϣ�դ����Ƥ�������̾
     *  @param  string  $forward_path   �ӥ塼�˴�Ϣ�դ����Ƥ���ƥ�ץ졼�ȥե�����̾
     */
    function Ethna_ViewClass(&$backend, $forward_name, $forward_path)
    {
        $c =& $backend->getController();
        $this->backend =& $backend;
        $this->config =& $this->backend->getConfig();
        $this->i18n =& $this->backend->getI18N();

        $this->action_error =& $this->backend->getActionError();
        $this->ae =& $this->action_error;

        $this->action_form =& $this->backend->getActionForm();
        $this->af =& $this->action_form;

        $this->session =& $this->backend->getSession();

        $this->forward_name = $forward_name;
        $this->forward_path = $forward_path;

        foreach ($this->helper_action_form as $key => $value) {
            if (is_object($value)) {
                continue;
            }
            $this->helper_action_form[$key] =& $this->_getHelperActionForm($key);
        }
    }

    /**
     *  ����ɽ��������
     *
     *  �ƥ�ץ졼�Ȥ����ꤹ���ͤǥ���ƥ����Ȥ˰�¸���ʤ���Τ�
     *  ���������ꤹ��(��:���쥯�ȥܥå�����)
     *
     *  @access public
     */
    function preforward()
    {
    }

    /**
     *  ����̾���б�������̤���Ϥ���
     *
     *  �ü�ʲ��̤�ɽ���������������ä˥����С��饤�ɤ���ɬ�פ�̵��
     *  (preforward()�Τߥ����С��饤�ɤ�����ɤ�)
     *
     *  @access public
     */
    function forward()
    {
        $renderer =& $this->_getRenderer();
        $this->_setDefault($renderer);
        $renderer->perform($this->forward_path);
    }

    /**
     *  helper���������ե����४�֥������Ȥ����ꤹ��
     *
     *  @access public
     */
    function addActionFormHelper($action)
    {
        if (is_object($this->helper_action_form[$action])) {
            return;
        }
        $this->helper_action_form[$action] =& $this->_getHelperActionForm($action);
    }

    /**
     *  helper���������ե����४�֥������Ȥ�������
     *
     *  @access public
     */
    function clearActionFormHelper($action)
    {
        unset($this->helper_action_form[$action]);
    }

    /**
     *  ���ꤵ�줿�ե�������ܤ��б�����ե�����̾(w/ �������)���������
     *
     *  @access public
     */
    function getFormName($name, $params)
    {
        $def = $this->_getHelperActionFormDef($name);
        $form_name = null;
        if (is_null($def) || isset($def['name']) == false) {
            $form_name = $name;
        } else {
            $form_name = $def['name'];
        }

        return $form_name;
    }

    /**
     *  ���ꤵ�줿�ե�������ܤ��б�����ե����ॿ�����������
     *
     *  experimental(�Ȥ������Ȥꤢ����-�٤����������̥��饹�˹Ԥ������Ǥ�)
     *
     *  @access public
     *  @todo   form_type�Ƽ��б�/JavaScript�б�...
     */
    function getFormInput($name, $params)
    {
        $def = $this->_getHelperActionFormDef($name);
        if (is_null($def)) {
            return "";
        }

        if (isset($def['form_type']) == false) {
            $def['form_type'] = FORM_TYPE_TEXT;
        }
        
        switch ($def['form_type']) {
        case FORM_TYPE_BUTTON:
            $input = $this->_getFormInput_Button($name, $def, $params);
            break;
        case FORM_TYPE_CHECKBOX:
            // T.B.D.
            break;
        case FORM_TYPE_FILE:
            $input = $this->_getFormInput_File($name, $def, $params);
            break;
        case FORM_TYPE_HIDDEN:
            $input = $this->_getFormInput_Hidden($name, $def, $params);
            break;
        case FORM_TYPE_PASSWORD:
            $input = $this->_getFormInput_Password($name, $def, $params);
            break;
        case FORM_TYPE_RADIO:
            // T.B.D.
            break;
        case FORM_TYPE_SELECT:
            // T.B.D.
            break;
        case FORM_TYPE_SUBMIT:
            $input = $this->_getFormInput_Submit($name, $def, $params);
            break;
        case FORM_TYPE_TEXTAREA:
            $input = $this->_getFormInput_Textarea($name, $def, $params);
            break;
        case FORM_TYPE_TEXT:
        default:
            $input = $this->_getFormInput_Text($name, $def, $params);
            break;
        }

        print $input;
    }

    /**
     *  ���������ե����४�֥�������(helper)����������
     *
     *  @access protected
     */
    function &_getHelperActionForm($action)
    {
        $af = null;
        $ctl =& Ethna_Controller::getInstance();
        $form_name = $ctl->getActionFormName($action);
        if ($form_name == null) {
            // TODO: logging
            return null;
        }
        $af =& new $form_name($ctl);

        return $af;
    }

    /**
     *  �ե�������ܤ��б�����ե�����������������
     *
     *  @access protected
     */
    function _getHelperActionFormDef($name)
    {
        $def = $this->af->getDef($name);
        if (is_null($def)) {
            foreach ($this->helper_action_form as $key => $value) {
                if (is_object($value) == false) {
                    continue;
                }
                $def = $value->getDef($name);
                if (is_null($def) == false) {
                    break;
                }
            }
        }
        return $def;
    }

    /**
     *  �ե����ॿ�����������(type="button")
     *
     *  @access protected
     */
    function _getFormInput_Button($name, $def, $params)
    {
        $r = array();
        $r['type'] = "button";
        $r['name'] = $name;

        return $this->_getFormInput_Html("input", $r, $params);
    }

    /**
     *  �ե����ॿ�����������(type="file")
     *
     *  @access protected
     */
    function _getFormInput_File($name, $def, $params)
    {
        $r = array();
        $r['type'] = "file";
        $r['name'] = $name;
        $r['value'] = "";

        return $this->_getFormInput_Html("input", $r, $params);
    }

    /**
     *  �ե����ॿ�����������(type="hidden")
     *
     *  @access protected
     */
    function _getFormInput_Hidden($name, $def, $params)
    {
        $r = array();
        $r['type'] = "hidden";
        $r['name'] = $name;
        $r['value'] = $this->af->get($name);

        return $this->_getFormInput_Html("input", $r, $params);
    }

    /**
     *  �ե����ॿ�����������(type="password")
     *
     *  @access protected
     */
    function _getFormInput_Password($name, $def, $params)
    {
        $r = array();
        $r['type'] = "password";
        $r['name'] = $name;
        $r['value'] = $this->af->get($name);

        return $this->_getFormInput_Html("input", $r, $params);
    }

    /**
     *  �ե����ॿ�����������(type="submit")
     *
     *  @access protected
     */
    function _getFormInput_Submit($name, $def, $params)
    {
        $r = array();
        $r['type'] = "submit";
        $r['name'] = $name;
        $r['value'] = $def['name'];

        return $this->_getFormInput_Html("input", $r, $params);
    }

    /**
     *  �ե����ॿ�����������(textarea)
     *
     *  @access protected
     */
    function _getFormInput_Textarea($name, $def, $params)
    {
        $r = array();
        $r['name'] = $name;

        return $this->_getFormInput_Html("textarea", $r, $params, $this->af->get($name));
    }

    /**
     *  �ե����ॿ�����������(type="text")
     *
     *  @access protected
     */
    function _getFormInput_Text($name, $def, $params)
    {
        $r = array();
        $r['type'] = "text";
        $r['name'] = $name;
        $r['value'] = $this->af->get($name);
        if (isset($def['max']) && $def['max']) {
            $r['maxlength'] = $def['max'];
        }

        return $this->_getFormInput_Html("input", $r, $params);
    }

    /**
     *  HTML�������������
     *
     *  @access protected
     */
    function _getFormInput_Html($tag, $attr, $user_attr, $element = false)
    {
        // user defs
        foreach ($user_attr as $key => $value) {
            if ($key == "type" || $key == "name" || preg_match('/^[a-z0-9]+$/i', $key) == 0) {
                continue;
            }
            $attr[$key] = $value;
        }

        $r = "<$tag";

        foreach ($attr as $key => $value) {
            $r .= sprintf(' %s="%s"', $key, htmlspecialchars($value, ENT_QUOTES));
        }

        if ($element !== false) {
            $r .= sprintf('>%s</%s>', htmlspecialchars($element, ENT_QUOTES), $tag);
        } else {
            $r .= " />";
        }

        return $r;
    }

    /**
     *  �����饪�֥������Ȥ��������
     *
     *  @access protected
     *  @return object  Ethna_Renderer  �����饪�֥�������
     */
    function &_getRenderer()
    {
        $_ret_object =& $this->_getTemplateEngine();
        return $_ret_object;
    }

    /**
     *  �����饪�֥������Ȥ��������(���Τ���_getRenderer()�����礵���ͽ��)
     *
     *  @access protected
     *  @return object  Ethna_Renderer  �����饪�֥�������
     *  @obsolete
     */
    function &_getTemplateEngine()
    {
        $c =& $this->backend->getController();
        $renderer =& $c->getRenderer();

        $form_array =& $this->af->getArray();
        $app_array =& $this->af->getAppArray();
        $app_ne_array =& $this->af->getAppNEArray();
        $renderer->setPropByRef('form', $form_array);
        $renderer->setPropByRef('app', $app_array);
        $renderer->setPropByRef('app_ne', $app_ne_array);
        $message_list = Ethna_Util::escapeHtml($this->ae->getMessageList());
        $renderer->setPropByRef('errors', $message_list);
        if (isset($_SESSION)) {
            $tmp_session = Ethna_Util::escapeHtml($_SESSION);
            $renderer->setPropByRef('session', $tmp_session);
        }
        $renderer->setProp('script', htmlspecialchars(basename($_SERVER['PHP_SELF']), ENT_QUOTES));
        $renderer->setProp('request_uri', htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES));

        return $renderer;
    }

    /**
     *  �����ͤ����ꤹ��
     *
     *  @access protected
     *  @param  object  Ethna_Renderer  �����饪�֥�������
     */
    function _setDefault(&$renderer)
    {
    }
}
// }}}
?>
