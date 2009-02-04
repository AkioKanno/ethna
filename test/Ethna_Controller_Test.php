<?php
// vim: foldmethod=marker
/**
 *  Ethna_Controller_Test.php
 *
 *  @author     Yoshinari Takaoka <takaoka@beatcraft.com>
 *  @version    $Id$
 */

//{{{    Ethna_Controller_Test
/**
 *  Test Case For Ethna_Controller_Test 
 *
 *  @access public
 */
class Ethna_Controller_Test extends Ethna_UnitTestBase
{
    var $test_ctl;

    function setUp()
    {
        $this->test_ctl =& new Ethna_Controller();
    }

    function tearDown()
    {
        unset($GLOBALS['_Ethna_controller']);
    }

    // {{{ checkAppId
    function test_checkAppId()
    {
        //  ͽ���(app, ethna)����������
        //  ����ˤĤ��Ƥ���ʸ������ʸ������̤��ʤ�
        $r = $this->test_ctl->checkAppId('ethna');
        $this->assertTrue(Ethna::isError($r));

        $r = $this->test_ctl->checkAppId('EthNa');
        $this->assertTrue(Ethna::isError($r));

        $r = $this->test_ctl->checkAppId('ETHNA');
        $this->assertTrue(Ethna::isError($r));

        $r = $this->test_ctl->checkAppId('app');
        $this->assertTrue(Ethna::isError($r));

        $r = $this->test_ctl->checkAppId('ApP');
        $this->assertTrue(Ethna::isError($r));

        $r = $this->test_ctl->checkAppId('APP');
        $this->assertTrue(Ethna::isError($r));

        //  �����ǻϤޤäƤ�����
        $r = $this->test_ctl->checkAppId('1');
        $this->assertTrue(Ethna::isError($r));

        $r = $this->test_ctl->checkAppId('0abcd');
        $this->assertTrue(Ethna::isError($r));

        //  �Ϥ᤬�������������������
        $r = $this->test_ctl->checkAppId('_');
        $this->assertTrue(Ethna::isError($r));

        $r = $this->test_ctl->checkAppId('_abcd');
        $this->assertTrue(Ethna::isError($r));

        //  ��ʸ���Ǥ�ѿ����ʳ��������������
        $r = $this->test_ctl->checkAppId('ab;@e');
        $this->assertTrue(Ethna::isError($r));

        $r = $this->test_ctl->checkAppId('@bcde');
        $this->assertTrue(Ethna::isError($r));

        $r = $this->test_ctl->checkAppId('abcd:');
        $this->assertTrue(Ethna::isError($r));

        //  �����ѿ����Ǥ����OK
        $r = $this->test_ctl->checkAppId('abcd');
        $this->assertFalse(Ethna::isError($r));
    }
    // }}}
}
// }}}

?>
