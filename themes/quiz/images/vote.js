//�ж����������
function checkFormsInput(n){
	var objForm=document.forms['votebox'];
	var nID=objForm.elements['id[]'];
	var i=0,j=0;
	for(i=0;i<nID.length;i++){if(nID[i].checked) j++;}
	if(!j){alert('��ѡ��ͶƱ��Ŀ!');nID[0].focus();return false;}
	if(j>n){alert('�Բ����������ѡ��'+n+'��!');nID[0].focus();return false;}
	return true;
}