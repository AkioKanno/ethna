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

    /** @var    object  Ethna_Logger    �����֥������� */
    var $logger;

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

    // {{{ Ethna_ViewClass
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
        $this->logger =& $this->backend->getLogger();

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
    // }}}

    // {{{ preforward
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
    // }}}

    // {{{ forward
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
    // }}}

    // {{{ addActionFormHelper
    /**
     *  helper���������ե����४�֥������Ȥ����ꤹ��
     *
     *  @access public
     */
    function addActionFormHelper($action)
    {
        if (isset($this->helper_action_form[$action])
            && is_object($this->helper_action_form[$action])) {
            return;
        }
        $this->helper_action_form[$action] =& $this->_getHelperActionForm($action);
    }
    // }}}

    // {{{ clearActionFormHelper
    /**
     *  helper���������ե����४�֥������Ȥ�������
     *
     *  @access public
     */
    function clearActionFormHelper($action)
    {
        unset($this->helper_action_form[$action]);
    }
    // }}}

    // {{{ getFormName
    /**
     *  ���ꤵ�줿�ե�������ܤ��б�����ե�����̾(w/ �������)���������
     *
     *  @access public
     */
    function getFormName($name, $action, $params)
    {
        $def = $this->_getHelperActionFormDef($name, $action);
        $form_name = null;
        if (is_null($def) || isset($def['name']) == false) {
            $form_name = $name;
        } else {
            $form_name = $def['name'];
        }

        return $form_name;
    }
    // }}}

    // {{{ getFormSubmit
    /**
     *  submit�ܥ�����������(�����襢�������Ǽ������褦
     *  �������Ƥ��ʤ��Ȥ��ˡ������submit�ܥ������Τ˻Ȥ�)
     *
     *  @access public
     */
    function getFormSubmit($name, $params)
    {
        $def = array('form_type' => FORM_TYPE_SUBMIT);
        $input = $this->_getFormInput_Submit($name, $def, $params);
        return $input;
    }
    // }}}

    // {{{ getFormInput
    /**
     *  ���ꤵ�줿�ե�������ܤ��б�����ե����ॿ�����������
     *
     *  experimental(�Ȥ������Ȥꤢ����-�٤����������̥��饹�˹Ԥ������Ǥ�)
     *
     *  @access public
     *  @todo   form_type�Ƽ��б�/JavaScript�б�...
     */
    function getFormInput($name, $action, $params)
    {
        $def = $this->_getHelperActionFormDef($name, $action);
        if (is_null($def)) {
            return "";
        }

        if (isset($def['form_type']) == false) {
            $def['form_type'] = FORM_TYPE_TEXT;
        }

        if (is_array($def['type'])) {
            $name .= '[]';
        }
        
        switch ($def['form_type']) {
        case FORM_TYPE_BUTTON:
            $input = $this->_getFormInput_Button($name, $def, $params);
            break;
        case FORM_TYPE_CHECKBOX:
            $input = $this->_getFormInput_Checkbox($name, $def, $params);
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
            $input = $this->_getFormInput_Radio($name, $def, $params);
            break;
        case FORM_TYPE_SELECT:
            $input = $this->_getFormInput_Select($name, $def, $params);
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

        return $input;
    }
    // }}}

    // {{{ getFormBlock
    /**
     *  �ե����ॿ�����������(type="form")
     *
     *  @access protected
     */
    function getFormBlock($content, $params)
    {
        $attr = array();

        // action
        if (isset($params['action'])) {
            $attr['action'] = htmlspecialchars($params['action'], ENT_QUOTES);
            unset($params['action']);
        } else {
            $action = basename($_SERVER['PHP_SELF']);
        }

        // method
        if (isset($params['method'])) {
            $attr['method'] = htmlspecialchars($params['method'], ENT_QUOTES);
            unset($params['method']);
        } else {
            $attr['method'] = 'post';
        }

        // enctype
        if (isset($params['enctype'])) {
            $attr['enctype'] = htmlspecialchars($params['enctype'], ENT_QUOTES);
            unset($params['enctype']);
        }

        return $this->_getFormInput_Html('form', $attr, $params, $content, false);
    }
    // }}}

    // {{{ _getHelperActionForm
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
            $this->logger->log(LOG_WARNING,
                'action form for the action [%s] not found.', $action);
            return null;
        }
        $af =& new $form_name($ctl);

        return $af;
    }
    // }}}

    // {{{ _getHelperActionFormDef
    /**
     *  �ե�������ܤ��б�����ե�����������������
     *
     *  @access protected
     */
    function _getHelperActionFormDef($name, $action = null)
    {
        $def = null;
        if (is_null($action)) {
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
        } else {
            if (isset($this->helper_action_form[$action])
                && is_object($this->helper_action_form[$action])) {
                $af =& $this->helper_action_form[$action];
                $def = $af->getDef($name);
            }
        }
        if (is_null($def)) {
            $this->logger->log(LOG_WARNING,
                'form definition [%s] not found in action [%s]', $name, $action);
        }
        return $def;
    }
    // }}}

    // {{{ _getFormInput_Button
    /**
     *  �ե����ॿ�����������(type="button")
     *
     *  @access protected
     */
    function _getFormInput_Button($name, $def, $params)
    {
        $attr = array();
        $attr['type'] = "button";
        $attr['name'] = $name;

        return $this->_getFormInput_Html("input", $attr, $params);
    }
    // }}}

    // {{{ _getFormInput_Checkbox
    /**
     *  �����å��ܥå����������������(type="check")
     *
     *  @access protected
     */
    function _getFormInput_Checkbox($name, $def, $params)
    {
        $source = array();

        // ���ץ����ΰ���(alist)�����
        // XXX: experimental
        if (isset($def['source'])) {
            $source = $def['source'];
        }

        // default�ͤ�����
        if (isset($params['default'])) {
            $current_value = $params['default'];
            unset($params['default']);
        }
        $current_value = to_array($current_value);

        // �����Υ��ѥ졼��
        if (isset($params['separator'])) {
            $separator = $params['separator'];
            unset($params['separator']);
        } else {
            $separator = '';
        }

        $ret = array();
        $i = 1;
        foreach ($source as $key => $value) {
            $attr = array();
            $attr['type'] = 'checkbox';
            $attr['name'] = $name;
            $attr['value'] = $key;
            $attr['id'] = $name . '_' . $i++;
            if (in_array((string) $key, $current_value)) {
                $attr['checked'] = 'checked';
            }

            // <input type="checkbox" />
            $input_tag = $this->_getFormInput_Html('input', $attr, $params, $value);

            // <label for="id">..</label>
            $ret[] = $this->_getFormInput_Html('label', array('id' => $attr['id']),
                                               $params, $input_tag, false);
        }

        return implode($separator, $ret);
    }
    // }}}

    // {{{ _getFormInput_File
    /**
     *  �ե����ॿ�����������(type="file")
     *
     *  @access protected
     */
    function _getFormInput_File($name, $def, $params)
    {
        $attr = array();
        $attr['type'] = "file";
        $attr['name'] = $name;
        $attr['value'] = "";

        return $this->_getFormInput_Html("input", $attr, $params);
    }
    // }}}

    // {{{ _getFormInput_Hidden
    /**
     *  �ե����ॿ�����������(type="hidden")
     *
     *  @access protected
     */
    function _getFormInput_Hidden($name, $def, $params)
    {
        $attr = array();
        $attr['type'] = "hidden";
        $attr['name'] = $name;
        if (isset($params['default'])) {
            $attr['value'] = $params['default'];
            unset($params['default']);
        } else if (isset($params['value'])) {
            $attr['value'] = $params['value'];
            unset($params['value']);
        }

        return $this->_getFormInput_Html("input", $attr, $params);
    }
    // }}}

    // {{{ _getFormInput_Password
    /**
     *  �ե����ॿ�����������(type="password")
     *
     *  @access protected
     */
    function _getFormInput_Password($name, $def, $params)
    {
        $attr = array();
        $attr['type'] = "password";
        $attr['name'] = $name;
        if (isset($params['default'])) {
            $attr['value'] = $params['default'];
            unset($params['default']);
        } else if (isset($params['value'])) {
            $attr['value'] = $params['value'];
            unset($params['value']);
        }

        return $this->_getFormInput_Html("input", $attr, $params);
    }
    // }}}

    // {{{ _getFormInput_Radio
    /**
     *  �饸���ܥ��󥿥����������(type="radio")
     *
     *  @access protected
     */
    function _getFormInput_Radio($name, $def, $params)
    {
        $source = array();

        // ���ץ����ΰ���(alist)�����
        // XXX: experimental
        if (isset($def['source'])) {
            $source = $def['source'];
        }

        // default�ͤ�����
        if (isset($params['default'])) {
            $current_value = $params['default'];
            unset($params['default']);
        }

        // �����Υ��ѥ졼��
        if (isset($params['separator'])) {
            $separator = $params['separator'];
            unset($params['separator']);
        } else {
            $separator = '';
        }

        $ret = array();
        $i = 1;
        foreach ($source as $key => $value) {
            $attr = array();
            $attr['type'] = 'radio';
            $attr['name'] = $name;
            $attr['value'] = $key;
            $attr['id'] = $name . '_' . $i++;
            if ($current_value === (string) $key) {
                $attr['checked'] = 'checked';
            }

            // <input type="radio" />
            $input_tag = $this->_getFormInput_Html('input', $attr, $params, $value);

            // <label for="id">..</label>
            $ret[] = $this->_getFormInput_Html('label', array('id' => $attr['id']),
                                               $params, $input_tag, false);
        }

        return implode($separator, $ret);
    }
    // }}}

    // {{{ _getFormInput_Select
    /**
     *  ���쥯�ȥܥå����������������(type="select")
     *
     *  @access protected
     */
    function _getFormInput_Select($name, $def, $params)
    {
        $source = array();

        // ���ץ����ΰ���(alist)�����
        if (isset($def['source'])) {
            $source = $def['source'];
        } else if (isset($params['actionform'])) {
            $method = sprintf('list%s', str_replace('_', '', $params['action_form']));
            $source = $this->af->$method();
            unset($params['actionform']);
        } else if (isset($params['manager']) && is_array($params['manager'])) {
            list($manager_key, $manager_attr) = $params['manager'];
            $manager =& $this->backend->getManager($manager_key);
            $source = $manager->getAttrList($manager_attr);
            unset($params['manager']);
        } else if (isset($params['callback']) && is_callable($params['callback'])) {
            $source = call_user_func($params['callback']);
            unset($params['callback']);
        }

        // default�ͤ�����
        if (isset($params['default'])) {
            $current_value = $params['default'];
            unset($params['default']);
        }

        // �����Υ��ѥ졼��
        if (isset($params['separator'])) {
            $separator = $params['separator'];
            unset($params['separator']);
        } else {
            $separator = '';
        }

        // select��������Ȥ���
        $contents = array();
        foreach ($source as $key => $value) {
            if ($current_value === (string) $key) {
                $attr = array('value' => $key, 'selected' => null);
            } else {
                $attr = array('value' => $key);
            }
            $contents[] = $this->_getFormInput_Html('option', $attr, $params, $value);
        }

        $attr = array('name' => $name);
        $element = $separator . implode($separator, $contents) . $separator;
        return $this->_getFormInput_Html('select', $attr, $params, $element, false);
    }
    // }}}

    // {{{ _getFormInput_Submit
    /**
     *  �ե����ॿ�����������(type="submit")
     *
     *  @access protected
     */
    function _getFormInput_Submit($name, $def, $params)
    {
        $attr = array();
        $attr['type'] = "submit";
        $attr['name'] = $name;
        if (isset($params['value'])) {
            $attr['value'] = $params['value'];
            unset($params['value']);
        }

        return $this->_getFormInput_Html("input", $attr, $params);
    }
    // }}}

    // {{{ _getFormInput_Textarea
    /**
     *  �ե����ॿ�����������(textarea)
     *
     *  @access protected
     */
    function _getFormInput_Textarea($name, $def, $params)
    {
        $attr = array();
        $attr['name'] = $name;
        if (isset($params['default'])) {
            $element = $params['default'];
            unset($params['default']);
        } else if (isset($params['value'])) {
            $element = $params['value'];
            unset($params['value']);
        }

        return $this->_getFormInput_Html("textarea", $attr, $params, $element);
    }
    // }}}

    // {{{ _getFormInput_Text
    /**
     *  �ե����ॿ�����������(type="text")
     *
     *  @access protected
     */
    function _getFormInput_Text($name, $def, $params)
    {
        $attr = array();
        $attr['type'] = "text";
        $attr['name'] = $name;
        if (isset($params['default'])) {
            $attr['value'] = $params['default'];
            unset($params['default']);
        } else if (isset($params['value'])) {
            $attr['value'] = $params['value'];
            unset($params['value']);
        }
        if (isset($def['max']) && $def['max']) {
            $attr['maxlength'] = $def['max'];
        }

        return $this->_getFormInput_Html("input", $attr, $params);
    }
    // }}}

    // {{{ _getFormInput_Html
    /**
     *  HTML�������������
     *
     *  @access protected
     */
    function _getFormInput_Html($tag, $attr, $user_attr,
                                $element = null, $escape_elemet = true)
    {
        // user defs
        foreach ($user_attr as $key => $value) {
            if ($key == "type" || $key == "name"
                || preg_match('/^[a-z0-9]+$/i', $key) == 0) {
                continue;
            }
            $attr[$key] = $value;
        }

        $r = "<$tag";

        foreach ($attr as $key => $value) {
            if (is_null($value)) {
                $r .= sprintf(' %s', $key);
            } else {
                $r .= sprintf(' %s="%s"', $key, htmlspecialchars($value, ENT_QUOTES));
            }
        }

        if (is_null($element)) {
            $r .= " />";
        } else {
            $r .= sprintf('>%s</%s>', $escape_elemet
                ? htmlspecialchars($element, ENT_QUOTES) : $element, $tag);
        }

        return $r;
    }
    // }}}

    // {{{ _getRenderer
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
    // }}}

    // {{{ _getTemplateEngine
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
        $renderer->setProp('script',
            htmlspecialchars(basename($_SERVER['PHP_SELF']), ENT_QUOTES));
        $renderer->setProp('request_uri',
            htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES));
        $renderer->setProp('config', $this->config->get());

        return $renderer;
    }
    // }}}

    // {{{ _setDefault
    /**
     *  �����ͤ����ꤹ��
     *
     *  @access protected
     *  @param  object  Ethna_Renderer  �����饪�֥�������
     */
    function _setDefault(&$renderer)
    {
    }
    // }}}
}
// }}}
?>
