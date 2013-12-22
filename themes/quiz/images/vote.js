//判断输入表单内容
function checkFormsInput(n){
	var objForm=document.forms['votebox'];
	var nID=objForm.elements['id[]'];
	var i=0,j=0;
	for(i=0;i<nID.length;i++){if(nID[i].checked) j++;}
	if(!j){alert('请选择投票项目!');nID[0].focus();return false;}
	if(j>n){alert('对不起，最多允许选择'+n+'项!');nID[0].focus();return false;}
	return true;
}