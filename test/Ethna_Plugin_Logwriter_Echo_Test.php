<?php
/**
 *  Ethna_Plugin_Logwriter_Echo_Test.php
 */

/**
 *  Ethna_Plugin_Logwriter_Echo���饹�Υƥ��ȥ�����
 *
 *  @access public
 */
class Ethna_Plugin_Logwriter_Echo_Test extends UnitTestCase
{
    function testLogwriterEcho()
    {
		$ctl =& Ethna_Controller::getInstance();
		$plugin =& $ctl->getPlugin();
		$lw = $plugin->getPlugin('Logwriter', 'Echo');

		$option = array(
						'ident' => 'testident',
						'facility' => 'mail',
						);
		$lw->setOption($option);
		$message = 'comment';

		$level_array = array(
							 LOG_EMERG,
							 LOG_ALERT,
							 LOG_CRIT,
							 LOG_ERR,
							 LOG_WARNING,
							 LOG_NOTICE,
							 LOG_INFO,
							 LOG_DEBUG,
							 );

		foreach($level_array as $level){
			ob_start();			// ���󥽡���ؤν��Ϥ򥭥�ץ��㳫��
			// �ؿ����֤�ʸ����˲��ԥ�����Ϳ������
			$funcout = $lw->log($level, $message)
				. sprintf("%s", $ctl->getGateway() != GATEWAY_WWW ? "" : "<br />");
			$stdout = trim(ob_get_contents());
			$this->assertEqual($funcout, $stdout);
			ob_end_clean();		// ���󥽡���ؤν��Ϥ򥭥�ץ��㽪λ
		}
    }
}
?>
