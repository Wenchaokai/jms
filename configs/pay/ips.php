<?php
//ips֧����ز���

$jieqiPayset['ips']['payid']='000015';  //�̻����

$jieqiPayset['ips']['paykey']='123456';  //��Կֵ

$jieqiPayset['ips']['strcert']="GDgLwwdK270Qj1w4xho8lyTpRQZV9Jm5x4NwWOTThUa4fMhEBK9jOXFrKRT6xhlJuU2FEa89ov0ryyjfJuuPkcGzO5CeVx5ZIrkkt1aBlZV36ySvHOMcNv8rncRiy3DQ";	/**����֤��**/

$jieqiPayset['ips']['foreignpayid']='12345678';  //�⿨�̻����

$jieqiPayset['ips']['foreignpaykey']='xxxxxxxx';  //�⿨��Կ

$jieqiPayset['ips']['payurl']='http://pay.ips.com.cn/ipayment.aspx';  //�ύ���Է�����ַ

$jieqiPayset['ips']['payreturn']='http://www.domain.com/modules/pay/ipsreturn.php';  //���շ��صĵ�ַ (www.domain.com ��ָ�����ַ)

//������������õĻ����û����Թ�������ֵ��������ң�����һԪǮ100�����㡣������������������������ֻ�ܰ�����������ã���Ӧ��Ҳ��Ǯ����Ӧ��ϵ���㣬�� '1000'=>'10' ��ָ 1000�������Ҫ10Ԫ
$jieqiPayset['ips']['paylimit']=array('1000'=>'10', '2000'=>'20', '3000'=>'30', '5000'=>'50', '10000'=>'100');

$jieqiPayset['ips']['moneytype']='0';  //0 ����� 1��ʾ��Ԫ

$jieqiPayset['ips']['paysilver']='0';  //0 ��ʾ��ֵ�ɽ�� 1��ʾ����

$jieqiPayset['ips']['Lang']='GB';  //GB ���� EN Ӣ��

$jieqiPayset['ips']['RetEncodeType']='12';  //������֤��ʽ 10-�Ͻӿ� 11-MD5WithRSA 12-MD5

$jieqiPayset['ips']['OrderEncodeType']='2';  //�ύ��֤��ʽ 0-�޼���  2-MD5

$jieqiPayset['ips']['Rettype']='0';  //���ط�ʽ 0����ѡ 1��server to server

$jieqiPayset['ips']['addvars']=array();  //���Ӳ���
?>