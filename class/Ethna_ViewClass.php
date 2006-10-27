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

        foreach (array_keys($this->helper_action_form) as $action) {
            $this->addActionFormHelper($action);
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

        $ctl =& Ethna_Controller::getInstance();
        if ($action === $ctl->getCurrentActionName()) {
            $this->helper_action_form[$action] =& $this->af;
            return;
        }

        $form_name = $ctl->getActionFormName($action);
        if ($form_name == null) {
            $this->logger->log(LOG_WARNING,
                'action form for the action [%s] not found.', $action);
            return;
        }

        $this->helper_action_form[$action] =& new $form_name($ctl);
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

    // {{{ _getHelperActionForm
    /**
     *  ���������ե����४�֥�������(helper)���������
     *  $action == null �� $name �����ꤵ��Ƥ���Ȥ��ϡ�$name�������
     *  �ޤ��Τ�õ��
     *
     *  @access protected
     *  @param  string  action  �������륢�������̾
     *  @param  string  name    �������Ƥ��뤳�Ȥ���Ԥ���ե�����̾
     *  @return object  Ethna_ActionForm�ޤ��ϷѾ����֥�������
     */
    function &_getHelperActionForm($action = null, $name = null)
    {
        // $action �����ꤵ��Ƥ�����
        if ($action !== null) {
            if (isset($this->helper_action_form[$action])
                && is_object($this->helper_action_form[$action])) {
                return $this->helper_action_form[$action];
            } else {
                $this->logger->log(LOG_WARNING,
                    'helper action form for action [%s] not found',
                    $action);
                return null;
            }
        }

        // �ǽ�� $this->af ��Ĵ�٤�
        $def = $this->af->getDef($name);
        if ($def !== null) {
            return $this->af;
        }

        // $this->helper_action_form ����Ĵ�٤�
        foreach (array_keys($this->helper_action_form) as $action) {
            if (is_object($this->helper_action_form[$action]) === false) {
                continue;
            }
            $af =& $this->helper_action_form[$action];
            $def = $af->getDef($name);
            if (is_null($def) === false) {
                return $af;
            }
        }

        // ���դ���ʤ��ä�
        $this->logger->log(LOG_WARNING,
            'action form defining form [%s] not found', $name);
        return null;
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
        $af =& $this->_getHelperActionForm($action, $name);
        if ($af === null) {
            return $name;
        }

        $def = $af->getDef($name);
        if ($def === null || isset($def['name']) === false) {
            return $name;
        }

        return $def['name'];
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
     *  @access public
     *  @todo   JavaScript�б�
     */
    function getFormInput($name, $action, $params)
    {
        $af =& $this->_getHelperActionForm($action, $name);
        if ($af === null) {
            return '';
        }

        $def = $af->getDef($name);
        if ($def === null) {
            return '';
        }

        if (isset($def['form_type']) == false) {
            $def['form_type'] = FORM_TYPE_TEXT;
        }

        switch ($def['form_type']) {
        case FORM_TYPE_BUTTON:
            $input = $this->_getFormInput_Button($name, $def, $params);
            break;

        case FORM_TYPE_CHECKBOX:
            $def['option'] = $this->_getSelectorOptions($af, $def, $params);
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
            $def['option'] = $this->_getSelectorOptions($af, $def, $params);
            $input = $this->_getFormInput_Radio($name, $def, $params);
            break;

        case FORM_TYPE_SELECT:
            $def['option'] = $this->_getSelectorOptions($af, $def, $params);
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
            $attr['action'] = $params['action'];
            unset($params['action']);
        } else {
            $action = basename($_SERVER['PHP_SELF']);
        }

        // method
        if (isset($params['method'])) {
            $attr['method'] = $params['method'];
            unset($params['method']);
        } else {
            $attr['method'] = 'post';
        }

        // enctype
        if (isset($params['enctype'])) {
            $attr['enctype'] = $params['enctype'];
            unset($params['enctype']);
        }

        return $this->_getFormInput_Html('form', $attr, $params, $content, false);
    }
    // }}}

    // {{{ _getSelectorOptions
    /**
     *  select, radio, checkbox ���������������
     *  ($def, $params��񤭴����뤳�Ȥ����)
     *
     *  @access protected
     */
    function _getSelectorOptions(&$af, &$def, &$params)
    {
        // $params, $def �ν��Ĵ�٤�
        $source = null;
        if (isset($params['option'])) {
            $source = $params['option'];
            unset($params['option']);
        } else if (isset($def['option'])) {
            $source = $def['option'];
        }

        // ̤��� or ����Ѥߤξ��Ϥ��Τޤ�
        if ($source === null) {
            return null;
        } else if (is_array($source)) {
            return $source;
        }
        
        // ���������
        $options = null;
        $split = preg_split('/%s*,%s*/', $source, 2, PREG_SPLIT_NO_EMPTY);
        if (count($split) == 1) {
            // ���������ե����फ�����
            $method_or_property = $split[0];
            if (method_exists($af, $method_or_property)) {
                $options = $af->$method_or_property();
            } else {
                $options = $af->$method_or_property;
            }
        } else {
            // �ޥ͡����㤫�����
            $mgr =& $this->backend->getManager($split[0]);
            $options = $mgr->getAttrList($split[1]);
        }

        if (is_array($options) === false) {
            $this->logger->log(LOG_WARNING,
                'selector option is not valid. [actionform=%s, option=%s]',
                get_class($af), $source);
            return null;
        }

        return $options;
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
        $attr['name'] = is_array($def['type']) ? $name .'[]' : $name;

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
        // ���ץ����ΰ���(alist)�����
        if (isset($def['option']) === false
            || is_array($def['option']) === false) {
            return '';
        }
        $options = $def['option'];

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
        $attr = array();
        $attr['type'] = 'checkbox';
        $attr['name'] = is_array($def['type']) ? $name .'[]' : $name;
        foreach ($options as $key => $value) {
            $attr['value'] = $key;
            $attr['id'] = $name . '_' . $i++;
            if (in_array((string) $key, $current_value)) {
                $attr['checked'] = 'checked';
            } else {
                unset($attr['checked']);
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
        $attr['name'] = is_array($def['type']) ? $name .'[]' : $name;
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
        $attr['name'] = is_array($def['type']) ? $name .'[]' : $name;
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
        $attr['name'] = is_array($def['type']) ? $name .'[]' : $name;
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
        // ���ץ����ΰ���(alist)�����
        if (isset($def['option']) === false
            || is_array($def['option']) === false) {
            return '';
        }
        $options = $def['option'];

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
        $attr = array();
        $attr['type'] = 'radio';
        $attr['name'] = is_array($def['type']) ? $name .'[]' : $name;
        foreach ($options as $key => $value) {
            $attr['value'] = $key;
            $attr['id'] = $name . '_' . $i++;
            if ($current_value === (string) $key) {
                $attr['checked'] = 'checked';
            } else {
                unset($attr['checked']);
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
        // ���ץ����ΰ���(alist)�����
        if (isset($def['option']) === false
            || is_array($def['option']) === false) {
            return '';
        }
        $options = $def['option'];

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
        $attr = array();
        foreach ($options as $key => $value) {
            $attr['value'] = $key;
            if ($current_value === (string) $key) {
                $attr['selected'] = 'selected';
            } else {
                unset($attr['selected']);
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
        $attr['name'] = is_array($def['type']) ? $name .'[]' : $name;
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
        $attr['name'] = is_array($def['type']) ? $name .'[]' : $name;
        $element = '';
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
        $attr['name'] = is_array($def['type']) ? $name .'[]' : $name;
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
                                $element = null, $escape_element = true)
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

        if ($element === null) {
            $r .= " />";
        } else if ($escape_element) {
            $r .= sprintf('>%s</%s>', htmlspecialchars($element, ENT_QUOTES), $tag);
        } else {
            $r .= sprintf('>%s</%s>', $element, $tag);
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
