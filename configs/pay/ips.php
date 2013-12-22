<?php
//ips支付相关参数

$jieqiPayset['ips']['payid']='000015';  //商户编号

$jieqiPayset['ips']['paykey']='123456';  //密钥值

$jieqiPayset['ips']['strcert']="GDgLwwdK270Qj1w4xho8lyTpRQZV9Jm5x4NwWOTThUa4fMhEBK9jOXFrKRT6xhlJuU2FEa89ov0ryyjfJuuPkcGzO5CeVx5ZIrkkt1aBlZV36ySvHOMcNv8rncRiy3DQ";	/**测试证书**/

$jieqiPayset['ips']['foreignpayid']='12345678';  //外卡商户编号

$jieqiPayset['ips']['foreignpaykey']='xxxxxxxx';  //外卡密钥

$jieqiPayset['ips']['payurl']='http://pay.ips.com.cn/ipayment.aspx';  //提交到对方的网址

$jieqiPayset['ips']['payreturn']='http://www.domain.com/modules/pay/ipsreturn.php';  //接收返回的地址 (www.domain.com 是指你的网址)

//这个参数不设置的话，用户可以购买任意值的虚拟货币，按照一元钱100币折算。如果设置了这个参数，则购买金额只能按照里面的设置，对应的也金钱按对应关系折算，如 '1000'=>'10' 是指 1000虚拟币需要10元
$jieqiPayset['ips']['paylimit']=array('1000'=>'10', '2000'=>'20', '3000'=>'30', '5000'=>'50', '10000'=>'100');

$jieqiPayset['ips']['moneytype']='0';  //0 人民币 1表示美元

$jieqiPayset['ips']['paysilver']='0';  //0 表示冲值成金币 1表示银币

$jieqiPayset['ips']['Lang']='GB';  //GB 中文 EN 英文

$jieqiPayset['ips']['RetEncodeType']='12';  //返回验证方式 10-老接口 11-MD5WithRSA 12-MD5

$jieqiPayset['ips']['OrderEncodeType']='2';  //提交验证方式 0-无加密  2-MD5

$jieqiPayset['ips']['Rettype']='0';  //返回方式 0－不选 1－server to server

$jieqiPayset['ips']['addvars']=array();  //附加参数
?>