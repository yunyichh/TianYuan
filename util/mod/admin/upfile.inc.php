<?php
$acts=array('index'=>true,'save'=>true);
$c=isset($_REQUEST['c'])?trim($_REQUEST['c']):'list';
if(!isset($acts[$c])){
	$c='index';
}
$tb_rule = isset($_REQUEST['tb']) ? intval($_REQUEST['tb']) : 3;
$tb_rtn = isset($_REQUEST['rtn']) ? trim($_REQUEST['rtn']) :'';
$create_thmb=true;
if($tb_rule==2){
	$w=360;
	$w1=$h1=$h=200;
}elseif($tb_rule==1){
	$w=$h=344;
	$w1=$h1=200;
}elseif($tb_rule==4){
	$create_thmb=false;
}elseif($tb_rule==5){
	$w=60;
	$h=60;
	$w1=60;
	$h1=80;
}elseif($tb_rule==6){
	$w=150;
	$h=60;
	$w1=60;
	$h1=80;
}

$spuer_w = $spuer_h = 1200;

switch($c) {
	case 'index':
		if($ismobile||$_GET['t']==11){
		include T('func/m','upfile_card');
		}else{
			include T('func','upfile_card');
		}
		break;
	case 'save':
		$url	= '/?m=admin&a=upfile&tb='.$tb_rule;
		if(!empty($tb_rtn)){
			$url.='&rtn='.$tb_rtn;
		}
		$f = $_FILES['file'];
		if( $f['size']>1024*600 ){
			$msg	= '图片大小超过系统限制（600KB）';
			include T('func','alert');
			exit;
		}

		if(!empty($f)){
			$ext = strtolower(array_pop(explode('.', $f['name'])));
			$allow_img=array('gif','png','jpg','jpeg');
			$msg_001='图片只允许JPG,GIF,PNG格式';

			if(!in_array($ext, $allow_img)){
				$msg = $msg_001;
			}else{
				$d =PHPCMS_ROOT.APP_DIR.'/static/temp/';
				$filemain = date('YmdHis').mt_rand(100,999).'_real';
				$filename = $filemain.'.'.$ext;
				$real_name=$d.$filename;
				$realfilename=str_replace(PHPCMS_ROOT.APP_DIR.'/static','/static',$real_name);
				if(move_uploaded_file($f['tmp_name'], $real_name)){
					reStore($real_name,$spuer_w,$spuer_h);
					if($create_thmb){
						if($tb_rule>1){
							$file_a=str_replace('_real','_'.$w.'_'.$h,$real_name);
							getThumb($real_name,$w,$h,$file_a);
						}
						if($tb_rule<3){
							$file_b=str_replace('_real','_'.$w1.'_'.$h1,$real_name);
							getThumb($real_name,$w1,$h1,$file_b);
						}
						if($tb_rule==2){
							$file_c=str_replace('_real','_480_240',$real_name);
							getThumb($real_name,480,240,$file_c);
						}
						if($tb_rule==5){
							$file_b=str_replace('_real','_'.$w1.'_'.$h1,$real_name);
							getThumb($real_name,$w1,$h1,$file_b);
						}
						
					}
					echo '<font color="green" style="font-size:14px;">图片上传成功!</font>';
					if(!empty($tb_rtn)){
						echo '<script type="text/javascript">
								window.parent.setImg("'.$realfilename.'","'.$tb_rtn.'");';
					}else{
						echo '<script type="text/javascript">
								window.parent.setImg("'.$realfilename.'");';
					}
					echo '</script>';
				}else{
					$msg='文件上传失败！';
				}
			}
		}
		include T('func','alert');
		break;
}