<?php
/** Adminer Editor - Compact database editor
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2009 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.3.0
*/namespace
Adminer;const
VERSION="5.3.0";error_reporting(24575);set_error_handler(function($Xb,$Yb){return!!preg_match('~^Undefined (array key|offset|index)~',$Yb);},E_WARNING|E_NOTICE);$oc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($oc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Rg=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Rg)$$X=$Rg;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($h=null){return($h?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$nb=adminer()->credentials();$H=Driver::connect($nb[0],$nb[1],$nb[2]);return(is_object($H)?$H:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$Fd=substr($t,-1);return
str_replace($Fd.$Fd,$Fd,substr($t,1,-1));}function
q($Q){return
connection()->quote($Q);}function
escape_string($X){return
substr(q($X),1,-1);}function
idx($na,$w,$j=null){return($na&&array_key_exists($w,$na)?$na[$w]:$j);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$jf,$oc=false){if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){while(list($w,$X)=each($jf)){foreach($X
as$yd=>$W){unset($jf[$w][$yd]);if(is_array($W)){$jf[$w][stripslashes($yd)]=$W;$jf[]=&$jf[$w][stripslashes($yd)];}else$jf[$w][stripslashes($yd)]=($oc?$W:stripslashes($W));}}}}function
bracket_escape($t,$wa=false){static$Bg=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($t,($wa?array_flip($Bg):$Bg));}function
min_version($dh,$Qd="",$h=null){$h=connection($h);$Nf=$h->server_info;if($Qd&&preg_match('~([\d.]+)-MariaDB~',$Nf,$z)){$Nf=$z[1];$dh=$Qd;}return$dh&&version_compare($Nf,$dh)>=0;}function
charset(Db$g){return(min_version("5.5.3",0,$g)?"utf8mb4":"utf8");}function
ini_bool($kd){$X=ini_get($kd);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
sid(){static$H;if($H===null)$H=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$H;}function
set_password($ch,$M,$V,$D){$_SESSION["pwds"][$ch][$M][$V]=($_COOKIE["adminer_key"]&&is_string($D)?array(encrypt_string($D,$_COOKIE["adminer_key"])):$D);}function
get_password(){$H=get_session("pwds");if(is_array($H))$H=($_COOKIE["adminer_key"]?decrypt_string($H[0],$_COOKIE["adminer_key"]):false);return$H;}function
get_val($F,$l=0,$db=null){$db=connection($db);$G=$db->query($F);if(!is_object($G))return
false;$I=$G->fetch_row();return($I?$I[$l]:false);}function
get_vals($F,$d=0){$H=array();$G=connection()->query($F);if(is_object($G)){while($I=$G->fetch_row())$H[]=$I[$d];}return$H;}function
get_key_vals($F,$h=null,$Qf=true){$h=connection($h);$H=array();$G=$h->query($F);if(is_object($G)){while($I=$G->fetch_row()){if($Qf)$H[$I[0]]=$I[1];else$H[]=$I[0];}}return$H;}function
get_rows($F,$h=null,$k="<p class='error'>"){$db=connection($h);$H=array();$G=$db->query($F);if(is_object($G)){while($I=$G->fetch_assoc())$H[]=$I;}elseif(!$G&&!$h&&$k&&(defined('Adminer\PAGE_HEADER')||$k=="-- "))echo$k.error()."\n";return$H;}function
unique_array($I,array$v){foreach($v
as$u){if(preg_match("~PRIMARY|UNIQUE~",$u["type"])){$H=array();foreach($u["columns"]as$w){if(!isset($I[$w]))continue
2;$H[$w]=$I[$w];}return$H;}}}function
escape_key($w){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$w,$z))return$z[1].idf_escape(idf_unescape($z[2])).$z[3];return
idf_escape($w);}function
where(array$Z,array$m=array()){$H=array();foreach((array)$Z["where"]as$w=>$X){$w=bracket_escape($w,true);$d=escape_key($w);$l=idx($m,$w,array());$lc=$l["type"];$H[]=$d.(JUSH=="sql"&&$lc=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($lc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($l,q($X)))));if(JUSH=="sql"&&preg_match('~char|text~',$lc)&&preg_match("~[^ -@]~",$X))$H[]="$d = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$w)$H[]=escape_key($w)." IS NULL";return
implode(" AND ",$H);}function
where_check($X,array$m=array()){parse_str($X,$Ma);remove_slashes(array(&$Ma));return
where($Ma,$m);}function
where_link($r,$d,$Y,$ze="="){return"&where%5B$r%5D%5Bcol%5D=".urlencode($d)."&where%5B$r%5D%5Bop%5D=".urlencode(($Y!==null?$ze:"IS NULL"))."&where%5B$r%5D%5Bval%5D=".urlencode($Y);}function
convert_fields(array$e,array$m,array$K=array()){$H="";foreach($e
as$w=>$X){if($K&&!in_array(idf_escape($w),$K))continue;$oa=convert_field($m[$w]);if($oa)$H
.=", $oa AS ".idf_escape($w);}return$H;}function
cookie($_,$Y,$Kd=2592000){header("Set-Cookie: $_=".urlencode($Y).($Kd?"; expires=".gmdate("D, d M Y H:i:s",time()+$Kd)." GMT":"")."; path=".preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]).(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_settings($kb){parse_str($_COOKIE[$kb],$Rf);return$Rf;}function
get_setting($w,$kb="adminer_settings"){$Rf=get_settings($kb);return$Rf[$w];}function
save_settings(array$Rf,$kb="adminer_settings"){$Y=http_build_query($Rf+get_settings($kb));cookie($kb,$Y);$_COOKIE[$kb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($vc=false){$Yg=ini_bool("session.use_cookies");if(!$Yg||$vc){session_write_close();if($Yg&&@ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$X){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($ch,$M,$V,$i=null){$Vg=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($i!==null?"db|":"").($ch=='mssql'||$ch=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Vg,$z);return"$z[1]?".(sid()?SID."&":"").($ch!="server"||$M!=""?urlencode($ch)."=".urlencode($M)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($V).($i!=""?"&db=".urlencode($i):"").($z[2]?"&$z[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($Md,$ae=null){if($ae!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($Md!==null?$Md:$_SERVER["REQUEST_URI"]))][]=$ae;}if($Md!==null){if($Md=="")$Md=".";header("Location: $Md");exit;}}function
query_redirect($F,$Md,$ae,$rf=true,$cc=true,$hc=false,$sg=""){if($cc){$bg=microtime(true);$hc=!connection()->query($F);$sg=format_time($bg);}$Yf=($F?adminer()->messageQuery($F,$sg,$hc):"");if($hc){adminer()->error
.=error().$Yf.script("messagesPrint();")."<br>";return
false;}if($rf)redirect($Md,$ae.$Yf);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($F){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(preg_match('~;$~',$F)?"DELIMITER ;;\n$F;\nDELIMITER ":$F).";";return
connection()->query($F);}function
apply_queries($F,array$T,$Zb='Adminer\table'){foreach($T
as$R){if(!queries("$F ".$Zb($R)))return
false;}return
true;}function
queries_redirect($Md,$ae,$rf){$mf=implode("\n",Queries::$queries);$sg=format_time(Queries::$start);return
query_redirect($mf,$Md,$ae,$rf,false,!$rf,$sg);}function
format_time($bg){return
lang(0,max(0,microtime(true)-$bg));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($Ne=""){return
substr(preg_replace("~(?<=[?&])($Ne".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($w,$vb=false,$yb=""){$mc=$_FILES[$w];if(!$mc)return
null;foreach($mc
as$w=>$X)$mc[$w]=(array)$X;$H='';foreach($mc["error"]as$w=>$k){if($k)return$k;$_=$mc["name"][$w];$zg=$mc["tmp_name"][$w];$ib=file_get_contents($vb&&preg_match('~\.gz$~',$_)?"compress.zlib://$zg":$zg);if($vb){$bg=substr($ib,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$bg))$ib=iconv("utf-16","utf-8",$ib);elseif($bg=="\xEF\xBB\xBF")$ib=substr($ib,3);}$H
.=$ib;if($yb)$H
.=(preg_match("($yb\\s*\$)",$ib)?"":$yb)."\n\n";}return$H;}function
upload_error($k){$Wd=($k==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($k?lang(1).($Wd?" ".lang(2,$Wd):""):lang(3));}function
repeat_pattern($Ve,$Id){return
str_repeat("$Ve{0,65535}",$Id/65535)."$Ve{0,".($Id%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",lang(4)),preg_split('~~u',lang(5),-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$ic=false){$H=table_status($R,$ic);return($H?reset($H):array("Name"=>$R));}function
column_foreign_keys($R){$H=array();foreach(adminer()->foreignKeys($R)as$o){foreach($o["source"]as$X)$H[$X][]=$o;}return$H;}function
fields_from_edit(){$H=array();foreach((array)$_POST["field_keys"]as$w=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$w];$_POST["fields"][$X]=$_POST["field_vals"][$w];}}foreach((array)$_POST["fields"]as$w=>$X){$_=bracket_escape($w,true);$H[$_]=array("field"=>$_,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($w==driver()->primary),);}return$H;}function
dump_headers($ad,$ge=false){$H=adminer()->dumpHeaders($ad,$ge);$Je=$_POST["output"];if($Je!="text")header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($ad).".$H".($Je!="file"&&preg_match('~^[0-9a-z]+$~',$Je)?".$Je":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$H;}function
dump_csv(array$I){foreach($I
as$w=>$X){if(preg_match('~["\n,;\t]|^0|\.\d*0$~',$X)||$X==="")$I[$w]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$I)."\r\n";}function
apply_sql_function($q,$d){return($q?($q=="unixepoch"?"DATETIME($d, '$q')":($q=="count distinct"?"COUNT(DISTINCT ":strtoupper("$q("))."$d)"):$d);}function
get_temp_dir(){$H=ini_get("upload_tmp_dir");if(!$H){if(function_exists('sys_get_temp_dir'))$H=sys_get_temp_dir();else{$n=@tempnam("","");if(!$n)return'';$H=dirname($n);unlink($n);}}return$H;}function
file_open_lock($n){if(is_link($n))return;$p=@fopen($n,"c+");if(!$p)return;chmod($n,0660);if(!flock($p,LOCK_EX)){fclose($p);return;}return$p;}function
file_write_unlock($p,$sb){rewind($p);fwrite($p,$sb);ftruncate($p,strlen($sb));file_unlock($p);}function
file_unlock($p){flock($p,LOCK_UN);fclose($p);}function
first(array$na){return
reset($na);}function
password_file($lb){$n=get_temp_dir()."/adminer.key";if(!$lb&&!file_exists($n))return'';$p=file_open_lock($n);if(!$p)return'';$H=stream_get_contents($p);if(!$H){$H=rand_string();file_write_unlock($p,$H);}else
file_unlock($p);return$H;}function
rand_string(){return
md5(uniqid(strval(mt_rand()),true));}function
select_value($X,$y,array$l,$qg){if(is_array($X)){$H="";foreach($X
as$yd=>$W)$H
.="<tr>".($X!=array_values($X)?"<th>".h($yd):"")."<td>".select_value($W,$y,$l,$qg);return"<table>$H</table>";}if(!$y)$y=adminer()->selectLink($X,$l);if($y===null){if(is_mail($X))$y="mailto:$X";if(is_url($X))$y=$X;}$H=adminer()->editVal($X,$l);if($H!==null){if(!is_utf8($H))$H="\0";elseif($qg!=""&&is_shortable($l))$H=shorten_utf8($H,max(0,+$qg));else$H=h($H);}return
adminer()->selectVal($H,$y,$l,$X);}function
is_mail($Ob){$pa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Eb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Ve="$pa+(\\.$pa+)*@($Eb?\\.)+$Eb";return
is_string($Ob)&&preg_match("(^$Ve(,\\s*$Ve)*\$)i",$Ob);}function
is_url($Q){$Eb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^(https?)://($Eb?\\.)+$Eb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable(array$l){return
preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea~',$l["type"]);}function
count_rows($R,array$Z,$td,array$Gc){$F=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($td&&(JUSH=="sql"||count($Gc)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$Gc).")$F":"SELECT COUNT(*)".($td?" FROM (SELECT 1$F GROUP BY ".implode(", ",$Gc).") x":$F));}function
slow_query($F){$i=adminer()->database();$tg=adminer()->queryTimeout();$Tf=driver()->slowQuery($F,$tg);$h=null;if(!$Tf&&support("kill")){$h=connect();if($h&&($i==""||$h->select_db($i))){$Ad=get_val(connection_id(),0,$h);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$Ad&token=".get_token()."'); }, 1000 * $tg);");}}ob_flush();flush();$H=@get_key_vals(($Tf?:$F),$h,false);if($h){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$H;}function
get_token(){$pf=rand(1,1e6);return($pf^$_SESSION["token"]).":$pf";}function
verify_token(){list($_g,$pf)=explode(":",$_POST["token"]);return($pf^$_SESSION["token"])==$_g;}function
lzw_decompress($Ca){$Bb=256;$Da=8;$Ua=array();$xf=0;$yf=0;for($r=0;$r<strlen($Ca);$r++){$xf=($xf<<8)+ord($Ca[$r]);$yf+=8;if($yf>=$Da){$yf-=$Da;$Ua[]=$xf>>$yf;$xf&=(1<<$yf)-1;$Bb++;if($Bb>>$Da)$Da++;}}$Ab=range("\0","\xFF");$H="";$kh="";foreach($Ua
as$r=>$Ta){$Nb=$Ab[$Ta];if(!isset($Nb))$Nb=$kh.$kh[0];$H
.=$Nb;if($r)$Ab[]=$kh.$Nb[0];$kh=$Nb;}return$H;}function
script($Vf,$Ag="\n"){return"<script".nonce().">$Vf</script>$Ag";}function
script_src($Wg,$wb=false){return"<script src='".h($Wg)."'".nonce().($wb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($_,$Y=""){return"<input type='hidden' name='".h($_)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace("\0","&#0;",htmlspecialchars($Q,ENT_QUOTES,'utf-8'));}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($_,$Y,$Oa,$Bd="",$xe="",$Ra="",$Dd=""){$H="<input type='checkbox' name='$_' value='".h($Y)."'".($Oa?" checked":"").($Dd?" aria-labelledby='$Dd'":"").">".($xe?script("qsl('input').onclick = function () { $xe };",""):"");return($Bd!=""||$Ra?"<label".($Ra?" class='$Ra'":"").">$H".h($Bd)."</label>":$H);}function
optionlist($B,$Hf=null,$Zg=false){$H="";foreach($B
as$yd=>$W){$Be=array($yd=>$W);if(is_array($W)){$H
.='<optgroup label="'.h($yd).'">';$Be=$W;}foreach($Be
as$w=>$X)$H
.='<option'.($Zg||is_string($w)?' value="'.h($w).'"':'').($Hf!==null&&($Zg||is_string($w)?(string)$w:$X)===$Hf?' selected':'').'>'.h($X);if(is_array($W))$H
.='</optgroup>';}return$H;}function
html_select($_,array$B,$Y="",$we="",$Dd=""){static$Bd=0;$Cd="";if(!$Dd&&substr($B[""],0,1)=="("){$Bd++;$Dd="label-$Bd";$Cd="<option value='' id='$Dd'>".h($B[""]);unset($B[""]);}return"<select name='".h($_)."'".($Dd?" aria-labelledby='$Dd'":"").">".$Cd.optionlist($B,$Y)."</select>".($we?script("qsl('select').onchange = function () { $we };",""):"");}function
html_radios($_,array$B,$Y="",$L=""){$H="";foreach($B
as$w=>$X)$H
.="<label><input type='radio' name='".h($_)."' value='".h($w)."'".($w==$Y?" checked":"").">".h($X)."</label>$L";return$H;}function
confirm($ae="",$If="qsl('input')"){return
script("$If.onclick = () => confirm('".($ae?js_escape($ae):lang(6))."');","");}function
print_fieldset($s,$Hd,$gh=false){echo"<fieldset><legend>","<a href='#fieldset-$s'>$Hd</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$s');",""),"</legend>","<div id='fieldset-$s'".($gh?"":" class='hidden'").">\n";}function
bold($Ea,$Ra=""){return($Ea?" class='active $Ra'":($Ra?" class='$Ra'":""));}function
js_escape($Q){return
addcslashes($Q,"\r\n'\\/");}function
pagination($C,$qb){return" ".($C==$qb?$C+1:'<a href="'.h(remove_from_uri("page").($C?"&page=$C".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($C+1)."</a>");}function
hidden_fields(array$jf,array$dd=array(),$ef=''){$H=false;foreach($jf
as$w=>$X){if(!in_array($w,$dd)){if(is_array($X))hidden_fields($X,array(),$w);else{$H=true;echo
input_hidden(($ef?$ef."[$w]":$w),$X);}}}return$H;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
enum_input($U,$ra,array$l,$Y,$Rb=null){preg_match_all("~'((?:[^']|'')*)'~",$l["length"],$Td);$H=($Rb!==null?"<label><input type='$U'$ra value='$Rb'".((is_array($Y)?in_array($Rb,$Y):$Y===$Rb)?" checked":"")."><i>".lang(7)."</i></label>":"");foreach($Td[1]as$r=>$X){$X=stripcslashes(str_replace("''","'",$X));$Oa=(is_array($Y)?in_array($X,$Y):$Y===$X);$H
.=" <label><input type='$U'$ra value='".h($X)."'".($Oa?' checked':'').'>'.h(adminer()->editVal($X,$l)).'</label>';}return$H;}function
input(array$l,$Y,$q,$va=false){$_=h(bracket_escape($l["field"]));echo"<td class='function'>";if(is_array($Y)&&!$q){$Y=json_encode($Y,128|64|256);$q="json";}$wf=(JUSH=="mssql"&&$l["auto_increment"]);if($wf&&!$_POST["save"])$q=null;$Dc=(isset($_GET["select"])||$wf?array("orig"=>lang(8)):array())+adminer()->editFunctions($l);$Cb=stripos($l["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$ra=" name='fields[$_]'$Cb".($va?" autofocus":"");$Vb=driver()->enumLength($l);if($Vb){$l["type"]="enum";$l["length"]=$Vb;}echo
driver()->unconvertFunction($l)." ";$R=$_GET["edit"]?:$_GET["select"];if($l["type"]=="enum")echo
h($Dc[""])."<td>".adminer()->editInput($R,$l,$ra,$Y);else{$Nc=(in_array($q,$Dc)||isset($Dc[$q]));echo(count($Dc)>1?"<select name='function[$_]'$Cb>".optionlist($Dc,$q===null||$Nc?$q:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($Dc))).'<td>';$md=adminer()->editInput($R,$l,$ra,$Y);if($md!="")echo$md;elseif(preg_match('~bool~',$l["type"]))echo"<input type='hidden'$ra value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked='checked'":"")."$ra value='1'>";elseif($l["type"]=="set"){preg_match_all("~'((?:[^']|'')*)'~",$l["length"],$Td);foreach($Td[1]as$r=>$X){$X=stripcslashes(str_replace("''","'",$X));$Oa=in_array($X,explode(",",$Y),true);echo" <label><input type='checkbox' name='fields[$_][$r]' value='".h($X)."'".($Oa?' checked':'').">".h(adminer()->editVal($X,$l)).'</label>';}}elseif(preg_match('~blob|bytea|raw|file~',$l["type"])&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$_'>";elseif($q=="json"||preg_match('~^jsonb?$~',$l["type"]))echo"<textarea$ra cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';elseif(($og=preg_match('~text|lob|memo~i',$l["type"]))||preg_match("~\n~",$Y)){if($og&&JUSH!="sqlite")$ra
.=" cols='50' rows='12'";else{$J=min(12,substr_count($Y,"\n")+1);$ra
.=" cols='30' rows='$J'";}echo"<textarea$ra>".h($Y).'</textarea>';}else{$Lg=driver()->types();$Yd=(!preg_match('~int~',$l["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$l["length"],$z)?((preg_match("~binary~",$l["type"])?2:1)*$z[1]+($z[3]?1:0)+($z[2]&&!$l["unsigned"]?1:0)):($Lg[$l["type"]]?$Lg[$l["type"]]+($l["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$l["type"]))$Yd+=7;echo"<input".((!$Nc||$q==="")&&preg_match('~(?<!o)int(?!er)~',$l["type"])&&!preg_match('~\[\]~',$l["full_type"])?" type='number'":"")." value='".h($Y)."'".($Yd?" data-maxlength='$Yd'":"").(preg_match('~char|binary~',$l["type"])&&$Yd>20?" size='".($Yd>99?60:40)."'":"")."$ra>";}echo
adminer()->editHint($R,$l,$Y);$pc=0;foreach($Dc
as$w=>$X){if($w===""||!$X)break;$pc++;}if($pc&&count($Dc)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $pc);");}}function
process_input(array$l){if(stripos($l["default"],"GENERATED ALWAYS AS ")===0)return;$t=bracket_escape($l["field"]);$q=idx($_POST["function"],$t);$Y=$_POST["fields"][$t];if($l["type"]=="enum"||driver()->enumLength($l)){if($Y==-1)return
false;if($Y=="")return"NULL";}if($l["auto_increment"]&&$Y=="")return
null;if($q=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?idf_escape($l["field"]):false);if($q=="NULL")return"NULL";if($l["type"]=="set")$Y=implode(",",(array)$Y);if($q=="json"){$q="";$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}if(preg_match('~blob|bytea|raw|file~',$l["type"])&&ini_bool("file_uploads")){$mc=get_file("fields-$t");if(!is_string($mc))return
false;return
driver()->quoteBinary($mc);}return
adminer()->processInput($l,$Y,$q);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Jf="<ul>\n";foreach(table_status('',true)as$R=>$S){$_=adminer()->tableName($S);if(isset($S["Engine"])&&$_!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$G=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$G||$G->fetch_row()){$hf="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$_</a>";echo"$Jf<li>".($G?$hf:"<p class='error'>$hf: ".error())."\n";$Jf="";}}}echo($Jf?"<p class='message'>".lang(9):"</ul>")."\n";}function
on_help($Za,$Sf=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $Za, $Sf) }, onmouseout: helpMouseout});","");}function
edit_form($R,array$m,$I,$Ug,$k=''){$kg=adminer()->tableName(table_status1($R,true));page_header(($Ug?lang(10):lang(11)),$k,array("select"=>array($R,$kg)),$kg);adminer()->editRowPrint($R,$m,$I,$Ug);if($I===false){echo"<p class='error'>".lang(12)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$m)echo"<p class='error'>".lang(13)."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$va=!$_POST;foreach($m
as$_=>$l){echo"<tr><th>".adminer()->fieldName($l);$j=idx($_GET["set"],bracket_escape($_));if($j===null){$j=$l["default"];if($l["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$j,$tf))$j=$tf[1];if(JUSH=="sql"&&preg_match('~binary~',$l["type"]))$j=bin2hex($j);}$Y=($I!==null?($I[$_]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$l["type"])&&is_array($I[$_])?implode(",",$I[$_]):(is_bool($I[$_])?+$I[$_]:$I[$_])):(!$Ug&&$l["auto_increment"]?"":(isset($_GET["select"])?false:$j)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$l);$q=($_POST["save"]?idx($_POST["function"],$_,""):($Ug&&preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Ug&&$Y==$l["default"]&&preg_match('~^[\w.]+\(~',$Y))$q="SQL";if(preg_match("~time~",$l["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$q="now";}if($l["type"]=="uuid"&&$Y=="uuid()"){$Y="";$q="uuid";}if($va!==false)$va=($l["auto_increment"]||$q=="now"||$q=="uuid"?null:true);input($l,$Y,$q,$va);if($va)$va=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($m){echo"<input type='submit' value='".lang(14)."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($Ug?lang(15):lang(16))."' title='Ctrl+Shift+Enter'>\n",($Ug?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".lang(17)."…', this); };"):"");}echo($Ug?"<input type='submit' name='delete' value='".lang(18)."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$Id=80,$fg=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$Id).")($)?)u",$Q,$z))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$Id).")($)?)",$Q,$z);return
h($z[1]).$fg.(isset($z[2])?"":"<i>…</i>");}function
icon($Zc,$_,$Yc,$vg){return"<button type='submit' name='$_' title='".h($vg)."' class='icon icon-$Zc'><span>$Yc</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}@ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M±h´ÄgÌбÜÍ\"PÑiÒmcQCa¤é	2óéÞd<Ìfóa¼ä:;NBqR;1Lf³9ÈÞu7&)¤l;3ÍÑñÈÀJ/CQXÊr2MÆaäi0)°ìe:LuÝhæ-9ÕÍ23lÈÎi7³màZw4њ<-Ò̴¹!U,Févt2S,¬äa´҇FêVXúaNqã)-ÖÎǜhê:n5û9ÈY¨;jµ-Þ÷_9krùٓ;.ÐtTqËo¦0³­Öò®{íóyùý\rçHnìGS Zh²;¼i^ÀuxøWΒC@Äö¤©kÒ=¡Ðb©Ëâì¼/AØà0¤+Â(ÚplÂÉÂ\\ê Ãxè:\rèÀb8\0æ0!\0FÆ\nB͎ã(Ò3 \r\\ºÛêȄa¼'Iâ|ê(i\n\r©¸ú4Oüg@4ÁCº@@!ÄQB°Ý	°¸c¤Ê¯Äq,\r1EhèÈ&2PZ¦ðiGûH9G\"v§ê¢££¤4rÆñÍDÐR¤\npJë-A|/.¯cêDu·£¤ö:,Ê=°¢RÅ]U5¥mVÁkÍLLQ@-\\ª¦Ë@9Áã%ÚSrÁÎñMPDãÂIa\r(YY\\ã@XõpÃê:£p÷lLC Åñè¸ÍÊO,\rÆ2]7?m06ä»pÜTÑÍaҥC;_˗Ñyȴd>¨²bnð«n¼ܣ3÷X¾ö8\rí[ˀ-)Ûi>V[Yãy&L3¯#ÌX|Õ	X \\ù`ËC§çå#ÑÙHÉÌ2Ê2.# öZ`Â<¾ãs®·¹ªÒ£º\0uh־¥M²Í_\niZeO/CӒ_`3Ýòð1>=Ðk3£R/;ä/dÛÜ\0úãÞڵmùúò¾¤7/«ÖAÎXÂÿ°Ãq.½sáL£ý :\$ÉF¢¸ª¾£w8ó߾~«HÔj­\"¨¼¹Գ7gSõä±âFLéίçQò_¤O'WØö]c=ý5¾1X~7;iþ´\rí*\n¨JS1Z¦ø£ØÆßÍcåtüAÔVí86fÐdÃy;Y]©õzIÀp¡Ñû§ðc3®YË]}@¡\$.+1¶'>ZÃcpdàéÒGLæá#kô8PzYÒAuÏvÝ]s9ÑØ_AqÎD:ÆÅ\nKhB¼;­ÖXbAHq,âCIÉ`çj¹S[ˌ¶1ÆVÓrñÔ;¶pÞBÃÛ)#鐉;4ÌHñÒ/*Õ<Â3L Á;lfª\n¶s\$K`Ð}ÆôՔ£¾7jx`d%j] ¸4Y¤HbY ØJ`¤GG .ÅÜKòfÊI©)2MfָÝXRC¸̱V,©ÛÑ~g\0èàg6Ý:õ[jí1H½:AlIq©u3\"êæq¤æ|8<9s'ãQ]JÊ|Ð\0Â`p ³jfOÆbÐÉú¬¨q¬¢\$é©²Ã1J¹>RH(ǔq\n#rà@e(yóVJµ0¡Q҈£ò6Pæ[C:·G伞 Ý4©Ò^ÓðÃPZµ\\´è(\nÖ)~¦´°9R%×Sj·{7ä0Þ_Çs	z|8ÅHê	\"@Ü#9DVLÅ\$H5ÔWJ@z®a¿J Ä^	)®2\nQvÀÔ]ëÇÄXj (A¸Ó°BB05´6b˰][èkªAwvkgôƴöºÕ+k[jmzc¶}èMyDZií\$5e«ʷ°º	A CY%.Wb*뮼.­Ùóq/%}BÌX­çZV337ʻaºòÞwW[áLQÊ޲ü_È2`Ç1IÑi,÷曣Mf&(s-äëÂAİØ*DwØÄTNÀɻÅjX\$éxª+;ÐðËFÚ93µJkS;·§ÁqR{>l;B1AÈIâb) (6±­r÷\rÝ\rڇÚìZR^SOy/ÞM#ÆÏ9{kàê¸v\"úKCâJ¨rEo\0øÌ\\,Ñ|fa͚³hI©/oÌ4Äk^pî1HÈ^ÍphǡVÁvox@ø`íg&(ù­ü;~ǍzÌ6×8¯*°ÆÜ5®Ü±E ÁÂpéâîӘ¤´3öņgrDÑLó)4g{»ä½峩L&ú>脻¢ØÚZì7¡\0ú°̊@×ÐÓÛffÅRVh֝²çIۈ½âðrÓw) =x^,k2ôÒݓjàbël0uë\"¬fp¨¸1ñRI¿z[]¤wpN6dIªzëõån.7X{;ÁÈ3ØË-I	âûü7pjÝ¢R#ª,ù_-ÐüÂ[ó>3À\\æêÛWqÞqJ֘uh£ÐFbLÁKÔåçyVľ©¦ÃÞѕ®µªüVîÃf{K}S ÊޝMþ·̀¼¦.M¶\\ªix¸ba1+£α?<Å3ê~HýÓ\$÷\\Ð2Û\$î eØ6tÔÖã\$s¼¼©xÄþxó§CánSkVÄÉ=z6½¡Ê'æäNa¢ָhÜü¸º±ý¯R¤噣8g¢äÊw:_³î­íÿêҒIRKÝ¨.½nkVU+dwj§%³`#,{醳ËðÊYý×õ(oվÉð.¨c0gâDXOk7®èKäÎlÒÍhx;Ï؏ ݃Lû´\$09*9 ÜhNrüMÕ.>\0ØrP9ï\$Èg	\0\$\\Fó*²d'ÎõLå:búð42Àô¢ð9Àð@ÂHnbì-¤óE #ĜÉÃ¨\0ÀpYê¨ tÍ Ø\nð5.©àÊâî\$op lX\n@`\r	à\rÐ Î ¦   àêðÛ`\r ´\r £`` 0åpä	Þ@\0ÀÐ	 V\0ò`fÀÏ*\0¤ Îf\0j\n f`â	 ®\n`´@\$n=`\0ÈànIÐ\$ÿP(Âd'ËðôÄà·gÉ\n¬4±\n0·¤.0ÃpËðÒ\r\0`1`àÎ\n\0_ óqñ1qµ`ß\0¡ ä؆\0¢\n@â fÍPææ RǠÞÇì@ÙrÇF¯h\r@J¶Ñ^LNË!Àé\"\nÒÄeÊ]r:ÊZ7Ò9#\$0¬µ\"gڭtRB׍|/¼#í×¸D1\"®Ff\"nºòæ(Yp`WYƑÒ]\$ÀFðF¨ð¯ÜRn\ràw!MrìæK²*s%S\$² Ä¨.s*G*R©(=+ދ	\n)Òdûò£*mp\$rÐìä×\$ÜÀë-â?.2©+r:~²ЂI69+4H¼h ú\nz\"Ð(,2 +Döjuåt@q. 𳲽RÃ&i,kJr`cÀÕ\"¢CIÑ	êâz8ڍ¥¾Û\r´¯8êÒøÝf¢¿ëÃ.\"úÖËäê®Ó*h(åé\0ôOªª̀Õ r| ޅM\nÐ徭o|LJªê²v1N´Ü3E(R\".fh+FW/ÒÎIΓ~ð/)Àڦ\rÄï<ÀÛ=h1b]¢Ô&Åiò-òmRôç?ä0Íîú¦ÐäÔï êïl¦ ×®×@Îڜo~ò³DÒìT7t	>k'\$1+î*ã)2tëzÃ2©<Y)sæðÓêta4û1³,\rø+îµ=7l©B/ï;î²×åû¯¾ì­)!>í<f¡j]¸ ê\\àÉKç\$Ī5*rQ4");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M±h´ÄgÆÈh0ÁLЁàd91¢S!¤Û	F!°æ\"-6NÄbdGgÓ°Â:;Nr£)öc7\rç(HØb81s9¼¤Ük\rçc)Êm8OVA¡Âc1c34Of*ª- P¨1©r41Ùî6Ìd2ց®Ûo½ÜÌ#3BÇf#	Ög9Φê،fc\rÇIÐÂb6EC&¬Ð,buÄêm7aVãÂÁs²#m!ôèhµårùÞv\\3\rL:SAÂdk5ÝnÇ·×ìýÊaF¸3éÒe6fS¦ëy¾óør!ÇLú -ÎK,Ì3Lâ@ºJ¶˲¢*J ä쵣¤»	¸ð¹Zb©cèà9­ê9¹¤æ@ÏÔè¿ÃHÜ8£ \\·Ãê6>«`ðŎ¸Þ;Aà<T'¨p&q´qEê4Å\rl­ÃhÂ<5#pÏÈR Ñ#IÝ%êfBIØÞܲ¨>ʫ29<«åCîj2¯¶7j¬8jÒìc(nÔÄç?(a\0Å@5*3:δæ6£æ0ã-àAÀlLPÆ4@Êɰê\$¡H¥4 n31¶æ1Ítò0®á͙9éWO!¨r¼ÚÔØÜÛÕèHÈ£Ã9Q°Â96èF±¬«<ø7°\r-xC\n Üã®@ÒøÜÔ:\$iÜضm«ªË4íKid¬²{\n6\rxhˋâ#^'4Vø@aÍÇ<´#h0¦Sæ-c¸Ö9+p«a2Ôcyh®BO\$Áç9öwiXɔùVY9*r÷Htm	@bÖÑ|@ü/l\$z¦­ +Ô%p2lÉ.õØúÕÛìÄ7ï;Ç&{ÀËmX¨C<l9ðí6x9ïmìò¤¯-7RüÀ0\\ê4Î÷PÈ)AÈoxÄÚqÍO#¸¥Èf[;»ª6~PÛ\ra¸ÊTGT0èìu¸ޟ¾³Þ\n3ð\\ \\ʎJ©udªCG'©PZ÷>³Áûd8ÖҨèéñ½ïåôC?V·dLðÅL.(ti­>«,ôÖLÀ");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':̢Ðäi1ã³1ÔÝ	4Í#ÌQ6a&ó°Ç:OAIìäe:NFáD|Ý!Cyêm2ËÅ\"ãÔÊr<̱ÙÊ/C#Ùö:DbqSeJ˦Cܺ\n\n¡ǱS\rZH\$RAܞS+XKvtdÜg:£í6EvXŞ³jÉmҩej×2M§©äúB«Ç&ʮL§C°3åQ0ÕLÆé-xè\nÓìDÈÂyNaäPn:ç¼äès͐( cLÅÜ/õ£(Æ5{ÞôQy4øg-ý¢êi4ڃfÐÎ(ÕëbUýÏk·îo7Ü&ãºäô*ACb¾¢Ø`.­Û\rÎÐÜü»ÏÄú¼Í\n ©ChÒ<\r)`èإ`æ7¥CʒÈâZùµãXÊ<QÅ1X÷¼@·0dp9EQüf¾°ÓFØ\rä!æ(hô£)Ã\np'#Č¤£HÌ(i*r¸æ&<#¢æ7KÈÈ~# ÈA:N6ã°Ê©lÕ,§\rôJPÎ3£!@Ò2>Cr¾¡¬h°Ná]¦(a0M3Í2×6ÔUæãE2'!<·Â#3R<ðÛãXÒæÔCHÎ7#nä+±a\$!èÜ2àP0¤.°wd¡r:Yö¨éE²æ!]<¹jâ¥ó@ß\\×pl§_\rÁZ¸ғ¬TͩZÉsò3\"²~9)³jãPØ)QYbݕDëYc¿`zácµѨÌÛ'ë#tBOh¢*2ÿ<ŒOêfg-Z£Õ# è8aÐ^ú+r2bø\\á~0©áþ¥ùàW©¸ÁÞnÙp!#`åëZö¸6¶12×Ã@é²kyÈÆ9\rìäB3çpޅî6°è<£!pïG¯9àno6s¿ð#FØ3íÙàbA¨Ê6ñ9¦ýÀZ£#ÂÞ6ûÊ%?s¨È\"ÏÉ|؂§)þbJc\r»½NÞsÉÛih8Ï¹æݟè:;èúHåތõuI5û@è1ªAèPaH^\$H×vãÖ@ÛL~¨ùb9'§ø¿±S?PÐ-¯ò0Cð\nRòmÌ4ÞÓȓ:ÀõÜԸï2òÌ4µh(k\njIÈ6\"EY#¹Wrª\rG8£@tÐáXԓâÌBS\nc0ÉkC I\rʰ<u`A!ó)ÐÔ2ÖC¢\0=¾ æáäP1ӢK!¹!åpÄIsÑ,6âdÃéÉi1+°ÈâÔkê<¸^	á\nÉ20´Fԉ_\$ë)f\0 ¤C8E^¬Ä/3W!א)u*äÔè&\$ê2Y\n©]EkñDV¨\$ïJ²xTse!RY» R`=Lò¸ãàޫ\nl_.!²V!Â\r\nHÐk²\$א`{1	|± °i<jRrPTG|w©4b´\r¡Ç4d¤,§E¡È6©äÏ<Ãh[Nq@Oi×>'ѩ\r¥ó;¦]#æ}Ð0»ASIJdÑA/QP´⸵Â@t\r¥UGÄ_G<éÍ<y-IÉz򄤝Ð\" PÂàB\0ýíÀÈ\q`ïvAa̡Jå Räʮ)JB.¦TÜñL¡îy¢÷ Cpp\0(7cYYa¨Mé1em4Óc¢¸r£«S)oñÍàpæC!I¼¾Sb0mìñ(dEHø¸߳Xª£/¬P©èøyÆXé85ÈÒ\$+֖»²gdèöÎÎyÝÜϝ³J×Øë ¢lE¢urÌ,dCX}e¬ìÅ¥õ«m]Ð2 ̽È(-z¦Zåú;Iöî¼\\) ,\n¤>ò)·¤æ\rVS\njx*w`ⴷSFiÌÓd¯¼,»áÐZÂJFM}Њ \\Z¾PìÝ`¹zØZûE]íd¤ɟOëcmԁ]À ¬Y%þ\"w4¥\n\$øÉzV¢SQDÛ:Ý6«äGwMÔîS0B-sÆê)ã¾Zí¤cǁ2δA;æ¥n©Wz/AÃZh G~cc%Ë[ÉD£&lFRæ77|ªI¢3¹íg0ÖLa½äcÃ0RJ2ÏÑ%³ÃFáº SÃ ©L½^ trÚîÙtñÃ¡ʩ;Ç.喚Ł>ùÃá[®aN»¤Ï^Ã(!g@1ððó¢üN·zÔ<béݖäÛÑõO,ÛóCuº¸D×tj޹I;)®݀é\nnäcºáȂíW<sµ	Å\0÷hN¼PÓ9ÎØ{ue¤ut뵕öè°ºó§½ 3òî=g¥ëº¸ÎÓJìÍºòWQ0øØw9p-:	ý§øËðÙ'5»´\nOÛ÷e)MÈ)_kàz\0V´ÖÚúÞ;jîlîÎ\n&êçxÕPf-ä`CË.@&]#\0ڶpðy͖ƛtËdú¶ Ãó¼b}	G1·mßrußÀ*ñ_ÀxD²3Çq¼BÓsQæ÷uús%ê\nª5s§ut½Â{sòy¥øN¯4¥,J{4@®þ\0»PÄÊÃ^º=¯l²`èe~F١h3oé\"¤q·R<iUT°[QàôUÇM6üT. ºê0'pe\\¼½ôÞ5ßÖÌpCe	ٕÔ\"* M	¨¦Dþ±?ûhüØ2¡ÐãzU@7°CÓ4ýaµ²iE!fË\$üB¤<9o*\$¯ælH\$ Å@ààÊæP\rNÀYn<\$²	ÀQ=F&¥ *@]\0ÊÏË W'dÖ z\$æÐjÐP[¢ö\$òä¯Ð0#& _Ì`+B)wv%	âÔLcJRSÀÂi`ÌŮ	FW	êË\nBP\nç\r\0}	瑩0²Zð¸ò/`j\$«: §8ieüÀØφxâ¹Â±îa ¬GnøsgO¢äU%VU°@N$ϐúd+®(oJï@XÆèàzM'F٣àWhV®I^٢1>Ý@Ð\"î¨¤ ÈQñR!\\¢`[¥¤«¨.Ø0fbF;ëçFpÏp/t`Â ô®(§ÀVé¸ø bȲ(HlÁÎԯ1v­ÞðHÐï1Tï3ñqàÉ1¦ѪfË\nT\$°éàNq+Ëí`ލvÖǜï\rüVmûÇr°¨Ø'ϸ±ñg%«\"Lm¼((CLz\"hâXØm= \\H\n0U f&M\$¤g\$ñU`a\rPþ>`Ë#gªhôî`R4HÑ'ç©­³²GK;\"M¶ۨThµBEn\"b> Ú\r©#\0æN:í#_	QQ1{	f:BËÂáRª&àÜã)JµÄBr¹+ÂK.\$ÐPqõ-r®S%TIT&Qö·Ò{#2o(*P¯â5ï`1H®¢'	<Tðd±÷ª¾sÀì,NÚÊ ÒÉÔì^\r%3îÐ\r&à4Bì/\0ÐkLH\$³4dÓ>àÒ/³ඵHö·* ºù3JÇА¥<Hh©pú'çO/&ï2I.îx3V.¢s5Óe3íªێZÛ(õ9Eg§;R;±J½QÃ@ªÓvgz@¶Þó'dZ&Â,Uã²ßò¦F æb*²DòH! ä\r;%x'G#°͠wÁ#°֠È2;#òBvÀXÉâaí\nb{4KG¦ß%°ÒGuE`\\\rB\r\0¨-mW\rM\"¶#EôcFbFÕnzÓóÿ@4JÈÒ[\$Êë%2V%ô&TÔVdÕ4hemN¯-;Eľ%E¥E´r <\"@»FÔP·L Ü߭Ü4EÉð°ÒÄz`Ðu7éN4¯Ë\0°F:hÎKh/:\"MÊZÔö\r+P4\r?¤SøO;B©0\$FCEpÇM\"%H4D´|LNFtEÑþgþ°5å=J\r\"޼5³õ4à¾KñP\rbZà¨\r\"pEQ'DwKõW0îg'l\"hQFïC,ùCc®òIHÒP hF]5µ& fTæÌiSTUS¨ÿîÉ[4[uºNe\$oüKìÜO àÿb\" 5ï\0DÅ)EÒ%\"±]Âî/­âÈЌJ­6UÂdÿ`õña)V-0DӔbMÍ)­ïÔ¯ØýÄ`æ%ñELt+ìÛ6C7jëdµ¤:´V4ơ3î -ßR\rGòIT®#¥<4-CgCP{V\$'ëÓ÷gàûR@ä'Ð²S=%=óFñk: ¢kØ9®²¤óe]aO¼ÒG9;îù-6Ûâ8W(*øx\"U®YlBïîöò¯ðÖ´°·	§ý\nîp®ðÉlÉìÒZm\0ñ5¢òä®ðOq̨ÌÍbÊW1s@ÐùKéº-pîûÆE¦Spw\nGWoQÓqG}vpw}qñqÓ\\Æ7ÆRZ÷@Ìì¡týtÆ;pG}w׀/%\"LE\0tÀhâ)§\ràJÚ\\W@à	ç|D#S³¸ƃVÏâR±z2Ïõövµú©	ã}¨¢¯(¸\0y<¤X\r×Ýx±°q·<µIsk1Sñ-Q4Yq8î#ÞîvîÐd.ֹS;qË!,'(òä<.è±J7Hç\".³·¨ñu°ü#ÊQ\rerÀXv[¬h\$â{-éY °ûJBgéiM8¸'Â\nƘtDZ~/bÖÕ8¸\$¸¸DbROÂOÆû`O5S>¸öÎ[ DÇꔸ¥ä_3Xø)©À'éÄJd\rX»©¸UDìU X8òx¯-旅àPÌN` 	à¦\nZ˔@Ra48§Ì:ø©\0éx°ÖN§\\ê0%ã·f\\ ð>\"@^\0ZxàZ\0ZaBr#åXÇð\r¨{à˕¹flFb\0[ވ\0[6	¢° ©=â\n ¦WBøÆ\$'©kG´(\$yÌe9Ò(8Ù& h®îRܔÙæoØȼ ǇøY£4Øô7_­dùã9'ý¢ú Üúï²ûz\rÙÖ  åðþvGèO8èØìMOh'æèXöS0³\0\0Ê	¸ý9s?öI¹MY¢8Ø 9ðüä£HO,4	xsP¤*G¢çc8·ªQɠøwB|Àz	@¦	à£9cÉK¤¤QGÄbFjÀXúoSª\$dFHĂPÃ@ѧ<嶴Å,}ï®m£rÿ\"Å'k`¡cà¡x¦e»C¨ÑCìì:¼ÞØ:XÌ ¹TÞÂÂ^´dÆÆqh¤ÎsÃ¹×LvÊҮ0\r,4µ\r_vÔLòj¥jMáb[  ðlsÀÞZ°@øºäv;fí`2Ycëeº'MerÊÛF\$È!êê\n ¤	*0\rºAN»LP¥äjٓ»»¿¼;ƣVÓQ|(ð3ÄÊ[p8óú¼|Ô^\räBf/DÆØÕҞ Bð_¶N5Mô© \$¼\naZЦ¶È~ÀUleõrŧrÒZ®aZ³¹ãøգs8RÀGZ w®¢ªN_Ʊ«Yϣòm­âªÀ]¦;ƚLÚÿº¶cøû°ŰÆÚIÀQ3¹OãÇ|y*`  ê5ÉÚ4ð;&v8#¯Rô8+`XÍbVð6¸ƫi3Fõ×EÐôØoc82ÛM­\"¶¹©G¦Wb\rOÐC¿Vdèӭ¤w\\äͯ*cSiÀQүã³R`úd7}	º)¢ϴ·,+bd§۹½FN£3¾¹L\\ãþeRn\$&\\rôê+dæÕ]O5kq,&\"DCU6j§pçÇÉ\\'@oµ~è5N=¨|&è´!ÏÕBØwHÚyyz7Ï·(Ǎøâ½b5(3փ_\0`zÐb®Уr½8	ð¢ZàvÈ8L˓·)²SÝM<²*7\$º\rRb·âB%ýàƴDszÏR>[Q½Ð&Q«¨/¡Ì'\rppÌz·/<}L¢#°ΕÂÐâZ¹ã²\"tÆï\n.4Þgæ«Pºp®Dìnà¥ʹNÈâFàd\0`^åä\rnȂ׳#_âÄ w(ü2÷<7-ªX޹\0··s¬ø,^¹hC,å!:×\rKÓ.äÝӢ¯Ţï¹ÔØ\\ò+vZàê\0§Q9eʛ˞Eöw?>°\$}£·D#ªðã cÓ0MV3½%Y»ÛÀ\rûÄtj5ÔÅ7¼ü{ŝLz=­<ë8IøMõ°õâGØÑÎÞLÅ\$á2{(ÿpe?uÝ,Rïd*Xº4é®ý¿Í\0\"@}<.@õ	ÞN²²\$î«XUjsÝ/üî<>\"* è#\$Ôþ÷Õ&CPI	ÿèt¿áùü¦î?è ´	ðOËÇ\\ Ì_èÎQ5YH@ÙbâÑcÑhî·ùæ뱖O0T©' 8¡wü»­öj+Hv_#ºíïì06Èw֎Xà»d+£ܓ\\Àå\n\0	\\ð>sÓA	PFöd8m'@\nH´\0¬cèOwSßØYá`²¨¢R×ýDna\" ì~Â?Ámð|@6ä½+ìGxVä\0°WӰnw.¡؃b«9Í¸EÈ|E·ÃÂ\rЈr¬\"Ðøx¸-¸êâ\rN6n·\$Ҭý-BíHæ^Ó)â¥y&ããךWǧàbvRì	¸¥³N\0°Ànâ	T`8X¬ðA\r:{Oþ@\" !Á¤\$KäqoÐËjY֪J´þÂíÜh}d<1IÇxdÊÎTT4NeeC0䥿:DFÚ5LÞ*::HjZå­FõRªMրnS\n>POó[\$V8;#K\\'ùBÖè»R®د°RÑ_8Ájé*Ej \\~vÆÂÐvÄÛp@TX\0002dE	HíVðñ×D\"Q'EDJB~A´A¤Il*'\n¶Yå.è+©9¾ñpgÒ/\"¸18Ä0IAÊFCȨV*aèPÀdÖУ5H\" AØå6Ýs¬YÝØ;訞È/¨¸0ãv}y\rÍâÎץ1u\"ˋmãñ_º0焄`ß¯¿\\B1^\nk\r]lhø}]HBW`±0½꨹rFf)W,Õҧ]sm9'O¢xԽÍ,ê9J8§£? 4ÉÉï¡\"҅è۽Ì<Ñ-S¨ÉÃþMÃ;ÐvÌñ6y|ZòK¨%àa#8¢TC!pºË\nØïCZ(ï½9|ܾæª,Ú\nº+Q\$äŭôÈ+Ý_+ãÊ\$¸ú%d  eDQJØü¥iX}\0P׾²Çü·æBPë¾ÄW?¥úɍ词7áHQ~§üWòþS¾É\n?	Å çÊúö>µ!oÐ\0ðR1áÂ9cx\$bÐ6zB\"ÄY«Ö²©ù\$k#w 4Èr¿ÆîΎ|J y>ãú\$¹'Ýà)æ~8Àé-¼«ÒDÄu!¥~öCÌ&cdPú&ö¡þÈAî<=bnIÿ	\\xÑÈX'@	ùËÛOìçSª`Xɍ[dÓ!Պâ&¹èå±Aà!I\$'íUS(&SîÚl¨¼®ukGÉ'»¡R>WI¡~ÒjL¦õ>ôbË(Йßé'U²IÝÄº½¤<òI(¡*Jc¢XBÖ|zGprñÔb+LZU­fQ±<DáçU\nTô\"¥ìñaÃ~St¤Â٩E|NRÐ");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress('');}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo"PNG\r\n\n\0\0\0\rIHDR\0\0\09\0\0\09\0\0\0~6¶\0\0\0000PLTE\0\0\0­+NvYts£®¾´¾ÌÈÒÚüüsuüIJ÷ÓÔü/.üü¯±úüúC¥×\0\0\0tRNS\0@æØf\0\0\0	pHYs\0\0\0\0\0\0\0´IDAT8ՔÍNÂ@ÇûEáìl϶õ¤p6G.\$=£¥Ç>á	w5r}z7²>På#\$³K¡j«7üݶ¿ÌÎÌ?4mÑ÷t&î~À3!00^½Af0Þ\"å½í,Êð* ç4¼âo¥Eè³è×X(*YÓó¼¸	6	ïPcOW¢ÉÎ܊m¬r0Ã~/ áL¨\rXj#ÖmÊÁújÀC]G¦mæ\0¶}ÞË¬ߑu¼A9ÀX£\nÔØ8¼V±YÄ+ÇD#¨iqÞnKQ8Jà1Q6²æY0§`P³bQ\\h~>ó:pSɀ£¦¼¢ØóGEõQ=îIÏ{*3ë2£7÷\neÊLèB~Ð/R(\$°)Êç ÁHQni6J¶	<×-.wÇɪjêVm«êüm¿?SÞH vÃÌûñƩ§Ý\0àÖ^Õq«¶)ªÛ]÷U¹92Ñ,;ÿǍî'pøµ£!X˃äÚÜÿLñD.»tæý/wÃÓäìR÷	w­dÓÖr2ïƤª4[=½E5÷S+ñc\0\0\0\0IEND®B`";}exit;}if($_GET["script"]=="version"){$n=get_temp_dir()."/adminer.version";@unlink($n);$p=file_open_lock($n);if($p)file_write_unlock($p,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));@ini_set("session.use_trans_sid",'0');if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",HTTPS,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$oc);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",'15');function
lang($t,$A=null){if(is_string($t)){$bf=array_search($t,get_translations("en"));if($bf!==false)$t=$bf;}$ma=func_get_args();$ma[0]=Lang::$translations[$t]?:$t;return
call_user_func_array('Adminer\lang_format',$ma);}function
lang_format($Cg,$A=null){if(is_array($Cg)){$bf=($A==1?0:(LANG=='cs'||LANG=='sk'?($A&&$A<5?1:2):(LANG=='fr'?(!$A?0:1):(LANG=='pl'?($A%10>1&&$A%10<5&&$A/10%10!=1?1:2):(LANG=='sl'?($A%100==1?0:($A%100==2?1:($A%100==3||$A%100==4?2:3))):(LANG=='lt'?($A%10==1&&$A%100!=11?0:($A%10>1&&$A/10%10!=1?1:2)):(LANG=='lv'?($A%10==1&&$A%100!=11?0:($A?1:2)):(in_array(LANG,array('bs','ru','sr','uk'))?($A%10==1&&$A%100!=11?0:($A%10>1&&$A%10<5&&$A/10%10!=1?1:2)):1))))))));$Cg=$Cg[$bf];}$Cg=str_replace("'",'’',$Cg);$ma=func_get_args();array_shift($ma);$_c=str_replace("%d","%s",$Cg);if($_c!=$Cg)$ma[0]=format_number($A);return
vsprintf($_c,$ma);}function
langs(){return
array('en'=>'English','ar'=>'العربية','bg'=>'Български','bn'=>'বাংলা','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','el'=>'Ελληνικά','es'=>'Español','et'=>'Eesti','fa'=>'فارسی','fi'=>'Suomi','fr'=>'Français','gl'=>'Galego','he'=>'עברית','hi'=>'हिन्दी','hu'=>'Magyar','id'=>'Bahasa Indonesia','it'=>'Italiano','ja'=>'日本語','ka'=>'ქართული','ko'=>'한국어','lt'=>'Lietuvių','lv'=>'Latviešu','ms'=>'Bahasa Melayu','nl'=>'Nederlands','no'=>'Norsk','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','ro'=>'Limba Română','ru'=>'Русский','sk'=>'Slovenčina','sl'=>'Slovenski','sr'=>'Српски','sv'=>'Svenska','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','tr'=>'Türkçe','uk'=>'Українська','uz'=>'Oʻzbekcha','vi'=>'Tiếng Việt','zh'=>'简体中文','zh-tw'=>'繁體中文',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(19).": ".html_select("lang",langs(),LANG,"this.form.submit();")."</label>"," <input type='submit' value='".lang(20)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$aa="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$aa=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$aa=$_SESSION["lang"];else{$da=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Td,PREG_SET_ORDER);foreach($Td
as$z)$da[$z[1]]=(isset($z[3])?$z[3]:1);arsort($da);foreach($da
as$w=>$lf){if(idx(langs(),$w)){$aa=$w;break;}$w=preg_replace('~-.*~','',$w);if(!isset($da[$w])&&idx(langs(),$w)){$aa=$w;break;}}}define('Adminer\LANG',$aa);class
Lang{static$translations;}Lang::$translations=(array)$_SESSION["translations"];if($_SESSION["translations_version"]!=LANG.
3422726030){Lang::$translations=array();$_SESSION["translations_version"]=LANG.
3422726030;}if(!Lang::$translations){Lang::$translations=get_translations(LANG);$_SESSION["translations"]=Lang::$translations;}function
get_translations($Ed){switch($Ed){case"en":$f="%ÌÂ(ªn0QÐÞ :\ró	@a0±p(a<M§Sl\\Ù;bѨ\\ÒzNb)̅#FáCyfn7Y	ÌéÌh5\rÇQå<ΰC­\\~\n2NCÈ(Þr4Í0`(:Bag8éÈi:&㙔åy·Fó½ÐY­\r´2 8Zӣ<ú'HaÐÑ2܌±Ҟ0Ê\nÒãbæ豌Þn:ZΰÉUãQ¦ÕŭwÛøÝD¼êmfpQËÎqêaÊo°cq®w7PÎX3to¢	æZB9ÄNzÃÄs;Ù̑҄/Å:øõðÃ|<Úâø4µéj'J:0ÂrH1/È+¾Î7(jDӊc¢Ð栢Ö0K(2ä5B8Ê7±\$Bé/Èhò8'ÀRì¼,ñäE P ÷ÄÃ#7­Ct|¿\r®`ÊØ·£¸@¼, PJCË8Êá²Î £Ìj A bÐµ,@1\0S<ãBý=RDÛ#b×ͨ\\ü:àP\$Bh\nb-4ò.£hÚ¦㌳óÈðJɚF£àʸ讻.b.í#Èx!ò;SU^9ʽgZ¦×`»ɓE`:!HbkdVC%p\"ø\rÓÄÄàÌ3\r[G6Lhå!êÒò*\rè|7(ø:c|9èÙ&ȍØXÄ(¸Î0Øvº6¶Là\\Þç{=7Í÷~ßòv^®ìáH¶7³±\"]Êæ,9ߗò:*1nªf3YOˆ#&F¿`mt2F53«ÁèD4 à9Ax^;êr+»«8ÎÉazxOãp^ÚÛ6á×õ´ۖZÐPà4ªã&MÏ5VàëZ:¨ыݖH;VؕÕ*04h6¥iv ;êYÕͪZ¾³iÀm~¿T	+¶-³Ãz,H֘½²DØ4ÞÒô[{Q¨PÑ>`V`3öÒ;£wÿmY å¢ÃÌÎZ8­õcxò; Z{l،#dò­\0··öKýìv8\n@¡@_2ø	¨SW÷¶\rWA¸߻ãé2¨ÈńOÊ	&%\0½çü¹MkÞ6¥ðgvUҙ3#Ì÷/¤¦vOü'È\$äõúpt¥¥¤%ã:öa?+½ÁpÒES\nA˺pÜàT٬zä1ãV<݈ñ \$Dß ôb`hãBfQÑ*p\0ú·bìfX聕¶]ȱç -љòfUÉ³TRVrÕ\"YÙdLñã@{«/^)x´Æ@¢..)¥ñ\rÉÙàZ4x²ò¼+­מFþïi1n\"Dȩ0¢Ê	2d`#Gæs\rÑb=L¡GÄY×I~#¡\$ÎSLЂK<q÷̹b6FdÍÁIXV|ϛL¾骕ÔלSEV®5;5Räé,\$ÌŚ©ØEçr~\r\rÀW ÃHc\rk%V.là@mtA¤3/Rð	k´МP¨h8Yf¸»Ò:LRai'Üà'0ÐÜOzL{ ¡X¨\"\nKÍ#Ô.¦×Ѭ8`*JÀºAM©y\n§¤¤Ò×@Ý%\n.ô5ë\n¹ÂadELEj?:0\0£\0 ®y-*ç¥nΊTéM! ªn ș¿FÁá÷©_Ójo\rH,:r\\ÝѢ¨®·Gü½J\"d°0'¶lqh2ÖD1Ù2-eAp";break;case"ar":$f="%ÌÂ)²l*Âp±CÛ(X²l¡\"qd+aN.6­d^\"§Åå(<e°£l VÊ&,l¢S\nAÆ#RÆÂêNd¥|X\nFC1 Ôl7`ӄ\$F`Ç!2öÊ\r°¯l'àÑE<>!!%ó9J*\rrSÄUT¥e#}´Jü*¯Æd*V́il(nñëÕòý±ÛTÒIdÞu'c(ÜoF±¤Øe3Nb¦ êp2NS¡ ӳ:LZùú¶&Ø\\bä\\uÄZuJ¶Í+´ωBHd±Nlæ#Çd2ޯR\n)èÍ&ã<:³\\%7%ÓaSpl|0Ñ~ (ª7\rm8î7(ä9\rã@\"7NÂ9´£ ÞÙ4Ãxè6ãxæ;Á#\"~¸¿2ѰW,ê\nï¤NºlêEË­¥Rv9Äj\nV¤:Οh\\p³¾O*ÚX¨åsò')irÛ*»&ÁVÌ3J;îl1åBÊû+lÂÐø>ìj\\ÊzÇ1,þ\$q1büJ±{Í7!S.jí6¢R	)¼i](Ë(Uì\"+Ezq¹M?¡lÁj¤\$ºWA(Ȉ\nUCËd4àPHá gZ)\rRkZJJ̔É\n/hAP¤u\$hRÔ12ÚÉ2eÈP뉰Q1 P\$Bh\nb5\rAxÚ6âØÃw\"êBDŵ1!#!V'\0Ú:p¸x0§ðä2¨å8d0!à^0ÉcxÜ3\r#>2âX¤v=qÜ߸M~G#vRÒ߹eú2\rØÜ98ÔxN±MÎCè6Ló@0àá\0Þ3ÃcÖ2ǫX¶Î63ò¥ÑnµI	X\n{P6á£È@:æã¨Æ1¶CÌ:\0Ø7ïXæ6Ì0ãKÔ×óÖ:·a@æ°©5¬Q¦¸¾UÂú¶©\0¨Ñ@\r͖`XÎ# ۧ[Àǝ\r#\$`m3¡Ð:æáxï݅ÃEÒÐHÎ£p_	eæsãA÷ÝsÙ*ųÙ°Ɖ*å4bC\"ȡljôW¯Z½ÆÛTµ 	°{v950ô@9Tù à4àñX]pevÉÚ;gpîã¾ta¹<äðÞ+Äf¯1 ^(I\r¡À׆׊#Æô7ªsj¿k4¡¥ fÉpt'â亖S*E!£Y\"ŀ\\\"åöº9r`҆ÆòÐ 湸#J3PySÌüÐÃkm­½¸·6ê\"Y½6<ýá¤0ÆvHR7}£T׊cÔ#±ÄÈ#tt@p\0 Z!¡#¢¤bÄxH% T2 dGsÆÐÔ£XkeTèi¡3~P¼mnaÞQõðP9Q¯¹LHØêDTúÚ\$rU°DF)à\0sC­± C{\nÃpppÔ9¡ôCðc\r3»0A£¥a5Bè²) -¤|¸FUB\"bJ&>ÅF¥rZDª¹¸ñH	I2d)ɔ¡döч/ÈИ\"hMËgª`iRњÚIáï²`Hy3à2:PA,&7Æ̓êl¸fAµÐ;øý&:mÍF9NM\0#°ЍH¥öO\naPi^¤b¡04T¸¥R^Ùé!Ԉ2J%C%5YCÏSҩEªO¦z¢ä[#\"jýÖÂÝYg P	@ÎÒ9¶\r»sw\0&:Q	2£[\00T\n7*pÓÐÛ\$¦4Δ#H3\$b
£HhXø±q)èëuiñ\$ò{§¥ Z\$Q¶Û%æc	Kô'·ÎK¤`Ù'éSI`è`+§a¤1¶Ã\0v´¾N/èJC3f#JüÀ¼Ød¡T*`Zk§¤§²Ւv®µíËèp-\\[×z\0ªÒ1¸¥µkWlKA6ïÞC,FÅk«KÍ+%\$rՌ9ØO%nUH·\0*\"2±åÃ0êå۔+yü07Ø0ڂP\rXÑß9âBM¤»I3ö:¦GªQËíÉXd¨¹{öOýNáL2ø(1`>¹«)û\\CðÝøªé·Ã]р(\n`í|ÙC/æ`Ķ¹ö>B2";break;case"bg":$f="%ÌÂ) h-Z(6 ¿´Q\rëA| ´P\rÃAtÐX4Pí)	EVL¹h.ÅÐdäu\r4eܞ/-è¨ÖO!AH#8´Æ:ʥ4©l¾cZ§2͠¤«.Ú(¦\n§YØÚ(Ë\$É\$1`(`1ƃQ°Üp9(g+8]*¸OqJÔ_Ð\r¼ú¾ GiÙTÆh£ê»~McN\\4PÑÊ򂞴[õ1¼UkIN¬qëÖÐè呺6Á}rZ״)Ý\"QÚr#Y]7O㬸2]õf,¤éµ©¼D5(7£'êÆ1|FÃ'7ÕêQþßLsâ*nËø÷¿Ès¸¶æ0Ê,¬Ë{ ī(H4ʴìÁ\0\n£pÖ7\rã¸Ü£ä7I0Ä0c(@2\rã(æD¢:Qæ;Å\"¼¸ë>P!\$Âp9r·»Å낏¸îó0³2Pb&٩ì;BҫC¼°2izꤨRF´-Ë\"؅-ÊK´A·ªñO©łJ<¯ä\$i؃§,«²ߚJãµ)(fl Ěã§hQ̴-Ârã:Hz-¾;RƵ3\\â¶*L4õ=?T¿ÕawZ<¨ܡ?¢B©¥¤S2tìÑ@í³>¦J£S¨¢Ê£\"0ÓÍ+(lÛi{J>È5!hHÝAªQ-Dp£YÊ:à·4I¹úь4ÙK·­_~QpM\0£JsªäÁ5«²l­ßÞf`Oe8th&¤æ6Á P·daf.ªy¶1¡7Û*n«ìÒjØ@6£t*Ø|9£ ê9Dc#Èx!òg hBÞ7ÃHϣº~£2LF­ÅCHç\ruDvѵFûft2 Ê7cN¯3053ֵ? PØ:UaRҫ5	z¯7à²CpTb8ùq?Jâ|'nÆHÁ*(;¦¾\nOVê#×0úɝesq1¨Â ûññh­[J8¼!póät	÷F³ôÕ7Rã?[\0Bsebö¾·on÷6B=ާ®/æøhg\\¯BíG³ÒÖJÂXtè&ì&b ѥ\rq¸ÌÛëbà @Úؑ*Lmì4DJ\0 \r\r3ÐD tÌð^á\0.0\"PÁ{wèѷFôx\"б@&¼ԛêt?Â=2ò:PaM`í椓BíI+åA-¼åWӌ\$¤ª»²¯݉MïÉ ó¿	P.	AH- ÔЀ;Â(H(.ð¦7FìÞ!|1hNY!»`*\nyî2¿\r f§@UVsÒyT戊ÆxæO©7j]CҤ¼ÉÁ1jÜàs¸F³òK¥\n?RþCB2!5p@ÃHl\r1# à_ør\r¡mÌ݃;a0ÌeØl\r᝱KpÒ@ ɳÌ0@Ú\$\rи0ÆøAÎ	Í8eãèaÔa#©`祷t±d˔Rę1; ëb3µ0/HvA[%.ÓHñ3jz8KH6x È4irC<ģHý¶dpÑÔåAÞb¢âù°z-©UT	8#ÉSXÒ%FT243\n#u\$8T r[AÜ4Ç6`8gñT°ÆYèe¥Å*4ÂKù-æ��4Dq\"L½Ö²zm¢Q20çHBÐKѺO	(É=RÞè¢y46\$8Ҝ¤PÐ5¬\\Ït¡!©Þ2'ÒGNÕé\\ÒE9IoÌä4ß^[!ñÒ3I\$w¡ý|6F ð°uÛ1wJT[ÀHßd1ÎQå¼T&bӫ?ƬX>Ĝd©ÃÔ'V͔÷;ÃÍtE1ÒS×U\\ÈBÉyUùêÙ\":ügÞØ[Bâm1w,è¸ÝS'mÉ!-9Në­lÎâG)ª^òHvFçå\$r<ï]ËDN\0S\n!0äÒUÉ¼*P±*^y9'rEy:ë¡+rGj}\\Ô\"疪VPWÔÞ؀¨^BQ+ÃÓOo)×Z	cÂlՒLk6¥öGâ£ë[£c̎Y±¥¸,T¦e¸\n~V^û%È{pC6]P4ü!¸@Ø\nËZ]#ìLï-\$qÜr§¹'ڻhp9MÆ7±3©)\r¦ø8\0ª0-º|&Ä	àR®ÿ,TÕ>%¿Æ×/­­7!ì<ɚ~I¬LtL=cz.¢vݚhÌRü9ò|ðÞjsÈWÆêJ)TåÖ*c-ùcÙÛTÃ\n\0+SsÈ~²N)¤õ;P	ö©ÚæÜ§	R½7z~ίJz»ÉVC¤óDöì#\rJï-t\\·×F¦æôï\n´®՞ÞÄxV¾>Ñoên'±mÖM㎘µBDÝú¯ñ¶u&]ëŊ³M׮©vצ_ÞÅÞI¹¥ËØÌ\rR&Þ°ÅæéûªGP";break;case"bn":$f="%ÌÂ)&UÁ×Ðt<d ¡ ê¨sN¨b\nd¬a\n® êè²6­«#k:jKMŐñµD)%RAÒ%4}O&S+&Êe<JÆаyª#FÊj4I©¡jhjVë©Á\0æBÎ`õULªÏcqؽ2`©ÜþS4C- ¡dOTSÑTôÕLZ(§©èJyBH§W΢Jh¢j¦_ÜèØ\rmyioCùÒZ²£N±ôür,«N®%Dn৮еUüõ8O2ôín©ŭr`è(:¾£NS7]|􆇵ÐÓ8Ø8:>¾ÀÐn<LÞuCéO觽øßg~S+ã~ßj<Ñ.®ê¥¾êyL/MÄà0NBðS:lüè9Fù'Ð1PéBò¼¥4/¾jrìò¥.½¯.jç³åò6ԫmTëÅâdÇ\nÜ7Â-ªè D-êI£lëjá'Êú@Ep­\0¾Ò3mÃPä´0 éaL?	2ð4̙­oKa3ÉÒPÿÑ	|2jpáLJÙ`@´}0A/Ä2®\r,S=ó¥­²jéq\$¨iá&ê ñÔé&£1\\NÅ,O+§Óc ÑmÝFóJôz7VHp{~®³í;º³#sÍ0õÓëÿPî-¶0Jò6ÚÇÔSMo\"vé ísñ§KÝ6¸Õ|SSÕÙxé?\rûh¸SÃÇSÃÍt¦«´M.üÓ.úòJ§Nͦ¡Á\n<uE7½y_BHí®Õڋ	p㖉¨n}éb.Ååeà¯ê¶W6T¬QðóðP§Q±/bÇö{ñQ?Å;(-Ù¸í?«ì5\\®0®Um¤OÆd£&i:] N{­«|ËÂ%Êt_ÊÙu¡@PHÁ iª-k¹ԧSUPL eÏ«tEÜ è˗oxîõP8JÛSՍtâ\$	К&B¦pÚcW5¶ªQZm&î¼ï)mG'ÛeqÛm=ü\nÌElS)zn=S<F& JÜâ­Ì7éãPät½<Hϫ7Ö]?KóQÞwË(çWùÌ?» ù²XÂWՓäæ}CmÆ^óç[PVoím+'ÇcxG>ãÅEÚѪãNã`è93Xfì]ÈAnh«°ýÒMTYacÅ9§æBAÙqb%½@ ²ºpFǄõ/å@)HÛ\"dQC9¥õZ6õôg×Ññʖ«Ǒ	ì(hÅiù,\"Գà	^\$å\rHÂ DH<Jõrd\r¡¤7PäÍò|>-dª^OÑ`å5=>_\nÝ( \rÈ2`z@tÀ9 ^üyF)EH¬xrà¼2à^xn!Ð4Ȑ^AS(nÕt§çegëéH¹¥O`D?g%uõ*ZÉa¤mÑCV·¢q2q\"+ø)r´9¶rPérÒù¿4Éïbl˕ÙT4¬=\"ÞWÌib	ÃåQ+èº*i~äÊªì±R;s#Lk±¾8Ç8ëãÈwqö*) ¤\$¡à:HPç#d0\"xL½Âóʂˌ\"K¾JWYAï'¥õ­ÔJÙí//½íÅÈy-¢¦ÙE«ÎÜ]y昸Ì÷ìNÈÙÏ{KÆs 򲍠kºÒâÑèoA*¦U¶ù¶·OÂ.MÛQøN<?l)ZÑJÅ2¶GƐ±´oBH\nZÇÊö*½\n¥t`R¥¢fõ9±ʸaù³]+é9¯¤ÞXÔë@*Y¥0Dº_©È¦ c.T©V4©ETq*R«]¬åi`4K³3TUî5&proÐe¤×Lâ+©­(EY)ßÂéDu<¾ªj©Vr2b\"1\0F=n¿2®h½FufmÌÌ*VÅ¸(.ÇË\"^Áë/SÆðPØ<ܰiR4d÷´ܣdäªh3lÌÙ{Ö6#ÂZcÒÙø8E%ñt^^oL»Qgñÿ«ºd\"S¤mÊA{QPàüD¥HSÕËë\nÍlKZ\"º}©y»*9՜*Q0T?ZH-µ/\0P	áL*µVØíú´ø²§Ä\"ö\nú¯W.x\0ÊÓ`O¸¼zSVU0´Æ\"REq\$Æ	ɤc¿è`0T¬n¦SËØÅ(}g0pácp@L¼-\0CS:ʑ£Öö¸LùNEzEÜ**s#euÚâ¥a,Vy^P®cT͔Iµ¬ÊfdÕj]F׾&3uáùÂ_ZHîS¢6F1hX­XÓJ̿:ŚٽE­µ3G\"Í%éäkT®¾úºüMrÐôÛÖ{¼3­M	9Wêçê݃¬	ժ¨�Ã2â6EL]:!¿°Ø\néD·½Y5\r,åAµG\n¡P#ÐpùLûaQ«é)SEL´ÕO§#lZ½^ÁFÊí&·¬Tcô¹yu7_ÄÝ_°Ú-âͣq®9¤Û ´܎Jí=ϰÉÖÁÖYôþ¹|PÛޙµ\$62öVڠZÒQ,3̺PCLnRù\"ÛLÃâ©HhâmJþë¨¿xmãîWe<_r¼hæ0Ö-N×Й^qbť7_êI©eªâ*tåyP?ÀMTÈP[ù~b´îï>XýV¤ì»Mné¹C¦ìó@§³Êx»٩\r%¨0G1Ðå^(§UWÏ`¹\ná'ZSØs>rzyÜB¦«íÊ'¢7È;\\Ù)ÅÙ,¿r,äÈ5G+Ë+ÕÀõVª ";break;case"bs":$f="%ÌÂ(¦l0FQÂt7¦¸a¸ÓNg)°Þ.&£±0ÃM磱¼Ù7Jd¦ÃKiÃañ20%9¤IÜH×)7Có@ÔiCÈf4ãÈ* A\"PCIêrÁGôn7ç+,àÂl§¡ÐÂbdѶ.e¦Ó)Óz¾¦CyÛ\n,΢AJ ¸-±¤Øe3NwÓ|dá±\r]øŧÌ3c®XÕݣw²1§@a¦ç¸Öy2Gào7ÜXÎã搳\$eàiMÆpVÅtb¨M ¢UìÒkî{C§¬ªn5Üæáä9.j¿¹c(Õ4:\nXä:4N@æ;®c\"@&¥ÃHÚ\ro4¬nâ\rã#ä²Ê8@ @H;Í§*Ì\0ߨ ë\r¸ò腱P¨©«Êì´.\"k\$bÃ#£{:Gòsäºh²l5¸ϪұLH´ãKDl:âÈ\"bV(àP¡*²ÒÚ5'-ÔÏIÂs*ÅKIèÐa\n5/ÔE	COàR) PHÁ gL)æƭ£<14ÍhÎ2:ææ¤rzڋrp5h¼Â/ïóÖ9# \$	К&B§IBÃhÚc͜<¬õ\\®`J\0@6£Px0¨ó%-F0¿ãòã|[÷7Ã2Êæ]tVէچÚ/í=÷OêbۡÒ灌£Â\$7cMè9¤cèð\r£0Í\\̔H3ɢܿÉ(ä	õDù!\ríhڄ(Î :cT9èذ\rã:p9è嘌5û~Ñ,0Ü:¢A@æ¤ª62½)ÌK=²É\0¨ο\rXͅ£IÀÎ#&ÿh#&4»¸-¾Î2C0z\r è8aÐ^ûè]^ìã\\¹á{²ëzá}ġûówB°ÂôÐ\rUl#¸քh3Ӕ±jUèʣàPä\"X(á	BMK%xѸî{®ï¼ï{èï¿ìÉÃýN\\/áø\\\"HÚ84ì`Ü:rx¦4D­e¶0¢, YzëÂëJYèêÄ22OHÖÍÚh'4ëCBC?ÜÓ3³\0hó_|¦MsgZ٫7g,ì3èA}Ä0À@ݹ>¥݊öJàO¦ǺüߪM\"Ðd(¡\n*\0Jj!\$yâ\0[Þ4fӚL¢ü5eÁr<̙ 	TË\$üY-7		ÿ1ãh¹#¯fÇþ\"NLB!Í	¡P@͘c&3·UC#t\$h2Ũ¸åÅñÞ8Á) (.	̦³&1p% -Å<dyé1p2ðÖÄYxl\"Ê)¢XKi%, @	¢7§yC0JHyc¶  éCr&6QÉêjf@m²¸5(Ãt°j3ºóS'âá=ç(ð¦´9H`¶2A1O,oÎRåâ!ú\$ª÷-ªÕz glDϩElͫøò½\n±0¢\$ᦟa¤`©\rÈJ&CQAñIɦþ&7%ø;\"§:;\0&4¬ÏRèÞaL9\n1\\)ÒÄT¶Ár↝ IIÛ£5!ÌÍԲè«ä-PP'¥\$ʘ«ÝHCJ°¹óÞqÁü;ù?ÓN	u\$-µ»ã` \n¡P#Ðp[TÄ<u;yHFI,^\r:¨;u\0få݊©ê%ۧûddIpËm÷£´Ì /ªä6SÊaIà\r§ÞwÙ@dǮ·czҹôG¥íükNLٕhüÉÖÄjI² Ol5óÓ:Kòq¹ޖAÒ*qk³õ= ALè¿E³aL2aõDê®9	.!TÓ~a	hz1Wå9.2ړ%!ï͛cËÒ¤IÁHɩWª¹ÄKÀ";break;case"ca":$f="%ÌÂ(m8Îg3IØeL£©¸èa9¦ÁÒt<NBàQ0Â 6Lsk\r@x4dç	´Ês#qØü2ÃTÄ¡\0æBcé@n7Ʀ3¡ÒxCÈf4ãÈ(TPfS9Ôä?±ðQ¼äi3MÆ`(Q4D9ÂpEΦÃ\r\$É0MֳXñ~À`°6#+yªedy×a;D*ÜìiæøÔá+ªâp4(¼8Ë\$\"Mò<k¶å£Xø¼Xēà¯YNTïÃ^yÅ=EèÎ\n)í®ój¼o§M|õ*u¹º4r9]¸é֡횠: ²9@ü9ë°Ȓ\nlê¡`ê«Ø6=É:*¢z2\n«&4욊9©*Zz§\rI<H4ª²H¿£*ã¢n̈;I¸!/HÀÒÀðȈã+Ð2»\"*\r#&¡Ä!<&:ÉK3»3j/sîqLªÿ;CË\"\$ÃHÆ4¤ìb¡fðOLüÖ&£ÜíÃL©³\0Ä<ª M!IR0ó#*\0PHá gP3bDÃI8ã0\rXì7£`Ø7`P9'KÚJa©M s¼ý@!öQ]#\$Bh\nb2xÚ6âØÃm\"í2ØÑJs\" ª2Q	à£̨è·>Ô2\"ü#Èx!òIu]h܏-¬­ï|Ån3ì£8 Âß`«²4½á6sD2ŉ,9 £dª420\rã0ÍPðD׌#1Å48ƋB ި¦Cpó²c­>ÌXAY¯,1*çcÎ0¯X2H¥#t42Rà&ùÝC¦`ü\\ºH*03aЛ¼#&¢CÒ#7SʌÁèD4 à9Ax^;ðr%·¤k°Î»A~Z½½H^Ü{øï`wÒJ­h]Y´êà4O掻/h\r;O[Cçh&¿ʪ3!pmqÑt,nÃFñ½o÷Áp¿\r·/I]Åqw»X¿¤]<+FÑÎ\n@ßHÀÂ5¢ý9g4BI!£Z ½k}P¡\rD¸Üóla f±Á\0w6p1ðÚc¾\"!Û V~BÚ\n±\ríðÐn8&ÆpʫØ_ÊC1Uê¹ó0`Ìх0ïý&,±ÊP	A r.N(($¡£BMóïf-µ¾SXklPø¡#BÐ!|VaÝß>øJv¼(`Áؗ!ȁ\0Éé(Nq7Ò}oKH%»¥\"Ìcä¤3·Å\$D#(L߳PÞú°C\naH#F8ÊJp fÁÉԣ ÜkÈÁ)eØ6´9AjÁõ² L©(%ÄÁ3CB` Nʨâ.É I aäÒÄU\"ÓØn|ìWäUéùL͵ÄBìՙÿ|1Y\"H\r+\0(ð¦\"~>ÄȅpÎéL\nNø:¨k6Q/RÑîdòN]ìȀÎ\"\0[ 3^cìmuA¼\0¦Ba<!àL\0!âR\$Æd¦R{F¤	Bç1}OõFKiA(¬C¨ú]IÙÓ\$D^zZ¢E8%4ã½?\"8:O8ê%F?>MOPèÆB1³\" !¥PØ\nævuUn}«7nSmQNÄ`«2H¢ç5§P¨h8aìÀǆàÏKΉ.ÔòªôhMýM©çÁ ¦¥ì1¡&òæÁ9æZÖ*w\\De=£ìêyZmdíB`:Ìe2`)ßԆÐëC§yY)Ó=­XNüÔaÈaè²¢JèUi&àȄˑ_NøP1蕶K!ª¼(HìÅÓÅʤlR׀¶Xtv·ٛ¾MÍ]Pñf\rS®9¶v³³É*H;?\\b2\0µñ@è";break;case"cs":$f="%ÌÂ(e8̆*dÒl7Á¢qÐra¨NCyÔÄo9DÓ	àÒm\rÌ5hv7²µìe6Mfólç¢TLJs!Ht	PÊeON´Y0cA¨Øn8çìd:ÁVHÉèÉ+TÚتù¾X\nb¯c7eHèa1M³̈«d³N¢´A¾Å^/Jà{ÂH òÔLßlPÌDܮZe2bçclèu:Doø×\rÈbʻPÃ.7ì¬Dn¯[6j1F¤»7ã÷»ó¶ò761T7r©¬Ù{āE3iõ­¼Ǔ^0òb²âà穦p@c4{Ì2\"&·\0¶¢cr!*\r(æ\$Bä%k:ºCP艨«z=	ØÜ1µc(Ö(êR99*^ªF!Acþµð~â()L££H=c(!\r) äÓ<iaRB8Ê7±èä4ÈB«¤ÖBã`æ5kèè<È\"µ-äçÊíjp	R \nhÒ4;åæގr¨95úú8NS¢2í&k¸ռ(Kp5ÆA(ÈC,Ñԅ%/Ҕ}#.`PÂ7\ràT°\\8b÷NCÐ5b4Ïʆ3(&\$숂5È/pà9½¹G͂@t&¡Ц)C \\6ÂØåkBíb:ïð£hê9¥\0x0¨áðì²È(Ý2xÂ\$W=Ò'0ðÌ4ã¬\rÞ7H9¹(-_(a}àjr>à&fNԔLЩmÁaø0¦2¾Û1bS8³Ã0̜ÀJPË>eëÉAŇâL¥˵pÔB\"wægôëB4Á`@=h9Bü¾\\6ßңlØ6ÀNo9Ø۞çîWèvA8Ε¦\\xcc¥Ô¶§Qú°ÜØ0ºÓ.VçI®¾7çÎ¡è¨vi¤%^·jçªêã¢D'æz>ÒÓ%ÐxÄÒNìà2즂þ%&ߡ0£sr#&헎ºUÏC(ÌC@è:XaÐ^þ]\\õé\n@3ãæz*:\r>p^Ô9\r\$-x^Aò(êeê`5eÜïç&Ó\0åB\rëNkµO<Ì VÂZåý£¥@OPCí4D± búK#ì?kߑwlîӼnùà<'Yx.xï%å.8nzk¤\$ÐàLnElî®zBGiF\0<çúCȉ=%!òÂìM n.q®PÄøWq«&*T%~TuV/؛«*p\0ÃO`Ò¢ğrëú²ÔJÅëVlÎù\nЈi\"腆 ÒHÉ{lQ\nÒÆH34©cãÒ]ig\0002 SÀ \n (øvãós\0 £HjQÌ\"2ȭxeò=ðÆ@@N`ĵ@fCýq6ÆõË£¬<Ni?! Ú\\Ìyâô:4©Q<ä./ ÜD҈zeçî@Âs[xk&`!0¤p rÜP&âhâe¸5\"DÈlhIÆyI1%KZ)ÀBgôI;â¶t ì_,Ý	0` ؔxjiÐ}·®K2\")Ä%ë^+î/Ëä¿rĤh.;Gô/'ÙÃ|k¤ԛ3ÂxS\nÿPØR¡a/ì9¨Pú\"R 5èuç/µúpÄk5.Ô5½\nÀÉV'	p^\0¦Ba#Å6cùÃ©#1\$6øàKB0T¦ü Sb*K\"iå\$LN¾QB|ðæyCÑ\"¶EðÅcMõ]¡©GdӅ1ê1pYwÚMÁ3â}²rh¢tie´­@k!­®VÒÃU+dE!d`ºÛϒ#n­¥\"6þÚ2\"æH0\r¬ï«y\\A1%´ªºËäQø]¤L=DJb5kErٖFÜú\\o4;Y%ÊB F â{ϐA_e}¶Wá\\Kqá6¾²ß(ü	m[ͬ\"8\09Wû%rðuͭ²Đ´äD[¬Ú4yqO2ÞÀô_ÏÈ\n¦ ;y1A¤/Éu·³âÞÒúB¬0(Ä9Ë.5rD0D`·N̘¨ÿ&dáZ\ny<tÂÉ(ÂÖO	v (!ÂM\n5o¨÷B¬CZ2ÌáÑåÃZ	ì\0ȃq=<Wà	t0V*¶¥e3©\\'àC¤Q;3\nt7\0À";break;case"da":$f="%ÌÂ(u7¢I¬×:\ró	f4"iÖs4N¦ÑÒ2l\"ñц¸9¦Ü,Êr	Nd(Ù2e7±óL¶o7C±±\0(`1ƃQ°Üp9gC¬9ÁGCy´o9LæqØ\n\$ô	)Å36Mãe#)Õ7¸6遹ÐNXZQÊ6D®L7+ÌâdtÍÚDØ 0\\ÈAÂΗkÅ6G2ٶCy@f´0aÊýs´܁[1ÖÝèØZ7bmÀï8rÀåµGS8(ªn5çz߯47c×No2Ä-Î\"p܈әÞÐ2#nӸÊ\0ص%ª0 hÂòÁ&i¨ä'#z¨(Ä!BrFèOKB7¸­²L2Bè.C+²¶0±2´b5¹Ë,h´».ۀ:#\0¾7éºØîÀNÚ;\rÈ0ދ?àPã-@ ²HPÜÑ\"k\\'-bA(ÈSÊÌÓDԂC\r@PHá hR»£@ì´k+6:Ë\0&\$#K\$2c\$á\nÌ(Øîè\$	К&B¦zH-e-SU¢èJԻNõ̯E\nt2ã¬&ÐAiÂ<xÂ#uÍv!ãr03×Ã-c®<*Ǩ«²ój­U´§N*qp£ÂT7\$l9F(ÑÙBÈޮ-0Þ3Ԛ¦ð,C)¶ìP*\rñ\$²<Cì1oæ3¬üº¶abJ9aÎ0­´Ú6­ªTaJ74\n6\rʔ:#b Ðɣ�:¤@ 9bÃÔ4¢ÉÅr\r	ÌCC08aÐ^ú¨\\çËÒÐ3ê\0^2Y­Ófá}°-©2åiÙàçtËJq\rzîÑ¡QÐ8Ïʨc£·hZ5Éã+ÿnXM%»FpA£é#.¦éú§ªú¾z¶kC¹¯\\·>á²×bHډ6*T9mP@è,¶mq¾MÂpЄ:^Ó\"Í9K=r6)áÛûÀäúkt	¡cêRñöbʹMfºO4!~tèLðg^¸fbM®;ñBKîc m÷\\Kr.&%ÇfQt.0ӑ i/3p\0kÄXPNK3ÎøS\"Öü¾Öa©{É¢C¬`1y.iç¢|¡ e¾°ªÃÈlH .tÎȐya¹# ̊<74ý°լýHYlýÃü8w\r¤1¿f@fw~ä,1p¾áüA7æ!0¤id!å¼r\ng|渎â@H!5f|30èРo_ÁÌ5¿æðhÅvoù¢6Hy^f¢0Þ}d÷(¦ô2ê|Mf:䑞5q\nc\"g¹úʳö\\Ü\$¡<)EfjÜl&òRK9\$ûJ@m4Ô2J,)\n\0â`L éA\r(Uì՞¯͐@=DH&Ï,²¦B`-AÈ\0\0%!­E<é,¦Â_Ò\"ÒÃÖF©ã\nDH£ÑZB%wT¦ÔÆùULD=&ÄÏIÉÀp¥!¦¹ÑXla­³ôª]6тq»£@h d y òôLýB4ӌ*@#Èeç'¤zJÌX¦)N4ÃZ:©q½1²PGYÈ1)\$=é«æ]K¼ÚI£CT\\q¨\"À(¾¾ÏÀQ¾/è\$ÊĊj§ÕÞTjk­Dæ	ºemh٫0.>Ï%Jl²d}}0鶰ªÓmK¯-\Ä²&B)¥·:ôÿ&f¹¯µCX[òD5ò¿T@";break;case"de":$f="%ÌÂ(o1\r!Ü ;áäC	ÐÊi°£9ç	ÇMÂàQ4Âx4L&Áå:¢Â¤XÒg90ÖÌ4ù@i9S\nI5ËeLºn4ÂNA\0(`1ƃQ°Üp9Í&ã Å>9ÔMáø(Øeç)½V\n%ÅÍÓ⡄Äe6[ä`¢Âr¿bÆàQÆfa¯\$WÔún9°ԇCіIg/Ðá¯* )jFQ`ÉM9ß4xñèê 0·Y]rgÎxL»SáڸÂ­@wŎBþ°òx§(6ÊnÍBh:KÖC%ìñ-|i¸éîz9#A:Îù¨W ª7/ãXÂ7=Ép@##kx䣩©¢*PÖæ@£ȳL±Â9¿Cx䩰Rfʡèk¦¤1Cˆ¨¢:³)J\0èߨHøЉ\$ÐÂþ±¨ê6(´èR[74ã°!,lÐä	Ã+8èCX#£xÛ-.+	ƣ3,qâù=+^:DS8Ý3²¼=ñè	xαSÜC ÓÖÆK#¬ˎh¨ô«(<¢ÀM*Rè²\n5 R÷B°\\8b°®+¿hě®SRöÆ0©ÂÛ\rÃ;îÝ\r(o¡ìênÒ\",`Õ-Sø\$Bh\nb©¡p¶5[P»a±mÃòý%(9kót\n/A\09>È@Û'%àÆ5£«øÜã|]·xM8êà`t\r¬V£°Ö\0ìߴb7ÅMÆ-!í^£ Å%ÎjEä¢\$dtô\r¤®Ð-.pÞ3Պöϥòâ S؆±Óo°	ò\\3\rã`Î6\ríA@ÐcpZ+%ÝZµÔäLßY¥ØÌ'ñÊ&¬q¢|7!BZ±¥C`\\éH·¥£¡©j¶°±ëzî¾ÏlRɳmCµÝå7¸N÷èîãvòh0ɕp<tµ(ðX×C÷C@ öé¤SÑ_Û\rÚи0z\rà9Ax^;ùrÙ%ÁtD3èo¢\"¶ÜAóixÒ
sÆ78n°±UàÎ9\ré¯Èê`\rÍmgÇp+ÜhÜsÌêWA&<aÈ¨dú¢ѸOÉ÷?Gt]àewÏá<Gò»Êv%æ¼÷¢Axe,´'°»ÂHm½ñ£¶ҿØ>,È5²ù\r5©®R·(FÙtRÖ`ÏùÒBâñõ\"tH\n\0´)èJfJëcÈí¦(bXÐa>+\0=äÔtFt­EӝµäÿZ;¤b%	¤ؿ£{¢¯µp¾àæGO0hDd¼ÁÀ@ÉY9ÒeF±V¬JqÌtæĠèi¤1NDR¯M ((¤ dFÁµÃXÝ½^æݲ.[ÔNIùÊÊb T	0&Dÿs\"ÍV<yTÜÔ\"Õ\"HØ\"®LëW.Éc*â~r½4aÁÖÀpèk_j膡UDbмCY:@X0¦0 \n¼ÓÕ{1ÑØgl)QüCPQMKüWö_ñQ±\"ó0_q2A¸=\$2XÇPCG@Ð\$ȗô,BK£\$n4£\\	ÔG\$\\92|ÈڰAçЎƂÔPJònÌÓc*¹£~¤Òxک¹°'ÑU@ T¦\\K	gQ[1<l&¥ч¦Aê>ä䝓ÖZÇZr'꧝ÊÐk%4QÅ1Ö3ɻFÐOl\$'¾§`¢*Ú#ý@ h[©{§yÖ IN\"¨r®3|8'æ@êa\"9ì,ÔS\0Nò\$R¨;ÚÂ|GLԪk)8mªjiŎ@ΉÚóGfölÎàp\nlØ6°¬WܳIëIäRÛ+Ҟ¢áu\"\0@B F àұÄxAÑ\$SvÌÎZ»i-1NowÍb6go³Ý%6£Ç@¸èõx:eõX84Gñ &LaKtc(6\$A暯0Æ\"ï'dñT+ÁfIBí)ÌFuÍ%8¼è9Ho rá³G &ÄÍU±nY0)ÚtéP ·òԙÅ4YTF§ÜüÕÈB²\r×Ä&4<`H\\äT)Ë́VVÛ>X0Àûxf2,!à[Ô~\0";break;case"el":$f="%ÌÂ)g-èVrõ±g/Êøx\"ÎZ³Аözg cLôK=Î[³ÐQeDÙËøXº¤ŢJÖr͜¹F§1z#@ÑøºÖCÏf+ªY.S¢D,ZµO.DS\nlΜ/êò*ÌÊÕ	¯Dº+9YX®fÓaÄd3\rFÃqÀæÐck[)>®Hj¨!Üuq¨²é*?#BÝWðe<\$¯«]bè^2³¥n´åõ>ã¡øz< ³TÚM5'Q+^rJÙU)qs+4,eÁrÎËÄ5ºÆ-¬¹ç©3J7g?g+¹1]_CFx|÷-U±³¤tLꢻ)9n?O+øô¿ë¤;)û©îò©Ij¶èãtP#öþÁ0\nQ!ðsß'®\n|W+ÌÙ¦©êâI¦HsÙ¬H<?5ÐRP9î»~É%¤3ÓÅٞG(-ó4C²OT\n£pÖ7\rã¸Ü£ä7I°0Ä0c(@2\rã(æK¢:Á9@æ;Ì\"ÎP#K[ÉDrç())JNë¢O1~ô+LR0=ò8¥¾*ªqt¡.é:M¬cδ­izb­®m\n»­ËòÉ:ê¥ ĺÉQèn§¢´±Ir\"MUqљĤ E>FH	>Ï!dh»ØӷkAF¿v%ôÒPÙ(ͣl©7*ÕÑ}î¢Í*)(WQ4àÚ.½¦ ÅƧÚ(̦gbFDfvFá\n&NÉå,§'ÎÈá(ÈCÈè29IMÈ3X³I`\\A jp±mt#tT~º¢ꄚ©¼Jںú4__97R@¤ºöc9ÍGpݏAvóÅlÓ=&PH1Rg!ќMí{SJ Õùn¢åà^-¦*vô.°뫯¯§ó¨a+{¬\0RÈ\r£¨ç9\nÈC(è:RØÃÍKÃòã|r<7Ã0Ò3ó/AÑT¡\0Ó-¬Ò9ÌÃdÕÅÌàÝÜ÷s{Ō£ÆN7cOQS ÛcPAº Ø:H1\$ӹ1ï ö¦6»>äz¢ê.m«!>ü:íÝ¤\nªü8®Ýz\"­ëõú\$¨ÆʴJÿJ1@[\n©HxÊ\n¯@ílנ÷Ä\\)@±Âs.ÄÆբc¤ÜZI)DFàýJ4¡º¸ú§!S~4ÄÀeyK´R¥ø¾6,J	TpÇÞ)#«\nx¬æ2µò2RÈجVg È|SكKyÀfL	6\n¡͆´ÞëvaÈ]]冐ȗA¡ʆ`z@tÀ9 ^üF7Ç8/¡¸¦Ç~T@úK&HÖëÝ¹AñX£	ãùdb\n4D}ȂoÊ¹tR Â\"+ô.Ezÿ\\Pɑeþw	Ø{àº<÷cürCȐï\"ähnK ºHI)(ñ^;Éu\0¼95Öý̑\\«¡öY@|º'QG­ÕIòB;äU£ÔH+'CÜ8ô(Ôþr͚&<%£ÿPʎ=ê1â]\"£ì=SÚÑw¡)\nt¦(â8Æ Кc pÒc\$MAÁ.ƀäC+#!\$æÃna:ÓØÃ;³¥¡¤:ASSS¸§\01¹Xî¤Àa\r0g, D	k¤ñk¡§éW½aRä|ù*J0üê´Âئ-éЧ\"{ÝT¬£°  ÁZWª8¯ ªÅEŹ6táºmR¨×SÃx È4j^C=9³IÝ7»tàӕZ¨áޜ¥'- ©szôOz蘂Ɠ%ÔÕi¦psO5Ù%º¡qCuO	é>&FÃ@iu:8yLª}'ªÇRΑù¡<hȩXS\nAf1#¨LQí_¥̏55ñ*àìººµ,«4ñ¶EIõ´rJ9âm<¸Rº OB¶*¢4¹®Òàvé)PµAKÏt«_µڳÒåy¾qîÓß[9,1-ûÕ0|Tnh9JÓá¼N13´÷/)ááZ6ð(×ÚüJ\rþP T½¢qUlPñ³®De(`Êa!×©5û?	Ó#lô2DÅ0ëDx#\$¤^å[فBDØÏ\\¶hR»ê\0×Ë~°)<*X<O1Dðä#s«±-ó%A¾¥\$+8±u\"2ӫII.é_íc¬õÉH٧3ÑD0Ð>½#üëF?°á\n£9'¨K\"ßJ1))%:ãC\\XÙÍØ)ôÂhQ6Ùv]8x¹2­ÄǶ|Ã9@{àÂÔ\r¬1ˤâZJhæò54>)GN´¤QZ§(zH¶ªÃ6RNfÔ¢#?2U@ÄQ¡Oª0-Ø2:øP5ՈÏ[SdÂfUËèEx7«£jÄP/NqС(¡Yyoí:V^vI¼7?ALÍ^9\"ł ¨¼¹Âô¯+¹/°N\"9,B|Úc1A¨ÜÙ'Mf­\\Kp¥ȏ?nÑ7˅VÞ\nYÅ1ÐùyÁr-£ðùÜÄî{`6ä7WÐĎ#f`ĉ_ÉFî÷ÄâèFÎÒáEúCx¨FåL?\"]sª\"®O.öÜÃy/F»ÒØç\\+!%y/\0£ù#Q7 àÆÃ9óÒCùÉ³æÕp<ïðàGØd`5Pf¿&þ)`Á=Òã Rwú%òPY#\\PB";break;case"es":$f="%ÌÂ(oNb¼æi1¢ägBM±Ði;Å",lèa6XkAµ¡<M°\$N;ÂabS\nFE9͎Qé Ý2ÌNgC,@\nFC1 Ôl7AL%ý\0é/LçS¼~\n7MÖ:8(Þr4íFdJ¦xç#&̆1¦*rLç+Zí	¼oX˕.ËifS {4ä¢gØÓ¹C¡cpÆt:Ý\r'¨Ì*O{0ßdd}ÈɎÞE·ç!æ(o7-[ØNNn2Á\\öÔAj ¤üH}CÉ2f5®Hl\\ñ¾S9㈧+/js1ò\ræ3OFF&5£ü¦¡~:5Løæ7¡®ÓZ8/Ø ·3ȷ\0ê ÃÃs[ó ¼¡îB'ü@¨®+Z¤,ÚF'eÐÚ2²àP2£Ík4-ã!)¬DOPÒé\nLã¦2½Ã(è9el*\r(j°«K¢hÔ<9·²zHç-ïÒénD¯¥r07®Cs¦޸n;9N'£*s(²²»¤³£ò¦4`AG)(ò-Há gN2;g=:.& ÈÂ{|É1ÂcҦ1#KSL,Ç2%\0	@t&¡Ц)C È£h^-6Â.B´jؽ\r£ü¼¹M@8С\0x0§óQ:¯H³(6\ráà^0ÉÏtÎ#s\n±Ðí|E[%¤b\"0µé2FåØzb\nLQ'ÈSÿ¤è(Ù+¾\nc:϶њôôc£\nF)Ǎ3¢Äqæ°¡õ4ô&¨óTµ/0D¾VÈR\$۪b½RCk¡eèPӝC+³\$µ¶à@a)@Â7\rhXÍCëPÎ#&P�¦®%Î\r`ÌC@è:tã¿I;|x-ã8^ÉõHÂå9xDrNRׁß/¥ª\"£&{¬C³,4¸PƸ@H êD£´Ô*CB0|%-²ÊÈM\0ÿ\\Ínò2ï{îÿÀð|/ĭ\\_ǍÜxñò¡Ò\$£%¼æÞJÌPÆ^dxã¯2X>RâlCN4¤íúHYAE,tÌ \"ãcôPԐ«ýDd(8(VuËY>'ڨbPdSZñêÌá?яöÄqђg*à'6¥áÁ#!º¢° A¾	°POI:+â#RZHMTLt¢¢Ô)Ú5FrÄRJ	!,½@äӎSþ\$fi7P^B±vf¸1´h0~O¢@\$ä\$¼	Ò? ½R6Buö5°C*;0嬆>ˀPT\rᭃ0¦4ļô<@¬¡Cc#,Óèܙª&¤m³ÆpêK\"\$â:3%£c)%ÞòbJP1\"¯v-¾÷øHp.\\â:t1%JE§4çÏ\$H§Ä#Ð\rL­µÅ:r#¬'41æ% ðdÀP	áL* g'á4`: Ŷ2k4¢DU3rRJÈC³dÎSclª@«qHÕ&ٚdÒBÌ.Fd\0S\n!2(TùÂ\"a*D&LiÛ}§ÝÈh̋y#QßÈc҈ð\n¨fX#a£\rBõ1xÿeҒVl± Íª3µn®Åz¾kã	i\\6¸âa¢b1¯CúÎ\"ËП´`NW\\kIaH¬/AT*`Z	2SXlSQD.Ë äƍÊBd!Ä@\$­ä®5¥ñ«ÊÉuFHÁÂQ-SÎÃÕNTç\$lÉ[æg3STæ­ôÃd¶¦mSD'yLëN}\$¶ 	|øH呓\$ B¥½X8ït fTIe¬@ÉÔÂ\nÌ-;&HëÑwKºrcåÿ1	yüКY¸/¤*[RìÚG\rr%B5Qê";break;case"et":$f="%ÌÂ(a4\r\"ðØe9&!¤Úi7D|<@va­bÆQ¬\\\n&Mg92 3B!G3©Ôäu9§2	apóIÐêdCÈf4ãÈ(aÇL¦A®0d2ࣤ4ÐiÎF<b&l&+\r\n¹BQ(ԉDÔÈaÍ'8Ó9á\rfu¸p¿NÑI9dÞu'hѸßµ¡&S<@@tϝNó¤hégáPù9NI9á°;|)@jßjC¦,@m\"ûٳqßï¦|ÈÏîôFã=ZqFÝ̶µ`ëº*y㹸@e9­Rr!eX\rúlñÒÕ#ü8+îµ/H:½ÌZhè,Ïò\$4¬k¾§C|7ã¨Äß©[־HēèÚ1-i¶ï5NÊ;:*ê-\"ã·#HÈKpÂ9B²B9\ra\0P<B8Ê7¯走\n¼0¸)xQð)+iSQ\"KO<¸Æ\"ËDù# P7­¨#ÂBSè;\n䪰¬ß+ÐÔ2A*¹¨ MQ¢Ð<¢\0S<°\\Øb	ã¢X2c@Ë«L\nÕ-`P 4#\0*4pï2t²Çâ@	¢ht)`R6ÂØóe\"슉º`É*ÌIێïàÕN=Ì`A!ÖCÈx!òOm[Þ7Ëbl2Ü÷L2#ËéA¶hլµ=ãræ_ãî2\rhܖ]ðÕÇãd®2ÌĆ\rã0̅#-«àÎHêc¸ìøÚÆ!c 9èؼΈÀæ4ãZ0èCÝ|ÑîÃÎ2RÈÙ+6g.Â4ÈËr\$â£4õ'#6\0#8@ ÚhÃáxl|ø[-ðÑoÁèD4 à9Ax^;ïr)©ArÔ3ð¨^ý`TÞA÷ûl7­ÔÀæcNR÷]÷I(ùä6AÂÚÒåë\n¥Hʓ¥-ÎÙ12rî<¶m¬Îá¹n¶ñ½o¾ý²£<Ü.\náHEn	#hàʣpéÉ^ò«^7«C#J*6øehϭ1<µß|lÚ(+YZó>Ê>³S\\c÷èNn]rkÁÉ*ÀÂkfÈĳVnÔkü(¡ ӿæR¨ kx°=`ÒIpE(e½C!ßrɐ26V`¼nNýJàP	B!D,ëÊJ'm,.æª_Óa4æ|8FiLz«'0¸þ®0ØÔ»·A²ԷZ¢+kÆÀWî|	97ÁÍ3%ð{{á\rÆA Wd¹gpPvè¤\rsøa:µTYYF) çYî#ôÃ:@ìZk¥tÆVKÓ\$dÄP\$4hCHy/ná:ÜMhùL¸ \"¥r*CarÞ. cå&[#¨V\r5	0	Ê4¤(ð¦jÒ{DiÇ÷2B û?äÚVI>pÕ4²th»NrE8Öôꖄl4\0Za% t2²æAãTÔy¾L-)L\rà#Hx´ÃÒUkàÿLd¨Aùð\$á,簾¤C¬Gjì8*ºieGڣV(Ì8%«¿Ïq[¦.q9VJ	jMPB¨t|Ñ|i§VÍÒrÑäJá°²Jê¹a'Ô\$äD\0¾B¨T40ÒENá'z(©*Yk%̰³&B¨øeCô¤«p÷Hf (ŵ´fI£\ræ`*¤@RkzJÊéæOq¼Iӛï\r¥¨ücfkÝ	S¹PwM#;!Pá¡BJQÍ;6µU¨úÒa˫úEá-¨.ø¶Jë!h¬h ´*XWS£µȉº\\¡p\nHø:ÛWIi+À";break;case"fa":$f="%ÌÂ)²l)Û\nöÂÄ@ØT6PðõD&چ,\"ËÚ0@Ù@Âc­\$}\rl,Û\n©B¼\\\n	Nd(z¶	m*[\n¸l=NÙCMáK(~B§¡%ò	2ID6¾MBÂåâ\0Sm`ێ,k6ÚѶµm­kvÚᶹ![vÍÉM@¡å2¹ka>\nl+¡2HîµÂ#0\nÈ]SP©U!uxd)cZ\"%zB1°´ÀC2êÌ©o\rä*u\\¤o1ºÂgØæ{-PÍÓsóéW㤵>·--¶#JìÜKËæÄê<­֋TÜçsüüF¡ÑT¢Ì/\nS0&ã>l°`Q\r{US!\\8(ª7\rcpÞ;Á\0Ê9Cx䗈è0Cæ2 Þ2a: ¨à8APàá	c¼2)d\"æýêrԢŒ>_%,rþ6N\"| %m¢T\$͊S%©楨êJ>B²M[&%ES<¬ªÀHÚPW;æ¹'ﲲZ%nºôS´,Í+>'.r%!ú²R @µȩb̻)Ah!¨2³ÚtË>ã8²&ò\"ÿKMLÊ5<²îB°PüÑ*ÌÏàPJ2<nÑTÕumFÀ\"4úî³ÓøH¡ g^¬[ðȑ2ôXόdÐزò́>2îìئÓ]N°ˬB%QTÃGË°Ӊ\rB@	¢ht)`P¶<ރȺ£hZ2q\"Ò°þUPÈÚ:pàx0¦Aðä2¨å8¤0!à^0É~cxÜ3\r#>\$2ãXä=r44pÙ\n&A\rٖiæù¨Ê<(Ü99[ô¢! Pä:\ri!!å@)«Âë\"¨RÍT9)QcK0ƨ´«¦3ª½Ç(:)A6q±jõbƐI¢Ôô\"2ÅZô0#®@µ1³;SmäF®Anlñiº¦©¾õVo÷\0WÅÍüð­ځ!q;hÙ:ܖ㾳ÅRq«%b2tP¨4b£\\23føCÖ3È6½p8Xz0Ò2@á\rxÌC@è:tã¿Ä>?ÁC8_¡ð¶r:h£p^ßtáå8í\r7Ê1tQi¶)ä½æ°]\n9à2MuM\"¢£\n±°M½±&ìaÌyô!EÐệ\0B.!íݳTtôިezïeí½׾ø_åy¹çÒúߓ?h-\rø¿66EòÝq¤<2:þTQY?µc|F/!,3Ít #UÓ#ýD¡(¸òByIù%pDܗ\0§vc~pÒ` HP8 wheU34äCcCÌ3Xòxge±Ø:A%P£1/1½\0Üülhñ¹\$nۍåYÊ\"ã|ª¥æILh \n (?è²J\"á'#ɾ%d\nRZMSBµÂ,Ôѹ/d!ºG'%Ãx È4¦fäÚD(e! އ%ò	)©IVmѩA=Cʔz|oaì±äS,@²b\0ꈑ\"&J°;Òä³Èïj=!JÃ³H) 6[BÊU(Óg`QQI¡rjW4Vnå	>ÇR©*Ā¤¸ÙrlVR^6inVú*ÊÄw-Mӗq±X\$0òêyó!0ܫ\$¬åaáÄ:¡8Hmx¯Hy0CBr^!G8I6*j+D³]`Ê~KÄb¸ELëڝ?e>òõTâE:kvd¬Cªj9tQ£.DQ¼^)ÌíY	Zꍂ`IhÀüÀøºª\n3Y\$¡L(Ç3<Êr¥©Èću܂0T°hËHj 0s\nm6DKOSÌJpå6*'b]\\Éð56¬U*~2MØËØ	w	|&»êN;Ъåë»Å,ªmzñTw̹_RvOf!ɰM´ì+¢xÜgqé2Ë·luƇµkÌÁL¤®ؙҟíU\nZgâ³{4×BÝ,~nu¾2B÷zuL(õÚ0#p4d6©_Eþ屉0púC¤Ô´§ÝNMÆRn6Á39ðñ¾:Ø6xBÊQâ1µ­B\\ulàáp(\$zɻ&ѧ¬PØ1V§qDmrÆRE^úI\rÆîß/ìxÀnâwTÔ͟t£¢³iūF\$­°´ù«FõÏà~ä\"ÂMdÒéXÓr._rÉ¨";break;case"fi":$f="%ÌÂ(¨i2\rç3¡¼Â 2Dcy¤É6bçHyÀÂl;MlØegS©ÈÒnGägC¡Ô@tB¡ó\\ðÞ 7Ì§2¦	ÃañR,#!Ðj6 ¢|é=NFÓüt<\rL5 *>k:§+d¼ÊnbQé°êj0ÊI§Yá¬Âa\r';e²óHmjIIN_}Ä\"Fù=\0Òk2f۩ØÓ4Ʃ&öå²na¾p0iÜ݈*mMÛqza¯Ã͸C^ÂmÅÇ6É>î¾ã㞄å;n7Fã,pÃx(Ea\\\"F\n%Û:ÛiPên:lنähA¡Ü7½£*bnû½%#Ö×\rCz8\nZ#Sl:c٨éÒ &ãä0p*R'©(åBJõmø@0³ì@¹¸£L7E^ԥÎâð+G	è#£zJ:%Ï#ÔÔ`´#N¼8ޗ={Ù)\$JTĐ3Ð4L\0ê2 P¶Ià<cË\\5ÍRjî.ì@ª:ÃI»÷\0A(È׎èCQ\r5.cA j0ÍÃËGƑÊ>å	«Nø¤àP¥\n·ªJ7JÃ%>+#í;ª\rÏ\\Ü\$Bh\nb8scµ; Uhº¡6Ä(aà¡Óº¦üf9:ãÈx!òSm[3\r#:Z2Ü÷L#Â\nRðÃa\0×'e²×Z°z_Òö¦¤,ÝÐ»^_LL\$Q`Ù&+ò3É¸§\$M£éE3Í-Ø/¨[47ڠá(à¶(#s÷´XD4é-tñ´QK<¥ËFRòx³©edñÜTc7%Â`¸/ɣd,Vjfïò!î®4h#ÔèÓË`¹®:=zÐ\r:¬Ö2ë\n­밆`ì`Pª6!z<õ¥HQ|£L!£ü÷á0SØ:ɭ®cJ٨¢=²ðÌCBh8aÐ^ýè\\u]-ázî¡Mºp á|ø#\\­Hªk®íYI§޷P\$ó~ìÏÃá öí5¦ڲL2®ìHøAl@Ìқ.íJ&«\"&uà؆Wfí]»¹wnô;»÷N]ÄxyãâÓÌKp* ãBGê÷©,7¥öO o!\$æC±l¤¥ð'²õÌÚ!caìØ[ ʸ¡ðæaiÝ0äÀê&\rwsè*9rjؗóëo,³wfWû¢Kæ°÷µÈZs{îp¤¤½Ï\n	¦ºv.æO \n (!3Ãã3Ca\0 ¡ô	ë;§°Ó\"RØJB˶¡åY:E¾EM¢nd4!\0àØÀi¤ÃHé\$C÷&dÔÀ¬Á&1gv,´ðrIÊ2¡ä<ã\\ëJiN¬ø´㤀Ó)·D¤=CÙLHÃbvÇÁÍ,é`üav&) NÉãEHä\0[ÁìÉ(0`àÎcð\rÈ »B	§&ÅܒÒêçñÓÐÕ)O\"ÃÌå	h찂ÄE{	FqÉÑ\"Ú)ë{TKÃàÀ],;¸ñµµ:¥D̸¢9-\"A@'0¨V-r	îmrÓ!ìÃ:CèybDô7/äâ)§\$îeÊç­ÑÀ!ôÅo\0¦Bc\$ªgÐ`¨Ó_nì ßXKVÞÊc	ɔ\"HHáFPh\nOÍ'ú[õðÒÐyìKëՃZ§DÖz{ÑgìQCá'd	vE,¹( ¬¹Õ~ (!¤ÀØ\nͻ¸nõ 9Q¬×NóØCV%´zåäÁ!áT*MðqÌ!:dê0Ƞ`%ĥ¨ÕVë`Mɱ?ïüӓc²Lѳ]1|»YÂN	PuGt3\naÒë¢5טât&KÑÍë¶Ù³XkªI³T§z77:´ªшTµ¼e5wV@°W AnQ¦QjâåuC¡èrôJù꘏s8LI½T#^lPٴ6*ªåçLð_Ñ5°\naÜÓöOùÞ&²OðmÃ~8̟Àå\nÜ°ÛGpîã¾xAá'ñÈò%él2<ո{\"ì{ ¸";break;case"fr":$f="%ÌÂ(m8Îg3IØeæA¼ät2ñÒc4c\"àQ0Â :M&Èá´ÂxcC)Î;ÆfÓS¤F %9¤ȄzA\"OqÐäo:0ã,X\nFC1 Ôl7AL4T`æ-;T&Æ8̦(2DðQØÓ4E&zdÈA:Φ脦©\$&̆ôfn9°Õ',vn²G3©²Rt­BpÂv2Ú62SÍ'I´\$ë6Nè\r@ 5T#VÍÞ§MÙKáÏxrrBáè@c7ÓiXȃ%:{=_S­LÈäû§\n|Tnòs\r<ì¦æÑ36΄Ü3»Pªퟴ�\"L£n¥ÎÀÜ7;N15¨h»#s\$´88!(»V֣pàÚ7¶ôFª P¬2©ZÕ°\$\r;C(ð2 (\n)ª`çE¢pÞ6L¢\n\"(ê(c@ÂaÌ\"\n!/£L¤\nLØÊ0 PÉI쒜B ò8CªVùʲÒÆD¼ô=sën1)ì.ÖEük,»J4«!Qå ðÒO²գ)s¦ T¨£c¼Tì:A(È\rã0:5uaY7H* 4CsÆ¡xHØAÐ2¤͒è4¨¤C+\ræs:JBJ6uYR\$ېË)\0Âû@t&¡Ц)CPÔ£h^-7ÈÂ.¯ïÊpîµðÄ\r ª\0@6®p@*\0|Ê»1s0!à^0É&nÊ=\0002Ö9\"&ÊfPÛöðµÖ¨b¶æcVw²²9¨oÌ3΃E»ÅRÌ\r³\\½° ¦µ>ZTÁÌ\"`ñxák1ô2I¡ª5.ó_Tb#s¦հÚí£p®úü/°ër˦»FÕB;k|Ò*\rFä2n%_»Â2HÀ3#*Ó3ë/\rÃZl3\"Á¦á\02rÎÓ!¢5i ÌCCv8aÐ^þ\\0õbFá~zÍ&ìá}æԛÿc V¦ÈRPá3C{N«m«N6RÙí1\$ñ{:ÈØ<QQ ÇÞû4Tº Ðíúñß<ñiÓy(7<¦xϠh\"aÄ8*RªÍKØ#d6§&Ø0a#ÆaÃ(OÉO>£B!ªeᥛ°ì÷ù\$fé+C56ýø\r¡¶*{¨&ÁÌ6eºâVFIƉI´FäØ!Ø90<)µÊSʉÇ[Fæv#*C\n (@ê°tࠠ#JRXs!!¸JIÜÝMàÕǓjµK´/lÒr£ª5/G¥ҤHûÏ3\r³ZU0yb®ÌSX[C%¯ÍE×ìuJkûÅÈÓoM¸i&ò\rtØý T\r᭞\0FAIC¨щ,m°´¸pQH³\r¨Ê?|\".& 6¶¼B	Û\nIú)¯&Ô2¡3¦¤Aæ&ƔӶÙ0ÏfìT8XÊKȜ pF\$ÙÕÒLqS­¶ ÂÑhDҘ\0¡(k 3ÄPMN¢i!¶ÙÆJ	TڍÌ	\rRE`dÌü}TؓÍD´3R!³o(Ts\nћcP ¨oxtÁÒèÒAÝ\naD&\"24¤u:ÌôÎ-6Dȩ;´F¢RÕÑCª¡U>©V¢¢=R{Z÷]1èT¿#G¬(L°õô¦ÂOcaéd[-û(OI=±¨ǆì­U}福{h¬q´¥ %Xc@²ñ¶Â5j26Èâ«dHÉ*(Ää9EØi_¨|Pg§byÛIB F âÎ*ô\nÌ%³:<¡¶ô­®úbV6ËÅKÈEØ!\$(DTzWAì\0b¢.2RT1&Ö}¥DÒ\\s  Ð+ö5DqÌÐOLhròøÜ`Gh	2¯)q/*@F «HBïR\\Hũ«ABªe!\"J[¦FpÏ%YpxLuèjÔ¼AVò¨Yëæ\nRàl;(<,ó|ëÐÁ+-eË\\/ö.\n©kÞ]E^1¼6\n<Ñã\\ic\$ÚE)\rÑV,ê!¢u³iÐܻ·zïÞwxoÂ@¬M{õäX2=M\rΒJ'°";break;case"gl":$f="%ÌÂ(o7jÀÞs4Q¤Û9'!¼@f4ÍSIÈÞ.Ä£i±XjÄZ<dH\$RI44Êr6N\$z §2¢U:ÉcÆè@Ë59²\0(`1ƃQ°Üp9k38!ÎuÁF#N¤\n73SuÖe7[Íƃ®fb7eS%\n6\n\$sù-ÿÃ]BNFSÔÙ¢Ê ðÑÎz;bsX|670·[©¤õ«Vp§L>&PG1ü\n9¶ÛäµllhÝEö]ÄPӒÊqíÇ^½k£Á0óÍà¢äå&uíæQTç*uC¼&&9JÕӐ¢³¨: ¨à@Â9cºò2%ò#´&:¹«¸M2®­2CIY²JPæ§#é\n¢*®4«*ÚÌ\r©ú?hҬ\rËØ!)ÃØ!:èØÒñC*p(ßV½ ҇4ÉÂ@7(j6#Ðç#­.jòö³²3Æ!¡\"TØ7­('élè1§á\0ƄN­ƃÂʲ'ãÆ\0Ä<«@MGRàJ Ü¨^tøbòC),Z¸¬Ñ)¨.ð1qH\nc*'ZHëèä#<|	@t&¡Ц)C È£h^-6¨Â.×ÔëZ¡\rÅE ªX@6Ã x0©aó6َHKÂ#o8òã|\\÷Læ-ìÝé{DȪ¥¿Q\"'Wa'qCì DàSÒÝ ¬ý~ѥpÿ:&±ܔW#R¢×óÂDÜ2hE©j! µ®ëp)¦?äAf#Iº(ªP¬F¨#pëEÀHÍù£MQ-jä1&}ªh9Ì7è¸ëéh|#:ǰÂ(Â嵲¡EJk¦B£ù4Ã4.0!\02i)þ}¢	Â9sǌØÌC@è:tã¿,H<ò¼á{\0¨«ꆄá}пUÿ{ÎRë?·ۗkHl&3B°c>8Z��3ôÚóNëְPÙ?u݀\\qGÆq܇%Êrÿ1À:éø]ÎsÃw<<DÄ]\"J/CæãVÜh]¢à0SÏ\nO| <9¼dM3mdId2&gÑ0Õä:J³k6JôsTòà3ûoE%/\0ÌV;¯O03à@9ׂ¥4²FA\n5OÆl(Ò]X«DYò1&ÜAR0!ÃÇÊۍ0P	@ôb(()`¤ܛÁBR%äDæÊ}QOԑ0ÏPCS4È\rxÄÀÒT^Z`BbMEX÷bÇÓsÅ\rPëbfë&lf؂ãÇX7kb8°T4\"¥ÆC;QÄP:BõLڠo\rm!0¤2 Ђ\0¤0­\"%ߑԲ±k6&£áD0¥M#gâM	³^TÄ]/'\"^z#áÀÂTAZD̑¡S\0jM#CÆè5jșÉÏ\"y¿¹¢¬¢gpmºvf_R8\$'0¨ÎY7~óÏ\$NHd¦Þ_DFqàÎÈƙ¸&ã:!ð¸Ý8ÀR':ÀÂ8I;*@)רbï%¸	raB0TZjêЧð2l.Ó\"L~9ymNCy'UJy¨fbÍÞ©iMµ?\n«X \nRuªÁåÍmk}VGjÞQX¨CJ!°9âhbz&8ýqͨ蠨Â=Èu\" Ü'ú´HY£Â!!T*`Z	B\$ÔS@b´qDøA1Kp5v·ó²Æ öU.©ý+OÉDPf*µ2@)%å~{\\'=Osgz\$5ÏÜ6ՐÒNª1§Õ§\$\0äÍÁ21Ýo£{Íz)é>M®93Ê	á>°ʢ«Uª5AԢAUͷá\r0+âÒ\"z¿}XER;EkF·f\$Cz{6 ©\00063<FÀ";break;case"he":$f="%ÌÂ)®k¨éƺA®ªAÚêvºU®k©b*ºm®©ÁàÉ(«]'§¢mu]2וC!ɘ2\nAÇB)̅E\"шÔ6\\׎%b1I|½:\nÌh5\rÇ;* ñÂbJUu<UBkÚÓ0i]?³F'1eTk&«±èâéG»Çä¸~_&¢0E®A¾dæú4¾U¤ñìMæB¥¢°i~ã¬Íŕ´\"U Éhn2\\+]³í±[´vGÃb¢ҥE¹®ì(ŷMƳq¼înNG#yÈ\\\n\"Næe\ræSºtN/àà÷c»Ê2<è¼\$\rCªÎ6ë\"èiJ\$±\"Ék¦§'*V¡£*Z§9гw3räk·(²@s Æ5Kâ%èäL-LRúk¤{0ͬÑ<Z\$±ì\$ë3iH6QC`¯É0b>%©zZHhBÄè#dw-9ð3ÌÚ_\n1¦»§!)£\$±D\"A bø ΃¦TÆn19	.\n|ÌÄ+©\"ô:rbC@Ñz!Kè\$Bh\nb-5(ò.£hÚ%£lâ,D	è@6£ð)è|9£ ê9<\rñ#Èx!óÏ^WÂÞ7ÃHÏa¶]K¼â Ò9½cá[<¯ÝrÜï­Ó[£Àè2ØÓip+,£µËèä:\rìk&¨m¢Äû`µËìâ8±-ª:ø¸ÂIzN8òªJ¤¡èä,n:<!¨X°8äbÁT#8¢):áÌãvÐ!*ºГÁPLu\$¬hÂ2\r¶óŔFȊ@¸Põà@4X0z\r è8aÐ^û]£é#sÄ<£8_zïÖ:^ãp^É\nÄÆ¶Ð|,RT1,¤Ԅ´ù:¦Âs»§\$¤©%H\$!Élºi#Q©j.­¬kZ潰l[&óm;^åxÞw®ã¹×ÜJ(ÄÍ	iG½\")Ä%à¨C¦ÀåU!ºÐ<nBâÒ*=)%èô%©*\rÂ1î@;#`ØOàñÏ(Ú2ÎCÍyOÈÆ1¾Ì:üc`Þ3ÛÞøÒ@ Ár>°@Ö\0d^À0Åòc±v£\\Q, £@\$\0@\n	è)5*-- ÜٞâÞð7\0àHv|!3¾È\\Oªã>|ü@×ôßbMc\\³1j+\$ѮSÞÁâ>è0Cþýéàqd8SürNAÜ4Ç\0ZHgk/øE°ÆUÈe@¬ bYSaìl£×Éyx\$§LP¸£\\#!©H	;83e­äFC!!¼òL:P&OÈ ä¤Jx´ ´Nf¹z\$çÂ[¢ad°Dk¼3ňIъb\\¬e3E\n<)I,D¡f12|£9#0\"̨L7ðLÔÃKÆLÒ­ápíÉB0PÁ¡*AâJC³6æñ14 S+±¦ӎJwȎ\";Jh˵¡µ[\",jIg\$¦`àÓd}F)æTîHÓÜO°S KÓ+!Dyś£nKû§QÝMQN)/4¦`d#0-y9¦nÇXv5jT£°Ã4i²±ؿTózKI<pf®ɔN¢©ÍdAoT âS«9oLÅ#HêC	rSP³zYQjΑt*àÑ!f\"Ö®á&aÑy%aJÍ&ʏËí\$	5JÂ1dQ! #ÇLNj`Cm4vy*by²ªÁFRÅ!ٺé}N ";break;case"hi":$f="%ÌÂpàR¡à©X*\n\n¡AUpUô¤YAX*ª\n²\"áñ¨baTBñ¥tªA ²Ù4!RÂÜO_ÂàªIÂQ@Ìèq¨*¤Æ`¨j:\n°	Nd(ÒæO)´ú²§!å\"5)RW¨	|å`RÎő*?RÊTªDyKR«!\nØDµJ¯C³u\"¾)ª)Q¨:¤¸PTi´5@¥ݫðÑüñ-ÂÌu±eÁQ¤ü¹I[õW8mR[#Õkn¡H¤U÷È)âaéî²%&î;ÅRd²E\"ÿqÙqo3ÌÏ/%+ïó¨¿Î½Áx/Í!íñ>覭Tè®oûϦ#̣½#®µo[á¢ëSò䧎BåºhÑ(«³§¯Í4µ9<é|6êBðAÄQ\"\n튜:ÎbAíñk\n;hÒNôGî+Å#zæèjxÆ9º(/,�¼	\"ñ¢zRCL¡<ëº\nJ³ϻQ´7)©ìT6û@\nZºLFªÑ)HE̢sBé	ª²[ȊΌ/2êÒ5*¿´e):h+ISÓã2ĨM6*.ϲû¹1R5豢ÈúÌR­Bւ­ÒsO­(ËJ´Üò§\nrPɣ!ÓÏÃ)%´Kd{R§ÉE͚;Ïô(J§Jd)é£EKÄÀRG¯ìàñ²îý=%¤ªya\"¹/PÞ5szV´õ&óݩäOÐºHuêÑ-=E©òù\0b³Âc1\\Ô8սàNY:转ãÊ¹.t¬!hHèA«]ÃS+qÑä4Æ.nծ¥7öÞSÌnͩG¦öMò^\"§Éí2qéQÛô	@t&¡Ц)BØó»\"èZ6¡hÈ2:°òR4óI¥SJ*Ð|ӄª4HP/\nÑJtJPã}¬ÀÒoëÊ-ªõñwX0L¢^9שaÎsÊÈç]Õ\nÒÃÄӤ\\ç3¨»÷¬Ç]3ÛøÑÿyÈÍîCñ2ÔW)>2]÷!´?UÖèNêØÓٌ«®ê\r äö#¨Ñ\" ½°æ\"åD°`>íÍr¤آ*u{î·NM©¾\$ڔùM?>侚â>@°¬²֨Æ»49\08bà Èц+°ã¼G¸NѬ\nYe<ÃÇ4);þ\nC2¬ÃzÐ6'E8Wrà#JÀå«\$Âè=ìçò5]kÉlk\n8À@eÀôès@¼xÌd\r¡¤7Päxrà¼2à^xn!Ð4ǐ^yÍ9¡Ú9õjáá\n:9ð/èLz¡A2Cj©ö»\nqJ]:\"hÂÔrÐ1ýdFQÌȔý\"ؒuZ'^#׬=gÕ­EuØô.>\$ G]³¸Y²Ê+Eµ\"ô`Q3xÑ£dnÊ:G`Ê¤uqö;'TT\nR°ͮ*¥!e9~ÒNS#Sw!|9S爂¥¥äÃ#R4Õ>O\$V@¹ADÑ2gý\$ûjhm;=FsBCzj¥Ë!£\"Ó\r\"DUòÉaJ°s¼)b¥NHÍè¨ÇBϐã;¼ä(ޢ×x \n (*µ:ôé Sω jªy¬(ÓҤx3­l?Dѹ2%yϗG\$I¢PfCÌWJ	j8óӆ°¢õªP\r©ö#Ӵ~+èÏϪúr+úo¬s¥T=sªL¡>¯Íå\0Pĕ׉\"LeTQ2 !0¤ã\$¦\naÅqL9҆I9f á9<®-CËc£èÒT´ÿÀSEg\n*5X©¾'ÙÒõ«H\"NDWí:êryYúEmkh#T%[Ä\\w*­0@6öH§]j½wܴ¸2îF\"Ie	òÉÎl2I\0`H/¹B\0£¿\\X¼á&làÉ-±{eÖ	(WxdbouîÅÊÓY*h_¹͒wbíR«ºúÕFG @L¶B22¥EqNZUrm©ÿ9iîXµímEÈUK\n¬\"5 a}­ ÇY>SMÙN¬àgû:NIà\"àåÍöB²DÌáyÿ]­Û7º,â苺ÃΩÉC»ĂS꒮ÈRզW¡³0Ép¥ÄjrÏ3gƶY¤ÿPŰÒ/;-°%âÈq2ZGÌ]k}÷x¬<FüÐU\nh­ë[1ÆaÂå@q³f9랶f|Kdü9ë;3ÂCq)/Ð%åi´Szy%>ù­\0̕(ºZrTǞöNfڪ޼TÏOqH¥`ê}¯éÊg±+ÍGo½à[?¹ÍYðîKÀlTå=Ls¸r­ÊvcêÞ+Âäª[ǨJ W´ÉêÚHåkiNɊ¤Cb¼òvÇITEvB\$âZSÝ\rÄʗÔ.pg.®AFuÅvہ\r»ÔõGÚəð,Psڗ7 ,òÙÜÄٌ7>î7Çî³ìZ±Ì3aFGq¤¬¥@Ïß?`5eçÝÈƗ;aªåÃRÀ";break;case"hu":$f="%ÌÂk\rBs7SN2DC©ß3MFÚ6e7DjD!ði¨MNlªNFS K5!J¥e @n\r5IÐÊz4åB\0PÀb2£a¸àr\n!OGC|ÔÅ ¤ÁL5äìæ\nLÃL<Òn1Íc°Ã*)¡³)ÎÇ`k£Úñ56Læ¨ԁ­:'Tâd2¢ɼê 4NÆZ9¼@p9NƓfK×NC\r:&hDÌ7Ó,¨ *müsw&kLá°xtÞl<7°cÌêô³VôAgÃbñ¥=UÜÎ\n*GNT¾T<ó;1º6B¨Ü5Ãxî73Ðä7IP޸oãX6ð*z9·Càæ;C\"Tý¿¯ûʑÐR&£XҧL£çl¢R§*\n Ãh\" Ȣ\$ñ\r##9±E³V÷¬/ñBحâCþacÓzã*.6.ð51*e,\$HáZ8«{éÆr\\Æ6L£Ԥ¥`P=3Ȅ) £kèÂCÐ@9H+Y45\"qÚ4£#ΓM+K¨´Í61S\0Sî· PHÁ iT P¸tA)¼I\"v7.lSê5JYB:;æ)bն¶ Ù?ïâÆîCjËbKÀt8ÐÛ!cͼ<¡pÚÂ9;Cb/J2jz)ø|¢0ÞðPÂ<xÂ%Wä!ãpÌ4ãªw~ßñ`@»ŐrhᶸtÏ\rأX¾82\npÜ98(æ	£´#lkJÓÜí`Þ3ɚÀ0CVÆÙo¥ú{\\6ßÈ@:äc¨Æ1°£Ì:Ç7Pλab&9hÃÎ0â66®㪜aK\0¨Ö]:	P¨ÔÀi¨͋ͯp@ ´º«y4PÁèD4 à9Ax^;òpü©l3é\0^c#¦J7á}Î7¯v;ú@Ö0²*BzڍH5ѫ2«DQK;Æ]\rCþ￑_D-Jwp) ÑÂpÜGÆq܀ïÉr`]Ëó=Ad/Cy	#hàÚõ£§O2,ÊÜ%¬&!\"æÆÃoUÖ\$	àʁø l5Ϣ²@ˑL;<ն¢4ÔK@i8§ÀÍ0Æç( æÑ»¢r2*X0g[MiíD7µ4Ìɿ5tȂ\0ÆòÎ@i!±âòڑyFK<úâï̓FPèÍC8¤E\nÒkeĂ\0 @Fꂂ~Ù%L7.݋!¯6&ÌÚ,A58(XÅÔˑ*b%ÃDS±#76òâÃɩ\$hu¦0ôfMawMqâ\"4ph !ÄsY\$!°aM°N=ÇÑS̪aL)bF)e2äpYà@Ë`lXò;é^1ó(i²ZKɉ32<Î¢ÉìK]äâ:²±æxÁ@è\n	\$<`@I­BäKNÈu0¤ô3 ²\$ÞH 1®¨ÂoÎ\n\r¸ţÉV	áL*H#zA\0CV\"Ø5Cá\"rg¯DvՃ³ZhÇù'¨HË9ÿ)f`îLV_(PAº]NN&Q	ÚÃfI0T+áKàOQy%0³§µ1LR\"ÙFãVª©DÑX®»M«G ÅÊBJÝYG%0؜ÒxTÔíRU¬râAuq¬5J¶Wjßáósô4:I%!\rÉø-)Ð2/%AEÝ'¤p \n¡P#ÐpÆìè\r^´)b{æCgþ³׫Ojj=¬/ö¹ÔkVL«9)\$P\$\\j3_²úNù	\r6@;EIK\nJ\0P0Җ\0QÒF¥é=ڻ\nB óDÁ ØrhAdZ®µb(U¨ze¾-\"w9Ѐ\n	òÑó®ob:êÅ<1,Α53æìÙcµ}ò´öÐ9aÄ~e`¹rUÉá¥*<âµK¡+u\\¬Å ×Pµr3/ÊÝ²׃ðLg7çP";break;case"id":$f="%ÌÂ(¨i2MbIÀÂtL¦ã9Ö(g0#)ÈÖa9D#)ÌÂrÇcç1äÃM'£Iº>na&ÈȀJs!H¤é\0éNa2)Àb2£a¸àr\n ¡2ØTÔ~\n5Îfø *@l4i¹цa\$E8µÊS4Í'	½ªl­¤÷dÞu'c(ÜoF±¤Øe3ÉhÙ©ÂtƝ\ry/s4aÆàUãU/l'ãQÖ!7n³S>·S«ØÎ/W«æÂ95í·&n/x\n\$NX)\n3 æâЩx(«6ǝçӑ¼å\"\"CîiߚÇÄyӇ!9Îþc\$¢9:A*7;#I0èÄ£XæÐ\rËÒ|¤iRù¡(Òڑ+#:>%ã:068!\0îAmhèɬ¢jABS;¢8Ê7¢QZÒ%\"m àÄNØ\"HèóB¹_\"Bj@:r¦Çãڗ¢rÝ#i8ê£\"7#9ÊJ1àPH¡ g8 PH¨^·:m`ª96Ëë¬:\$RµÉ,°Ò4Mxîè	К&B¦cÍ4<¡hÚ6£ ÉD­Ó>æ'苨\n~C,9'\rO\0!à^0É[Wk Ì4¢µu^Bak	½ÊàËÙ/0@ÙìmSiU#(ñ/K\$(ôÔ ä\rXÃ1	;7Ã2Ð7«:Æ\\bXÊ6©:7²Ûr\"ϘÆ1¤£Ì:ă`Þ3¦£X£CÊrYv\\ʈ¦­°ÊaJ±KÔH5Þ÷Ë΃1I²X3ZN£x# ښ£¸xÇo!èAV±5ÌC@è:t㾔'Yàó¼Ã8^ït£¬xDjÈ{yc׭jJ4±+óäP,ôù¡iæJP~tü?Oå¦<Mbþçkpџh£iPï¦fY¦ 9jZ¥µn\r:ÈEW-S.Ü|Í#ÏY%û£·ò&:=¢,8[Ë@%@Ò3\$B Ðƌ#°ì´H2(îY-³0ÃpçXüàøNe÷­۠\n\${ßN4Àð r\n2֥9þå삀(+2uIP?ÿò\$VÝxwY@d¡3ȟbXi9\r(;¼T8ÑG~ä4;Wn¬VS± 9,¢phi5:gܐ7P䙃¹£divJ^aê/öÉ	ÀC\naH#?bp 	mè԰fÁÃ\r¬!½0êAJ,Pڂ(©m¬ûbBE&dԛÄþP i\rdÝf\0 @Ãɇ&)2&çÍ,V!Ä:R (mf%§ÃpØTvzhø0Ðz\0P	áL*b:OÁ)\$Ǟ!T\0E\"§'*Ä%9&Ɋ±\reܰÖ) lüÂÇ(fNë\n'Då3Ā¦BdI@ʖàbOYF¨û:8 H\"ó#¤\"¥ó&JC!2ÄÄÅ7½5!ǛH\"ÊdÄf¤àU3YNBó;Î(n\$L]ÓÍs@ú4PU;\"°Ø\nä@id,²KÀìãé\".yٱ\$H4Éh>,°ª0-L73tE'f0FLNÆDLMùæ\$AF r\"Y¥4«Ý(ÐÀÌ@Q¸ÄX%ï,QóKç<í HØdÙ< íK¾)y¦ã,[!5\0(&ùvO1í=Ur¯QóztIâþS܅5ÆÙ,ûl¬2R#AkwORÅ__1õ\\nVÁ'clT!±­4Ì6\"âHë¹ު5#Ðæ";break;case"it":$f="%ÌÂ(a9LfiÜt7S`Ìi6Dãy¸A	:Ìf¸L0č0ÓqÌÓL'9tÊ%F#L5@Js!I1X¼f7eÇ3¡M&FC1 Ôl7AE8QÔäoÇS|@oÍ&ãdN&(¤fLM7\r1xX(-2ÂdF}(æu¶GÍ&sá4M\"ÂvZÂgµZ-(ÑÄëJ¹.WCa³[¶;fʒ 1ÇN³®̧±ƭg<	§ ÄgJþÓerÐKÁDSd®׳&ZÌûÐQTç³\"«úH&æ9:ÉoÑS!W3G#ØsÂѩ8LÎg{ALï%,BRµ¨ÓP%Èë&¨J\"t¤©jh@µe:¡¨H\"=Î@´7Îc´4 PëÃBʦB8Ê7¡±f*\r#&¢ãrI­£`Nô¡Ñ`½\"º¦¡ñ( ý?È2#Ò^7D¢`޵#ÌàäKJ޲Ȋ(2¥0J2ò¦3\\Û7©@R\\#PHá g>(3¾ëÜÓD¢|ë¤î´X Êɂ·3Ò+`0¢i Þ	@t&¡Ц)C È£h^-5hò.B³޺£Ðõ½Ì\"~\r©è\n~4¢¼6CÄ0!à^0É5ahê.�u l¢=Cn2æ\$MvÜn«Ss¾ªò5]Àél®4¤Îʙ¹r0¦2Æî3Í,SI5ö9Ì¦AB¢¾ú²rBäݳðÌ9=60ޗ%A\0Ù-­abʴf2²Ô*á[ó`ÚÞ\$ N7áT\\â+^'¬Âõcv=2Ã.Eäûdò-iöU^ëԌ£0ÍØÐëp¤0ãÆÃ\ré0¨ȍÃ]º3-B\\7á\02erÀå<Ér&9X	HÐÒÁèD4 à9Ax^;ðu1¶°!rÐ3èp^2#µÊ:A÷ ¶7{EµhÒi¤®\$TÃÒ0ãÃ¤ݡctɍÓ&º¥/Êi&͌²֑¿*1T¼½än¡ï¨ï[æýÀp\\ ïÃm_Ņé'<IHÝÊXbHÚ8#3NOÍۚº^EkÊ#/#èvæ×3lÁ ©É?@Ã^i¼ʎB#ÉtܪmêJR«ñ1Bu\\¤IÆÒä-¡¥È5r<Q¹4#AÒ0ܕ &«Ýy ¢M!ÄJf \"nPÕ~¡@\$O©÷=ÄhðRJöGA/PÖ#NÄû§lfÉjp;·ڗÌ1¯ptB«\rÕ\"p£Db\")`ֻôݢÞP,1¢C6YM b.ä§ØBÉ	¯\n¼5à@Rт	¹Y%/°ÇPÒj\$FÆà#gèf)ý¡fyûDÉÉÒ{ucD¦J.S	IÌ!6·Ýd2rX:·8ýäflö¸+Á\0P	áL*Hø(WAW(|ÏJbÉå*X¬AÌ5¬bÊôjKp	'\$¬à9!9,&NJ0S\n!0ÕOVV0TU6´lrcsh¿¨	êALÇU¶Ë\n (:&:\"CÃz»8gõ3aÎ\\°ѕo4¢GÙm&0A֔\nVqINI4´Ќd¤9Ì\r¬1PÆá¹È#ф´Dã:KÉ{J)×ÒbF3Ð\rÕøJB¨T4däZIMNý8¦ÂÓ«Ul	J@\$û\rYu%'ÈÅF\\Qx/U}R¯çÔü\0R*5ÊTÂUui\"§U°«ʼ\"½±î¤ҚrDja¤\$Äx°Ò0ÅÔÚ7d%DҦÒ\\ÉTÐݓ\"üJý'¶ÀVskièI+oRЌûW\"üöá҂D,R5^P+	.uêĘ»u ";break;case"ja":$f="%ÌÂ:\$\nq Ү4¤ªá(b¥á*ØJòq Tòl}!MÃn4æN ªI*ADq\$Ö]HUâ)̄ ÈÐ)dºÏçt'*µ0åN*\$1¤)AJ堡`(`1ƃQ°Üp99UÉ÷B[Hi[½x9Õ+«A¡£°´FCw@ ¡«Í~UM-ÕÚ^_¹PªPU×!É ²ÙF^Ç! UМR<ÆÔÐI'2mhæK,/PÄ[P©t¦Rù§W^°X¥ÎEúvªu:ÕkÂLç[&|	®ÏW¯~Gºë×*)Aåí¦mÅä©4ª¡TO;%é~sC²\\§10G\$%R­eK8myC±d~©²\\¹#¡%{A¤	VråñÊ_éL«¢Ì(ªCe\$\$ÒÈi	\\se	Ê^§1Rºeê&r@I	FÆd		\n@ư² 'HFĺ-:ê´@«򘱜ĩ`ªéy.RÄÊ\\àó¡ÊDN¨K¡,¡U1	)dDKç)<E¡pAÄF¡%U%J!1<AÈêäMåñSOҰ0AJpÓQ#Õ!ÊH¬DFADMEB8A8T!Y¥åpñäÔk#heYI@BäÛfÙJáÌD\0PJ2)AKUgÝ! bè¥å!8s²]g1GҲutÏåµøB.\$Y+nGI\\ÄT\0sñr³!L³ÄôÏAÔþCH*-9Hò.ãhÚ#TeýÄ9ÇExx0¨¡ñÒB³ú4ݽD¢ôã}§AL@¹&ÊÍépJĄ¢:ʐÝçyéÐTµ>¥FI®@Ë\$·.êÚÓvs1Z\n[È\$\"HÒD&ª&´Ö\$úÃkíá8D½»ÄĮJ»^۷³ìIFC`è9R´ÊªEÎEÇ1 Ó^Ä÷\r£b\"¨eEtövYÈIc8ó1¦qöô@\F«qäg)|ÈEd1t%3M´ÊÒĵBÖ]»º¯+RÆ hÒ7£ùSMnv£5±D@±²ã*ùJh(Er±0PØ \rÈ2`z@tÀ9 ^üEô¾·Úxrà¼2æ`Ãpa¦ðD!'%³àR¥EÙ!܄Wxl9\"A?ªòîK]˥+kdZ=,ößòȑ\rv¯Uù·E)ND#Qù!LH8Õ?òBù	p6Á'`¼ðn¾Àå!\$T<HJád&Lö1Ð(Ä(äÂ,ÃRÝbCGA:ÄÄPÑÈéá7èSPåEøzîș0r\rÞI¦ĨڞÓn{¹[ <TNñN!Ì+R®{\"\rïD¦õ%I¨[£R¬1\$\\¡v|hU=¤TRXèI­Eû8¢H\nrk8)²Kð(¥ú-ÉH£t¸B® VyÐ5ĸsrq¨G168#)ã(MXQ»'´¯Óz} t0lIXÐü¥5S\n)Oi)BÈ8¤DæâÉF]q2 LDQ\$k2ÚoĆÓW#øåmµ #%+©ôú,&`ùÑy*S\nA¨åF¯Ýá-¯Ht·ã¨C¦.`±ªuRªè|ڦ¨\"hSÈbSXIIֈ-]|¦£&9ECYîRX6ȠÜ\0Sè}QپÄRlÔ9DÂ­+ʪDê聫çu%ÁÊ(¢· «°ØÙ|¨Թ»H¦ë\nååà	áL*h­PÛĭâ¼wm¶C(mØ(APMØå\r­-¶²N[lmW#ëníëk°fÃK(£-.Â\r\naD&W¢±¤¹,!*Î-Db%%ÚÊYè´V)°Á`²ùc¢U :±Û¬¶Õ>çæiÌ(ÐÄVºÙ6fìޛó`c(¸	J@h.¯irQQMá¾8\r6ÜÜølvIÍJQ»>&\"ìê\$:10®^&åH	 ª0-\$í|	wk#V\\\\ÝàÁñs&%Ę£¾ñ£ˏ\"5ké4(¤ü3FpÏCex¿ÍQÖ!2ï1¼ÎfwÑê ª#_|G´a¥ÑDFèý(|ϩ	´êÔ\\òc{堅¼Ha<'S.ÌE±Q<9øÐ3¬,¶`êUj®mÝÂ¸r¥}/ÄÏsÕ\\Xxҗg¥Q³S&ß@CÁMg";break;case"ka":$f="%ÌÂ)RAÒtÄ5B ê ÔPt¬2'K¢ª:R> äÈ5-%A¡(Ä:<PÅSsE,I5AÎâÓdNËÐiØ=	  §2Æi?ÈcXM­Í\")ôÓvÄÄ@\nFC1 Ôl7fIɥ	'Ø\"é1üÉUd Jì	þ.¬©æóüeiJª\"|:\r]G¢R1tYg0<ÉSW¦µÓKå{!©fëÒÚöeMÅs¹ýÍ'Im&K®ÙÁèÓ=e×\"±r'´¾Q+ÚÅø˿ðÁü}þ-ÂÕâèî<^ûï}nnZ,ó:ÏK<թè;Ý§SVè\"­z¼©Ðq=oú۳*#Ë\0¶LD¼¦ζ«S¼ä:÷-JsL¶\"ìÂÔ4MÚi(N\".Þ@è9Zë7ËÌBÔŴϻ´¦ì&ëèªVÞál7RR®ÇrëF\næÓKté-Y(˰Kp¶DÉóLΣ*ëxú#	ÜÞ¨¬«Sj2S!RÅL,âÎ*´ʐiìÝDO/³­ºÈۊÃj\r¶1´ÞЧÉK¿Ôìï;hÕ ¦J1ÓÕñJR-E#ÑM;¬¥-jʠ'ôÒV§FmÔzâÃïD:¯GMå6Ҽò¬`ÓTÓJÔO(HÁ gr)÷Qo\n÷Dؕ\n3)͕aûÈJ¼ô0TµÙN©T\"PÄRI(I[Ý<S³mÂք4ƤÔ/âê\rööÒóº-ã-¾D.·é>ªa\0Ú:c @*|9£ ê9\rÁ\0Ü#ä0!à^0ɎacxÜ3\r#>n2èZ\$Ä\r9ʧ\$¦}ÜJ9KiiÔJҳ©ëúõîL®*´µa3(Ù\r 咥ÓðØ)º/(¦M4µH)ri:![u­C¨锵7o8֋¾Jñ9m:ÉKxWÈC¬û¥mG!¬§TüZ͵½¥{AÔÛ^#uM&Z@©[8˦­F½VCڛïs ØחPô4ÖG~êËî¼»#.#܌Ê\"È6ê£(å+QÝ|ÛÎ?ËájÒRè³S·ËÊV®æ\0њÁèD4 àÁÐ/áÞàÂõÞÈr|9p^Cp/(0@Òҁx\"Ímc¨³nFMãòQhh´V(nzÄt/5gÜsÕ*öOÕZ¢2mΙ¼¥u8IÛ£R~¢.ߕy7FN âºáÀv)#C¢ÄYIº\\ý_¸e/íþ¿ø\0à,{¹í@¸àeÒ8/A2pÕ&¡ƹ	aYYQoM1CÊÞÏbT'L÷\"2ùßS£%¨î6Ãb5:fÕ?ÇכÊÂÃS%EÂ×ڼB)'ð¢Bù¿¤y%jÌâ<¥VóÁCå<W	^58á!TT/¸I;¢.ÚR¢aÒ,ôÚ*'Qð­hUÍÄÅHqîh%æتf¤³&ë­<@\n\n)4Êêb¾sç,\"þ~QRb<D¼Eܛ%(±P tÍC¾-á·Iª»7NñP:IòóÄ4 \"\rÕwX&'UyÒjVÎîÔ[MJ¦C\"2ºÏëÎA®+Êck\"¨å|'Yå\nLtÝä¦aE¤0¦3ܥB\roëÜIV´áoÜ%fËjD:HÒxJO¤(ے¹§ñ4¡+³W}La8d]IBX!ɺ¡å©÷\"ZYWbZ<J	ÓSIÔù]j¬°­)'}FÊУ1í|/꾳óÔZ5½§¹U¤Yg¼µʳÛIµs T\"¤dڕÚ%E¢ehñ&ÒPsFá%ۚ.ðJR³AÀÜNm}±¶vbÛKÇMéÅɠ³ñ6ÜRV¦¥½qYnM«±»A0[Jù2mÔCE5=-KJ!*¹6ïlÊV³2®¦ ¢Þ	(Jêۭ×^éZxë´Ó ê\0M+)G\"²ì 9ä5V¡ZÈÕšI[CH>³jpÆ͈«@çxë:yӴl=jâWZ©¥×bÅ~ںïÊN©֘!· Ø\nçúþ¾]^¢/US!4½RjÌIҀTW[õðÞÔD¨T© àÓ,¼Að!'SIýlY¾£DÞñÝ5ÛTg�!ß8øÄÎibS¬v³Öýpv0à\")´MðEy¨Ku¤í҅öÓv·bÍÃâ?ÔâUc§Ð6?Qy?DP«hÂ]h§¡¡.ëß7VÓ<[w2)Æ(½ùu/\"BM6@1aÅ`¸D°VIPÍÂøY´ÐRu Îµg&Z%EéãÃ	+®¹¢©-ÒeéÎyÝÚâg5ÙI-Úì]ÛÓn½*MöҩpDX";break;case"ko":$f="%ÌÂbÑ\nv£Äêò%Юµ\nq֓N©U¡¥«­)ЈT2;±db4V:\0æBÂapØbÒ¡Z;ÊÈÚaا;¨©O)CÈf4ãÈ)؋R;RȘÒVN:J\n¬ê\\£à§ZåìKRSȈb2̛H:ÖkB´u®Y\r֯h £ô½¥!a¼£±/\"]«díێ¢äri؆&XQ]¨¥Än:ê[##iÍ.-(ÌY\nRÌO)i®¥ýgC#cY¬çNwÏæôú¢	NL-¥\0S0&ã>yZìP',ÉlÞ<VÑR\n£pÖ7\rã¸Ü£ä7IX0Ä0c(@2\rã(æA @9£áDC09ðȠ\$«ÃçaHH­¤ÁÖAGE)xP¦¬ïºàv	RX¡¥ê3bW#ãµgaU©D̸=\"øV3dñ ÓbSËÇY´·a6á'Ñ0JIÑ`¦ÎS «A\0è<òÌK±\$¡(vÿ\02ËbSM+ñÖöevb(¸ìÙ:ÆI	ÔZÀvå6ò\"§U:1ÙZuEKÈS¹I;A(Èò\rhÝÓSXA bΥA+áTT\"åJeXå8©{-­+ZÌBiN¡Âþ¥ áa/,äàtuÄ t âØó\"èZ6¡hÈ2/ÃuDqò	t]DìEÈÎ\$^Ããhê9ÄAàÐÊ:£0äðpÂ<xÂ%xö@!ãpÌ4ù(˖åñ @ùƐÒ9ÂÃd47hº<?¥i(ð:£pæ4棚WZ(¥Ø6Í\$¤¡u¨ûBÕ9'aSM[W«ù6AB Þ7nP<®¬:c<9èØ\rxÎùadü9pÎ0è}¾¾c®¨aN·¹³{¬ÜA¡øybê«SԲòP¨4e\\>3iXÛæ3È6¾po\$1ë#HÉð@4dC0z\r è8aÐ^þÈ\\0÷þ#á~ªÚ`é¬\rÁxD|°§wf;6B©Ímo\\uÉ §ýz¬r	Qä-ÕV)Å<oKrE^©!%Ô0Õê*h± ÀðK\"Eï\$0¼·óދÓz¯]ìw¶÷Cr\rρñ>¤Õ³è}L\$ÐàуkU_Cèz¿ñ¿´4QJ\roФ7BV۟1*	¹1DI)\"UU+Ú×]+vhh07ÎÍA\0w\r!±ņ\$4 C¸På_ÍpÐMĸ·ãÐ\\jéø4!¦A\0cPè4ØֈB19§Ô?òRH\njIPOI¥Mc°\\£§ö`rS*©¥CÍt)Îí?7ÀàIòh\2«ôN¢hh7¢)ãC¼½ây\n¡v:āSjºÆ)\nșú¡Í8f~áüG\rÁÁÏ%¿á 40Ðç;ÑºB! ÆXØes6gÍ@RÄhC.nDñ0R@CdId9@´Å2&\0öÖ\"#\$Țb6ÙÈÁG¨ý~Axë¥t' èú:È¤z.I!¼:¡·1\rÑ\nv!öDCªDA×{\nDáAaơ	0ÑT¼%a@'0¨yQ­QAJa®\"õ¶@×ZV&ääÓr)ù­BÄX#²@Òu\r£.¦ºw©òJ´0¢Ñ-m\$Ø#@¡_hu°\$KÂ\"DȩҰöà_Ô#n#4tü±1i±1JnÂ\0*¬ի©ûao\rZ²k¤öκD³þ®¤­¤\\«® ¡7K|{ÍÖQ±ZæÜû¹tÀPCl!°ÕÉÚÚK'ÚGUjßbiÎ\$°ÐÓcÎq\0ª0-\r-âGwu%à#FòÛjAɓ¹\$טóepë7ԨÊá±HYùøNûô: :xvÁNgL¡ìWђ·E ë&»#Ö)Ä\rúáņ&Å`c[¢Q[âM«¹¹­Ué|QáJ-æ]a¦@EeDlgq«õLç»#B¡֪´[Lߋ*Ø,Åâ½Â\"Âð®fܺW\\ÐðYSð~ِtC0ûh(Wtؙ#ÉDkFZ¯\$ð¹-5«¥\ri²4kU7d¥HB%ÈÐ";break;case"lt":$f="%ÌÂ(e8NǓY¼@ÄW̦á¤@f0Mñp(a5Í&Ó	°êsÆcb!äÈiDS\n:Feã)Îz¦óQ: #!Ðj6 ¢¡¤ÖrÁT&*4AFó¤Îi7IgPf\"^° 6MÇH¥³¦C	1Պéç\0N¶ÛâE\rÞ:Y7DQ@n,§hÔøË(:C§åÐ@t4L4æÆ:I®Ì'S9¿°P춛h±¤å§b&NqQÊ÷}H؈PVãuµâo¡êüf,k49`¢\$ÜgªYnfQ.Jb±¶fMà(ªn5ææáär²GH¦²tË=û.Û à²9cºÈ2#¯Pêö;\r38¹9aìPÁCbڊ˱fiºr'Ê¡¨¨è¦5£*úÂò?oì4ߍÌ`*Bþ ¢ ì2C+ú´&\n¢Ð5Ç((2ãl²¨ P¬0MB5.í8҄¼Òø5´+*O+K҈µqÈàþ¥ðޗ ¢ʑ\rC«¾À\n	ÓB;\$ðTî4Ï.úFµÂJ*PŌ*êÖÐì§@M!I##:3KR4軁CË>3\0PH¡ gT®hʮ¡¬89£)*ÃJþò	(Ã*¬9¤(:<S#Ⱦ=°\$Bh\nb-6Èò.£hÚ cÙ. ȡÊQ\n\njO\nh8Êäú7Ãî0!à^0ÈíãycxÜ3,ըË`\$4ú&Ò`Ö.8rÈú­-k-vb÷`Ê<.rED®ãÆ# R)[2°Ë\rã0Ì6-,\\ûHs½Æ:OÐÎÀ\nz\rÃÈA=VÆώc0ê6.zϑ8å£9FÒ#jÐ:®@æ£¢QU¥æzïÃì°ܡ Ê6ù(¨Ê>	@͋¤ë@Î#&¼Z¨ǒ\r##ì^,3¡Ð:æáxïͅÃArÈ3ë^ö-(®A÷PþoØfî3ô&êSY¢C¥ζ¤ApA;&\$õ\n²ÐÛ.ٍHâ=?qp\\¾£Ààß/~4qü'ÊòüÏ6;ó¼ÿ	Ñ]'Mä9ÝÖ^bHÚ8.7HéÙaғQ9´ú@òPHp>Í¹Fné	G%X<¬SN­H°blç(V~Nëw2D1º£èÈÓPFZ&ö!ºöÒÐcNjm©@j\r8h2ÆÉ)\0ÆqQã\$¬ÄàPøi\r!¼¶Dc.°3h® ¢ÔU]\n (D. Ѳ\0¦\näoÏõA8\\C9{AH £0I)ïd\$eH4mmeºÅÐ\\ËvG\r6BZ[[5\$7ÁbJD;ÆifrjHËI8|lÛ6¥ÑâòtS\nAþ\"҉A\0B_\$µä7îpȴ'n¡s¥äjá¿F	dåЬo*@]¤BLJN%6¥ÔV 4K§aIy/>e²P@Ãɑ%% R¨5G8S>C1ø\r®ó8¹&}0äÕâGB/Ñáð¦\$4Æ\rÆ80êI&Üë4ÓtÎ0ҏ K¢+N;\r`噳ÆíÚï?5±Ëe9ô4dmÏR¥\$Y¨èQ	\\ÎÅÂ0T²í\r?4¨ àûÓÊ#©]Y¡ Uã£E¯!\$H CYÔps]Ð5«ZÊíl8r½K¨ÊâMyç¾JÐFl­¤¢Ã@lt\$40Ö5FĖFÆèKÃ3F;\$ަ®ã)ØU\n0áçPg#¶6è²\"DÏ퀶8\$EMºR5øƢ;}n\n4}\"\r(Äsø¹H±q.dMàäæm\n¤ø¶¢FC¥HÁ[ºÀT@/ªìBÚ_ñÅ8åøÞ_\"¬WCiÁyhL7ËÛԦämå9ÌÐ}En0Җ,çõx\$é¹\$ ºë%=4óú\0U»­!ÍN6Pq-ÄTLX:^Ï)Àf\0\"K[òÆø\$ªñB¬Hµ\$.IvéD<%åN·PKÖüNyl\"¡Àå";break;case"lv":$f="%ÌÂ(e4S³sL¦Èq:ÆI°ê : SÚHaÑÃa@m0Îfl:ZiBf©3AĀJ§2¦W¦YàéCÈf4ãÈ(­#æYá9\"F3Iºt9ÁGC©­¡ÎF\"Û67C8õ'aÐÂb:ǥ%#)ø£DdHèoÍ±bٸÈu¦ÚNá21	i@ »ñ¸ü S0ö¶ýÿMØөË_näi2¹|Ï·È9q#¶{oÐ5M¦þ·îaÅtÏ5_6̆Q3½¡2¯èÖb)Vù¥,Ã¬HÊLCØ÷%ÀÂ9\rëRR\$IÚ7Lóü£ãsu		jîýµCj\$6¨C\"\nbf÷*\rûÂ4©åàÒõ0mZ å	ºd¯\r#֥ ¢��P¨bc\\Ê7£(轶O«î5LhÒׁL£æ5³éì½-4\n(Bp¬Î3ÀP:i#2\"	ÝC¤éà£ AÇDÑhàè4ÑôUIBR\"àPHÁ gP P¦\"ÎôC(Ä5¹¨p@îËTؖ´3õ[èûET;90¥\\͏Hi'TÒk\"æà# \$	К&B¦ \\6¡p¶3Ü8»bXÖDø>k¢\n¡¥ã«Ü\nRӚå½&9#Èx!òSxÞbÞ7ÃHÎ挷þ¢.Z§xÙ݋Ð@2±C/cW`Ê<âgÅÎ-ùCGcd·LÀQÞ1?#fFR)¤2öÃ¨ Ï!±Bô!Óê.¾Å\rZ>9ÆëÍ	È(\$	¨¸ζÃË9&1¨TTT7@Y,\\èr֋Ué)~¿:N¨¢媢mF±­kÒ̋ìǲ¢öîѢ\r:6íĞ¸éÈ&é©îëVòod·?ïÚ÷±-¼!'næZ q#jUëhô3ÌÌD)Ü+fÎc,²ò\\=ü:#&ƒw£²ÁrÜýDÑx%ÃEê3¡Ð:æáxïïÖo9ËÐÎãxɃ°cNAóôÊÚv7ë ÒÙ6âdD[»4\r!ù7zkË!1\\îÌÙ¢\\Ê{£!Ĥ&6JkvNF\$ѐ°ä9(z\0éW¨õÃÚ{yð>\"ÚI(r|陋²PÜÉÃsð^a\$6¢PʖCtC©iûôØek½&íLùÁ÷XGÍ˔\"\$÷ô²g̳;I ä QM)5RÌs¤Wë¦'þ hB.t'§XlË3qG@AÈ1®(0¢w L1d ( bMÔ]Aå<U膋\"&-5Cð@\n\n)2ÄÊ[L¦,Åìúäðp/K%£`Ð1Ĥ·b¢_&ÉYÈéËbîá8±öl]¹B84»ÓÃ2 ù#ñJ·Ã/¬vä@!0¤>MÝÌàÞÛ*Åè6õʬȐn#ëԄvlÜähO¤ęQMßéunjIߒ@ÞÍ(¾~¦±Ì;¬ßHËS\$¹\$8AWNQÁ~4 ܤÄxм9;Ó¢âÅ\$t´ÄxRoanà'0©¢Ád!:NÐp\rIaºµG,ÕӼð¨xqN\$K?´ÔyêÄ	þ`ì%Áñ[â-%%}~¬ELËx# )Fa\$\$s´nÔQ4AÊ>Ô)î\"²ÊìRú5Xã7bTìRtàîyü±Ñn¤AH+!cJ´)AI¶§içõ©C\$rÑZÛKf,øL,\0¿ÉYìEì\"P2õàDtÒØl\0¬³:IwÞϔr[#d\n¡P#ÐqX°E'ÃÙffIr4¨ÔÞkF#Åê-§ͳÃËao½nÝ'Â[(ê¦ªÚZŰR̛L¤LVf©äbÐ`Yº±®\nÍR͖¨͑Ài\0[Ðè	ìA7Ïuһ¬¾*!gÖAF½xiDdAz£\"SÎBë¿WÌ0©SIuò4ÁÕru̓BYñ-ØP\"+îÐ/rTéJ¸ã!(T¿ÇÐ)díJÊSw)RI`ÂÍ	ÊwÂw®ö^ÛÝ{áÝðæ`ùD9BXì; É>yI¶#<ÕÁÄ@»èýÁp";break;case"ms":$f="%ÌÂ(u0ã	¤Ö 3CM9*lpÓÔB\$ 6Mg3I´êmL&ã8Èi1a#\\¬@a2M@Js!FHÑó¡¦s;MGS\$dX\nFC1 Ôl7ADtä@p0è£ðQ¬Þs7ËVa¤T4Î\"TâLSÈ5êk­÷õìäi9Ækê-@e6¦éQ¤@k2â(¦Ó)ÜÃ6ɝ/øùfBÂk4²×S%ÜA©4ÆJr[gNMÐC	´œofÖÓs6ïø!èe9NyCdyã`#h(<°õHù>©TÜk7Îû¾ÈÞr!&ÙÌË.7Np³|+8z°cî÷©®*v<âvhHêÞ7Îl¨Hú¥Á\"pÞß=ëxÂÍiët<(ÐèíBS­V3¦«#°ûÁBRdÄ+éÎ3¼Pޮc\"!Pù	Ø؄0B`ޖ¹+ø-KòÌ&£`\$¹éC:A(ÈC˞ÍӄäýIPƤ@PH¡ g@ PI|P©¢)ºL\$'\nÚF·\nJÚâ>ɨåÒb@t&¡Ц)BØóU\"í6£ ÈS4Ú\\ùͯÚL±àÓ:MÚmÓãÈx!ò3_XÒ3\r2%e٩óöÔÛOú\rº~=áep\\OÝËq£Þ7\$K\\ôúÜ6Ejں¹¾ÍÒZ7Ã2Ú7¨Ƞ2§iT³-²øB9-	¯n¢'CϷcf.¡øRZ¢áb	M.xÒ÷*¢̲r¥Jç:­èÌl¯Îê#\"£ ®iªH³úÒHo# ®9dØÃ7ƍ\0ÜÑ!õð@4XC0z\r è8aÐ^ûh\\jN\\÷ázdJ4­!xD3렐Ûuè|*¬é\$^=0¹S ½­¨̦:.xAÎ\$<Nqp]^®{\0˱l6ѵmvàÔî[¦ì7n×rex÷ñ¡\nqfý´-3³+ê6Cz̖&ϷJérâ5¨£3 ¸Â1¨«H@;ÓHc C3ÞÎMãÌçH ê1oÐæ3¨&·@t'°û¡8ñd&E/2à\\¡¿zØÞÁ\"*nH\n¥\0POÁKÚ-L§Rc:ðU³0WÐ%¼±S:\\á3Ú&\$Íï©g¬âJ1\"7¡ðîá`&¼ÝÀHãùa@È!óÒÉë5핞¸Âñ	Ò`¥½é @RÑ\0Ç!\r\r­Î?PÄ{àê'FXsbwD1èÅ:C8¸CiÍ|C\"ZKáÚ#{ñTDXMC¡ÌCô2Vc\"	yqͭ³Æ0Ԝá66\nÜÂ Ü_«ÔÑ	Ì%Qª>)P(ð¦rblõ\0@Ã(cSG0¹ÂnçEä\$æ÷8mVêú0¡½pŒBa#Êj8#¦#<äè:` Ú'6Æu¥s,I+#) È:'KË9\r°ö~\nq¦f¢]&tҸè;Údõ:Ó?#ô\rAº¯0Ã`+e´1±x jٖy2Èrg!ªݘ\$JY8U\n2L2@¢\$¾y&n­)j&ư×CbùÕS+ì8¤A )Gc7utR³Ԋõ(!±2?¥©\"\$R·s|7Rԙ:IEf!2qÒ&Æ`û%s£)jo¨\$¡ তRRcÆ\"4Q9@JW\"æËÍét.ÄéÚ4ÌwÌ9/N æ";break;case"nl":$f="%ÌÂ(n6ÌæSa¤Ôk§3¡Üd¢©ÀØo0¦áp(a<M§SldÞe1£tF'Ìç#y¼éNb)̅%!Mâуq¤ÊtBÎÆø¼K%FC1 Ôl7AEs->8 4YFSYä?,¦pQ¼äi3MÖS`(eÅbF˔I;ۍ`¢¤¾§0߰¹ª¬\n*͍Õ\nmm0ÍKÄ`ß-Zã&ÃÆÎÏ.O8æQh6çw5ÖéÊm9[MÛÝֿ5©!uYqÓæoÁEkqÞÅȕ5÷Ûùäu4âàñ.T@f7NR\$ÏY´Õ8±C)×6´,ûBюéèä¦)ϛ\$ó=bá6¦÷£Âh9©Øt¢jB¦ȣ^¨K(É²H«Ⱦ£¢X8- Ô21b(ïCÓª,7 ¢rä1kûN§®ã,ó½+rt2¤C2ô4e[ÈɁB(È4´ÉÃs^6¬pé®Ѡ\r¨bDåHhԁ¹Ìüä	Êpê;¨(ÔÐ£ò4\"#C˃PÂ/lH¡ gJ P´¹K«9+覻#Q,è2ËnH m\$¸%Ã4ê	@t&¡Ц)C È£hZ-V(Ô.²JF¡¶˕Z·2*(@¢\0x0¨¡ôKI«`0U\0òã|ÚöȆªJC8ëܗ48 ¦ª*J)´C\nÚ6^oB » Í÷BÖ\\3yè¤½\$¢bþ9c`è93Õþ3Èj÷{­ÔýV3N#dç¸hcÞøÕÎ]8NC½:a`@8'Ü\rã;?¢+Ò÷z£jô:¾Ap*:ËæNù)ÙSrò7f9j²¸ÙÎw@YóEhCsäˊc¨Æ1¢c¢×ÓÖDB,5¨o:Ã @ \nݙj¤0âÚÃ\nÑÁèDюÈtã¿\$\$;ðä.Ã8^ã\"¨* ^ÜôÌÒjÎÌP\r³j§(*>Ëi @¿¡ºNqOÔØÚs.¿4ÑMp21Ùç*·Ì¼Oñ܇\$;òêô·s5ÎB°¿B7tvȒ6£Ã,âÍëP@÷ZSè4\rïsO\0*KAª\n@rã¯\"kLDzhM#%@à=ã)ªÀ=*8\n/hÌþ#fm\r¨7¯ƞð«ò}ðDcÜBÐbMI=e\$J_Îá`É*RbÈX \n (0GÅ\0Ȑs4G¤1ó\n\n()\$§p95Ò.Ì ôNG¬Ð4`Ò\\ËX=̹Rëý%ä-bJAáùx#pÁ&ØfBáª(§6&͚®^ã G̐PhFÖ%ì] ªúv1½ª'\"ðS\nA@\0A£D8Ť¨7bVîZ˼?¯`TÎr*8P \"SÒ|äùn~)P\nupIV»ÒpKoY\0\" BÃ!ÎfDŦ³RlSpÓD³t¿¸P	áL*٢E]À'dö\\E@Ö\\Ô+-8¾ÒF';a2Ì9K°Ï<\r˵wº\0ÊILÈk6Nuà@L±\$:GÉ	KÎ#Hpe!A}SL·7ëÜA%AOüÎ!%¥©ÉSfE)ZIF°ÈITR`¦åTCæ¢HÅB\r¥¢Stؘls¼ô»´`pPà~Ƣ<\$¸H,ÔP¥ÀBÍ\n¨T4#\0ÜàN+09ÝÆÂdtê2 ä0Å}#Djԃ(CªÂWVÀX+ä¡	<՚Ҁ³C*ÏTʬSslrÂy¤\\\"A-_1Iªæ4D=ª9 UÃUV`ë%ð6¼f°ûϲô&L7ZCp̈P(丢3øvá)rPV\$ÀR2¶ʙÿfyÄAÐ[èxeÖ\rh·ÏzÕ)Se²3YóM±52Ò؅NNP";break;case"no":$f="%ÌÂ(u7¢I¬×6NgHY¼àp&Áp(a5Í&ө´@tÄNHÌn&Ã\\FSaÎe9§2t2Y	¦'8C!ÆXè0cA¨Øn8³!Ö	\r࣡¼Ú\n7&sI¸ÂlMÆzÂb'ґÉkfY\\2q¹ÝNF%ìD¯L7;Ôæg+µ0YÎÍ'ÜÎqH¥¡16:]ï4é0Âg¶ۈ©Hr:M°ºqÎÿtÜîõý÷醡B¨û­¼Ì傽JðGÖ\n!½ò©¸Ön7èS¦:D0ìLQ(YÞeÑú9ç3¬^Òçæ;­#\":+(#pص¢a\0Äñ\rmH@0ÉjôÕ&²iò¡#M|:	É(Ú>(@æ\$ãHÈÁð-¦L܉Ì è;'ø2¬Ì\"ÔðB	Àè<\núã§hÏ\"<iÂÜ;\rÀP¡@­Әß-²¨ 2HPÜ獎bÛ.+£\$<¢LÛ7¢(Ô0HPÒ°^\0H°I¾4ËK<6³I¨èÁ?ì¦5±o0Ê	É®`T6IãsOL	К&B¦zL-´õJӋ´òhßð	\$)Ø|ˎ£ÉÂiÂ<xÂ#¶!ãr43أ-gÌ48÷¨˺õn´ª\rą׍-x2 ܑZ±£\"¼ywÆ#(Z\rhИ-Ȳ7«Ã3ÔâÁ'ã®&Ka\0ëGxß^²sJþcð9èØL«eÞ4Kn0­¶ôæ6­¸ÊaJ:*.LßG`Ø<©c¢:*\rBßâIæ#&`Y@ÇxÅÊM\r	ÌCBV8aÐ^û(\\ÆiëÚÒ3ê^û-ÉEªAöàøÉÖݠdÀÖçâcb`89áhA{ÁPdɈˡ\0àâ\rӆ\"W¨[\0lðå7)<¡¾²2ëzì{(ï³éËnÔ9muÙw^véa	#hà½Dóq½BQ:ÊÆéàÌҸ8Ԙ5l02j'¾ïáj^\\%hXÚâѳX9҃¨É7qå.ÍBcä¡éLÉ#9DópÂ3^¨ ëãùFÉYkí ¾æQ8 géވˑt(LL;5Rj¥àuB´·B`ö^ P	@ÁH,X('`¤¡øò	tAeЎ­C^úq'bçp4ÁRô]!ÿ4e¤&ϏI~fá<ÂþÒÒë:IÍ)ù æ«ç)0цXòހd,¶&ãs`;Òà#0íy÷ÀRÃu&Ñ.&Äó|RËì7òáª:5 Êd!¸òI	16,ø@èÊoaς8WPa\rPË|¸0òÁ\rj.¡¼ýÝ\0â!3!ÔüðÌ{I3Mm0¸Í ƙI´\0 iH¥W&Åކwdd%Ǣ4àD15̠ٓVõɒ.¯ÐֵVºÅ\"Qawòfßáé=åx5¬\"QÑYBSD0¢idpV/`¨Z\\wJEæY°raÆÐ#]JHs;\$QÉÑã]hpi¾ÅÖN`9K¤u9·Èià¨r!í	9&êlR_-9§pÕÀVÐYqÇº°âþÒ*aª¢²Rg!õz÷ôdT*`p¾Ã(g<~?SJ|c)A#RSZ>NJMj­t%¬ÀBÙét.ÅáµT»OÆ6IæµL	Æ0\\ð3_¬X-BóX¿3Lj%µS5ò͆8mj'+èês®ڨ\n\$GL\$BLÏàl646VpÉOSQ:áX·uÓ*U¤µãDÎsW¹Ê¡²gÔv·×óVl@e";break;case"pl":$f="%ÌÂ(®g9MƓ(àl4΢劂7!fSi½¼̢àQ4Âk9M¦a¸Â ;Ã\r¸òmD\"B¤dJs!I\n¨Ô0@i9#f©(@\nFC1 Ôl7AD3Ñæ5/8Næãxüp:ÒL £ô =M0 Q\nkm¦É!Èy:M@¢!¼ÈaÁݤâhr20Ögy&*ðu8BlpÆ*@doæ3Q¦xe5^of!hÂp¤[î73qä®Øíúi¡¸èy7pB\rçHÄLíõû>\r¯Òy\r¯+ry;¡¢©Ìë¹Ó\\òb@¢t0õ.ÚÅ\"ìD)*a=KðûS¢ã拎£;A*ä7·N@@Ên) Ü2èÊßMÐÊõ¬èt'ê5B:¥©¢pê6Än3޵藴Ã򂊌r7¤K¨җPØ)¡¸#£|h:K*#½\n0	£65 RüÀ\r㓸˲\"òR.7¹C`ß5#Òä:Ã\nääGs¨Ê9S\"+Ìk2A(Ȋ1 EQSGÑcÉàTvò£`PHÁ iP2Xô:ÌÎ2Hbh6&,t2°,4®C¢¶®Ë!ë8ß0#ôÔFüB@	¢ht)`Sì6ÂØåkBè(Xc\n>ϡKZ~\r«KL)ø|=ã\$1ÎéÄv1\"ã|]P¦ތÃL90WÅõi\nè±úGØ@@6£`ù¸ADZ_q☴RVêKl6Jþ:D¤Ø@7Ã2!\r¤FLØlå: »×3ð¢2¯یÿ3,-ȎÐÂ If18c޲3hkóQk:9p ÿ¢¡Z:wjxç¦ãڀç©jN­¬#k?®Èã«±l6£?»^·C®Îã¨훪«ÍJ6·HkÛö<éR3«\r¨±6¡ V¹ÎïPÑÅ(ÂɯXú@ìÛ÷@@41£0z\r è8aÐ^þ(\\0õò<FL£8^í\\:\r:¸^Ívã85÷%ÏãR^;#<ÝjÎváÎЖïȩ¢½¼^WÖoó¥ã:/LCpê`ù_À.ÙÜWtïóÀxOã<``ryÏ@2X³ÖK¨×+G)¾`«ä1tÃqô\\D\$OځzKe\$0.S\nùGd]ø Âùrel½ GÌA	¦à( I\náÀ~¨\\=!vÞ_!½!Eͥ'<KISzs'hºꐪð\"fý4ôڜ!þlxʗsÑzDqé\0¡.ÞB.Þ<ǴJg¢hPBdtæ XËjjÌaBûD̃RÁêG@\n	ø)?ñ¤ã¤&N¡M¢Ghxra	Æc°Ca¨n\r¨¦JR6h`oNLÐöÃ3\rCHdMäpH(æ\\Øc÷Wòä~¼ m.¸56Xq&a4<¹f,ÈJä#ÂðT/-ñÀ>öYLfQ|ñaÂ@RÐ)·CùXaBá2®PØOSom¢@փÑó\\²[\n¨zO¸ÉÀ@¡°%½¤K;£|܏ Èx¯Í:uJ\\¯÷]ç!ù\r¡¬zËê oAÑꄖBàEC£A#äf8À ´JYºLÕ~äó!.¦åaH­]°9ÄCUiadR²Cȉ¦\naD&N¢>Ëxw!(DæÍÑŸ`©\"¬Z?0yZ7µ\0PR1ɖÉEe¼ Þ!ið<K4¡˅\"íêsRXb«´¶f͙Ça¥«4\rÖÖrF³ß=Zpm©¸2ßÜup	P6°ÖNª)0¢y`C*!QÅ'§d@a¬µw'DMr§b(ÁiGdū؂B F à\\Qn!ݠ²aµ«K)vد��¨ASÐÃe03\$jPÆ\\i,úÁøÊÛ#û=\n3hMìõ÷ XK-ëÌ2oYÏ5A8Yªi]â0I\nfNÎ)¤\r7zÜT.Ø\$ÉÙÀÕ|C°ÉâI>f¢f´r\rÎÁÚ-aNàgPi<Ǣ=tJHk;x\$ÈaK _óv2g(÷Î(ª!½f\"\n­@PIÒË°ÓtªÌù41¸hJåë¦n¶Pê`£Ôߎ%!AÔ";break;case"pt":$f="%ÌÂ(ÃQÄ5Hào9ØjӱØ 2Ɠ	ÈA\n3Lf)¤äoiÜhXjdÛ\n2H\$RI4* ÈR4îK'¡£,Ôæt2ÊD\0¡Äd3\rFÃqÀæTe6\"åP=Gࣱôi7ç#IÒn0 ԸÅ:¡a:LQc	ÎRM7\r2tI7ìÒkÜ&úi§Ø#-ڟMçQ Ã¤Hٳ:e9£©ÀÈa¨l])#c»s+ÎÆ,óqÒïX̸¦Æèq9W|Ò=£:IÁE==ÜÎ\n\"&×|q'o§<qTÜk7ÎæèÁÊN9%\"#pÁ0£(@¶\rHè6¨z·0£H ¹3O¬@:¥°;\n¾ªZÁ*\n£'¡\0ԙ²ìÊRCj̈På&ÂcȒÇâ¤錮0Êø¯\n8\r({c!¤#pҜ¶#,Ú9ÏRҜ¸¢+ٸ¢t¡ÀìÂ4Ìkƌ´ÏC8¢j9ÎJÆÉ%ñüÔ;!R[<فA(ÈCʠÑTeØCÍ(üA j8BN1³À8¸CeP³`ÞƯBxå!¯(õª\0Pêºâ,2kj#:ç@t&¡Ц)C È£h^-6ÈÂ.Îmuæ9-¼¹D¶#l;\n3¥Jþ%Ëü0!à^0ÉMÓunbB3ԣ-í|(\rïÀ,Ã}((ú<áx.)±(ça0ÀUn§° PØðMHÀÃxÌ3=Cr£9HÜȯCk؏2H®*\ré\\<Ãìê1¬¨Ì:¢õrìȅåX£Ò9Ý7¡@æ¥5ölË*Ø۹k矶P4ϻb3C¸Ü3ɩ,ÚHÆæbò=t´¬àÌC@è:tã¿H[¤\0¹áz2§+Üæá}ʷ6|Îzژ<	40é0t3\ráó¼\r!µ4#Â밂#	ÖkïJÎB¡ø4oÜÂpÜG;ñî³܇\$7rCÆ-as7X6\rtlæóøl:&[f%£\\4Áröb##¢R ¶9ª%Jã**°¶¢ç-¸ȨIcrçð;ØEÌ,ͱ2Ä«ch\r¡´PÞÑÁ|éDlYãí#±s,#\$Hdè·60©?@@@PA8ÀÃ @\n\n)#= æCÒÑ\r¥~æU\01²5F²õ1±?(,¼ªàîtÉIîF00«Ã,]ñ3=Fܔ¿òÌù͊²ø8n	¹Û!\$)MÙ#|ÕÁ(Ä\r aC¬­>cøRÑ؍F¤¶J\0E#Dp湃a.Ė>ÖVNÍKi	\"hM¡1¯`@¦¼sá},¦).èn҄:(S0@Ãɣ@émB2øٱ3¬§¡NX[ð1ªä<ø\räsì¬(ð¦;Hdµ¤\"ÑðnHrù%@êZHù½%¤¼L©öÍÌ1\$ÉD4ÔØrÃrÿT±ëdTÈfQ?ÕW\$\"ģ¨ \naD&!ªÙcÁRåM¡ävAakÌæZó\0R3²ðNIM23ïĘ²E;©ɝ¦a½Ã'NM'/ð=(Ô!R¡]L¦ Ø\næèiUt2¤ك%µWè揈Âg µW\"³\\ù<*@AÁ\rÍÜÅ6òR¤Lâ!T ¹Õ0]SÌÝB3ìZyÝ	Ká?JÌ^%L弰ð %fP£b0yZ+\0Î2ÊsRjK*ñú\0¢oh)ùÅ\n©¾¯ORéå°1µ2ò×Hé)	¾zÄmÌ¸ôófÑÑøÃL|\$£`Ñ*a{)D¸ØlìE¨d_-â ®XaÒXSÛc_ҡ6\n¡åÅ-Kq³f߻;N¡ÍU¢Ñ%©À";break;case"pt-br":$f="%ÌÂ(ÃQÄ5Hào9ØjӱØ 2Ɠ	ÈA\nN¦±¼\\\n*M¦q¢ma¨Ol(É 9H¤£äm4\r3x\\4Js!IÈ3@nB³3ÐË'Ìh5\rÇQXÞcaªchÇ>«#ðQØÂz4ÁFó¤Îi7Mjb©lµLQc	ÎNE2Pc ¢I¸ç>4°1¦ªe¶·ú©Ê!',΢A+O_cfÍçkNC\rZÖhÏbL[I9OvÍqŸÞn¡±ØÆDé,¹\\Ã(¾ǵGM7k]3c/_4Ițå`¢©Ï&U7ÍÆó¹º094ÒN\"7¬Sî¦í³`: Ò9A9ëàȓà@35ÐÐê˄ªV7º¦«2Æk(êRRbγé:]\r©ò õ@®j\\9.ÓÈô ¢\0¯«Ф2(#¯ھÈ\" ҇¶òhÌÀ(h7#ë\$/ S  ¯¤Å2/B2ÖÌ΄Í	zþÿ'©úXªÈ0ާIm#Ƚ£ @1*°BÐôKl4{ö¨^t¸b\n\r0Æ:Î`RDÁ\$Ið@6,ì'Rk:& e:¢í=óWCξH\"@	¢ht)`PÈ2ãhÚc\r¤0µÃi]9î£x ª8@6Ãð`x0¨áóD:TòX#²\0òã|Üw(çËc=@2ÞAo0ûõ3ÌSi#èò3;¶ô5oDèÈç]àbc<렣cÈ5-Z:îã0Ì60\n½p9#i»0³)@ޗ¥ÌBË£ވê/TNãXµY}?Ø\r :J°P9.(·;A­ìxkòیÐö&7á\02i	ö~1¹ø\\mcD3¡Ð:æáxｅÃɒÀKàÎ£!zn(Nx^Ü;zô_·p°ªàÌïÃÃpê=£([?k´rèdÂk«#В#\nլ7.ÑBáÚ4mûçºîûÎö;ï»øܟÜ7pÄQñAÊ\$£iӎu:f[º Ëó׆è8­Òøß(#lErÈÊ׶ÏsXÅ\r9#®ëCº0|Küߩ1o®ô»3¬jÜ9³v\0{&üîdr[¢|#±s82Êì&ôDúQ)yh©\0j~æ4\nHÁUFfD9ò E±'^á¹á¿ÓÐnq²~¦ÕCC#npOÚ0J ;¤ÂN| Ò[m=[Ã\0\$jä¡ý#¼mßL=|ÿ=ØªBnÁCsÃBß/ÍͩABÜPû&\n½ïàF;Äj0¥Ð\\)#\rÅÀa°tÉñ¦t\n~;¤HPwbÛÑKÉä %¸aª]1ä8IHôz5MR\$(h-õç¶p\r¹¢T¥U\rbÚØÛ)Çø1ª@öN\nì(ð¦\"=I¤­·âÒÆ*à%@ê\\ù%d´Lu'dÂ\\äSàܾU\\0¨µL¶ҨoÓä1à@LCq,·Ò¤0;zU!¼4Î}L¡â*¹Øhع¦¡¥ÇîI©2¥\$)®ÒÃztÙ!rƔP޷©¬<8(Åd[jaǶså¡©\0¶Ԫ·\"ä\r®jJAxbÄ(»*ZG_JL2@µTpE̀l*@AÁ\r͠ȶN£\r¥UC*SÁ¦mq`Û\nùç40¤0%¹Zêìê>RbÃbA	¡¼Ȇ`ò²(c4A̷ºí\nµµãèP©Ë Tæ\0005S%YÒߴ¦-Gâwk)|\$éօXÌo¸\n¹ôôbÒÃLh\$£lΩyÎz&ÑîTU\rb©õ½\n¦å®U \nj¡^Pña}¶Á	Ų<¶Òñ6ö|Æ\\	hm}=½9ªTGäkø";break;case"ro":$f="%ÌÂ(uM¢Ôé0ÕÆãr1DcK!2i2¦Èa	!;HE"4v?!\r¦fa2M'1\0´@%9ådætˤ!ºeÑâұ`(`1ƃQ°Üp9Φ㡕4Á\r&s©ÈQÁFsy°o9ZÍ&ã\rن7FÔhÉ&2l´ØAÎÇH:LFSaVE2l¸H°(n9ÈL¹ÄÄÎf;̄Ó+,á¦o^NƜ© :n§N,èhð2YYYNû)ÒXyú3ÔXA´ÍöKÙ׬eÌäNZ>³¡ÕAõãó#\r¦÷¡ñôy۳qÈLYN[àQ2lABz2B¨Ü5Ãxî$#𕈌S\$0´!\0È7·ðJÇð¤ æ;¯\"V#.£x掭ÆÃ/qpä6¢ÎÂñ²¡ ²´JҠDêR`*	Øèë0ãP ñ¢.B,Դ²»?JD¼ÂÉ229#õ\nHàÁ/q¸),ÄÛÈ#£xÚ2h2¡²ãJ£`Ò¸+ÈÌ3KÃM9˳ãy?±T0¡®£<²Lè7ñºL8\$)Ü2¬êþÐ5ZM»Í(J2|Å:5][W¬ãH<¨ÀT¨\\uøbh²M.ëÊÆì0ýÿ'C¤1֫)²-^èÌ#¨Ú	@t&¡Ц)C\$L6¡p¶<ÞȺ°Û6ÛÐ(	꣄à Ï90#\n9LÈx!òW`¶-§éäc J	ۖ_+Ät¾QÎB¦_1ÝA»cVÿ(ð Ù:MMU£0Í?SîjP³Ë2>ñÎã{qO1s@:j(æ9ɈÙL=ác9i#\r3ãubt§,¡@æ¨u¨@;\n@ÆÓéª2V*6:x3dWÚö3\nr½¤:³µ°êeü\rÌC@è:tã¿\$'úT¼á}@Âëäªíá}α[¾/Nò²bÐÅk|ÇCÀá-1Z´¼n°ìëoöÓ#³3,\rP°£ôDQ åV*Ç_¨×\n2ðüOÆñü'Ê]ÌsCw4<GgÄ`HÚ82³ÉÓã)è4\rõcö\ri34Æx5@ó­\rÆð'PÊ©  X)²ñ\0Ë+o\r0¹óÍá\$2oñº#3ÀBC3Ç1͵§µHÔÃ:1àA2BóÏ᤾²ãL±|¤cma b7Z2ÒnR=\$Èqg\$sÒ\n\ndXmO¦&îp\rÀp7Pß*Ä<£'hƦÏL[ËeDå s\$! àØF¡ñ&獴4ïÏg'h9¢ÙÌ	ÇA¸¢A\0w94\"ÖâÄe}eFä%dTbl6'Rlf4&q©'\"HLɩOdIRÔôNÐô0P¢¢éM«x¡¨¼ÌÇI&ÉY\rn~Ç 0âañAÄ-½9b\"cjhT㜔Bo!F!¨\0¤81qhўÃÇ*¡yZ)´´2ÒCUªBÆ*%Ç2dEËf°Á:þÅlÎ-´ÇJ?ÌØpڙ8v	¼0¢\nJ97dh#@ BfÓg}h}³Í8É8r%/x*3Ç\nJ:`\$iº/Â<fÓ¹d®£ZYÅLƤ7 5ª­W©F|եø{É	Áb¯ÀÚýCI#18ªÉxjåñ!Tґ[ÌÍr¡֮WkÖ\"u\r®uÆÉ*­ؾöp§£7²V¸ØäK \"ú_mP¨h8dgiàrV¬¼U0 \\¬Ue±\"a¶êwm`7!D0BeS°oå2ìkf+¤£˪T2lêÕ[4²µ²öXőÂetlõtmd&z(c	N\r\$!Wg+BíÙf¼^B?·ù»¥\nKà\n\$iT´RxÃÖ1D|WcT©M¸°¸aA-YC\"Äjj|èWdÌõ¿Ë9×IysVÊw2FR¢ká:Xà@";break;case"ru":$f="%ÌÂ) h-D\rAhÐX4móEÑFxAfÑ@C#mÃE¡#«i{ a2ÊfAÕÔZHÐ^GWqõ¢h.ahêÞhµh¢)-I¥ÓhyL®%0q )̅9h(§HôR»DÖèLÆÑDÌâè)¬ CÈf4ãÌ%GÃfÕ\nb֬W÷{ÜR\r%¡mú5!s,kP¨tv_¥h¡nø]ò#ªÉPօ'[ß\$´ÅôÖ!&c¢ÒhìÚK'FA¡IE\$e6jl°läѬÝ2\"²º\\횩mËK×V7ťs6õýÕÐP¢h¾NC¢h@©ª®zP<£¸¨lì:\nË,¸c¶;ðjA0ÍÀÈÑpï9m³#)©Đ¥ï~ZĎc(º1^ªåӔ¤0é7Ϛ8ÉŪ«ÀG£H©µEÒ ´*8õC«`Ù*­c¯	µ±ü.ùÄ.£®ð80´	ôÏ9\"\\ÇҫZöÅHÚû8M²ð\"ò¼?>jRʴñvȚºåkÂôæKòL´îÂd¹ ģÛEQc* \$|zÎ2ÑqR¸Î*JC²êÄ<hñªþä|â¨5úÕËJ~͑o\"ءÏ(ãÝS·ς7°Úxû¤11VJ垢ZN3>2ÊO¸ó Ç-㄃ҋÏS£]Âô'Í|­ǈ<@ãÜ2ýÅ:!(Èԥi0aõáfU/uhXIø¡pHäA\rvVÉ􌚣J-â5bÊ8Jг¿%Ú~ ¹õ£µ(¹Ñ ÇÉTì©&qD Õ	N	Ù9ª@\nr¶^ÂÚÚYìb쏋iNJʍBü.®aà²Î4~£S0ʷD_©¨x!òs¹nùX-ÿ,½ƭªù-eÚt0ºíiûrï¡¢GrÎ{èó9tRJËçÍÑñ«®Ðs© 6ÙŊDOäXÑBoúo»[º:¯yvPCø¨ö6&fª¸»!|ñ!°ç-1ÙÎ-ûó½ûG-£YEy	mÜú=·2\\Gì±n²bº­¼¡på7>dµç+O¤õ\rû×(êq=¨Æ^é}xü>ØyOY\"y\n\\>¾úÞî~<ù·\"߳ø;	]ýÿre\$OY\\=\nøÄ[ÜOy448©%ó.bôS}ª	¸:]Ì·3¨%uՓówËõªÁð{}5OLꢤì¸֋@ìÈCHn¡È(sL	qÖcG#z´7 @eÀôès@¼yg1¬ðäÁxe\r<2ðÜC i<7TN¡Ÿ\$z¬[a¾Otç*Ø~T¹2>h­Q¨.£lRmå¹ôF'8~󏊆tËÇêN<Ì\$\0µq'ÁÙSs°6?H	!\$4R2Gy!\$£Pr²^LɰÊ¤rM'\n¶J?)ÐôÊמ_Í­9î\$Ozé_ÄË?ø\nH¨LE]°+èÞOåãmBÆ\\ÔÀtMó }\\´*f-##åM'¾»¥^Ù'ÇÀÈÅø(rhreK¨«316¦ù ¥-M_aÌ4±XÆ:ÐAÓ<i)DVïM;[DVlӯϒ~4±ÿÕG¢ÝB}ð½®<4ֹª\$6@ ²Ðø}!HÖC7ƱÑy_¡µZUú)LÁfIXÀe2LÑWä\\GÕ}»«(¤Ö9¸]ÚäKýu(75_\"Ð-IÎ4Ó,\\Y]ÅÕM·¨tÌMP¦<ʁGa¶&ܟ×?eÕ'QÑ<+R6á{\rܕ9®]K¢*S**ÌH¨r7IP	>m1eÓ{¹¶pÆá^Ýí}¥÷\\J¤ÁzNE^2ðÝË(n(۳Ëëæ:Ѡ%i¥®⇑r\\¢»WԎãÃ>£,èsÔãÔ.Î\"1خ_Ì\$f,1\n¯®~òIU\0¤ÔL6XnÊcùÌ\0«ÊÎV V/!à4ÈêЁ\"µÏçäìl¬R@\$jbUz˻¤Äüâõã,kAÁ'êufàæóÎ-¾¶ã´ôRÌ1®NÆå`)u	Qp%°`¨IÕ_ö.ÏPû֚çAö\ré²Mæ-_ËÒ3\nçÊ:ÄRmMbïºÉoVjVæh1_=Jtίu¬ÜáW.U«&#¥Ϋ9b2vźÆB\"°g^¿vnϋEbÓµu³L;m X|ݨlc@T­W,qkôΆ%K'糇/½#@¢:à¸A¯mÛdnîdæARfµ\nǣãp3ùèy¦·Ld´B¨Tº`á:´Ï*Yͅ&B>+¶ýÖùAãú]ò\"¾Âù)lVPéd=ËMqnäeX܅(H\\AöL«aShH³ªý3ò!:ÞàÍ©CuÏ.;9zT¤Ôb­N0ç.*ÎÒÐ[ñãª\"ê}÷Æ}êÒÞÓ>ãx_;Ú7ìõd«[I!w¨Èé\$\$4ttdTúFDhڴJìXâ9àìf¿3ÇÕ¾4·±4u7©Õ\\ûOè\0®wæùb­ò+/gÌwgI>r¥°4WómÓ,ðV¹ؿwýojãÈ2wʦR.7tãRZßO\"\n¿ÚëR#âé6ÐëáÉÄX";break;case"sk":$f="%ÌÂ(¦Ã]ç(!@n2\ræC	ÈÒl7ÃÌ&¥¦dÚÃP\rÐèØÞl2¥±¾5Îqø\$\"r:\rFQ\0æBÁá0¸yË%9´90cA¨Øn8ÆyèÂj)AèÉBÍ&sLÊR\nb¯M&}èa1fæ³̄«k01ðQZ0Å_bԷÕò  _0qN¡:Q\rö¹AÚ n4Ñ%b	®¤a6OR¦¡5#7ü\n\n*ãò8Î	¿!Ö\"F¸ëo;G³A#vÚ8.D8íܞ1û*­à͗əÌÂ\n-L6la+æy5ãO&(î3:=.ϐ@1Øx¶¡Ȃ\$2\"J\r(æ\$\"ä<ãjhý£B¡«z=	ÈÜ1º\rH֢jJ|¦)ãJ¢©©	F<ð»Þ\"%\n<9Ã\n\n)¨ûæ1 Pº¥à)µ,`2ãhÊ:3. óº-\nn9fRÈà<B(È4·C(\r㬾VÌ)±|	²19¢Ã@ؔnCÜ\n£AÌëXÓAPR±:	\$á\roxJ2Î:4Ý;O5\r9O7.\rÃxÔI¡ l¥h¿CBn¾:@PÖ2©Ên¿\"Ðß.53{&\$Ӏ m«Þ+MQízУ£Ôø\$Bh\nb2Ãh\\-WHä.¿C¬\r49hꨄà£Ô¶:ÊM`ÜÖ]#òã|ßWà7Ã2ç¸F¬`MjPÈDj3X!NS 7¦ñ6=<¯=ýXc\r˒¨Øâʕ³`Ù. ÑÞ3͊ ¤Ä)¬ä'CB±ÓÎؼ©¤ô\rã]îèÍ¥tÊ )t¨¯ÈÌ\0è!Ë2N\r«¨ê©ÀNVê¥]äڰç¬kT\r¥ëÛȄlFÍVº!ÕQm£vÞÂîKfèÅîɮðåê㶲åoԍaÁl\\+uÅqJÆJÐèéæ½Îfc-óPö2jÉ¬ñ5½6M;Ó\0Ø#'»õ\n_Ã0z\r è8aÐ^þÈ\\¾xã\\á|È4âxDQØà2·8®#I¡^¥ÃV9и>ZÏ)°-­·÷\n{â¨y³2zâ­'gMAd*CAB¨ùÿ(zÁrù!Äp2¼ç ô£Ö{iîTB÷ÓáaÌóBèn}Kð\$ÐàLÐn*Oŋ?D´n!A(̀À¹%iqE÷÷]AëÕ{µ:Iá-%äÅ3 ´jEJX¸GÞyUj®xóC0t	Ze­µÎ(óÖÞΈcC¡¤ꋢ(i!/.Ü˲¶Z+Yz	ƥʍÄ[8æX¢\"[A2¨;²ÃÍ\$# ­\0 £2ECf½G¢`å÷D,Ý!æ`ã0æpÁ·R3h6¥Htrc\\R¼(Q?D& zD0\r§fIš(03H+NquN³ ©DáY1) ZAÆ%)"b½ù¨'÷`s@õzBLJ	TN\"G9-²b]0¼¤Rç£°/O*#-p)3TæÕKÙ7 º2%&ȡ.Eä@£Öl\\P x¯vn`èK~!¨ÁBʀiîQ»ÌO\naRK/*;O,üª¦3&J¨ý!(d·¬rg`©,Wñ®L:dõ\0S\n!0'R~½FáÉlÕ³û9ÈoE#@ rI27GDAFZꊛhdbpCQëCçȳk8¦-/«¦¸:e.E\r§³ÊeZ¼p£%±JKÙä\\§lëYÐü<j	CK° ÒÍB#5tntÒrØ\rq©è&\"0õL2)d1{8ë9	ãL¢Íòe*¨T4A¬¼41Q1B\næ7ޜ_E.S=ý-¬áÈ`B3¬ã!¡µ¸¬N!\n\0Áq\"±Eÿ®ó&|TDè©I`¥#i°:s\$ \$ìA6ïÁÑÖEZ]ùÇgÎàùjGLQ3°FrþxOl«%xÕÂœ2q!Ê)¥ÅX¦KU6D²0ʠèeB蕪۵8f_(kTÆaۘLç	ÈvA?\0QH.Yt¦0\"\$¡îZè_+Å༎¡¼éP Eï\nYÕ6¶ -U4DN¢ªËr";break;case"sl":$f="%ÌÂ(eMç#)´@n0\rìUñ¤èi'CyÐÊk2 ÆQØÊÄF\"	1°Òk7ΑÜv?5B§25åfèA¼Å2dB\0PÀb2£a¸àr\n)ǄepÓ(0#ðUpÂz7ÁP³IÓ6A£C	ÊlaCH(­H;_Iу±Êdi1ȋ&ó¨ÐaCÍ��l2̧1p@u8F«GCA§9t1f\$E3AÊÃ}Ök¬B|<Ã6¦¡ë?§&ÚƷ_´7K08üʱ·ÁDы*ÅPßIFSԼU8Bî·ҩ¸×i;òL§#.}ºNp!¿7ôÌàùcº2\$BÚ9#hXϿ´2¨:V7Ì(¦°@½èâ	¨ë¢T¥<ˌ R~:¨sj° ¬ºKxÂ9,@P\"Ȏ2ãhÊ:IDrð<CÄì\rkÒ8<\0Ê;\"+ÖïrPÓ&2pHÊGã\$@ÃJT ø¨×\rH)32H7ÃJàÊ2HC £H:3àA?\rK¥>ÏãMAÏó <³`RÒ°\\¸bé»	Bú5§#`+Z/6BdE\" Êà§.ªõ¯£Ò45B@	¢ht)`T6ÂÛØöµìʫp¸èOlàÚ:¦A\0x0§aòGÃo=òã|ZöȆ7Ã2Ú:¡%Í\r³¬94˫1zXA_IÓ9'wøð\rÐEØ9µnâ 6¯£d1lln¢Ã2\\¢5r Ë»ÂxÉ3£*\rìڰ,þ:c69èØ\"KvșhÃ	6·´+\rê<aL\nÊÌé]¨8cå\n \n{ÌÎ×õ¨èð·½pXǅ##×k%mhÊ3¡Ð:æáxﻅ;õÈXÎÂ{ê\"÷`^Ü6èÞW:±W¥i͚ PA&0Hß¼+Hì´Í3~H\$BkäJÜP¨Ê<JØ]´1Èfٷné»oÖÀ9o£ÿÀõøF7pöȒ6Ą7mù<£;)hÿ=yZè@°¤ޥo\$5Ô,H#xÎ7¥¨4!Ç¹#¸Ò6f벶zÚÈrHHT0gPA2lћ3Üm_Ѥ3¯Ђ{B\\°ÄhC\rñõ4Hdd ¿P©áp-ӣärá (\0PNÁI#	¸;órÉë\rÅûµÃ<W¹Në4þ¯S8ZI\"AÝ;\"KS9D2A?3ÖB\r2Þ ý'pphΪºÄ*Í(cÌ@3¶õB\r!aj˺gäpFva@rEDZnUԝY®XbPäÂ'fLúÃÓrL.<29\$ì|Ȣ]¹§8	8OìLal§á4ä*i#	!Ô͓0Ì{Hs^on¦<ÆD'üÍ4¹\0K͆h=° Tvaiï£±.Á¡Jrq!bDHÁáÒéB rÑ<2^ìV-ó@D¼bE´0¢î_f`©\rK\nA'ðj#4Hr\$Ï2´ó+cÕ2¡̰ÍäðéBÍhP£JpÝKËrAØÀScVy99.IA'j_&ÀHS¨B¡>RèáM&He*¤Գ*!¨µHêªC*¨Õ¨§tò\"\nD\r¬5ÎlKÙiZ³߹\n®H3û!Mj¿ãjB F áæÆ_pg/0öX¦êÉ\\<{«ÁVÆê°R*\"á4Ù;*E3³&Ȓe,U![Ifâµ{!d4ÚR§gK©wKá5\nÌK)·\$ÀF¥ð¤]߲fi4/îy̐®ªO£¸dEtu\0·ÝC:B*w®UuTúú¡ð¼¦Ü蕄X@PZ6ç	2î\rpzfÅÑ=(+^b\n¿ösk6p-qæaû§pȑbUR£;PÂJ¸³ÕgÝS&j.@hdöåʗìb\nÅVIÁÀ";break;case"sr":$f="%ÌÂ) ¡h.Úi µ4¶	 ¾ÃÚ¨|EzÐ\\4S֊\r¢h/ãP¥ðºHÖPön¯vÎ0GÖÖ h¡ä\r\nâ)E¨ÑȄ:%9¥Í¥>/©ÍéÙM}H×á`(`1ƃQ°Üp9WhtuO`¿J\r¢±®ðeþ;±¯ ьF\rgK¡B`ÉÒÞýX42¸]nG<^PdeCRµÇ×¼íûFÏt ¢ɼê 4NÆQ¸Þ 8'cI°Êg2ÄN9Ôàd08CA§¤t0¹ոD1%ÝCo-'Ñ3õDo¶8eAº¾á¶íÒZ½£ÎA½)ä¿@{b0*;p&Ð\0¦á\r#p΃4í\rY¡¨Éã] Ès(¤>ÍXª7\rn0î7(ä9\rã\\\";/Â9¸ Þ踣xè:Äk!Øæ;ƣ\"¶N\"ëã\\£:C¤*üÁí	z§E¢<E-ͦê¶½-н¨©ª\"#JҐ+d´¯*{Ð^@éë£5è1DKùÚ0j²F9A²hÒuPڬXDªû*±*LÐü¢Ìèü5ø¥¾\nMC+TM*¾Mr&ÉÔD±£ OÓÉÍKõ>Ǿ	|¢ø0(͏`A(Ȉ´oR*ÛVâck\\ÛEqªJØHü\0¡pHÞA·b?tëвöFe.ÀD?¯U1eԤ5H#fØÐÃ дm*T	h	@t&¡Ц)C\$6¡p¶<æȺÓb÷>5,³\n¼ª[.Ú:rx0«ðä2¨å:df0!à^0Év¢cxÜ3\r#>2êZ @¬®Æ9ÆîóÂçlákÛ[²mû Ê<;Cpæ4ë[Ey,\0PØ:Mã|:>Ü3Ãd*2¼£81<S÷P¨7¸ãn<®ô:c¢9èØ\rxÏ\nac¦9tÎ0ìÛ5Â6£«´aJ¶QԉSa©²i̾	p¨àŎͷè¨Î#'x7F]Ǿ#\$dhn3¡Ð:æáxïùÃµÇÆѨÎ£p^toøè\0v^³aj¤:PÕ!°0J}-D®á\nȔ¬õI!(\"äț¢Zj;;AÈä\$¤Ûw¥£¤çÈ_0e}©ö>çàü£ö{hÈ?§øÿ»yomh&Chp9uþHÛb¡Ü\rëlê<Î iIÉÏ=ÀÜ{!i(taI16ÍU¼±\"\$òB%HÅ{Î\r08ց\0w9®¬1@̞rl0hV+§u.­ֺôa\"áӐd0Å@@á¬U\r!67á g	NgQ¬ǭ L\$])'7c²^)\0%+ƅ`\0PVJc2ÉETsY\rÏrC=c¦qÎIË9§<2­´¡Ò;É!ÊÇZç)[&3©c`fI<'È:A#(Ât¡ÀsI.²£¹Cppwд9¤´\"Nèc\ry®¾°A#eÄa 9¥=|ö&¨0!0¤£ÊÄE|d2¡#UjBáÁÅá%^êPԐÄA.ʴ#§¬DÁ+£BBÜOhe+èL4JFVÓÕJDR:!F\r@ô º\n»~	\$<¸pÈøga±pî&C©ÑHaװýÞä, èÀ1ºÔy(§ZI:ìۛ¢VÕ<Å	áL*KéfµÝ+RQ¨ÀL¨ûQiHÖeµsVìu¤kz6%(DԂ¥\$è^neÑbiEwâCaTJ©°͜ÓFсÖu¯ÙÄQvºQ	®6Ù0T\r5m¢Û-s®µ¨94l{,3µ\\Áb§DÂð¦ø+Ì¨?ƪõÞïx[d¼6Ë-b\\¸TRн×íUr0j¥\\«Vû¦ªYãÔ\$ÀʿP]ð\n×A(á<df˄\r­4XtӅ§26©ÞUuT©Ð\nӎ/4F0U\nà÷ë:¿ënÓÞôa©AÂø®ūªñFÇÓ%äL´ɽäÊ9'ÂynY#q\$¥8½ßÆiáÌ䬒¶C0y2e.8â®ƃµ¸ٔ»åsê&½dùRwñn~Åå3D¢Q1U©K\rSBhjé##\"ЮË!-!JÒǤ×_fPe.!Fâ̬!L¦±=íkU³дÃ\\Xk½s\0S§:CNRó5âË\$LZ}%nk!1; ԴØ/ªBÎ,ä´*#51I¦BÛÖd\n\r£CÄÊꠥð";break;case"sv":$f="%ÌÂ(e:ì5)È@i7¢	È 6ELÔàp&Ã)¸\\\n\$0ÖÆsÒ8t!CtrZo9I\rb%9¤äiC7áñ,X\nFC1 Ôl7AL4\$8ÈuOMfSüt7ASI a6&ã<¼Âb2\$)9HÊd¶Ù7#qßuÂ]D(­ND°0è(àr4¨¶ë\$U0!1ãn%(Ɖì:]x½Idå3O´Û\ræ3Dpt9ÏtQNÊÿÆ·Þö!Å§²ݾ×r#-ÿ+/5&ã´ôÜdÍ~hIóÐÝÌ':4¶Td5gb(īè7'\"N+<Ãc7\"#̋¨Ã죦E#μ¾j(\n\$Crůã\nL	èÚ6¬3C7Mà@=è9<˫°!\"\rhé8C²Èðã*҄3	#cè<JüÀì#<²C&£p&?É,-°ìR \n Û\$¨J 6L#s+( Âퟒ�Ä<¢@LêNè\n50cpÊ5A b¹ISGÌbõmûC(ÏBCܺ+d(腧ÏDR\r Ü:¥@t&¡Ц)CÍl<±8ZU=RԾ R\r£ªZ\n8àëA( äÆ!à^0É=ec{\n¶Ùã-­lC\nR!r¶c¤jAöò½¬%#8!(Aw³¶`½'£<£Jnì\r°5­ÐÌ3@Ôf²R3ü±،Ï؈bè\r¸æ6#ÖƩseýOÆÁc<[N	lÀíÐÛ\"øíV:ÀN/c%.7bcÙEMsk*ÉåC3?fþeh»§ðAU££¦LºÀNÈÑem¯Á2¬&#&l²e[4\r§îΎRëBÙ!\0ÐãÁèD44CæáxïÅȮ־'8^ã%¼Ѝ6ð^ܕÆ\\v˔2c+2Hrd[\ré¶9¶\\ð:N\$£°ê2tïüoLûÔuAuoc.û¿ð<ÎüNԈ1Üo7qãÂ\$¤ô|½\$£I)å®^Ê4P´\"úVy»¡Lú8Õ!2hå3ÐHfÒ7û\n	ÐtVó:Ha-\"aØܯAJIÆ¢TÈÕ ÖZQCbuO¨Æ§i׺hL´VNeV!NHØæ¶Ê¤-XӒræHJûB+øđÄjHP	@Ã%úӃi ¨[B\n\n\0)j¦¸ªBèÛÃ	'<l}²^LA3&l4#UMû¾a'ìÃáJDÁé-Ì¶ÂXNeaIp´;Ä:ÈA	Eé1ðÂÈ{¿wP06ÒFXÈK#AÔ\"\"HA²DH\0F¡¼±`@ÃXihfùùT\"³Hzÿvd¨殉 W_´¥PBXKue¡¬Ì?©á315Ø5.ÒÌÛ\$d ¡Åò24¥ÕM\$Ú\\Ya EÀ}­鄺\"6O\naQ]Â`¤C'&\0æÈáÁ	_:aÌR®ÃIÆ\rdÁF!7#LPYäCQ	´À@fàF\nA纥Cð\$3Yý%\"Â*iÍޚèþN|7䞔§\"véZ©ç1\0٧5N'¸a6´ñazWMÝðb@Æhöôcz¨ÉÌÎӊêk,©æ44fXk@Ҏ/sd#ú}n|¡¥á\0,¢æä4\0ª\$Ð8¢á3Ɣ÷T)i£é(ÁSʤbÌlüwfZsBÉ8KnQ~e%UR]\$KEr0\\ÊEøüv2PR獳{Rr#\rÒvµ:ÙZ%ò[¡p¯,BÎÀS\r\$l:\"ZJÉ8ХQl@d°õAbàzZl& (\"áHܻöä5°%uu5óí¯YGbJ؄³¨½&\0";break;case"ta":$f="%ÌÂ).J¸è¸:ª:º¬¢ðu>8â@#\"°ñ\0 êp6Ì&ALQ\\! êøò¹_ FK£hÌâµ¯ã3Xҽ.B!PÅt9_¦Ð`ê\$RT¡êmq?5MN%Õurι@W DS\nÂâ4ûª;¢Ô(´pP°0cA¨Øn8ÒU©Ò_\\ÈdjåõÂÄ?¤Ú&Jèí¦GFM§¡äSI²XrJë΢_Ç'ìõÅJuCÇ^íêêʽp i4ä=¼ïxSúâÃ¶»î/Q*AdÞu'c(ÜoF±¤Øe3Nb§Nd0;§CA§Öt0¼û¼lî,WêKúɨNCR,H\0µkí7êS§*R¸ޢjÂ¶MY`³¸,ù#esÿ·ªÕrʢ±µñ\rBãÁÐÔàB¶4Ã;2¡)(³|\nD¡¬à@\0Pª7\rnøî7(ä9\r㒐\">/ÈÂ9»£ Þõ;Ãxè\$ãË9Xæ;̣#w¤I´@´¥Ìk6Gô\"I îuW(R0,d­ðù\rØÒ7Éj*+­]¦!1ã%Ðn,L·k\n.©uHY¦«3Vå7drڱĪ¹\\)êKz«0\\W+ê ÎÕÒq1ezwµv櫖J)ӮdB¦æÊH=ªͶ\nÑÑÒZ̫ÊÑkF¼¤¢8Ê7£-ÂÓ8l¸ª2=u@Þ)uï¢L³WbDqô#Çpªʬ´mç*ÔØ>7]P*̓ȭµpí´UÌj ÆÂ-mÕJ*4I+¬q[¹Xæî>ssPM^a8qµßU¶=	¦°¶[)£\$]×h¬ëj4jØÆÖ'y/PïA jàØÑ+`åÕñ\\L¦µN8F£·岪ª4ÓHpÔ\r¤ºą}®fÐ5®æ*bðÝiÂ9Í\\åB@	¢ht)`PÈ2ãhÚcÏl<¶2¹³eü¤ucêwsÍr\\åL£¨ç9\nØXê9L«1#Èx!òæùâ@3\r#?¦2û~êÖõȟ\\Îû?/?ÖL³Ýø»´ÏëL´ØʧTú7mY@®Ôj¾]òÅ\rc°^ôX6\$@ÊnØzq¾,·ÓÜeFeͱÕòV\n\n¼ð׬A\0uS¡Ô13ÔÃ0u\rtÁaìPÀ0pÂû_k`\r©: PÁL«8§x£Z	'̟ÂUq	ִUñiÀ X¨g	H\ni.°Ìý^RDà HAjODÄiÙz! Ð p`èðï#Áq+é&PÎßø/MÏÜ:@^ô>1Éô½ç\"fŻ\"*-¨µPÔcc¤_°âµê9- ¹e¤~M[º\"Zá®/¨ºq֑H	©¬ù#àrkOô8¢¤\0aABHi\"¤dJ&i-&tÐPI÷Mç`!¸:JWæÀO okG´BàÖwCJxLPº<OCù	eI«FKLµSÃ:-%@ª¶\$ \\ü5è%H\"UT&\n¯)ñ¨4ÐÂäê ÜóC°ÄwCbáɀµ Â¦jt°Þøzûiaô=×òt0ÀçRx`¡þ©ÀçDÔb1!êWmPP	@Pç1ÐzcÕI,2H\ne\\«Æäê\0§Ä£Å(G°ð#Èy@ekIØ9GØzÎús©¤¤;Ø#øº%ç\$2ÙQ¡\\¶UU×3¦Q©\"b ®m4ó\rí>\00073Ú{O´¬ú0Ñ?_,¾¡ÐÆ^T®ÁL)b\\lC}T0ÂRòPâҼEËÕJK¤TrIÔ}±¸9VYsBªµ»
b»×)5Uêlhádiô]@RÁGâ.·Ò)gޮå۽pxÐû´,Jå½ô]xV5Qêã]u¦HªÍ&«Baa¶EuòÖAÑfZ/(\n	\$<p@KZ±\$ÿϳêzވq§©9dÈc¤v¶õ02R*öç¤ݢz.Bª,pT  T%Ô?¸ÃV^p{A¦Ï/£¦0gZF&ôvd8ÉsÍf\r~g1«ÝWؘÊå\$±ì7>GÌ¥*ÆF½ʸØägÁJïӫ­ú`\$䤕	´( \naD&Èü,ÚÁR²=f´Mwµ8÷ã@åڤQʺÀyjÑnÖjÁ7¹«ßÕ.қS֚òû,«ö¶EøöQØðf¿ÁÛˣle¨(	'È6×åð%ÙÛ+ѽ¤R£ØWéRì]n«â֊[·½¤3éb¯ûZÝ[UªnM±¤½Þ8oyëÝêrX98)\rpÀWHc\roÑê\0íSqÝ|³è4ha@J@F°õå';xûB¨T4?h÷.èßYyüWPeo·¡¬­µªl-\\l¨Lló»væP;ãtóJցLni­Öw¤£´yîٞ:t(J¢:.»ß×ٲ#kìÀ̊`4üxXO¯V¢gÕq_EgöoãÅ\$è»3Îómmpñk*©á±F]à  )P٠Të+7¬] Q=혚\"&ë;³e;°з!,[«úJ%Î0x֮*K,©ÒÆÌX7hà	SZ\r©6¦ðcbï;±\\ºlA0K=á(^мo¡IÝå{\$,6ª0ê;åbBÔxó\n~X2ҋb۾çÁǆV#⩎Þõ}?`ò\$^Ië\"JëdîóOâiHÄȗ\\ΎØúîúqânîÑÎ֗æuüæ¸Íf(í.GÌ,íϬo6êà";break;case"th":$f="%ÌÂáOZAS0U/Z\$CDAUPȴqp£¥ ªØ*Æ\n  ª¸*\nÅW	ùlM1ÄÑ\"èâT¸®!«R4\\K3uÄmp¹¡ãPUÄåq\\-c8UR\n%bh9\\êÇEY*uq2[ÈÄS\ny8\\E×1ÌBñH¥#'\0PÀb2£a¸às=UW	8»{³®#+µ&Õ\\K#ð[á[=æ-¶¸O5Õ,§¶%Ê&ݶ\\&¤°TÔJ}Õ'·[®A«CÝó\\¶Öðßk%Ä'T¡ßL¯WȽg+!è'òMbãCãÐù ¢ɼê 4NÆQ¸Þ 8'cI°Ê3£@:>ã¨à2#£è:\rL:#ü»·-ڀ ¥³·EÂMªð˘ï³ÅÁa9­³~¥NsL©é¬^\\.-R\\Î\"¶ÓC²¬CEÚΩMÃRé:³¸½()E¸Ï<·äØ)¾CHÜ3§©srñR7Ë!p´ÅËbLB¨Ü5¾øÜ£ä7IàÂ#æú|úã @9ÀÄñCðæ;Ï\$(θì(¶34ÐÜ#mSAºJs¯±ت,»pòA\0b)±Ý>֪m«/:¬\$ÓJËRç\n;ªÓ~À&ËuUÉÈ* Ì9lô\\S,?#ÆNÃDôN\\ºM¼ÙGR®\\ÌìƏº6Ê\nH#Ê\nò÷jß&4Ýèŵ̝{8éúRõ!*¥µ¾éL1	pNYË52´-SRâhÅ.zz´ƗÒÙñU5Þ)ëÑCêv!T(´ZÜ(ju¾8ñ<+/ «â`Í ¥ ôVò՚â-Vráçn,¼ò(©­|5r|\$B¯`PJ2 ü:©`Pó\0O°¡xHíK µ(-ɇ2RÊÕ^º+­²Ù/hJ\$,Þçº¡î¡\\uH ³¯Eí/4¯!ÙÝ\n\neB@	¢ht)mE¹XÄ@_¢çüÊmz².ò÷ÌfÎæwCº¨):ô6£+ |9£ ê9Nc£;#Èx!òyäyBÞ7ÃHÏ猾¿³aՅ=AЋýôO Ý÷> AøþÃ(ñ\rØÒ÷Ö*#ÍYʤÀ r=¸ýÃ0f\rh2£¤\"Êã\\èͬÂzyӉ5jP³¤:oB o>áµé@_èua\00ÌC` \r¼3¥ ærD3Ôúø \r©h:  PÁJ	A5vÓ\rÔoë5µàVé'Pø¦ôÅKAÉÃru\0ȝAÈ>03ÐD tÌð^ä@.1¾\n'ðÁxe\r=@¿0èÿä\"ÒUƷÌö¼5\nÀÑÖ~y\nV*«E\"a	#iޚ¶lÚ[;)R²#½ÇW¦àõÔüøQÊ@96ôKÌRQä0ǰËcürCș¨.òFI¿§øÿø/O(\$Ðàl}ôOÛA5@ң¬,!¸:-RԾàÓ	T«ãV&A\0SO~1èC{à;Ør pN± 9OÀC4ÈPðÊ8m!Ô<}Tu :,¡ÃñiO ÒCd/lD:§KⳃJc²×\\¿©mԶ¨©éã* ( At*,:ÕjPÎëD­)µSCËÁE'`)ïèãFcZ>çäýÓþ[AÑ å¡ª;ö\nKSÈḯÚGdÅciÏ\\dN³ùMPæ£a£éNh2àáfU.; ÐÆ\"Sâ\0ScèÃł«Ú`¯T¾Úڥ !0¤\r5VQàÛ.º:°ªgÇ#R¥åP]\0ÎÛԦP¡|p¥Y«*[\nqgõhòH)L<ÉVm.Ë:FuÉÊö©,ÂH©§^íÇÖI002Gk¡äùA¨	æêT0fNá¶6ÈÈã2m\"spí?Ók£P ÷YVTwtʔ´hèÁÀòPèb¿,BýÔҀ¬Rå:´5BØp·:àµ^²ݎ+jÅÅíôRdáJr½/²«e¬Ê¹±'´÷ۄ恡܋¶ÑñQ¢á}çòj`©Wޓ`\r3¹E¾3ðr>iæ5VÕ.bPKÑe\"F&Õúa*V·¤ØÁ\$¬ÑØÑû5IS¡Èú°Ñ-^\r\$v^´jÓX@¹¶\r=2éÇÔfuǴRäõYÕ)IûC`+Äa¤1¼Qmñ|\$q\"~Es\nÕú%<U\rm_PU\nå0j['%YCA¬Ës:RԆé­USÎ|ȈµVäæEÎÈÇ#(PgÀiÁä֔FURèMÎ\rä[ÌáN¢(\"`ã:ÇQZeb¹/*Bٴcaâû5~ls§²WFpí¥êͥÊЊ®|óPmO\n>\0®o\"ãY=4Hݚ%ÔvÒ%/ЦJ׵rÑÜ҄ÿQ\0OJÅ¸YÜ`ºÑ=(;©~4v招¬³LÅóêòÖçÈâÉHS÷=H¶r»ó_Ê`";break;case"tr":$f="%ÌÂ(o9L\";\rln2NFaÚi<ÎBàS`z4hPË\"2B!B¼òu:`Eºhr§2r	L§cÀAb'âÁ\0(`1ƃQ°Üp9bò(¹ÎBi=ÁRÖ*|4¤&`(¨a1\râɮ|Ã^¤ñZɮøK0fK¡¾ì\n!L¾x7̦ȭÖ 4°Ôò¡èk¯°¸|æ\"titò3-ñz7eL§lDìa63ڜ®I7F¸Ӻ¿AE=éÉF¹qH7PuÊM"©¸Ön7äQ#j|aŘ'=©¼Êsx03©ÀáÂ=g3¼hȎ'a\0ê=;C¢h6)Âj2;I`҉¸Á\0ÖìA	²j%H\\:\$á¢&ãÁ0@ä·A#HÐÖ Úí:£ÐÎå#Í\0Ø4B\nã(ޡS\n;I ƜÀäÈB#^½¶cHÇ:îÌ-# P¡ðDy++¸ãCЛËÂÌÉ	0,c:3ä<µ\nw3D8HÁ i@1ˆ\rÉN1Aìè䈹iºØ0B`ҋµòÊʹc+4,´»åaЈ\"C\n3Ã\\-5ò.B²JSÅ<Þø\r£¨æÂ\nvLðê9\$Plá!à^0ÈåanÌ4ö8ËgZ	ÓàÎ'iðÒ9±l7CuËoD2àܯ8#9KWì\$\raê&6¢ʚi¤,=DC¨Ü:¨·ù\rx2̀0Á\0Ì0PEژC«r0³Qz¢Ï{>\nXӬâ\"8HօnC[9dãt¢ãaPdSË¦Î=¶.Ö^#\"¶BR\" )8RhÑQLä8²£X×%7H&ÑX!\0ÑbÁèD4 à9Ax^;ïn¬¢Ar43é8^ÝC Òàá|õk¥ʺ#_5ÅâÊÈ2壣i\rÈç%eJ<º2¡`@6/ Íç¹ú0ryfª]AB3X®2Eї*½Ó\r@\\0d˱~X³Ó9¶íã.ã¹ó½ï£ÿ¡#|\n7p·Oz|\0¨IOQóṶ́Ûô>ß*KÏ8·h؋ÞP±eäl6EQBÈIK£s.£`g\$ÄM§ÓÔÒÐSN!a½ЂH)Ùs1ný´fÓe\"¥Å2*d\\-#Uäô©=ìô´\"D@P9õÅ\$ÂBDI('`¥·(¥DÐñC(¦¤ ØÕÊ\ne±@äRP¬7>&ŢfB܀B®\\8I!q#0äW \r°9A&HàT\rH֮8<Aђ3:H9\n1úUGõG\0î¢C+\"᝺Ã[#Ml[WTGQND£À ) Q8!Õãb4¯YãevDdìÂ<H	p\$ǌ1¶:Bԛ¿M¢ÅçÁæZ 	µג\$HFB(&  ÜGI%(5V®<H1ä=µ>Ò0{!,&K<±·0ÍÏÔ7ìI\"ðKW\n<)CXDÊóªAäã±ÂÙ>¡G!h èC3+ÈD:²Hê	8Q¢V¤MߠF\n\n^QÖ=?å\0=F0¢1@´ÈäH £Mùôaå%kN!8tþ;8iL:B@ÄHah¼2&Nú2âï95Q\0'oUÍџuyAãWYêü­uT!(ٱ\"ªað´Ë=6Hz5¡\r ÀW\rËô˟düP¨h8BA¼2 cþÒÖTÙZ*<P©-2hFbñ³õFÐÐØci-¦EĹǤlN/)A D+Q¹eFËì2\$óbr e)*޼ÂÄ_[	(¸ÆÊ/Ƥ\nÂóÐBoÍ`ÓcÓ\nÓ2øh=:(±°ÔqxtRÍO§jà,S¥¨­õNÛuFÃxp!Ix\"1kI9mz'#ÑØãl˒&³7a\0ßU&¾/¼2wõs";break;case"uk":$f="%ÌÂ) h-ZƂù ¶h.ÚÊ h-Ú¬m ½h £ÑĆ& h¡#˘º.(.<»h£#ñvÒÐ_´Ps94R\\ÊøÒñ¢h %¨ä²p	Nm¹ ¤ÄcØL¢¡4PҒá\0(`1ƃQ°Üp9\$¤ñÕü&;dHÃø5õ}QÄ\$¥öÑCÆä˩üZB¡	D8±ÄÚ(iÍyA~Gt(êy¢g£²Yã1~ÍҚ(ùëBdׯKm®JI±\r.(²§èV­¼V1>#ãë\$:-ÀÇ÷r%CÎǴ)/½ÕÐtép­^Ö\rðâ>[73'ÎòÑ6ªSP5dZ¤{îh>/Ѡú¤êz0è)28Ë?Êvï(P|\"ùÀo¼¦­KBÚ\"i{* Äô Ä5ϲ¿:㹉úв¼H£ÈÓ8ޣ\"JB¸®Z薉£(F)µÊZY(Â\$×&Y¦¬£ç6,«X\\¹NÛzÀ#¼æÑDZ²9«Ëª±)éĵ+Å;DLh1(É3Ïë É(1@ݷ¬£lhQñɠMHª>Kò X Ěü!¨аq Q&«ëß1ód3WÁH³\\Cº%-£E5 ÄÕsEÊUë\$C Äû%-\")ÊQÔNáZHáx·pÓùC:pʠG6ð:£ղf£\"0Ð*©xÞi\\5{ÞPõW¸4ß Ì¡pHáA¢Ǒ\rat2úr·¥ªP×6kZךAVİcwGV1B)©q¬[K2-RhaѠRæù¶8P Éñh&Åà\\-Ö.åQ[ÅX2´3r°@*Ð| Eõ*Óê)£ã|j´nLsÖ=_(»Å?¥\n´á&¨c(ÁJSõ\"íe=»<J_0¿èÌõ>/Ë\$¸oôþ¯ctË å;iKCA¼ò¥¢ϖmåJ6=òÕR	ã\0ÅOa£&PÎj~XÂý(ar1Èð¸¬ÒIÈӻQ+JN×ÇtËEt8\\2vu)åé	õ¼rv\"݋EÚ&²ÜTD/v¡wƇjxTüãxÈùhʳyyé«ØNÅÃÓz¤x²=L÷س{©5ØwÆJ+Áw/©\0>Ç~߂o~o7?w>_kü}OAÿ¹×1[A<z	Á=Äëa\"3DL¯xLQ6ɜhx±J§&DÂC m\r!¸2\$ÎÖ ª\n3oÊä´ÔÃ \rÈ2`z@tÀ9 ^üiF\$ĸxrà¼2à^xn!Ð4ǐ^¹0\n¦ÜØØÙù\\Ǎã¢Hm(dFD÷âWÂ+ӢK'(^Uvn\nì~¯°+EJS\0½:êôwå)ϒXp§e¨RÒpbÄZz0F(É£Dj*&(á£¤v¡à:GPçc°\"lƜ6¢^ʐòõW;´ÿ!óçN¯²XC SÞ0Ñ	m@ç,´G«©	eæP)½Ou-¸\rUÿADù'=C/Ûaµ(i'uªv¬*	íãÏ&úÊÁÖPҘå¹Rþ)6VâÙ9³¢¨^G:ðºÃCoÌãO)쒧լ4\0{ð¼( iӇ§´0´\ngA<((é)êLPÄsy¢q=:P(é+Â}(+qV_Ã&¢+U<xú/té.}«\0u¤i MdýםҼf`ÁåÀÓ4M +	ÖWS©	}f)m5+ùΈ»ܚthRvĖH¡ñ¢#«ù¨ê%)ð@RǦB¸ÚB 5Z²Âª÷d	D>9hЌÃSMä\nkjF[&½-ã×#ýj£\r^Wf²°Àø¡næ\\b×fIɸF©ÀKªÞ8ò+ätéM\nޮÏ;ÑYä/Öy#5iâD̉·¥8¨YM§XKïP{§Ψ§þ¦­s·\\\n<^\0 Tc\r5ñ¾ӪÉ	½¸i+²0gÉåè³\$æö7tËI²®*4Υ¥[;{¡îh iJn܆â!9^-!×BôpÁIsJ]\0¦Ba(I;`¨M982y´exÇèï{y;.CèQ¡ï]ª.¼ôáÙè9,úD³¥&ÁÚB_âfÞߎw2\r\$>VCÒÁï¡÷ö»4*BDÖi¢#ÜI_5ÉCG©©4Î1ÑZtîéýX³	.nX6ºÍKÜ6ºOÑgêp]ºìӉ)ç)â K]»ïB¾Ú,® An)P¨h8%Ï9£Äv.´;}ÍqB»W-.¿\rÖæW\nßHî¢m`>Û!¯Or)}u6Â3Ö.8èæÆ:±÷փ¦ö|?ò-ɫ,u§³DiDÙsµMhadC&ã'TY.xFÐ\nBH©ç^pÕÅ{¤!û`t}õ4qp÷dé½P8 8Ðq_ìcw£Uñâ6YUóĜÓUҶ¥ôed(pcFÁÉ})ZDùpp]UÄ üܴßoa¼×ní{ý`´%aĲÉÏÊ)±+#/ Ý&¶×ÄÈ0¨b\\NÃXZDl¸±í\\\"V·Çdiîשm¼©A§óX¿¯";break;case"uz":$f="%ÌÂ(a<\rƑäêk6LB¼Nl6Lp(a5Í1`äu<Ì'Aèi6Ì&á%4MFØ`æBÁá\"ÉØÔu2Kc'8è0cA¨Øn8'3A¼Üc4MÆsIè@k7Ï#ø**'®'3`(;M6,q&¤å¸ðƙ}ģ+7áÌ7ÓÍþ:B:\rWÔ.3²b\r­ë4 êq×ß/Â|\0(¦a8è¶ۍò :`ð¹*æ{Vv´ËNü-Ço¹¹è÷æ³)æÅdgx¼i£wÐ7MçX('°bî%IÞyÕÄawu¤Ã:D°Ò5£¨ûðñÄ0K82cz(²ö­ì¼#\n2ø#Ðؗ¼CX³«:\$VL[<&¯{♺ê\nn¢*2MÄ4¾7csX߯#è%ct\$nÛü5Läñ P2­)s\n</sà½&c¨ìô¸«\$£êr98rôf7LS ܴ P¡®¬SïzF4+;Ôï-2(ÛÈá*# G\"<L«h¨\\tøb\n#J7=IÈڌ³QºێSâ!* 7ÀPJ)t®L3l)Bh\nb2uVcͤ<²D7Ã#<àÉN\n|)iì1;&4«Hx!ôIXÀøæE÷¸ÒôµaÃqcxôù÷}âÆò,ô°«üí+F¤MHÅ8jʎ1¨)\"ÃËùlPøäÛ,¡\0Þ3\"#ÈÜյ(è¦2C¶hVN:¸JCX´ら1#2m˃£RÒcJâùҍ^;tÅh\"-]4Y3,Q¨ߺ6è3q@%z^¸ù~\"¤êYÒ4êÁ>±uiêÇ\0ìº>Ñ¡KÈ묨è2'vhf¦¹ªÕv	,yKJ×&ЌÂ֎C(ÌCC8aÐ^ýh\\q9 \\Ȍá|JòV4«ÁxD?̶Öc¯ów^B¢z3æC(æKÝ̩7¦\0ß]÷çðØ¨Aë5i²#\0ªãOÉåâݍ.Óâ7¸êéþ3Î|Â:'H郣¨uN±×;Ä읣¶\rÎØ<DJHè\"\\hh½&T»^{ÏE鶅ЄbLH`Ñ1³ÆL	,BOäEèá!0Çì#®¶aÑòheå³4V{j¬»pA	¬{å6GBZ%&L`Ö)¤ªaBBа¿bH\n¢ÀƘp('$ֆRÁ*8GE%E2:Ci/2¬\r³ \n4+xVE¡0#0ĎPèE8'\$fȰw!t}(9~ò¸«T	H1TșoxŅ¡Ӷ¤ªؓh0¦4&+±ø2rDóY8\$4:ʦ<A*%ºPD6îVé}`,)v¼Ò]Á\0ICäïµï7\"£\$ȾÊÖêú±pîÄ95R*£<²)j=\"7þ'q*,\$ØÏPàǛ?%s,(ð¦!ðl?+`¾?D!|;PqH¢èÄÉê÷-¸3ΙL&Têؗ²C\",Ö0¢	©7k #HÖdWLH!Ä@Oã<٣6­Б	º	4r¥²KbÄGB*Qa¸0¹Ò\"¸\n«*)F÷*|«\rc«aъÊ\nT¢¬qpõ;a0êN>U¥lµw2u淑ÕJ\\ã\n®ÕáACLMt0ଊ A¸U\nÎÃ!¢°Ív¯2@Â\nJéåö¦Ô÷ðËÈå||6¬ÙZÐäK-}a.ᐼ 󀯈BDdk`Ô\n\rtqüÜ Nu]	¦ï(dÀQÁ0Ý¡bM0¸öÞƿ²iv­G(99\$å,¸K\"ÁÁõ#DÉeC7Ad\$¨yM½K¡¼<bÈÎ&µ¿jLr¶V¨Ȥ·Q0nA,6ãtI\rN¶åh6â\$níé|_²rQC'2U5ÉÅéRa,­¡ͫÆ.";break;case"vi":$f="%ÌÂ(ha­\rÆqÐÐá] á®ҌÓ]¡Îc\rTnAjÓ¢hc,\"	³b5HÅ؉q 	Nd)	R!/5Â!PäA&n®&°0cA¨Øn8QE\r ÆÃY\$±Eyt9D0°Q(£¤íVh<&b°-Ñ[¹ºnoâ\nÓ(©U`Ô+½~Âda¬®æH¾8iDåµ\\¤PnÐÌpâu<Ä4k{¸C3	2Rum£´Þ]/ãtUږ[­]á7;qöqwñN(¦a;m{\rB\n'îٻí_ÖH2[aTÜk7Îô)Èäo9HH¡Ä0c+Ô7£67 ê8Ä8@îüê@Ǣ¨» \\®ãj LÁ+@ÞƻÉl7)vOIvL®ãÂ:IÈ枧èÚfakÂÃjcÐ]/ÄP!\0ÎÌdè!ª K P k¼<ËM\0ÎÃ\rêà@Äh4 A³N!c3(7\$ÈXÐb,(4£ÊB]£\r>¼J NÃÆA1¨¡[¨(¡RÜA¯°åò,ÒôÍÅÑ\"OC¢òxÂ70ÌCÐò:¡@æLpÑ(`PH¡ g`´Xé\rn~Å/e,1¢LàaMÃ]èØðêPTêVÊè\$&ó¤á»c+4J<ü®K\"@	¢ht)`P¶<߃Ⱥ£hZ2P±u=L«.́IԺ:pHx0§Aðä2¨ä¤Ua\0Âû#Èx!òCâ¢N3\r#>42äy,t;QÓô4ÆÃÙaÏÀ@2ÏöwçØpÊ<Vpæ4ÄñØýÜÜR4CbÐ#{5ÀCxÌ3\rÐʐËe±e=Zî#£>¥.&j(¸蛢êʾ4ë¼ÍݍS46E`@rjôðR/3UA\reÔ72£\nCJˈvÙ8ΞX×Ùö í!È6ìC1é£HÈúÁèD4 à9Ax^;ùpÃ×ö!sð3ú\0^2Dïæ7á}êh#§YäÀP¡vPé@é;)ú[È0á~:pÜDPS\r4ñª^ÉGRå&´ßiÔA+;jã¾x	â<gÞSÌ\rÇÕç' ô;Iihì±SzJø¡\$¤Ùó¾ÎDRr,Eª§0ЅcCX½?Â`Ù U¨07¼Á\0w\r!°6¤«©A´2«Â rAA1ÀÌb(l\ré<¤D\"IÐ6»pÜ÷liÅԡ¤(eQr0FHÑ1¶øWÅ2N\"è[  'YFh±.CQA\$¡½%4Oáó¬!²08 ÒvYØg®M fpÐLiAÞ&ÖðÑo5ÕÒT'ɨsBU'àÑ+Ãpp¨=¡0ä¬CºìnÀ3¼7@-.,  Αh&¡) ÝKñH	5:*28¼Òk3H]\n°êéR	qª­tHj¡(¸\0PçvPÂìQ ·Ü9 ¢}Òîq	p&².1RpϺ!Dh\$1N\$ºE'0)\$!\$´z´ á¹Xé>ÅÍ:A!û'\\ì R!Í\0±E¤ 2Gé62Ra¡#à¼(ð¦ëpìMÀUÈâaIoYéöK	q\r\rì[Ðé<¡Xº²aÅÌ\n	]î9Q¦ÞWcs	þB_PB.á*Hà&ñΑc\nDë2ÔøÎp>¡;ÎlH%ÉÝ;&êQΪâtH\\+å!övC	ĪF	ÈpRZ\"-Y¯ ¿þZ'\"G Â#Â2Fí4Kϵ¶CQt,LjURE *@AŜH6fvSÔdL=]¶:6Py\nDÃ±L.Ƥs?ȩÒ2êÇd(VðE_\\ª	¾êÓ?4â.íM¶°Öà]³6Mͣa@İÍy:\n¬ÙÅ4`cJÉ!,Cº(\nAnFr¢7ÒÞÃP12I*«½!=%jÄۭ\\ÞR\"éI¬e²¯UڧèB+l[~H0I\"ëâ»ÌMfq3WÆa";break;case"zh":$f="%ÌÂ:\$\nr.®ör/d²Ȼ[8Ð S8r©NT*Ю\\9ÓHH¤Z1!S¹VøJè@%9£QÉl]m	F¹U©*qQ;CÈf4ãÌu¨s¨UÎUt w¯à§:¥t\nr£îU:.:²PǑ.\r7d^%äu)c©xU`æF«©j»árs'PnÊA̛ZEúfªº]£EúvitîUÊÙλSëծ{Íîû¤ÓPõg5ÿ	EÂPýNå1	VÚ\n¢èW«]\n!z¿s¥Ôâ©ΟRºR¿ÄV×I:(¯s#.UzΠ@Ò:w'_²T\$ùpV¸LùÌD')bJ¬\$ÒpŢ©ñÊ[MZó\n.T¨ñ>så±ÒKAZKåaLHAtF3ÙÊDË!zHµäâÐCé*reñÊ^K#´s¹ÎXg)<·6Òør.Ûÿ\$ç) F­«î@¬̚^®+â@ó2³G)v]ÏC£ A\rRxLëA SA b£¤8s*.]Ä\"h^§9zW#¤s\0]îyAÈ)ÊEìttIÌE+!¨ÀP\$Bh\nb-5èò.ÃhÚ TUUdâÃÎiÀ@6£è\npC(è:Cp@0ÛÃä9#Èx!ð\\Z¶¸7Ã0Ò3ۃ-Ït¦ã@4ÛÉÅÖsÓCµ|A--ïþ	apDJ©.ЁC`è9*ÊÁL]åÑ~rD3N(\$QC*JÓQ³Í3~R'AiÎé	4°ÆynÎљtKY G\$:Wâ¼HTdþZrålYODþ»Ђ2\r·àÊ9gÙÐ@gA2H¦²FØ]¨@4[#0z\r è8aÐ^ü\\0ìä\rãÎ£p^2^è4ÞxD1±X_,\$AÐE)iÒP§I:Q,\$Mî)4è4Цql¹öF³ZÉ֗l¼üBefNéÞåº»¶ñ½o÷ÁpݱñWÇ£ÀéƎ|§ZïÜÁ}^K'1fJ¤ðI\\AŁpr\$)ÊF؉~Bôri ÂNӮv¸ø#+E æb5S!QECOj-pÿq6.Ќ%Bª\nÓÒîûG@✺ÃcÄâªaÈ,·S<@\$\nÑø¡ΓRï\n	%\$âàÎ	áÌ#*3D¾\$9ÅX¯¢<Q³DD=&,«±&``Ùô>Çàä\$\n.ñf^6S;Åy£>Ax:8´Db¸t!1KR@X@R H	Ùûvé´GPI¨å­4l!^ \$DÇ0®«-¤òv~Ç(¢ʌP/\")æÂpÆ Zc¡p\"â9¯½&Ç䰀\$ÌøsíRȅh@Fé]9H\n<)F\nؤDÂW³æ¨#ɐYg¥6wí,¥£\"J©qÊ\$Hp»q\0C¢.xD¯â#@ CȈ &	8E0¢	é/5³2VNc±b(QÁ2)¹Æ(»¼.fD°°¤4#z`)á4Mä]Ӭ®Å1/±°,ňɄ/DxCbá°Ýɤ~!Ԁ|rî/ÄÜ%4¸÷ K°åoö\n ª0-	E/s	^\r¦19,V*N¥bn®¬q2PTáhC @CH\\D\n}J&Øë5ҰmKàébú#Zø%Íyª¢񈈡@:ìs3êu§9ihrXîÓ\$R<H9\"\0¹ñ'.Õo\"0´!Er@¤((Ñ.ƨY둂2¨¥(­*be¹fZMIà";break;case"zh-tw":$f="%ÌÂ:\$\ns¡.eUȸE9PK72©(æP¢h)ʅ@º:i	Æaè§Je åR)ܫ{º	Nd(ÜvQDCѮUjaʜTOABÀPÀb2£a¸àr\nr/TuéʮM9Rèçzñ?TאÈò9>åS¢ÁNeI̜Dºhw2Y2èPÒcº¡мWÜÒË*=sºÝï7»íýB¥9J¥Úñ\"X¹Qê÷2±æM­/«J2å@\"ïWËör¡TDÄ{u¼©ëãtsápøÎîÁÕãSÐô\\=\0çV¡«ôïp­\"RÕ )ЪOHêýΔT\\ӊ§:}JéF+üêJVÏ*rEZs!Z¥y®éV꽯yPê¤A.ÈyZë6YÌIÁ)\ns	ÎZÈæ̢ÊÊ[¹Ê2̒¥K®d¹J»ç12A\$±&¤ºY+;ZY+\$j[GAnæ%ò²J½sàt)ÒPÇ)<¹9,3r/Ê\\gA2³Á0YD¶äÉv«`\\É:Îä,òè±ÇIA?ep\0Ä<(P9+0æ0!pHÓjæHäreÙÌBòiÎ^ÑG1I@x¥ë<E¡Åé9[%Ä>C¡y|£¾\"@	¢ht)`P¶<ڃȺ\r£h\\2IïòýEa\0Ú:c @)Ð|9£ ê9\rÁ\0Ãz#ä0!à^0Ápv]ÂÞ7ÃHÏy·öMê`G9L@ÂXÌDÇ)*Oã)ÐAØÔ@Äq,NB)P©.ÛC`è9.lÙÒMNI4rD2rÒî¡Í(Dìrå֌]¿\"`Xĩ.ҡq©Ò@Ɂ4²DѰ©t\n1|F\$9t%32D%äVA£é,ÉOa:¬ËE@2\r¸Ê9kód@g1\nW&ÚH¶Ø\rÌC@è:tã¿D<9Ãxä3ã(Ü8Â:\r88^Ç1H4¥òÊD^ Kç<¡C+)ÊP¦2ckzÅ單GG,´­]<gåÄïA¯¯'¼*2òüÏ7ÎóýGÒð#wÔõ}o^2Üô°\"]Î b%^cÂä	QÐ+_q)í>QÊ#³Îzêô¼(\0¢ly2+E\"¥eR²Ûqíñ	q6È\$ØC	>&M­rñn`C!\$-¢\$HA3Â%T´®0´\$¨¤( Èâk)ߓ§øJ 4´Q«!JEç00ÓDQ@R¢[Qn	Ð+DI3f((¶Pb2Hp؎rÒ9\0¼k´ך1Ð ÅèæRäÌG¨Kðé§áÊ&ã¼J!Qê-\0FÐ7·\\O\\3Ü{Âd^ÒB`G(Çô[%æ°GÉ	#és\nájÔEÃi&°ìN`L\\¸AçHr¡@h¦CO	Ã4Å&3¦J!æþý\\u¢\0 ñÌ,DÚc¤KĂ\"G4(y¤N\"xS\nqñH*¦¹=ÂàM8üeó\$KXÇMپȢÙ\"¥|°͓9Í¯UÜ@	§® P%dHYÍtÄ\"Q	 àÎրЄãDqð6Øõ<6ÏTtB\$*xÄÉü0oTÔ£¡DêhÁB	ê±R§QÕõh¸1ôX>/ÌȌôG6fr:ÇPs	ò^lMóJ&\\rñ\\Ê,(bOU\nʃj0â½<6\neãī!­]o®RhV>6ÉAr­ÄÉC¨®'¨R³ðNbS)ËK0Ôå؉ĀäÂÖXú/Jxª­jk ҷùMfÎuAs\"\0É\\ȯëH#ÈÂÒ]Ð)£¤xü´fÓÁ)5\0\n±̩1æµ,Â\$M·kEÊ_L)";break;}$Dg=array();foreach(explode("\n",lzw_decompress($f))as$X)$Dg[]=(strpos($X,"\t")?explode("\t",$X):$X);return$Dg;}abstract
class
SqlDb{static$instance;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($M,$V,$D);abstract
function
quote($Q);abstract
function
select_db($tb);abstract
function
query($F,$Mg=false);function
multi_query($F){return$this->multi=$this->query($F);}function
store_result(){return$this->multi;}function
next_result(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($Jb,$V,$D,array$B=array()){$B[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$B[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($Jb,$V,$D,$B);}catch(\Exception$ac){return$ac->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($F,$Mg=false){$G=$this->pdo->query($F);$this->error="";if(!$G){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(21);return
false;}$this->store_result($G);return$G;}function
store_result($G=null){if(!$G){$G=$this->multi;if(!$G)return
false;}if($G->columnCount()){$G->num_rows=$G->rowCount();return$G;}$this->affected_rows=$G->rowCount();return
true;}function
next_result(){$G=$this->multi;if(!is_object($G))return
false;$G->_offset=0;return@$G->nextRowset();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($ee){$H=$this->fetch($ee);return($H?array_map(array($this,'unresource'),$H):$H);}private
function
unresource($X){return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){$I=(object)$this->getColumnMeta($this->_offset++);$U=$I->pdo_type;$I->type=($U==\PDO::PARAM_INT?0:15);$I->charsetnr=($U==\PDO::PARAM_LOB||(isset($I->flags)&&in_array("blob",(array)$I->flags))?63:0);return$I;}function
seek($re){for($r=0;$r<$re;$r++)$this->fetch();}}}function
add_driver($s,$_){SqlDriver::$drivers[$s]=$_;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;protected$conn;protected$types=array();var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();static
function
connect($M,$V,$D){$g=new
Db;return($g->attach($M,$V,$D)?:$g);}function
__construct(Db$g){$this->conn=$g;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$l){}function
unconvertFunction(array$l){}function
select($R,array$K,array$Z,array$Gc,array$Ce=array(),$x=1,$C=0,$hf=false){$td=(count($Gc)<count($K));$F=adminer()->selectQueryBuild($K,$Z,$Gc,$Ce,$x,$C);if(!$F)$F="SELECT".limit(($_GET["page"]!="last"&&$x&&$Gc&&$td&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$K)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($Gc&&$td?"\nGROUP BY ".implode(", ",$Gc):"").($Ce?"\nORDER BY ".implode(", ",$Ce):""),$x,($C?$x*$C:0),"\n");$bg=microtime(true);$H=$this->conn->query($F);if($hf)echo
adminer()->selectQuery($F,$bg,!$H);return$H;}function
delete($R,$nf,$x=0){$F="FROM ".table($R);return
queries("DELETE".($x?limit1($R,$F,$nf):" $F$nf"));}function
update($R,array$N,$nf,$x=0,$L="\n"){$bh=array();foreach($N
as$w=>$X)$bh[]="$w = $X";$F=table($R)." SET$L".implode(",$L",$bh);return
queries("UPDATE".($x?limit1($R,$F,$nf,$L):" $F$nf"));}function
insert($R,array$N){return
queries("INSERT INTO ".table($R).($N?" (".implode(", ",array_keys($N)).")\nVALUES (".implode(", ",$N).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$J,array$E){return
false;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($F,$tg){}function
convertSearch($t,array$X,array$l){return$t;}function
convertOperator($ze){return$ze;}function
value($X,array$l){return(method_exists($this->conn,'value')?$this->conn->value($X,$l):$X);}function
quoteBinary($Bf){return
q($Bf);}function
warnings(){}function
tableHelp($_,$wd=false){}function
inheritsFrom($R){return
array();}function
inheritedTables($R){return
array();}function
partitionsInfo($R){return
array();}function
hasCStyleEscapes(){return
false;}function
engines(){return
array();}function
supportsIndex(array$S){return!is_view($S);}function
indexAlgorithms(array$ig){return
array();}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R)."
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'",$this->conn);}function
allFields(){$H=array();if(DB!=""){foreach(get_rows("SELECT TABLE_NAME AS tab, COLUMN_NAME AS field, IS_NULLABLE AS nullable, DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS length".(JUSH=='sql'?", COLUMN_KEY = 'PRI' AS `primary`":"")."
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY TABLE_NAME, ORDINAL_POSITION",$this->conn)as$I){$I["null"]=($I["nullable"]=="YES");$H[$I["tab"]][]=$I;}}return$H;}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach($n,$V,$D){$this->link=new
\SQLite3($n);$dh=$this->link->version();$this->server_info=$dh["versionString"];return'';}function
query($F,$Mg=false){$G=@$this->link->query($F);$this->error="";if(!$G){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($G->numColumns())return
new
Result($G);$this->affected_rows=$this->link->changes();return
true;}function
quote($Q){return(is_utf8($Q)?"'".$this->link->escapeString($Q)."'":"x'".first(unpack('H*',$Q))."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($G){$this->result=$G;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$d=$this->offset++;$U=$this->result->columnType($d);return(object)array("name"=>$this->result->columnName($d),"type"=>($U==SQLITE3_TEXT?15:0),"charsetnr"=>($U==SQLITE3_BLOB?63:0),);}function
__destruct(){$this->result->finalize();}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach($n,$V,$D){$this->dsn(DRIVER.":$n","","");$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{function
attach($n,$V,$D){parent::attach($n,$V,$D);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($n){if(is_readable($n)&&$this->query("ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$n)?$n:dirname($_SERVER["SCRIPT_FILENAME"])."/$n")." AS a"))return!self::attach($n,'','');return
false;}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($M,$V,$D){if($D!="")return
lang(22);return
parent::connect(":memory:","","");}function
__construct(Db$g){parent::__construct($g);if(min_version(3.31,0,$g))$this->generated=array("STORED","VIRTUAL");}function
structuredTypes(){return
array_keys($this->types[0]);}function
insertUpdate($R,array$J,array$E){$bh=array();foreach($J
as$N)$bh[]="(".implode(", ",$N).")";return
queries("REPLACE INTO ".table($R)." (".implode(", ",array_keys(reset($J))).") VALUES\n".implode(",\n",$bh));}function
tableHelp($_,$wd=false){if($_=="sqlite_sequence")return"fileformat2.html#seqtab";if($_=="sqlite_master")return"fileformat2.html#$_";}function
checkConstraints($R){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R),0,$this->conn),$Td);return
array_combine($Td[2],$Td[2]);}function
allFields(){$H=array();foreach(tables_list()as$R=>$U){foreach(fields($R)as$l)$H[$R][]=$l;}return$H;}}function
idf_escape($t){return'"'.str_replace('"','""',$t).'"';}function
table($t){return
idf_escape($t);}function
get_databases($uc){return
array();}function
limit($F,$Z,$x,$re=0,$L=" "){return" $F$Z".($x?$L."LIMIT $x".($re?" OFFSET $re":""):"");}function
limit1($R,$F,$Z,$L="\n"){return(preg_match('~^INTO~',$F)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($F,$Z,1,0,$L):" $F WHERE rowid = (SELECT rowid FROM ".table($R).$Z.$L."LIMIT 1)");}function
db_collation($i,$Wa){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name = 'sqlite_sequence'), name");}function
count_tables($ub){return
array();}function
table_status($_=""){$H=array();foreach(get_rows("SELECT name AS Name, type AS Engine, 'rowid' AS Oid, '' AS Auto_increment FROM sqlite_master WHERE type IN ('table', 'view') ".($_!=""?"AND name = ".q($_):"ORDER BY name"))as$I){$I["Rows"]=get_val("SELECT COUNT(*) FROM ".idf_escape($I["Name"]));$H[$I["Name"]]=$I;}foreach(get_rows("SELECT * FROM sqlite_sequence".($_!=""?" WHERE name = ".q($_):""),null,"")as$I)$H[$I["name"]]["Auto_increment"]=$I["seq"];return$H;}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
fields($R){$H=array();$E="";foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($R).")")as$I){$_=$I["name"];$U=strtolower($I["type"]);$j=$I["dflt_value"];$H[$_]=array("field"=>$_,"type"=>(preg_match('~int~i',$U)?"integer":(preg_match('~char|clob|text~i',$U)?"text":(preg_match('~blob~i',$U)?"blob":(preg_match('~real|floa|doub~i',$U)?"real":"numeric")))),"full_type"=>$U,"default"=>(preg_match("~^'(.*)'$~",$j,$z)?str_replace("''","'",$z[1]):($j=="NULL"?null:$j)),"null"=>!$I["notnull"],"privileges"=>array("select"=>1,"insert"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$I["pk"],);if($I["pk"]){if($E!="")$H[$E]["auto_increment"]=false;elseif(preg_match('~^integer$~i',$U))$H[$_]["auto_increment"]=true;$E=$_;}}$Yf=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R));$t='(("[^"]*+")+|[a-z0-9_]+)';preg_match_all('~'.$t.'\s+text\s+COLLATE\s+(\'[^\']+\'|\S+)~i',$Yf,$Td,PREG_SET_ORDER);foreach($Td
as$z){$_=str_replace('""','"',preg_replace('~^"|"$~','',$z[1]));if($H[$_])$H[$_]["collation"]=trim($z[3],"'");}preg_match_all('~'.$t.'\s.*GENERATED ALWAYS AS \((.+)\) (STORED|VIRTUAL)~i',$Yf,$Td,PREG_SET_ORDER);foreach($Td
as$z){$_=str_replace('""','"',preg_replace('~^"|"$~','',$z[1]));$H[$_]["default"]=$z[3];$H[$_]["generated"]=strtoupper($z[4]);}return$H;}function
indexes($R,$h=null){$h=connection($h);$H=array();$Yf=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R),0,$h);if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Yf,$z)){$H[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$z[1],$Td,PREG_SET_ORDER);foreach($Td
as$z){$H[""]["columns"][]=idf_unescape($z[2]).$z[4];$H[""]["descs"][]=(preg_match('~DESC~i',$z[5])?'1':null);}}if(!$H){foreach(fields($R)as$_=>$l){if($l["primary"])$H[""]=array("type"=>"PRIMARY","columns"=>array($_),"lengths"=>array(),"descs"=>array(null));}}$ag=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($R),$h);foreach(get_rows("PRAGMA index_list(".table($R).")",$h)as$I){$_=$I["name"];$u=array("type"=>($I["unique"]?"UNIQUE":"INDEX"));$u["lengths"]=array();$u["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($_).")",$h)as$Af){$u["columns"][]=$Af["name"];$u["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($_).' ON '.idf_escape($R),'~').' \((.*)\)$~i',$ag[$_],$tf)){preg_match_all('/("[^"]*+")+( DESC)?/',$tf[2],$Td);foreach($Td[2]as$w=>$X){if($X)$u["descs"][$w]='1';}}if(!$H[""]||$u["type"]!="UNIQUE"||$u["columns"]!=$H[""]["columns"]||$u["descs"]!=$H[""]["descs"]||!preg_match("~^sqlite_~",$_))$H[$_]=$u;}return$H;}function
foreign_keys($R){$H=array();foreach(get_rows("PRAGMA foreign_key_list(".table($R).")")as$I){$o=&$H[$I["id"]];if(!$o)$o=$I;$o["source"][]=$I["from"];$o["target"][]=$I["to"];}return$H;}function
view($_){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($_))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($i){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($_){$fc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($fc)\$~",$_)){connection()->error=lang(23,str_replace("|",", ",$fc));return
false;}return
true;}function
create_database($i,$c){if(file_exists($i)){connection()->error=lang(24);return
false;}if(!check_sqlite_name($i))return
false;try{$y=new
Db();$y->attach($i,'','');}catch(\Exception$ac){connection()->error=$ac->getMessage();return
false;}$y->query('PRAGMA encoding = "UTF-8"');$y->query('CREATE TABLE adminer (i)');$y->query('DROP TABLE adminer');return
true;}function
drop_databases($ub){connection()->attach(":memory:",'','');foreach($ub
as$i){if(!@unlink($i)){connection()->error=lang(24);return
false;}}return
true;}function
rename_database($_,$c){if(!check_sqlite_name($_))return
false;connection()->attach(":memory:",'','');connection()->error=lang(24);return@rename(DB,$_);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($R,$_,$m,$wc,$ab,$Sb,$c,$ta,$Se){$Xg=($R==""||$wc);foreach($m
as$l){if($l[0]!=""||!$l[1]||$l[2]){$Xg=true;break;}}$b=array();$Ie=array();foreach($m
as$l){if($l[1]){$b[]=($Xg?$l[1]:"ADD ".implode($l[1]));if($l[0]!="")$Ie[$l[0]]=$l[1][0];}}if(!$Xg){foreach($b
as$X){if(!queries("ALTER TABLE ".table($R)." $X"))return
false;}if($R!=$_&&!queries("ALTER TABLE ".table($R)." RENAME TO ".table($_)))return
false;}elseif(!recreate_table($R,$_,$b,$Ie,$wc,$ta))return
false;if($ta){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $ta WHERE name = ".q($_));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($_).", $ta)");queries("COMMIT");}return
true;}function
recreate_table($R,$_,array$m,array$Ie,array$wc,$ta="",$v=array(),$Hb="",$ea=""){if($R!=""){if(!$m){foreach(fields($R)as$w=>$l){if($v)$l["auto_increment"]=0;$m[]=process_field($l,$l);$Ie[$w]=idf_escape($w);}}$gf=false;foreach($m
as$l){if($l[6])$gf=true;}$Ib=array();foreach($v
as$w=>$X){if($X[2]=="DROP"){$Ib[$X[1]]=true;unset($v[$w]);}}foreach(indexes($R)as$zd=>$u){$e=array();foreach($u["columns"]as$w=>$d){if(!$Ie[$d])continue
2;$e[]=$Ie[$d].($u["descs"][$w]?" DESC":"");}if(!$Ib[$zd]){if($u["type"]!="PRIMARY"||!$gf)$v[]=array($u["type"],$zd,$e);}}foreach($v
as$w=>$X){if($X[0]=="PRIMARY"){unset($v[$w]);$wc[]="  PRIMARY KEY (".implode(", ",$X[2]).")";}}foreach(foreign_keys($R)as$zd=>$o){foreach($o["source"]as$w=>$d){if(!$Ie[$d])continue
2;$o["source"][$w]=idf_unescape($Ie[$d]);}if(!isset($wc[" $zd"]))$wc[]=" ".format_foreign_key($o);}queries("BEGIN");}$Ka=array();foreach($m
as$l){if(preg_match('~GENERATED~',$l[3]))unset($Ie[array_search($l[0],$Ie)]);$Ka[]="  ".implode($l);}$Ka=array_merge($Ka,array_filter($wc));foreach(driver()->checkConstraints($R)as$Ma){if($Ma!=$Hb)$Ka[]="  CHECK ($Ma)";}if($ea)$Ka[]="  CHECK ($ea)";$ng=($R==$_?"adminer_$_":$_);if(!queries("CREATE TABLE ".table($ng)." (\n".implode(",\n",$Ka)."\n)"))return
false;if($R!=""){if($Ie&&!queries("INSERT INTO ".table($ng)." (".implode(", ",$Ie).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($Ie)))." FROM ".table($R)))return
false;$Jg=array();foreach(triggers($R)as$Hg=>$ug){$Gg=trigger($Hg,$R);$Jg[]="CREATE TRIGGER ".idf_escape($Hg)." ".implode(" ",$ug)." ON ".table($_)."\n$Gg[Statement]";}$ta=$ta?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($R));if(!queries("DROP TABLE ".table($R))||($R==$_&&!queries("ALTER TABLE ".table($ng)." RENAME TO ".table($_)))||!alter_indexes($_,$v))return
false;if($ta)queries("UPDATE sqlite_sequence SET seq = $ta WHERE name = ".q($_));foreach($Jg
as$Gg){if(!queries($Gg))return
false;}queries("COMMIT");}return
true;}function
index_sql($R,$U,$_,$e){return"CREATE $U ".($U!="INDEX"?"INDEX ":"").idf_escape($_!=""?$_:uniqid($R."_"))." ON ".table($R)." $e";}function
alter_indexes($R,$b){foreach($b
as$E){if($E[0]=="PRIMARY")return
recreate_table($R,$R,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$X){if(!queries($X[2]=="DROP"?"DROP INDEX ".idf_escape($X[1]):index_sql($R,$X[0],$X[1],"(".implode(", ",$X[2]).")")))return
false;}return
true;}function
truncate_tables($T){return
apply_queries("DELETE FROM",$T);}function
drop_views($fh){return
apply_queries("DROP VIEW",$fh);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
move_tables($T,$fh,$mg){return
false;}function
trigger($_,$R){if($_=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$t='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$Ig=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$t\\s*(".implode("|",$Ig["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($t))?\\s+ON\\s*$t\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($_)),$z);$qe=$z[3];return
array("Timing"=>strtoupper($z[1]),"Event"=>strtoupper($z[2]).($qe?" OF":""),"Of"=>idf_unescape($qe),"Trigger"=>$_,"Statement"=>$z[4],);}function
triggers($R){$H=array();$Ig=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R))as$I){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$Ig["Timing"]).')\s*(.*?)\s+ON\b~i',$I["sql"],$z);$H[$I["name"]]=array($z[1],$z[2]);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
begin(){return
queries("BEGIN");}function
last_id($G){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain($g,$F){return$g->query("EXPLAIN QUERY PLAN $F");}function
found_rows($S,$Z){}function
types(){return
array();}function
create_sql($R,$ta,$dg){$H=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($R));foreach(indexes($R)as$_=>$u){if($_=='')continue;$H
.=";\n\n".index_sql($R,$u['type'],$_,"(".implode(", ",array_map('Adminer\idf_escape',$u['columns'])).")");}return$H;}function
truncate_sql($R){return"DELETE FROM ".table($R);}function
use_sql($tb){}function
trigger_sql($R){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R)));}function
show_variables(){$H=array();foreach(get_rows("PRAGMA pragma_list")as$I){$_=$I["name"];if($_!="pragma_list"&&$_!="compile_options"){$H[$_]=array($_,'');foreach(get_rows("PRAGMA $_")as$I)$H[$_][1].=implode(", ",$I)."\n";}}return$H;}function
show_status(){$H=array();foreach(get_vals("PRAGMA compile_options")as$Ae)$H[]=explode("=",$Ae,2)+array('','');return$H;}function
convert_field($l){}function
unconvert_field($l,$H){return$H;}function
support($jc){return
preg_match('~^(check|columns|database|drop_col|dump|indexes|descidx|move_col|sql|status|table|trigger|variables|view|view_trigger)$~',$jc);}}add_driver("pgsql","PostgreSQL");if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
PgsqlDb
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string,$database=true;function
_error($Xb,$k){if(ini_bool("html_errors"))$k=html_entity_decode(strip_tags($k));$k=preg_replace('~^[^:]*: ~','',$k);$this->error=$k;}function
attach($M,$V,$D){$i=adminer()->database();set_error_handler(array($this,'_error'));$this->string="host='".str_replace(":","' port='",addcslashes($M,"'\\"))."' user='".addcslashes($V,"'\\")."' password='".addcslashes($D,"'\\")."'";$O=adminer()->connectSsl();if(isset($O["mode"]))$this->string
.=" sslmode='".$O["mode"]."'";$this->link=@pg_connect("$this->string dbname='".($i!=""?addcslashes($i,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$i!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($Q){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$Q):"'".pg_escape_string($this->link,$Q)."'");}function
value($X,array$l){return($l["type"]=="bytea"&&$X!==null?pg_unescape_bytea($X):$X);}function
select_db($tb){if($tb==adminer()->database())return$this->database;$H=@pg_connect("$this->string dbname='".addcslashes($tb,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($H)$this->link=$H;return$H;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($F,$Mg=false){$G=@pg_query($this->link,$F);$this->error="";if(!$G){$this->error=pg_last_error($this->link);$H=false;}elseif(!pg_num_fields($G)){$this->affected_rows=pg_affected_rows($G);$H=true;}else$H=new
Result($G);if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$H;}function
warnings(){return
h(pg_last_notice($this->link));}function
copyFrom($R,array$J){$this->error='';set_error_handler(function($Xb,$k){$this->error=(ini_bool('html_errors')?html_entity_decode($k):$k);return
true;});$H=pg_copy_from($this->link,$R,$J);restore_error_handler();return$H;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($G){$this->result=$G;$this->num_rows=pg_num_rows($G);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$H=new
\stdClass;$H->orgtable=pg_field_table($this->result,$d);$H->name=pg_field_name($this->result,$d);$U=pg_field_type($this->result,$d);$H->type=(preg_match(number_type(),$U)?0:15);$H->charsetnr=($U=="bytea"?63:0);return$H;}function
__destruct(){pg_free_result($this->result);}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach($M,$V,$D){$i=adminer()->database();$Jb="pgsql:host='".str_replace(":","' port='",addcslashes($M,"'\\"))."' client_encoding=utf8 dbname='".($i!=""?addcslashes($i,"'\\"):"postgres")."'";$O=adminer()->connectSsl();if(isset($O["mode"]))$Jb
.=" sslmode='".$O["mode"]."'";return$this->dsn($Jb,$V,$D);}function
select_db($tb){return(adminer()->database()==$tb);}function
query($F,$Mg=false){$H=parent::query($F,$Mg);if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$H;}function
warnings(){}function
copyFrom($R,array$J){$H=$this->pdo->pgsqlCopyFromArray($R,$J);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$H;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($F){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$F),$z)){$J=explode("\n",$z[2]);$this->affected_rows=count($J);return$this->copyFrom($z[1],$J);}return
parent::multi_query($F);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";var$operators=array("=","<",">","<=",">=","!=","~","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL");var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";static
function
connect($M,$V,$D){$g=parent::connect($M,$V,$D);if(is_string($g))return$g;$dh=get_val("SELECT version()",0,$g);$g->flavor=(preg_match('~CockroachDB~',$dh)?'cockroach':'');$g->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$dh);if(min_version(9,0,$g))$g->query("SET application_name = 'Adminer'");if($g->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$g;}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(25)=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),lang(26)=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),lang(27)=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),lang(28)=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),lang(29)=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),lang(30)=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$g)){$this->types[lang(27)]["json"]=4294967295;if(min_version(9.4,0,$g))$this->types[lang(27)]["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$g))$this->generated=array("STORED");$this->partitionBy=array("RANGE","LIST");if(!$g->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$l){$Tb=$this->types[lang(31)][$l["type"]];return($Tb?type_values($Tb):"");}function
setUserTypes($Lg){$this->types[lang(31)]=array_flip($Lg);}function
insertReturning($R){$ta=array_filter(fields($R),function($l){return$l['auto_increment'];});return(count($ta)==1?" RETURNING ".idf_escape(key($ta)):"");}function
insertUpdate($R,array$J,array$E){foreach($J
as$N){$Ug=array();$Z=array();foreach($N
as$w=>$X){$Ug[]="$w = $X";if(isset($E[idf_unescape($w)]))$Z[]="$w = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$Ug)." WHERE ".implode(" AND ",$Z))&&connection()->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($N)).") VALUES (".implode(", ",$N).")")))return
false;}return
true;}function
slowQuery($F,$tg){$this->conn->query("SET statement_timeout = ".(1000*$tg));$this->conn->timeout=1000*$tg;return$F;}function
convertSearch($t,array$X,array$l){$pg="char|text";if(strpos($X["op"],"LIKE")===false)$pg
.="|date|time(stamp)?|boolean|uuid|inet|cidr|macaddr|".number_type();return(preg_match("~$pg~",$l["type"])?$t:"CAST($t AS text)");}function
quoteBinary($Bf){return"'\\x".bin2hex($Bf)."'";}function
warnings(){return$this->conn->warnings();}function
tableHelp($_,$wd=false){$Ld=array("information_schema"=>"infoschema","pg_catalog"=>($wd?"view":"catalog"),);$y=$Ld[$_GET["ns"]];if($y)return"$y-".str_replace("_","-",$_).".html";}function
inheritsFrom($R){return
get_vals("SELECT relname FROM pg_class JOIN pg_inherits ON inhparent = oid WHERE inhrelid = ".$this->tableOid($R)." ORDER BY 1");}function
inheritedTables($R){return
get_vals("SELECT relname FROM pg_inherits JOIN pg_class ON inhrelid = oid WHERE inhparent = ".$this->tableOid($R)." ORDER BY 1");}function
partitionsInfo($R){$I=connection()->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".driver()->tableOid($R))->fetch_assoc();if($I){$ra=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $I[partrelid] AND attnum IN (".str_replace(" ",", ",$I["partattrs"]).")");$Ha=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$Ha[$I["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$ra)),);}return
array();}function
tableOid($R){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($R)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
indexAlgorithms(array$ig){static$H=array();if(!$H)$H=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = 'btree' DESC, amname");return$H;}function
supportsIndex(array$S){return$S["Engine"]!="view";}function
hasCStyleEscapes(){static$Ja;if($Ja===null)$Ja=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$Ja;}}function
idf_escape($t){return'"'.str_replace('"','""',$t).'"';}function
table($t){return
idf_escape($t);}function
get_databases($uc){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($F,$Z,$x,$re=0,$L=" "){return" $F$Z".($x?$L."LIMIT $x".($re?" OFFSET $re":""):"");}function
limit1($R,$F,$Z,$L="\n"){return(preg_match('~^INTO~',$F)?limit($F,$Z,1,0,$L):" $F".(is_view(table_status1($R))?$Z:$L."WHERE ctid = (SELECT ctid FROM ".table($R).$Z.$L."LIMIT 1)"));}function
db_collation($i,$Wa){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($i));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$F="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$F
.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$F
.="
ORDER BY 1";return
get_key_vals($F);}function
count_tables($ub){$H=array();foreach($ub
as$i){if(connection()->select_db($i))$H[$i]=count(tables_list());}return$H;}function
table_status($_=""){static$Pc;if($Pc===null)$Pc=get_val("SELECT 'pg_table_size'::regproc");$H=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($Pc?",
	pg_table_size(oid) AS \"Data_length\",
	pg_indexes_size(oid) AS \"Index_length\"":"").",
	obj_description(oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples as \"Rows\",
	inhparent AS inherited,
	current_schema() AS nspname
FROM pg_class
LEFT JOIN pg_inherits ON inhrelid = oid
WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($_!=""?"AND relname = ".q($_):"ORDER BY relname"))as$I)$H[$I["Name"]]=$I;return$H;}function
is_view($S){return
in_array($S["Engine"],array("view","materialized view"));}function
fk_support($S){return
true;}function
fields($R){$H=array();$ka=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz',);foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	col_description(a.attrelid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_attribute a
LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
WHERE a.attrelid = ".driver()->tableOid($R)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$I){preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$I["full_type"],$z);list(,$U,$Id,$I["length"],$fa,$na)=$z;$I["length"].=$na;$Na=$U.$fa;if(isset($ka[$Na])){$I["type"]=$ka[$Na];$I["full_type"]=$I["type"].$Id.$na;}else{$I["type"]=$U;$I["full_type"]=$I["type"].$Id.$fa.$na;}if(in_array($I['attidentity'],array('a','d')))$I['default']='GENERATED '.($I['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$I["generated"]=($I["attgenerated"]=="s"?"STORED":"");$I["null"]=!$I["attnotnull"];$I["auto_increment"]=$I['attidentity']||preg_match('~^nextval\(~i',$I["default"])||preg_match('~^unique_rowid\(~',$I["default"]);$I["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(preg_match('~(.+)::[^,)]+(.*)~',$I["default"],$z))$I["default"]=($z[1]=="NULL"?null:idf_unescape($z[1]).$z[2]);$H[$I["field"]]=$I;}return$H;}function
indexes($R,$h=null){$h=connection($h);$H=array();$lg=driver()->tableOid($R);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $lg AND attnum > 0",$h);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, (indpred IS NOT NULL)::int as indispartial, pg_am.amname as algorithm, pg_get_expr(pg_index.indpred, pg_index.indrelid, true) AS partial
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $lg
ORDER BY indisprimary DESC, indisunique DESC",$h)as$I){$uf=$I["relname"];$H[$uf]["type"]=($I["indispartial"]?"INDEX":($I["indisprimary"]?"PRIMARY":($I["indisunique"]?"UNIQUE":"INDEX")));$H[$uf]["columns"]=array();$H[$uf]["descs"]=array();$H[$uf]["algorithm"]=$I["algorithm"];$H[$uf]["partial"]=$I["partial"];if($I["indkey"]){foreach(explode(" ",$I["indkey"])as$hd)$H[$uf]["columns"][]=$e[$hd];foreach(explode(" ",$I["indoption"])as$id)$H[$uf]["descs"][]=(intval($id)&1?'1':null);}$H[$uf]["lengths"]=array();}return$H;}function
foreign_keys($R){$H=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($R)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$I){if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$I['definition'],$z)){$I['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$z[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$z[2],$Rd)){$I['ns']=idf_unescape($Rd[2]);$I['table']=idf_unescape($Rd[4]);}$I['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$z[3])));$I['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$z[4],$Rd)?$Rd[1]:'NO ACTION');$I['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$z[4],$Rd)?$Rd[1]:'NO ACTION');$H[$I['conname']]=$I;}}return$H;}function
view($_){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($_).")")));}function
collations(){return
array();}function
information_schema($i){return
get_schema()=="information_schema";}function
error(){$H=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$H,$z))$H=$z[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($z[3]).'})(.*)~','\1<b>\2</b>',$z[2]).$z[4];return
nl_br($H);}function
create_database($i,$c){return
queries("CREATE DATABASE ".idf_escape($i).($c?" ENCODING ".idf_escape($c):""));}function
drop_databases($ub){connection()->close();return
apply_queries("DROP DATABASE",$ub,'Adminer\idf_escape');}function
rename_database($_,$c){connection()->close();return
queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($_));}function
auto_increment(){return"";}function
alter_table($R,$_,$m,$wc,$ab,$Sb,$c,$ta,$Se){$b=array();$mf=array();if($R!=""&&$R!=$_)$mf[]="ALTER TABLE ".table($R)." RENAME TO ".table($_);$Kf="";foreach($m
as$l){$d=idf_escape($l[0]);$X=$l[1];if(!$X)$b[]="DROP $d";else{$ah=$X[5];unset($X[5]);if($l[0]==""){if(isset($X[6]))$X[1]=($X[1]==" bigint"?" big":($X[1]==" smallint"?" small":" "))."serial";$b[]=($R!=""?"ADD ":"  ").implode($X);if(isset($X[6]))$b[]=($R!=""?"ADD":" ")." PRIMARY KEY ($X[0])";}else{if($d!=$X[0])$mf[]="ALTER TABLE ".table($_)." RENAME $d TO $X[0]";$b[]="ALTER $d TYPE$X[1]";$Lf=$R."_".idf_unescape($X[0])."_seq";$b[]="ALTER $d ".($X[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) STORED~','EXPRESSION\1',$X[3]):(isset($X[6])?"SET DEFAULT nextval(".q($Lf).")":"DROP DEFAULT"));if(isset($X[6]))$Kf="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($Lf)." OWNED BY ".idf_escape($R).".$X[0]";$b[]="ALTER $d ".($X[2]==" NULL"?"DROP NOT":"SET").$X[2];}if($l[0]!=""||$ah!="")$mf[]="COMMENT ON COLUMN ".table($_).".$X[0] IS ".($ah!=""?substr($ah,9):"''");}}$b=array_merge($b,$wc);if($R==""){$P="";if($Se){$Sa=(connection()->flavor=='cockroach');$P=" PARTITION BY $Se[partition_by]($Se[partition])";if($Se["partition_by"]=='HASH'){$Te=+$Se["partitions"];for($r=0;$r<$Te;$r++)$mf[]="CREATE TABLE ".idf_escape($_."_$r")." PARTITION OF ".idf_escape($_)." FOR VALUES WITH (MODULUS $Te, REMAINDER $r)";}else{$ff="MINVALUE";foreach($Se["partition_names"]as$r=>$X){$Y=$Se["partition_values"][$r];$Qe=" VALUES ".($Se["partition_by"]=='LIST'?"IN ($Y)":"FROM ($ff) TO ($Y)");if($Sa)$P
.=($r?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$X)?$X:idf_escape($X))."$Qe";else$mf[]="CREATE TABLE ".idf_escape($_."_$X")." PARTITION OF ".idf_escape($_)." FOR$Qe";$ff=$Y;}$P
.=($Sa?"\n)":"");}}array_unshift($mf,"CREATE TABLE ".table($_)." (\n".implode(",\n",$b)."\n)$P");}elseif($b)array_unshift($mf,"ALTER TABLE ".table($R)."\n".implode(",\n",$b));if($Kf)array_unshift($mf,$Kf);if($ab!==null)$mf[]="COMMENT ON TABLE ".table($_)." IS ".q($ab);foreach($mf
as$F){if(!queries($F))return
false;}return
true;}function
alter_indexes($R,$b){$lb=array();$Gb=array();$mf=array();foreach($b
as$X){if($X[0]!="INDEX")$lb[]=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");elseif($X[2]=="DROP")$Gb[]=idf_escape($X[1]);else$mf[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R).($X[3]?" USING $X[3]":"")." (".implode(", ",$X[2]).")".($X[4]?" WHERE $X[4]":"");}if($lb)array_unshift($mf,"ALTER TABLE ".table($R).implode(",",$lb));if($Gb)array_unshift($mf,"DROP INDEX ".implode(", ",$Gb));foreach($mf
as$F){if(!queries($F))return
false;}return
true;}function
truncate_tables($T){return
queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$T)));}function
drop_views($fh){return
drop_tables($fh);}function
drop_tables($T){foreach($T
as$R){$P=table_status1($R);if(!queries("DROP ".strtoupper($P["Engine"])." ".table($R)))return
false;}return
true;}function
move_tables($T,$fh,$mg){foreach(array_merge($T,$fh)as$R){$P=table_status1($R);if(!queries("ALTER ".strtoupper($P["Engine"])." ".table($R)." SET SCHEMA ".idf_escape($mg)))return
false;}return
true;}function
trigger($_,$R){if($_=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($R)." AND trigger_name = ".q($_);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$I)$e[]=$I["event_object_column"];$H=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$I){if($e&&$I["Event"]=="UPDATE")$I["Event"].=" OF";$I["Of"]=implode(", ",$e);if($H)$I["Event"].=" OR $H[Event]";$H=$I;}return$H;}function
triggers($R){$H=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($R))as$I){$Gg=trigger($I["trigger_name"],$R);$H[$Gg["Trigger"]]=array($Gg["Timing"],$Gg["Event"]);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF"),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($_,$U){$J=get_rows('SELECT routine_definition AS definition, LOWER(external_language) AS language, *
FROM information_schema.routines
WHERE routine_schema = current_schema() AND specific_name = '.q($_));$H=idx($J,0,array());$H["returns"]=array("type"=>$H["type_udt_name"]);$H["fields"]=get_rows('SELECT parameter_name AS field, data_type AS type, character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = '.q($_).'
ORDER BY ordinal_position');return$H;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()
ORDER BY SPECIFIC_NAME');}function
routine_languages(){return
get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language");}function
routine_id($_,$I){$H=array();foreach($I["fields"]as$l){$Id=$l["length"];$H[]=$l["type"].($Id?"($Id)":"");}return
idf_escape($_)."(".implode(", ",$H).")";}function
last_id($G){$I=(is_object($G)?$G->fetch_row():array());return($I?$I[0]:0);}function
explain($g,$F){return$g->query("EXPLAIN $F");}function
found_rows($S,$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($S["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$tf))return$tf[1];}function
types(){return
get_key_vals("SELECT oid, typname
FROM pg_type
WHERE typnamespace = ".driver()->nsOid."
AND typtype IN ('b','d','e')
AND typelem = 0");}function
type_values($s){$Vb=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $s ORDER BY enumsortorder");return($Vb?"'".implode("', '",array_map('addslashes',$Vb))."'":"");}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return
get_val("SELECT current_schema()");}function
set_schema($Cf,$h=null){if(!$h)$h=connection();$H=$h->query("SET search_path TO ".idf_escape($Cf));driver()->setUserTypes(types());return$H;}function
foreign_keys_sql($R){$H="";$P=table_status1($R);$sc=foreign_keys($R);ksort($sc);foreach($sc
as$rc=>$qc)$H
.="ALTER TABLE ONLY ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." ADD CONSTRAINT ".idf_escape($rc)." $qc[definition] ".($qc['deferrable']?'DEFERRABLE':'NOT DEFERRABLE').";\n";return($H?"$H\n":$H);}function
create_sql($R,$ta,$dg){$zf=array();$Mf=array();$P=table_status1($R);if(is_view($P)){$eh=view($R);return
rtrim("CREATE VIEW ".idf_escape($R)." AS $eh[select]",";");}$m=fields($R);if(count($P)<2||empty($m))return
false;$H="CREATE TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." (\n    ";foreach($m
as$l){$Pe=idf_escape($l['field']).' '.$l['full_type'].default_value($l).($l['null']?"":" NOT NULL");$zf[]=$Pe;if(preg_match('~nextval\(\'([^\']+)\'\)~',$l['default'],$Td)){$Lf=$Td[1];$Xf=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($Lf)):"SELECT * FROM $Lf"),null,"-- "));$Mf[]=($dg=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $Lf;\n":"")."CREATE SEQUENCE $Lf INCREMENT $Xf[increment_by] MINVALUE $Xf[min_value] MAXVALUE $Xf[max_value]".($ta&&$Xf['last_value']?" START ".($Xf["last_value"]+1):"")." CACHE $Xf[cache_value];";}}if(!empty($Mf))$H=implode("\n\n",$Mf)."\n\n$H";$E="";foreach(indexes($R)as$fd=>$u){if($u['type']=='PRIMARY'){$E=$fd;$zf[]="CONSTRAINT ".idf_escape($fd)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$u['columns'])).")";}}foreach(driver()->checkConstraints($R)as$eb=>$gb)$zf[]="CONSTRAINT ".idf_escape($eb)." CHECK $gb";$H
.=implode(",\n    ",$zf)."\n)";$Qe=driver()->partitionsInfo($P['Name']);if($Qe)$H
.="\nPARTITION BY $Qe[partition_by]($Qe[partition])";$H
.="\nWITH (oids = ".($P['Oid']?'true':'false').");";if($P['Comment'])$H
.="\n\nCOMMENT ON TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." IS ".q($P['Comment']).";";foreach($m
as$kc=>$l){if($l['comment'])$H
.="\n\nCOMMENT ON COLUMN ".idf_escape($P['nspname']).".".idf_escape($P['Name']).".".idf_escape($kc)." IS ".q($l['comment']).";";}foreach(get_rows("SELECT indexdef FROM pg_catalog.pg_indexes WHERE schemaname = current_schema() AND tablename = ".q($R).($E?" AND indexname != ".q($E):""),null,"-- ")as$I)$H
.="\n\n$I[indexdef];";return
rtrim($H,';');}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
trigger_sql($R){$P=table_status1($R);$H="";foreach(triggers($R)as$Fg=>$Eg){$Gg=trigger($Fg,$P['Name']);$H
.="\nCREATE TRIGGER ".idf_escape($Gg['Trigger'])." $Gg[Timing] $Gg[Event] ON ".idf_escape($P["nspname"]).".".idf_escape($P['Name'])." $Gg[Type] $Gg[Statement];;\n";}return$H;}function
use_sql($tb){return"\connect ".idf_escape($tb);}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field($l){}function
unconvert_field($l,$H){return$H;}function
support($jc){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$jc);}function
kill_process($X){return
queries("SELECT pg_terminate_backend(".number($X).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("oracle","Oracle (beta)");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="oci8";var$_current_db;private$link;function
_error($Xb,$k){if(ini_bool("html_errors"))$k=html_entity_decode(strip_tags($k));$k=preg_replace('~^[^:]*: ~','',$k);$this->error=$k;}function
attach($M,$V,$D){$this->link=@oci_new_connect($V,$D,$M,"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$k=oci_error();return$k["message"];}function
quote($Q){return"'".str_replace("'","''",$Q)."'";}function
select_db($tb){$this->_current_db=$tb;return
true;}function
query($F,$Mg=false){$G=oci_parse($this->link,$F);$this->error="";if(!$G){$k=oci_error($this->link);$this->errno=$k["code"];$this->error=$k["message"];return
false;}set_error_handler(array($this,'_error'));$H=@oci_execute($G);restore_error_handler();if($H){if(oci_num_fields($G))return
new
Result($G);$this->affected_rows=oci_num_rows($G);oci_free_statement($G);}return$H;}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($G){$this->result=$G;}private
function
convert($I){foreach((array)$I
as$w=>$X){if(is_a($X,'OCILob')||is_a($X,'OCI-Lob'))$I[$w]=$X->load();}return$I;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$H=new
\stdClass;$H->name=oci_field_name($this->result,$d);$H->type=oci_field_type($this->result,$d);$H->charsetnr=(preg_match("~raw|blob|bfile~",$H->type)?63:0);return$H;}function
__destruct(){oci_free_statement($this->result);}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";var$_current_db;function
attach($M,$V,$D){return$this->dsn("oci:dbname=//$M;charset=AL32UTF8",$V,$D);}function
select_db($tb){$this->_current_db=$tb;return
true;}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(25)=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),lang(26)=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),lang(27)=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),lang(28)=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),);}function
begin(){return
true;}function
insertUpdate($R,array$J,array$E){foreach($J
as$N){$Ug=array();$Z=array();foreach($N
as$w=>$X){$Ug[]="$w = $X";if(isset($E[idf_unescape($w)]))$Z[]="$w = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$Ug)." WHERE ".implode(" AND ",$Z))&&connection()->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($N)).") VALUES (".implode(", ",$N).")")))return
false;}return
true;}function
hasCStyleEscapes(){return
true;}}function
idf_escape($t){return'"'.str_replace('"','""',$t).'"';}function
table($t){return
idf_escape($t);}function
get_databases($uc){return
get_vals("SELECT DISTINCT tablespace_name FROM (
SELECT tablespace_name FROM user_tablespaces
UNION SELECT tablespace_name FROM all_tables WHERE tablespace_name IS NOT NULL
)
ORDER BY 1");}function
limit($F,$Z,$x,$re=0,$L=" "){return($re?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $F$Z) t WHERE rownum <= ".($x+$re).") WHERE rnum > $re":($x?" * FROM (SELECT $F$Z) WHERE rownum <= ".($x+$re):" $F$Z"));}function
limit1($R,$F,$Z,$L="\n"){return" $F$Z";}function
db_collation($i,$Wa){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
get_current_db(){$i=connection()->_current_db?:DB;unset(connection()->_current_db);return$i;}function
where_owner($ef,$Ke="owner"){if(!$_GET["ns"])return'';return"$ef$Ke = sys_context('USERENV', 'CURRENT_SCHEMA')";}function
views_table($e){$Ke=where_owner('');return"(SELECT $e FROM all_views WHERE ".($Ke?:"rownum < 0").")";}function
tables_list(){$eh=views_table("view_name");$Ke=where_owner(" AND ");return
get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = ".q(DB)."$Ke
UNION SELECT view_name, 'view' FROM $eh
ORDER BY 1");}function
count_tables($ub){$H=array();foreach($ub
as$i)$H[$i]=get_val("SELECT COUNT(*) FROM all_tables WHERE tablespace_name = ".q($i));return$H;}function
table_status($_=""){$H=array();$Ef=q($_);$i=get_current_db();$eh=views_table("view_name");$Ke=where_owner(" AND ");foreach(get_rows('SELECT table_name "Name", \'table\' "Engine", avg_row_len * num_rows "Data_length", num_rows "Rows" FROM all_tables WHERE tablespace_name = '.q($i).$Ke.($_!=""?" AND table_name = $Ef":"")."
UNION SELECT view_name, 'view', 0, 0 FROM $eh".($_!=""?" WHERE view_name = $Ef":"")."
ORDER BY 1")as$I)$H[$I["Name"]]=$I;return$H;}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return
true;}function
fields($R){$H=array();$Ke=where_owner(" AND ");foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($R)."$Ke ORDER BY column_id")as$I){$U=$I["DATA_TYPE"];$Id="$I[DATA_PRECISION],$I[DATA_SCALE]";if($Id==",")$Id=$I["CHAR_COL_DECL_LENGTH"];$H[$I["COLUMN_NAME"]]=array("field"=>$I["COLUMN_NAME"],"full_type"=>$U.($Id?"($Id)":""),"type"=>strtolower($U),"length"=>$Id,"default"=>$I["DATA_DEFAULT"],"null"=>($I["NULLABLE"]=="Y"),"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),);}return$H;}function
indexes($R,$h=null){$H=array();$Ke=where_owner(" AND ","aic.table_owner");foreach(get_rows("SELECT aic.*, ac.constraint_type, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_constraints ac ON aic.index_name = ac.constraint_name AND aic.table_name = ac.table_name AND aic.index_owner = ac.owner
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($R)."$Ke
ORDER BY ac.constraint_type, aic.column_position",$h)as$I){$fd=$I["INDEX_NAME"];$Ya=$I["DATA_DEFAULT"];$Ya=($Ya?trim($Ya,'"'):$I["COLUMN_NAME"]);$H[$fd]["type"]=($I["CONSTRAINT_TYPE"]=="P"?"PRIMARY":($I["CONSTRAINT_TYPE"]=="U"?"UNIQUE":"INDEX"));$H[$fd]["columns"][]=$Ya;$H[$fd]["lengths"][]=($I["CHAR_LENGTH"]&&$I["CHAR_LENGTH"]!=$I["COLUMN_LENGTH"]?$I["CHAR_LENGTH"]:null);$H[$fd]["descs"][]=($I["DESCEND"]&&$I["DESCEND"]=="DESC"?'1':null);}return$H;}function
view($_){$eh=views_table("view_name, text");$J=get_rows('SELECT text "select" FROM '.$eh.' WHERE view_name = '.q($_));return
reset($J);}function
collations(){return
array();}function
information_schema($i){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){return
h(connection()->error);}function
explain($g,$F){$g->query("EXPLAIN PLAN FOR $F");return$g->query("SELECT * FROM plan_table");}function
found_rows($S,$Z){}function
auto_increment(){return"";}function
alter_table($R,$_,$m,$wc,$ab,$Sb,$c,$ta,$Se){$b=$Gb=array();$Ge=($R?fields($R):array());foreach($m
as$l){$X=$l[1];if($X&&$l[0]!=""&&idf_escape($l[0])!=$X[0])queries("ALTER TABLE ".table($R)." RENAME COLUMN ".idf_escape($l[0])." TO $X[0]");$Fe=$Ge[$l[0]];if($X&&$Fe){$te=process_field($Fe,$Fe);if($X[2]==$te[2])$X[2]="";}if($X)$b[]=($R!=""?($l[0]!=""?"MODIFY (":"ADD ("):"  ").implode($X).($R!=""?")":"");else$Gb[]=idf_escape($l[0]);}if($R=="")return
queries("CREATE TABLE ".table($_)." (\n".implode(",\n",$b)."\n)");return(!$b||queries("ALTER TABLE ".table($R)."\n".implode("\n",$b)))&&(!$Gb||queries("ALTER TABLE ".table($R)." DROP (".implode(", ",$Gb).")"))&&($R==$_||queries("ALTER TABLE ".table($R)." RENAME TO ".table($_)));}function
alter_indexes($R,$b){$Gb=array();$mf=array();foreach($b
as$X){if($X[0]!="INDEX"){$X[2]=preg_replace('~ DESC$~','',$X[2]);$lb=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");array_unshift($mf,"ALTER TABLE ".table($R).$lb);}elseif($X[2]=="DROP")$Gb[]=idf_escape($X[1]);else$mf[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R)." (".implode(", ",$X[2]).")";}if($Gb)array_unshift($mf,"DROP INDEX ".implode(", ",$Gb));foreach($mf
as$F){if(!queries($F))return
false;}return
true;}function
foreign_keys($R){$H=array();$F="SELECT c_list.CONSTRAINT_NAME as NAME,
c_src.COLUMN_NAME as SRC_COLUMN,
c_dest.OWNER as DEST_DB,
c_dest.TABLE_NAME as DEST_TABLE,
c_dest.COLUMN_NAME as DEST_COLUMN,
c_list.DELETE_RULE as ON_DELETE
FROM ALL_CONSTRAINTS c_list, ALL_CONS_COLUMNS c_src, ALL_CONS_COLUMNS c_dest
WHERE c_list.CONSTRAINT_NAME = c_src.CONSTRAINT_NAME
AND c_list.R_CONSTRAINT_NAME = c_dest.CONSTRAINT_NAME
AND c_list.CONSTRAINT_TYPE = 'R'
AND c_src.TABLE_NAME = ".q($R);foreach(get_rows($F)as$I)$H[$I['NAME']]=array("db"=>$I['DEST_DB'],"table"=>$I['DEST_TABLE'],"source"=>array($I['SRC_COLUMN']),"target"=>array($I['DEST_COLUMN']),"on_delete"=>$I['ON_DELETE'],"on_update"=>null,);return$H;}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($fh){return
apply_queries("DROP VIEW",$fh);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
last_id($G){return
0;}function
schemas(){$H=get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX')) ORDER BY 1");return($H?:get_vals("SELECT DISTINCT owner FROM all_tables WHERE tablespace_name = ".q(DB)." ORDER BY 1"));}function
get_schema(){return
get_val("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");}function
set_schema($Df,$h=null){if(!$h)$h=connection();return$h->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($Df));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$H=array();$J=get_rows('SELECT * FROM v$instance');foreach(reset($J)as$w=>$X)$H[]=array($w,$X);return$H;}function
process_list(){return
get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field($l){}function
unconvert_field($l,$H){return$H;}function
support($jc){return
preg_match('~^(columns|database|drop_col|indexes|descidx|processlist|scheme|sql|status|table|variables|view)$~',$jc);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="sqlsrv";private$link,$result;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$k){$this->errno=$k["code"];$this->error
.="$k[message]\n";}$this->error=rtrim($this->error);}function
attach($M,$V,$D){$fb=array("UID"=>$V,"PWD"=>$D,"CharacterSet"=>"UTF-8");$O=adminer()->connectSsl();if(isset($O["Encrypt"]))$fb["Encrypt"]=$O["Encrypt"];if(isset($O["TrustServerCertificate"]))$fb["TrustServerCertificate"]=$O["TrustServerCertificate"];$i=adminer()->database();if($i!="")$fb["Database"]=$i;$this->link=@sqlsrv_connect(preg_replace('~:~',',',$M),$fb);if($this->link){$jd=sqlsrv_server_info($this->link);$this->server_info=$jd['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($Q){$Ng=strlen($Q)!=strlen(utf8_decode($Q));return($Ng?"N":"")."'".str_replace("'","''",$Q)."'";}function
select_db($tb){return$this->query(use_sql($tb));}function
query($F,$Mg=false){$G=sqlsrv_query($this->link,$F);$this->error="";if(!$G){$this->get_error();return
false;}return$this->store_result($G);}function
multi_query($F){$this->result=sqlsrv_query($this->link,$F);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($G=null){if(!$G)$G=$this->result;if(!$G)return
false;if(sqlsrv_field_metadata($G))return
new
Result($G);$this->affected_rows=sqlsrv_rows_affected($G);return
true;}function
next_result(){return$this->result?!!sqlsrv_next_result($this->result):false;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($G){$this->result=$G;}private
function
convert($I){foreach((array)$I
as$w=>$X){if(is_a($X,'DateTime'))$I[$w]=$X->format("Y-m-d H:i:s");}return$I;}function
fetch_assoc(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC));}function
fetch_row(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC));}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$l=$this->fields[$this->offset++];$H=new
\stdClass;$H->name=$l["Name"];$H->type=($l["Type"]==1?254:15);$H->charsetnr=0;return$H;}function
seek($re){for($r=0;$r<$re;$r++)sqlsrv_fetch($this->result);}function
__destruct(){sqlsrv_free_stmt($this->result);}}function
last_id($G){return
get_val("SELECT SCOPE_IDENTITY()");}function
explain($g,$F){$g->query("SET SHOWPLAN_ALL ON");$H=$g->query($F);$g->query("SET SHOWPLAN_ALL OFF");return$H;}}else{abstract
class
MssqlDb
extends
PdoDb{function
select_db($tb){return$this->query(use_sql($tb));}function
lastInsertId(){return$this->pdo->lastInsertId();}}function
last_id($G){return
connection()->lastInsertId();}function
explain($g,$F){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach($M,$V,$D){return$this->dsn("sqlsrv:Server=".str_replace(":",",",$M),$V,$D);}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach($M,$V,$D){return$this->dsn("dblib:charset=utf8;host=".str_replace(":",";unix_socket=",preg_replace('~:(\d)~',';port=\1',$M)),$V,$D);}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";static
function
connect($M,$V,$D){if($M=="")$M="localhost:1433";return
parent::connect($M,$V,$D);}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(25)=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20),lang(26)=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>10),lang(27)=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823),lang(28)=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),);}function
insertUpdate($R,array$J,array$E){$m=fields($R);$Ug=array();$Z=array();$N=reset($J);$e="c".implode(", c",range(1,count($N)));$Ia=0;$nd=array();foreach($N
as$w=>$X){$Ia++;$_=idf_unescape($w);if(!$m[$_]["auto_increment"])$nd[$w]="c$Ia";if(isset($E[$_]))$Z[]="$w = c$Ia";else$Ug[]="$w = c$Ia";}$bh=array();foreach($J
as$N)$bh[]="(".implode(", ",$N).")";if($Z){$bd=queries("SET IDENTITY_INSERT ".table($R)." ON");$H=queries("MERGE ".table($R)." USING (VALUES\n\t".implode(",\n\t",$bh)."\n) AS source ($e) ON ".implode(" AND ",$Z).($Ug?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$Ug):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($bd?$N:$nd)).") VALUES (".($bd?$e:implode(", ",$nd)).");");if($bd)queries("SET IDENTITY_INSERT ".table($R)." OFF");}else$H=queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($N)).") VALUES\n".implode(",\n",$bh));return$H;}function
begin(){return
queries("BEGIN TRANSACTION");}function
tableHelp($_,$wd=false){$Ld=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$y=$Ld[get_schema()];if($y)return"relational-databases/system-$y".preg_replace('~_~','-',strtolower($_))."-transact-sql";}}function
idf_escape($t){return"[".str_replace("]","]]",$t)."]";}function
table($t){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($t);}function
get_databases($uc){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($F,$Z,$x,$re=0,$L=" "){return($x?" TOP (".($x+$re).")":"")." $F$Z";}function
limit1($R,$F,$Z,$L="\n"){return
limit($F,$Z,1,0,$L);}function
db_collation($i,$Wa){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($i));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables($ub){$H=array();foreach($ub
as$i){connection()->select_db($i);$H[$i]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$H;}function
table_status($_=""){$H=array();foreach(get_rows("SELECT ao.name AS Name, ao.type_desc AS Engine, (SELECT value FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($_!=""?"AND name = ".q($_):"ORDER BY name"))as$I)$H[$I["Name"]]=$I;return$H;}function
is_view($S){return$S["Engine"]=="VIEW";}function
fk_support($S){return
true;}function
fields($R){$bb=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($R).", 'column', NULL)");$H=array();$jg=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($R));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name, t.name type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($jg))as$I){$U=$I["type"];$Id=(preg_match("~char|binary~",$U)?intval($I["max_length"])/($U[0]=='n'?2:1):($U=="decimal"?"$I[precision],$I[scale]":""));$H[$I["name"]]=array("field"=>$I["name"],"full_type"=>$U.($Id?"($Id)":""),"type"=>$U,"length"=>$Id,"default"=>(preg_match("~^\('(.*)'\)$~",$I["default"],$z)?str_replace("''","'",$z[1]):$I["default"]),"default_constraint"=>$I["default_constraint"],"null"=>$I["is_nullable"],"auto_increment"=>$I["is_identity"],"collation"=>$I["collation_name"],"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$I["is_primary_key"],"comment"=>$bb[$I["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($jg))as$I){$H[$I["name"]]["generated"]=($I["is_persisted"]?"PERSISTED":"VIRTUAL");$H[$I["name"]]["default"]=$I["definition"];}return$H;}function
indexes($R,$h=null){$H=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($R),$h)as$I){$_=$I["name"];$H[$_]["type"]=($I["is_primary_key"]?"PRIMARY":($I["is_unique"]?"UNIQUE":"INDEX"));$H[$_]["lengths"]=array();$H[$_]["columns"][$I["key_ordinal"]]=$I["column_name"];$H[$_]["descs"][$I["key_ordinal"]]=($I["is_descending_key"]?'1':null);}return$H;}function
view($_){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($_))));}function
collations(){$H=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$c)$H[preg_replace('~_.*~','',$c)][]=$c;return$H;}function
information_schema($i){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($i,$c){return
queries("CREATE DATABASE ".idf_escape($i).(preg_match('~^[a-z0-9_]+$~i',$c)?" COLLATE $c":""));}function
drop_databases($ub){return
queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$ub)));}function
rename_database($_,$c){if(preg_match('~^[a-z0-9_]+$~i',$c))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $c");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($_));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($R,$_,$m,$wc,$ab,$Sb,$c,$ta,$Se){$b=array();$bb=array();$Ge=fields($R);foreach($m
as$l){$d=idf_escape($l[0]);$X=$l[1];if(!$X)$b["DROP"][]=" COLUMN $d";else{$X[1]=preg_replace("~( COLLATE )'(\\w+)'~",'\1\2',$X[1]);$bb[$l[0]]=$X[5];unset($X[5]);if(preg_match('~ AS ~',$X[3]))unset($X[1],$X[2]);if($l[0]=="")$b["ADD"][]="\n  ".implode("",$X).($R==""?substr($wc[$X[0]],16+strlen($X[0])):"");else{$j=$X[3];unset($X[3]);unset($X[6]);if($d!=$X[0])queries("EXEC sp_rename ".q(table($R).".$d").", ".q(idf_unescape($X[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$X)][]="";$Fe=$Ge[$l[0]];if(default_value($Fe)!=$j){if($Fe["default"]!==null)$b["DROP"][]=" ".idf_escape($Fe["default_constraint"]);if($j)$b["ADD"][]="\n $j FOR $d";}}}}if($R=="")return
queries("CREATE TABLE ".table($_)." (".implode(",",(array)$b["ADD"])."\n)");if($R!=$_)queries("EXEC sp_rename ".q(table($R)).", ".q($_));if($wc)$b[""]=$wc;foreach($b
as$w=>$X){if(!queries("ALTER TABLE ".table($_)." $w".implode(",",$X)))return
false;}foreach($bb
as$w=>$X){$ab=substr($X,9);queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($_).", @level2type = N'Column', @level2name = ".q($w));queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $ab,
@level0type = N'Schema',
@level0name = ".q(get_schema()).",
@level1type = N'Table',
@level1name = ".q($_).",
@level2type = N'Column',
@level2name = ".q($w));}return
true;}function
alter_indexes($R,$b){$u=array();$Gb=array();foreach($b
as$X){if($X[2]=="DROP"){if($X[0]=="PRIMARY")$Gb[]=idf_escape($X[1]);else$u[]=idf_escape($X[1])." ON ".table($R);}elseif(!queries(($X[0]!="PRIMARY"?"CREATE $X[0] ".($X[0]!="INDEX"?"INDEX ":"").idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R):"ALTER TABLE ".table($R)." ADD PRIMARY KEY")." (".implode(", ",$X[2]).")"))return
false;}return(!$u||queries("DROP INDEX ".implode(", ",$u)))&&(!$Gb||queries("ALTER TABLE ".table($R)." DROP ".implode(", ",$Gb)));}function
found_rows($S,$Z){}function
foreign_keys($R){$H=array();$ve=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($R).", @fktable_owner = ".q(get_schema()))as$I){$o=&$H[$I["FK_NAME"]];$o["db"]=$I["PKTABLE_QUALIFIER"];$o["ns"]=$I["PKTABLE_OWNER"];$o["table"]=$I["PKTABLE_NAME"];$o["on_update"]=$ve[$I["UPDATE_RULE"]];$o["on_delete"]=$ve[$I["DELETE_RULE"]];$o["source"][]=$I["FKCOLUMN_NAME"];$o["target"][]=$I["PKCOLUMN_NAME"];}return$H;}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($fh){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$fh)));}function
drop_tables($T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables($T,$fh,$mg){return
apply_queries("ALTER SCHEMA ".idf_escape($mg)." TRANSFER",array_merge($T,$fh));}function
trigger($_,$R){if($_=="")return
array();$J=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($_));$H=reset($J);if($H)$H["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$H["text"]);return$H;}function
triggers($R){$H=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($R))as$I)$H[$I["name"]]=array($I["Timing"],$I["Event"]);return$H;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($Cf){$_GET["ns"]=$Cf;return
true;}function
create_sql($R,$ta,$dg){if(is_view(table_status1($R))){$eh=view($R);return"CREATE VIEW ".table($R)." AS $eh[select]";}$m=array();$E=false;foreach(fields($R)as$_=>$l){$X=process_field($l,$l);if($X[6])$E=true;$m[]=implode("",$X);}foreach(indexes($R)as$_=>$u){if(!$E||$u["type"]!="PRIMARY"){$e=array();foreach($u["columns"]as$w=>$X)$e[]=idf_escape($X).($u["descs"][$w]?" DESC":"");$_=idf_escape($_);$m[]=($u["type"]=="INDEX"?"INDEX $_":"CONSTRAINT $_ ".($u["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($R)as$_=>$Ma)$m[]="CONSTRAINT ".idf_escape($_)." CHECK ($Ma)";return"CREATE TABLE ".table($R)." (\n\t".implode(",\n\t",$m)."\n)";}function
foreign_keys_sql($R){$m=array();foreach(foreign_keys($R)as$wc)$m[]=ltrim(format_foreign_key($wc));return($m?"ALTER TABLE ".table($R)." ADD\n\t".implode(",\n\t",$m).";\n\n":"");}function
truncate_sql($R){return"TRUNCATE TABLE ".table($R);}function
use_sql($tb){return"USE ".idf_escape($tb);}function
trigger_sql($R){$H="";foreach(triggers($R)as$_=>$Gg)$H
.=create_trigger(" ON ".table($R),trigger($_,$R)).";";return$H;}function
convert_field($l){}function
unconvert_field($l,$H){return$H;}function
support($jc){return
preg_match('~^(check|comment|columns|database|drop_col|dump|indexes|descidx|scheme|sql|table|trigger|view|view_trigger)$~',$jc);}}class
Adminer{static$instance;var$error='';private$values=array();function
name(){return"<a href='https://www.adminer.org/editor/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.3.0")."' width='24' height='24' alt='' id='logo'>".lang(32)."</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($lb=false){return
password_file($lb);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($M){}function
database(){if(connection()){$ub=adminer()->databases(false);return(!$ub?get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1)"):$ub[(information_schema($ub[0])?1:0)]);}}function
operators(){return
array("<=",">=");}function
schemas(){return
schemas();}function
databases($uc=true){return
get_databases($uc);}function
pluginsLinks(){}function
queryTimeout(){return
5;}function
headers(){}function
csp($ob){return$ob;}function
head($rb=null){return
true;}function
bodyClass(){echo" editor";}function
css(){$H=array();foreach(array("","-dark")as$ee){$n="adminer$ee.css";if(file_exists($n)){$mc=file_get_contents($n);$H["$n?v=".crc32($mc)]=($ee?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$mc)?'':'light'));}}return$H;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('username','<tr><th>'.lang(33).'<td>',input_hidden("auth[driver]","server").'<input name="auth[username]" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(34).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),"</table>\n","<p><input type='submit' value='".lang(35)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(36))."\n";}function
loginFormField($_,$Sc,$Y){return$Sc.$Y."\n";}function
login($Nd,$D){return
true;}function
tableName($ig){return
h(isset($ig["Engine"])?($ig["Comment"]!=""?$ig["Comment"]:$ig["Name"]):"");}function
fieldName($l,$Ce=0){return
h(preg_replace('~\s+\[.*\]$~','',($l["comment"]!=""?$l["comment"]:$l["field"])));}function
selectLinks($ig,$N=""){$a=$ig["Name"];if($N!==null)echo'<p class="tabs"><a href="'.h(ME.'edit='.urlencode($a).$N).'">'.lang(37)."</a>\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$hg){$H=array();foreach(get_rows("SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_SCHEMA = ".q(adminer()->database())."
AND REFERENCED_TABLE_NAME = ".q($R)."
ORDER BY ORDINAL_POSITION",null,"")as$I)$H[$I["TABLE_NAME"]]["keys"][$I["CONSTRAINT_NAME"]][$I["COLUMN_NAME"]]=$I["REFERENCED_COLUMN_NAME"];foreach($H
as$w=>$X){$_=adminer()->tableName(table_status1($w,true));if($_!=""){$Ef=preg_quote($hg);$L="(:|\\s*-)?\\s+";$H[$w]["name"]=(preg_match("(^$Ef$L(.+)|^(.+?)$L$Ef\$)iu",$_,$z)?$z[2].$z[3]:$_);}else
unset($H[$w]);}return$H;}function
backwardKeysPrint($ya,$I){foreach($ya
as$R=>$xa){foreach($xa["keys"]as$Xa){$y=ME.'select='.urlencode($R);$r=0;foreach($Xa
as$d=>$X)$y
.=where_link($r++,$d,$I[$X]);echo"<a href='".h($y)."'>".h($xa["name"])."</a>";$y=ME.'edit='.urlencode($R);foreach($Xa
as$d=>$X)$y
.="&set".urlencode("[".bracket_escape($d)."]")."=".urlencode($I[$X]);echo"<a href='".h($y)."' title='".lang(37)."'>+</a> ";}}}function
selectQuery($F,$bg,$hc=false){return"<!--\n".str_replace("--","--><!-- ",$F)."\n(".format_time($bg).")\n-->\n";}function
rowDescription($R){foreach(fields($R)as$l){if(preg_match("~varchar|character varying~",$l["type"]))return
idf_escape($l["field"]);}return"";}function
rowDescriptions($J,$yc){$H=$J;foreach($J[0]as$w=>$X){if(list($R,$s,$_)=$this->_foreignColumn($yc,$w)){$cd=array();foreach($J
as$I)$cd[$I[$w]]=q($I[$w]);$_b=$this->values[$R];if(!$_b)$_b=get_key_vals("SELECT $s, $_ FROM ".table($R)." WHERE $s IN (".implode(", ",$cd).")");foreach($J
as$ie=>$I){if(isset($I[$w]))$H[$ie][$w]=(string)$_b[$I[$w]];}}}return$H;}function
selectLink($X,$l){}function
selectVal($X,$y,$l,$He){$H=$X;$y=h($y);if(preg_match('~blob|bytea~',$l["type"])&&!is_utf8($X)){$H=lang(38,strlen($He));if(preg_match("~^(GIF|\xFF\xD8\xFF|\x89PNG\x0D\x0A\x1A\x0A)~",$He))$H="<img src='$y' alt='$H'>";}if(like_bool($l)&&$H!="")$H=(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?lang(39):lang(40));if($y)$H="<a href='$y'".(is_url($y)?target_blank():"").">$H</a>";if(preg_match('~date~',$l["type"]))$H="<div class='datetime'>$H</div>";return$H;}function
editVal($X,$l){if(preg_match('~date|timestamp~',$l["type"])&&$X!==null)return
preg_replace('~^(\d{2}(\d+))-(0?(\d+))-(0?(\d+))~',lang(41),$X);return$X;}function
config(){return
array();}function
selectColumnsPrint($K,$e){}function
selectSearchPrint($Z,$e,$v){$Z=(array)$_GET["where"];echo'<fieldset id="fieldset-search"><legend>'.lang(42)."</legend><div>\n";$_d=array();foreach($Z
as$w=>$X)$_d[$X["col"]]=$w;$r=0;$m=fields($_GET["select"]);foreach($e
as$_=>$zb){$l=$m[$_];if(preg_match("~enum~",$l["type"])||like_bool($l)){$w=$_d[$_];$r--;echo"<div>".h($zb).":".input_hidden("where[$r][col]",$_);$X=idx($Z[$w],"val");echo(like_bool($l)?"<select name='where[$r][val]'>".optionlist(array(""=>"",lang(40),lang(39)),$X,true)."</select>":enum_input("checkbox"," name='where[$r][val][]'",$l,(array)$X,($l["null"]?0:null))),"</div>\n";unset($e[$_]);}elseif(is_array($B=$this->foreignKeyOptions($_GET["select"],$_))){if($m[$_]["null"])$B[0]='('.lang(7).')';$w=$_d[$_];$r--;echo"<div>".h($zb).input_hidden("where[$r][col]",$_).input_hidden("where[$r][op]","=").": <select name='where[$r][val]'>".optionlist($B,idx($Z[$w],"val"),true)."</select></div>\n";unset($e[$_]);}}$r=0;foreach($Z
as$X){if(($X["col"]==""||$e[$X["col"]])&&"$X[col]$X[val]"!=""){echo"<div><select name='where[$r][col]'><option value=''>(".lang(43).")".optionlist($e,$X["col"],true)."</select>",html_select("where[$r][op]",array(-1=>"")+adminer()->operators(),$X["op"]),"<input type='search' name='where[$r][val]' value='".h($X["val"])."'>".script("mixin(qsl('input'), {onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});","")."</div>\n";$r++;}}echo"<div><select name='where[$r][col]'><option value=''>(".lang(43).")".optionlist($e,null,true)."</select>",script("qsl('select').onchange = selectAddRow;",""),html_select("where[$r][op]",array(-1=>"")+adminer()->operators()),"<input type='search' name='where[$r][val]'></div>",script("mixin(qsl('input'), {onchange: function () { this.parentNode.firstChild.onchange(); }, onsearch: selectSearchSearch});"),"</div></fieldset>\n";}function
selectOrderPrint($Ce,$e,$v){$Ee=array();foreach($v
as$w=>$u){$Ce=array();foreach($u["columns"]as$X)$Ce[]=$e[$X];if(count(array_filter($Ce,'strlen'))>1&&$w!="PRIMARY")$Ee[$w]=implode(", ",$Ce);}if($Ee)echo'<fieldset><legend>'.lang(44)."</legend><div>","<select name='index_order'>".optionlist(array(""=>"")+$Ee,(idx($_GET["order"],0)!=""?"":$_GET["index_order"]),true)."</select>","</div></fieldset>\n";if($_GET["order"])echo"<div style='display: none;'>".hidden_fields(array("order"=>array(1=>reset($_GET["order"])),"desc"=>($_GET["desc"]?array(1=>1):array()),))."</div>\n";}function
selectLimitPrint($x){echo"<fieldset><legend>".lang(45)."</legend><div>",html_select("limit",array("",50,100),$x),"</div></fieldset>\n";}function
selectLengthPrint($qg){}function
selectActionPrint($v){echo"<fieldset><legend>".lang(46)."</legend><div>","<input type='submit' value='".lang(47)."'>","</div></fieldset>\n";}function
selectCommandPrint(){return
true;}function
selectImportPrint(){return
true;}function
selectEmailPrint($Pb,$e){}function
selectColumnsProcess($e,$v){return
array(array(),array());}function
selectSearchProcess($m,$v){$H=array();foreach((array)$_GET["where"]as$w=>$Z){$Va=$Z["col"];$ye=$Z["op"];$X=$Z["val"];if(($w>=0&&$Va!="")||$X!=""){$cb=array();foreach(($Va!=""?array($Va=>$m[$Va]):$m)as$_=>$l){if($Va!=""||is_numeric($X)||!preg_match(number_type(),$l["type"])){$_=idf_escape($_);if($Va!=""&&$l["type"]=="enum")$cb[]=(in_array(0,$X)?"$_ IS NULL OR ":"")."$_ IN (".implode(", ",array_map('Adminer\q',$X)).")";else{$rg=preg_match('~char|text|enum|set~',$l["type"]);$Y=adminer()->processInput($l,(!$ye&&$rg&&preg_match('~^[^%]+$~',$X)?"%$X%":$X));$cb[]=driver()->convertSearch($_,$Z,$l).($Y=="NULL"?" IS".($ye==">="?" NOT":"")." $Y":(in_array($ye,adminer()->operators())||$ye=="="?" $ye $Y":($rg?" LIKE $Y":" IN (".($Y[0]=="'"?str_replace(",","', '",$Y):$Y).")")));if($w<0&&$X=="0")$cb[]="$_ IS NULL";}}}$H[]=($cb?"(".implode(" OR ",$cb).")":"1 = 0");}}return$H;}function
selectOrderProcess($m,$v){$gd=$_GET["index_order"];if($gd!="")unset($_GET["order"][1]);if($_GET["order"])return
array(idf_escape(reset($_GET["order"])).($_GET["desc"]?" DESC":""));foreach(($gd!=""?array($v[$gd]):$v)as$u){if($gd!=""||$u["type"]=="INDEX"){$Mc=array_filter($u["descs"]);$zb=false;foreach($u["columns"]as$X){if(preg_match('~date|timestamp~',$m[$X]["type"])){$zb=true;break;}}$H=array();foreach($u["columns"]as$w=>$X)$H[]=idf_escape($X).(($Mc?$u["descs"][$w]:$zb)?" DESC":"");return$H;}}return
array();}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return"100";}function
selectEmailProcess($Z,$yc){return
false;}function
selectQueryBuild($K,$Z,$Gc,$Ce,$x,$C){return"";}function
messageQuery($F,$sg,$hc=false){return" <span class='time'>".@date("H:i:s")."</span><!--\n".str_replace("--","--><!-- ",$F)."\n".($sg?"($sg)\n":"")."-->";}function
editRowPrint($R,$m,$I,$Ug){}function
editFunctions($l){$H=array();if($l["null"]&&preg_match('~blob~',$l["type"]))$H["NULL"]=lang(7);$H[""]=($l["null"]||$l["auto_increment"]||like_bool($l)?"":"*");if(preg_match('~date|time~',$l["type"]))$H["now"]=lang(48);if(preg_match('~_(md5|sha1)$~i',$l["field"],$z))$H[]=strtolower($z[1]);return$H;}function
editInput($R,$l,$ra,$Y){if($l["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$ra value='-1' checked><i>".lang(8)."</i></label> ":"").enum_input("radio",$ra,$l,($Y||isset($_GET["select"])?$Y:""),($l["null"]?"":null));$B=$this->foreignKeyOptions($R,$l["field"],$Y);if($B!==null)return(is_array($B)?"<select$ra>".optionlist($B,$Y,true)."</select>":"<input value='".h($Y)."'$ra class='hidden'>"."<input value='".h($B)."' class='jsonly'>"."<div></div>".script("qsl('input').oninput = partial(whisper, '".ME."script=complete&source=".urlencode($R)."&field=".urlencode($l["field"])."&value='); qsl('div').onclick = whisperClick;",""));if(like_bool($l))return'<input type="checkbox" value="1"'.(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?' checked':'')."$ra>";$Uc="";if(preg_match('~time~',$l["type"]))$Uc=lang(49);if(preg_match('~date|timestamp~',$l["type"]))$Uc=lang(50).($Uc?" [$Uc]":"");if($Uc)return"<input value='".h($Y)."'$ra> ($Uc)";if(preg_match('~_(md5|sha1)$~i',$l["field"]))return"<input type='password' value='".h($Y)."'$ra>";return'';}function
editHint($R,$l,$Y){return(preg_match('~\s+(\[.*\])$~',($l["comment"]!=""?$l["comment"]:$l["field"]),$z)?h(" $z[1]"):'');}function
processInput($l,$Y,$q=""){if($q=="now")return"$q()";$H=$Y;if(preg_match('~date|timestamp~',$l["type"])&&preg_match('(^'.str_replace('\$1','(?P<p1>\d*)',preg_replace('~(\\\\\\$([2-6]))~','(?P<p\2>\d{1,2})',preg_quote(lang(41)))).'(.*))',$Y,$z))$H=($z["p1"]!=""?$z["p1"]:($z["p2"]!=""?($z["p2"]<70?20:19).$z["p2"]:gmdate("Y")))."-$z[p3]$z[p4]-$z[p5]$z[p6]".end($z);$H=q($H);if($Y==""&&like_bool($l))$H="'0'";elseif($Y==""&&($l["null"]||!preg_match('~char|text~',$l["type"])))$H="NULL";elseif(preg_match('~^(md5|sha1)$~',$q))$H="$q($H)";return
unconvert_field($l,$H);}function
dumpOutput(){return
array();}function
dumpFormat(){return
array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($i){}function
dumpTable($R,$dg,$wd=0){echo"\xef\xbb\xbf";}function
dumpData($R,$dg,$F){$G=connection()->query($F,1);if($G){while($I=$G->fetch_assoc()){if($dg=="table"){dump_csv(array_keys($I));$dg="INSERT";}dump_csv($I);}}}function
dumpFilename($ad){return
friendly_url($ad);}function
dumpHeaders($ad,$ge=false){$dc="csv";header("Content-Type: text/csv; charset=utf-8");return$dc;}function
dumpFooter(){}function
importServerPath(){}function
homepage(){return
true;}function
navigation($de){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$le=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/editor/#download'".target_blank()." id='version'>".(version_compare(VERSION,$le)<0?h($le):"")."</a>","</span></h1>\n";switch_lang();if($de=="auth"){$pc=true;foreach((array)$_SESSION["pwds"]as$ch=>$Of){foreach($Of[""]as$V=>$D){if($D!==null){if($pc){echo"<ul id='logins'>",script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");$pc=false;}echo"<li><a href='".h(auth_url($ch,"",$V))."'>".($V!=""?h($V):"<i>".lang(7)."</i>")."</a>\n";}}}}else{adminer()->databasesPrint($de);if($de!="db"&&$de!="ns"){$S=table_status('',true);if(!$S)echo"<p class='message'>".lang(9)."\n";else
adminer()->tablesPrint($S);}}}function
syntaxHighlighting($T){}function
databasesPrint($de){}function
tablesPrint($T){echo"<ul id='tables'>",script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");foreach($T
as$I){echo'<li>';$_=adminer()->tableName($I);if($_!="")echo"<a href='".h(ME).'select='.urlencode($I["Name"])."'".bold($_GET["select"]==$I["Name"]||$_GET["edit"]==$I["Name"],"select")." title='".lang(51)."'>$_</a>\n";}echo"</ul>\n";}function
_foreignColumn($yc,$d){foreach((array)$yc[$d]as$xc){if(count($xc["source"])==1){$_=adminer()->rowDescription($xc["table"]);if($_!=""){$s=idf_escape($xc["target"][0]);return
array($xc["table"],$s,$_);}}}}private
function
foreignKeyOptions($R,$d,$Y=null){if(list($mg,$s,$_)=$this->_foreignColumn(column_foreign_keys($R),$d)){$H=&$this->values[$mg];if($H===null){$S=table_status1($mg);$H=($S["Rows"]>1000?"":array(""=>"")+get_key_vals("SELECT $s, $_ FROM ".table($mg)." ORDER BY 2"));}if(!$H&&$Y!==null)return
get_val("SELECT $_ FROM ".table($mg)." WHERE $s = ".q($Y));return$H;}}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$error='';private$hooks=array();function
__construct($Ze){if($Ze===null){$Ze=array();$Aa="adminer-plugins";if(is_dir($Aa)){foreach(glob("$Aa/*.php")as$n)$ed=include_once"./$n";}$Tc=" href='https://www.adminer.org/plugins/#use'".target_blank();if(file_exists("$Aa.php")){$ed=include_once"./$Aa.php";if(is_array($ed)){foreach($ed
as$Ye)$Ze[get_class($Ye)]=$Ye;}else$this->error
.=lang(52,"<b>$Aa.php</b>",$Tc)."<br>";}foreach(get_declared_classes()as$Ra){if(!$Ze[$Ra]&&preg_match('~^Adminer\w~i',$Ra)){$sf=new
\ReflectionClass($Ra);$hb=$sf->getConstructor();if($hb&&$hb->getNumberOfRequiredParameters())$this->error
.=lang(53,$Tc,"<b>$Ra</b>","<b>$Aa.php</b>")."<br>";else$Ze[$Ra]=new$Ra;}}}$this->plugins=$Ze;$ga=new
Adminer;$Ze[]=$ga;$sf=new
\ReflectionObject($ga);foreach($sf->getMethods()as$ce){foreach($Ze
as$Ye){$_=$ce->getName();if(method_exists($Ye,$_))$this->hooks[$_][]=$Ye;}}}function
__call($_,array$Oe){$ma=array();foreach($Oe
as$w=>$X)$ma[]=&$Oe[$w];$H=null;foreach($this->hooks[$_]as$Ye){$Y=call_user_func_array(array($Ye,$_),$ma);if($Y!==null){if(!self::$append[$_])return$Y;$H=$Y+(array)$H;}}return$H;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$A=null){$ma=func_get_args();$ma[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$ma);}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\MySQLi{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($M,$V,$D){mysqli_report(MYSQLI_REPORT_OFF);list($Wc,$af)=explode(":",$M,2);$O=adminer()->connectSsl();if($O)$this->ssl_set($O['key'],$O['cert'],$O['ca'],'','');$H=@$this->real_connect(($M!=""?$Wc:ini_get("mysqli.default_host")),($M.$V!=""?$V:ini_get("mysqli.default_user")),($M.$V.$D!=""?$D:ini_get("mysqli.default_pw")),null,(is_numeric($af)?intval($af):ini_get("mysqli.default_port")),(is_numeric($af)?null:$af),($O?($O['verify']!==false?2048:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,false);return($H?'':$this->error);}function
set_charset($La){if(parent::set_charset($La))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $La");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($M,$V,$D){if(ini_bool("mysql.allow_local_infile"))return
lang(54,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($M!=""?$M:ini_get("mysql.default_host")),("$M$V"!=""?$V:ini_get("mysql.default_user")),("$M$V$D"!=""?$D:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($La){if(function_exists('mysql_set_charset')){if(mysql_set_charset($La,$this->link))return
true;mysql_set_charset('utf8',$this->link);}return$this->query("SET NAMES $La");}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($tb){return
mysql_select_db($tb,$this->link);}function
query($F,$Mg=false){$G=@($Mg?mysql_unbuffered_query($F,$this->link):mysql_query($F,$this->link));$this->error="";if(!$G){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($G===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($G);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($G){$this->result=$G;$this->num_rows=mysql_num_rows($G);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$H=mysql_fetch_field($this->result,$this->offset++);$H->orgtable=$H->table;$H->charsetnr=($H->blob?63:0);return$H;}function
__destruct(){mysql_free_result($this->result);}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach($M,$V,$D){$B=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);$O=adminer()->connectSsl();if($O){if($O['key'])$B[\PDO::MYSQL_ATTR_SSL_KEY]=$O['key'];if($O['cert'])$B[\PDO::MYSQL_ATTR_SSL_CERT]=$O['cert'];if($O['ca'])$B[\PDO::MYSQL_ATTR_SSL_CA]=$O['ca'];if(isset($O['verify']))$B[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$O['verify'];}return$this->dsn("mysql:charset=utf8;host=".str_replace(":",";unix_socket=",preg_replace('~:(\d)~',';port=\1',$M)),$V,$D,$B);}function
set_charset($La){return$this->query("SET NAMES $La");}function
select_db($tb){return$this->query("USE ".idf_escape($tb));}function
query($F,$Mg=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Mg);return
parent::query($F,$Mg);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($M,$V,$D){$g=parent::connect($M,$V,$D);if(is_string($g)){if(function_exists('iconv')&&!is_utf8($g)&&strlen($Bf=iconv("windows-1250","utf-8",$g))>strlen($g))$g=$Bf;return$g;}$g->set_charset(charset($g));$g->query("SET sql_quote_show_create = 1, autocommit = 1");$g->flavor=(preg_match('~MariaDB~',$g->server_info)?'maria':'mysql');add_driver(DRIVER,($g->flavor=='maria'?"MariaDB":"MySQL"));return$g;}function
__construct(Db$g){parent::__construct($g);$this->types=array(lang(25)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(26)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(27)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(55)=>array("enum"=>65535,"set"=>64),lang(28)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(30)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$g))$this->types[lang(27)]["json"]=4294967295;if(min_version('',10.7,$g)){$this->types[lang(27)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version(9,'',$g)){$this->types[lang(25)]["vector"]=16383;$this->insertFunctions['vector']='string_to_vector';}if(min_version(5.1,'',$g))$this->partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");if(min_version(5.7,10.2,$g))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$l){return(preg_match("~binary~",$l["type"])?"<code class='jush-sql'>UNHEX</code>":($l["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):(preg_match("~geometry|point|linestring|polygon~",$l["type"])?"<code class='jush-sql'>GeomFromText</code>":"")));}function
insert($R,array$N){return($N?parent::insert($R,$N):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$J,array$E){$e=array_keys(reset($J));$ef="INSERT INTO ".table($R)." (".implode(", ",$e).") VALUES\n";$bh=array();foreach($e
as$w)$bh[$w]="$w = VALUES($w)";$fg="\nON DUPLICATE KEY UPDATE ".implode(", ",$bh);$bh=array();$Id=0;foreach($J
as$N){$Y="(".implode(", ",$N).")";if($bh&&(strlen($ef)+$Id+strlen($Y)+strlen($fg)>1e6)){if(!queries($ef.implode(",\n",$bh).$fg))return
false;$bh=array();$Id=0;}$bh[]=$Y;$Id+=strlen($Y)+2;}return
queries($ef.implode(",\n",$bh).$fg);}function
slowQuery($F,$tg){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$tg FOR $F";elseif(preg_match('~^(SELECT\b)(.+)~is',$F,$z))return"$z[1] /*+ MAX_EXECUTION_TIME(".($tg*1000).") */ $z[2]";}}function
convertSearch($t,array$X,array$l){return(preg_match('~char|text|enum|set~',$l["type"])&&!preg_match("~^utf8~",$l["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($t USING ".charset($this->conn).")":$t);}function
warnings(){$G=$this->conn->query("SHOW WARNINGS");if($G&&$G->num_rows){ob_start();print_select_result($G);return
ob_get_clean();}}function
tableHelp($_,$wd=false){$Pd=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower("information-schema-".($Pd?"$_-table/":str_replace("_","-",$_)."-table.html"));if(DB=="mysql")return($Pd?"mysql$_-table/":"system-schema.html");}function
partitionsInfo($R){$Bc="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$G=connection()->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Bc ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$H=array();list($H["partition_by"],$H["partition"],$H["partitions"])=$G->fetch_row();$Te=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Bc AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$H["partition_names"]=array_keys($Te);$H["partition_values"]=array_values($Te);return$H;}function
hasCStyleEscapes(){static$Ja;if($Ja===null){$Zf=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Ja=(strpos($Zf,'NO_BACKSLASH_ESCAPES')===false);}return$Ja;}function
engines(){$H=array();foreach(get_rows("SHOW ENGINES")as$I){if(preg_match("~YES|DEFAULT~",$I["Support"]))$H[]=$I["Engine"];}return$H;}function
indexAlgorithms(array$ig){return(preg_match('~^(MEMORY|NDB)$~',$ig["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($t){return"`".str_replace("`","``",$t)."`";}function
table($t){return
idf_escape($t);}function
get_databases($uc){$H=get_session("dbs");if($H===null){$F="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$H=($uc?slow_query($F):get_vals($F));restart_session();set_session("dbs",$H);stop_session();}return$H;}function
limit($F,$Z,$x,$re=0,$L=" "){return" $F$Z".($x?$L."LIMIT $x".($re?" OFFSET $re":""):"");}function
limit1($R,$F,$Z,$L="\n"){return
limit($F,$Z,1,0,$L);}function
db_collation($i,array$Wa){$H=null;$lb=get_val("SHOW CREATE DATABASE ".idf_escape($i),1);if(preg_match('~ COLLATE ([^ ]+)~',$lb,$z))$H=$z[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$lb,$z))$H=$Wa[$z[1]][-1];return$H;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$ub){$H=array();foreach($ub
as$i)$H[$i]=count(get_vals("SHOW TABLES IN ".idf_escape($i)));return$H;}function
table_status($_="",$ic=false){$H=array();foreach(get_rows($ic?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($_!=""?"AND TABLE_NAME = ".q($_):"ORDER BY Name"):"SHOW TABLE STATUS".($_!=""?" LIKE ".q(addcslashes($_,"%_\\")):""))as$I){if($I["Engine"]=="InnoDB")$I["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$I["Comment"]);if(!isset($I["Engine"]))$I["Comment"]="";if($_!="")$I["Name"]=$_;$H[$I["Name"]]=$I;}return$H;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
fields($R){$Pd=(connection()->flavor=='maria');$H=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$I){$l=$I["COLUMN_NAME"];$U=$I["COLUMN_TYPE"];$Fc=$I["GENERATION_EXPRESSION"];$gc=$I["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$gc,$Ec);preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$U,$Sd);$j=$I["COLUMN_DEFAULT"];if($j!=""){$vd=preg_match('~text|json~',$Sd[1]);if(!$Pd&&$vd)$j=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($j));if($Pd||$vd){$j=($j=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($z){return
stripslashes(str_replace("''","'",$z[1]));},$j));}if(!$Pd&&preg_match('~binary~',$Sd[1])&&preg_match('~^0x(\w*)$~',$j,$z))$j=pack("H*",$z[1]);}$H[$l]=array("field"=>$l,"full_type"=>$U,"type"=>$Sd[1],"length"=>$Sd[2],"unsigned"=>ltrim($Sd[3].$Sd[4]),"default"=>($Ec?($Pd?$Fc:stripslashes($Fc)):$j),"null"=>($I["IS_NULLABLE"]=="YES"),"auto_increment"=>($gc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$gc,$z)?$z[1]:""),"collation"=>$I["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$I[PRIVILEGES],where,order")),"comment"=>$I["COLUMN_COMMENT"],"primary"=>($I["COLUMN_KEY"]=="PRI"),"generated"=>($Ec[1]=="PERSISTENT"?"STORED":$Ec[1]),);}return$H;}function
indexes($R,$h=null){$H=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$h)as$I){$_=$I["Key_name"];$H[$_]["type"]=($_=="PRIMARY"?"PRIMARY":($I["Index_type"]=="FULLTEXT"?"FULLTEXT":($I["Non_unique"]?($I["Index_type"]=="SPATIAL"?"SPATIAL":"INDEX"):"UNIQUE")));$H[$_]["columns"][]=$I["Column_name"];$H[$_]["lengths"][]=($I["Index_type"]=="SPATIAL"?null:$I["Sub_part"]);$H[$_]["descs"][]=null;$H[$_]["algorithm"]=$I["Index_type"];}return$H;}function
foreign_keys($R){static$Ve='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$H=array();$mb=get_val("SHOW CREATE TABLE ".table($R),1);if($mb){preg_match_all("~CONSTRAINT ($Ve) FOREIGN KEY ?\\(((?:$Ve,? ?)+)\\) REFERENCES ($Ve)(?:\\.($Ve))? \\(((?:$Ve,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$mb,$Td,PREG_SET_ORDER);foreach($Td
as$z){preg_match_all("~$Ve~",$z[2],$Vf);preg_match_all("~$Ve~",$z[5],$mg);$H[idf_unescape($z[1])]=array("db"=>idf_unescape($z[4]!=""?$z[3]:$z[4]),"table"=>idf_unescape($z[4]!=""?$z[4]:$z[3]),"source"=>array_map('Adminer\idf_unescape',$Vf[0]),"target"=>array_map('Adminer\idf_unescape',$mg[0]),"on_delete"=>($z[6]?:"RESTRICT"),"on_update"=>($z[7]?:"RESTRICT"),);}}return$H;}function
view($_){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($_),1)));}function
collations(){$H=array();foreach(get_rows("SHOW COLLATION")as$I){if($I["Default"])$H[$I["Charset"]][-1]=$I["Collation"];else$H[$I["Charset"]][]=$I["Collation"];}ksort($H);foreach($H
as$w=>$X)sort($H[$w]);return$H;}function
information_schema($i){return($i=="information_schema")||(min_version(5.5)&&$i=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($i,$c){return
queries("CREATE DATABASE ".idf_escape($i).($c?" COLLATE ".q($c):""));}function
drop_databases(array$ub){$H=apply_queries("DROP DATABASE",$ub,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$H;}function
rename_database($_,$c){$H=false;if(create_database($_,$c)){$T=array();$fh=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$fh[]=$R;else$T[]=$R;}$H=(!$T&&!$fh)||move_tables($T,$fh,$_);drop_databases($H?array(DB):array());}return$H;}function
auto_increment(){$ua=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$u){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$u["columns"],true)){$ua="";break;}if($u["type"]=="PRIMARY")$ua=" UNIQUE";}}return" AUTO_INCREMENT$ua";}function
alter_table($R,$_,array$m,array$wc,$ab,$Sb,$c,$ta,$Se){$b=array();foreach($m
as$l){if($l[1]){$j=$l[1][3];if(preg_match('~ GENERATED~',$j)){$l[1][3]=(connection()->flavor=='maria'?"":$l[1][2]);$l[1][2]=$j;}$b[]=($R!=""?($l[0]!=""?"CHANGE ".idf_escape($l[0]):"ADD"):" ")." ".implode($l[1]).($R!=""?$l[2]:"");}else$b[]="DROP ".idf_escape($l[0]);}$b=array_merge($b,$wc);$P=($ab!==null?" COMMENT=".q($ab):"").($Sb?" ENGINE=".q($Sb):"").($c?" COLLATE ".q($c):"").($ta!=""?" AUTO_INCREMENT=$ta":"");if($Se){$Te=array();if($Se["partition_by"]=='RANGE'||$Se["partition_by"]=='LIST'){foreach($Se["partition_names"]as$w=>$X){$Y=$Se["partition_values"][$w];$Te[]="\n  PARTITION ".idf_escape($X)." VALUES ".($Se["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $Se[partition_by]($Se[partition])";if($Te)$P
.=" (".implode(",",$Te)."\n)";elseif($Se["partitions"])$P
.=" PARTITIONS ".(+$Se["partitions"]);}elseif($Se===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($_)." (\n".implode(",\n",$b)."\n)$P");if($R!=$_)$b[]="RENAME TO ".table($_);if($P)$b[]=ltrim($P);return($b?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$b)):true);}function
alter_indexes($R,$b){$Ka=array();foreach($b
as$X)$Ka[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$Ka));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$fh){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$fh)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$fh,$mg){$vf=array();foreach($T
as$R)$vf[]=table($R)." TO ".idf_escape($mg).".".table($R);if(!$vf||queries("RENAME TABLE ".implode(", ",$vf))){$xb=array();foreach($fh
as$R)$xb[table($R)]=view($R);connection()->select_db($mg);$i=idf_escape(DB);foreach($xb
as$_=>$eh){if(!queries("CREATE VIEW $_ AS ".str_replace(" $i."," ",$eh["select"]))||!queries("DROP VIEW $i.$_"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$fh,$mg){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$_=($mg==DB?table("copy_$R"):idf_escape($mg).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $_"))||!queries("CREATE TABLE $_ LIKE ".table($R))||!queries("INSERT INTO $_ SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$I){$Gg=$I["Trigger"];if(!queries("CREATE TRIGGER ".($mg==DB?idf_escape("copy_$Gg"):idf_escape($mg).".".idf_escape($Gg))." $I[Timing] $I[Event] ON $_ FOR EACH ROW\n$I[Statement];"))return
false;}}foreach($fh
as$R){$_=($mg==DB?table("copy_$R"):idf_escape($mg).".".table($R));$eh=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $_"))||!queries("CREATE VIEW $_ AS $eh[select]"))return
false;}return
true;}function
trigger($_,$R){if($_=="")return
array();$J=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($_));return
reset($J);}function
triggers($R){$H=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$I)$H[$I["Trigger"]]=array($I["Timing"],$I["Event"]);return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
routine($_,$U){$ka=array("bool","boolean","integer","double precision","real","dec","numeric","fixed","national char","national varchar");$Wf="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";$Tb=driver()->enumLength;$Kg="((".implode("|",array_merge(array_keys(driver()->types()),$ka)).")\\b(?:\\s*\\(((?:[^'\")]|$Tb)++)\\))?"."\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?";$Ve="$Wf*(".($U=="FUNCTION"?"":driver()->inout).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$Kg";$lb=get_val("SHOW CREATE $U ".idf_escape($_),2);preg_match("~\\(((?:$Ve\\s*,?)*)\\)\\s*".($U=="FUNCTION"?"RETURNS\\s+$Kg\\s+":"")."(.*)~is",$lb,$z);$m=array();preg_match_all("~$Ve\\s*,?~is",$z[1],$Td,PREG_SET_ORDER);foreach($Td
as$Ne)$m[]=array("field"=>str_replace("``","`",$Ne[2]).$Ne[3],"type"=>strtolower($Ne[5]),"length"=>preg_replace_callback("~$Tb~s",'Adminer\normalize_enum',$Ne[6]),"unsigned"=>strtolower(preg_replace('~\s+~',' ',trim("$Ne[8] $Ne[7]"))),"null"=>true,"full_type"=>$Ne[4],"inout"=>strtoupper($Ne[1]),"collation"=>strtolower($Ne[9]),);return
array("fields"=>$m,"comment"=>get_val("SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = ".q($_)),)+($U!="FUNCTION"?array("definition"=>$z[11]):array("returns"=>array("type"=>$z[12],"length"=>$z[13],"unsigned"=>$z[15],"collation"=>$z[16]),"definition"=>$z[17],"language"=>"SQL",));}function
routines(){return
get_rows("SELECT ROUTINE_NAME AS SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($_,array$I){return
idf_escape($_);}function
last_id($G){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$g,$F){return$g->query("EXPLAIN ".(min_version(5.1)&&!min_version(5.7)?"PARTITIONS ":"").$F);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$ta,$dg){$H=get_val("SHOW CREATE TABLE ".table($R),1);if(!$ta)$H=preg_replace('~ AUTO_INCREMENT=\d+~','',$H);return$H;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($tb){return"USE ".idf_escape($tb);}function
trigger_sql($R){$H="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$I)$H
.="\nCREATE TRIGGER ".idf_escape($I["Trigger"])." $I[Timing] $I[Event] ON ".table($I["Table"])." FOR EACH ROW\n$I[Statement];;\n";return$H;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$l){if(preg_match("~binary~",$l["type"]))return"HEX(".idf_escape($l["field"]).")";if($l["type"]=="bit")return"BIN(".idf_escape($l["field"])." + 0)";if(preg_match("~geometry|point|linestring|polygon~",$l["type"]))return(min_version(8)?"ST_":"")."AsWKT(".idf_escape($l["field"]).")";}function
unconvert_field(array$l,$H){if(preg_match("~binary~",$l["type"]))$H="UNHEX($H)";if($l["type"]=="bit")$H="CONVERT(b$H, UNSIGNED)";if(preg_match("~geometry|point|linestring|polygon~",$l["type"])){$ef=(min_version(8)?"ST_":"");$H=$ef."GeomFromText($H, $ef"."SRID($l[field]))";}return$H;}function
support($jc){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(5.1)?'|event':'').(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').')$~',$jc);}function
kill_process($X){return
queries("KILL ".number($X));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types(){return
array();}function
type_values($s){return"";}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($Cf,$h=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',$_GET[DRIVER]);define('Adminer\DB',$_GET["db"]);define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').($_GET["ext"]?"ext=".urlencode($_GET["ext"]).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));function
page_header($vg,$k="",$Ga=array(),$wg=""){page_headers();if(is_ajax()&&$k){page_messages($k);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$xg=$vg.($wg!=""?": $wg":"");$yg=strip_tags($xg.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang="',LANG,'" dir="',lang(56),'">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$yg,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=5.3.0"),'">
';$pb=adminer()->css();if(is_int(key($pb)))$pb=array_fill_keys($pb,'light');$Oc=in_array('light',$pb)||in_array('',$pb);$Lc=in_array('dark',$pb)||in_array('',$pb);$rb=($Oc?($Lc?null:false):($Lc?:null));$Zd=" media='(prefers-color-scheme: dark)'";if($rb!==false)echo"<link rel='stylesheet'".($rb?"":$Zd)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=5.3.0")."'>\n";echo"<meta name='color-scheme' content='".($rb===null?"light dark":($rb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=5.3.0");if(adminer()->head($rb))echo"<link rel='icon' href='data:image/gif;base64,R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.3.0")."'>\n";foreach($pb
as$Wg=>$ee){$ra=($ee=='dark'&&!$rb?$Zd:($ee=='light'&&$Lc?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$ra href='".h($Wg)."'>\n";}echo"\n<body class='".lang(56)." nojs";adminer()->bodyClass();echo"'>\n";$n=get_temp_dir()."/adminer.version";if(!$_COOKIE["adminer_version"]&&function_exists('openssl_verify')&&file_exists($n)&&filemtime($n)+86400>time()){$dh=unserialize(file_get_contents($n));$kf="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";if(openssl_verify($dh["version"],base64_decode($dh["signature"]),$kf)==1)$_COOKIE["adminer_version"]=$dh["version"];}echo
script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick".(isset($_COOKIE["adminer_version"])?"":", onload: partial(verifyVersion, '".VERSION."', '".js_escape(ME)."', '".get_token()."')")."});
document.body.classList.replace('nojs', 'js');
const offlineMessage = '".js_escape(lang(57))."';
const thousandsSeparator = '".js_escape(lang(4))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon("move","","menu","")."</span>".script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");if($Ga!==null){$y=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($y?:".").'">'.get_driver(DRIVER).'</a> » ';$y=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$M=adminer()->serverName(SERVER);$M=($M!=""?$M:lang(58));if($Ga===false)echo"$M\n";else{echo"<a href='".h($y)."' accesskey='1' title='Alt+Shift+1'>$M</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ga)))echo'<a href="'.h($y."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> » ';if(is_array($Ga)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Ga
as$w=>$X){$zb=(is_array($X)?$X[1]:h($X));if($zb!="")echo"<a href='".h(ME."$w=").urlencode(is_array($X)?$X[0]:$X)."'>$zb</a> » ";}}echo"$vg\n";}}echo"<h2>$xg</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($k);$ub=&get_session("dbs");if(DB!=""&&$ub&&!in_array(DB,$ub,true))$ub=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$ob){$Qc=array();foreach($ob
as$w=>$X)$Qc[]="$w $X";header("Content-Security-Policy: ".implode("; ",$Qc));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self'","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
get_nonce(){static$ne;if(!$ne)$ne=base64_encode(rand_string());return$ne;}function
page_messages($k){$Vg=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$be=idx($_SESSION["messages"],$Vg);if($be){echo"<div class='message'>".implode("</div>\n<div class='message'>",$be)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Vg]);}if($k)echo"<div class='error'>$k</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($de=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($de);echo"</div>\n";if($de!="auth")echo'<form action="" method="post">
<p class="logout">
<span>',h($_GET["username"])."\n",'</span>
<input type="submit" name="logout" value="',lang(59),'" id="logout">
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($ie){while($ie>=2147483648)$ie-=4294967296;while($ie<=-2147483649)$ie+=4294967296;return(int)$ie;}function
long2str(array$W,$hh){$Bf='';foreach($W
as$X)$Bf
.=pack('V',$X);if($hh)return
substr($Bf,0,end($W));return$Bf;}function
str2long($Bf,$hh){$W=array_values(unpack('V*',str_pad($Bf,4*ceil(strlen($Bf)/4),"\0")));if($hh)$W[]=strlen($Bf);return$W;}function
xxtea_mx($mh,$lh,$gg,$yd){return
int32((($mh>>5&0x7FFFFFF)^$lh<<2)+(($lh>>3&0x1FFFFFFF)^$mh<<4))^int32(($gg^$lh)+($yd^$mh));}function
encrypt_string($cg,$w){if($cg=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($cg,true);$ie=count($W)-1;$mh=$W[$ie];$lh=$W[0];$lf=floor(6+52/($ie+1));$gg=0;while($lf-->0){$gg=int32($gg+0x9E3779B9);$Kb=$gg>>2&3;for($Le=0;$Le<$ie;$Le++){$lh=$W[$Le+1];$he=xxtea_mx($mh,$lh,$gg,$w[$Le&3^$Kb]);$mh=int32($W[$Le]+$he);$W[$Le]=$mh;}$lh=$W[0];$he=xxtea_mx($mh,$lh,$gg,$w[$Le&3^$Kb]);$mh=int32($W[$ie]+$he);$W[$ie]=$mh;}return
long2str($W,false);}function
decrypt_string($cg,$w){if($cg=="")return"";if(!$w)return
false;$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($cg,false);$ie=count($W)-1;$mh=$W[$ie];$lh=$W[0];$lf=floor(6+52/($ie+1));$gg=int32($lf*0x9E3779B9);while($gg){$Kb=$gg>>2&3;for($Le=$ie;$Le>0;$Le--){$mh=$W[$Le-1];$he=xxtea_mx($mh,$lh,$gg,$w[$Le&3^$Kb]);$lh=int32($W[$Le]-$he);$W[$Le]=$lh;}$mh=$W[$ie];$he=xxtea_mx($mh,$lh,$gg,$w[$Le&3^$Kb]);$lh=int32($W[0]-$he);$W[0]=$lh;$gg=int32($gg-0x9E3779B9);}return
long2str($W,true);}$Xe=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($w)=explode(":",$X);$Xe[$w]=$X;}}function
add_invalid_login(){$_a=get_temp_dir()."/adminer.invalid";foreach(glob("$_a*")?:array($_a)as$n){$p=file_open_lock($n);if($p)break;}if(!$p)$p=file_open_lock("$_a-".rand_string());if(!$p)return;$rd=unserialize(stream_get_contents($p));$sg=time();if($rd){foreach($rd
as$sd=>$X){if($X[0]<$sg)unset($rd[$sd]);}}$qd=&$rd[adminer()->bruteForceKey()];if(!$qd)$qd=array($sg+30*60,0);$qd[1]++;file_write_unlock($p,serialize($rd));}function
check_invalid_login(array&$Xe){$rd=array();foreach(glob(get_temp_dir()."/adminer.invalid*")as$n){$p=file_open_lock($n);if($p){$rd=unserialize(stream_get_contents($p));file_unlock($p);break;}}$qd=idx($rd,adminer()->bruteForceKey(),array());$me=($qd[1]>29?$qd[0]-time():0);if($me>0)auth_error(lang(60,ceil($me/60)),$Xe);}$sa=$_POST["auth"];if($sa){session_regenerate_id();$ch=$sa["driver"];$M=$sa["server"];$V=$sa["username"];$D=(string)$sa["password"];$i=$sa["db"];set_password($ch,$M,$V,$D);$_SESSION["db"][$ch][$M][$V][$i]=true;if($sa["permanent"]){$w=implode("-",array_map('base64_encode',array($ch,$M,$V,$i)));$if=adminer()->permanentLogin(true);$Xe[$w]="$w:".base64_encode($if?encrypt_string($D,$if):"");cookie("adminer_permanent",implode(" ",$Xe));}if(count($_POST)==1||DRIVER!=$ch||SERVER!=$M||$_GET["username"]!==$V||DB!=$i)redirect(auth_url($ch,$M,$V,$i));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent($Xe);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(61).' '.lang(62));}elseif($Xe&&!$_SESSION["pwds"]){session_regenerate_id();$if=adminer()->permanentLogin();foreach($Xe
as$w=>$X){list(,$Qa)=explode(":",$X);list($ch,$M,$V,$i)=array_map('base64_decode',explode("-",$w));set_password($ch,$M,$V,decrypt_string(base64_decode($Qa),$if));$_SESSION["db"][$ch][$M][$V][$i]=true;}}function
unset_permanent(array&$Xe){foreach($Xe
as$w=>$X){list($ch,$M,$V,$i)=array_map('base64_decode',explode("-",$w));if($ch==DRIVER&&$M==SERVER&&$V==$_GET["username"]&&$i==DB)unset($Xe[$w]);}cookie("adminer_permanent",implode(" ",$Xe));}function
auth_error($k,array&$Xe){$Pf=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Pf]||$_GET[$Pf])&&!$_SESSION["token"])$k=lang(63);else{restart_session();add_invalid_login();$D=get_password();if($D!==null){if($D===false)$k
.=($k?'<br>':'').lang(64,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent($Xe);}}if(!$_COOKIE[$Pf]&&$_GET[$Pf]&&ini_bool("session.use_only_cookies"))$k=lang(65);$Oe=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Oe["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(35),$k,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".lang(66)."\n";echo"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Xe);page_header(lang(67),lang(68,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$g='';if(isset($_GET["username"])&&is_string(get_password())){list($Wc,$af)=explode(":",SERVER,2);if(preg_match('~^\s*([-+]?\d+)~',$af,$z)&&($z[1]<1024||$z[1]>65535))auth_error(lang(69),$Xe);check_invalid_login($Xe);$nb=adminer()->credentials();$g=Driver::connect($nb[0],$nb[1],$nb[2]);if(is_object($g)){Db::$instance=$g;Driver::$instance=new
Driver($g);if($g->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Nd=null;if(!is_object($g)||($Nd=adminer()->login($_GET["username"],get_password()))!==true){$k=(is_string($g)?nl_br(h($g)):(is_string($Nd)?$Nd:lang(70))).(preg_match('~^ | $~',get_password())?'<br>'.lang(71):'');auth_error($k,$Xe);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(59),lang(72));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($sa&&$_POST["token"])$_POST["token"]=get_token();$k='';if($_POST){if(!verify_token()){$kd="max_input_vars";$Xd=ini_get($kd);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$w){$X=ini_get($w);if($X&&(!$Xd||$X<$Xd)){$kd=$w;$Xd=$X;}}}$k=(!$_POST["token"]&&$Xd?lang(73,"'$kd'"):lang(72).' '.lang(74));}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$k=lang(75,"'post_max_size'");if(isset($_GET["sql"]))$k
.=' '.lang(76);}function
doc_link(array$Ue,$og=""){return"";}function
email_header($Qc){return"=?UTF-8?B?".base64_encode($Qc)."?=";}function
send_mail($Ob,$eg,$ae,$Bc="",array$nc=array()){$Wb=PHP_EOL;$ae=str_replace("\n",$Wb,wordwrap(str_replace("\r","","$ae\n")));$Fa=uniqid("boundary");$qa="";foreach((array)$nc["error"]as$w=>$X){if(!$X)$qa
.="--$Fa$Wb"."Content-Type: ".str_replace("\n","",$nc["type"][$w]).$Wb."Content-Disposition: attachment; filename=\"".preg_replace('~["\n]~','',$nc["name"][$w])."\"$Wb"."Content-Transfer-Encoding: base64$Wb$Wb".chunk_split(base64_encode(file_get_contents($nc["tmp_name"][$w])),76,$Wb).$Wb;}$Ba="";$Rc="Content-Type: text/plain; charset=utf-8$Wb"."Content-Transfer-Encoding: 8bit";if($qa){$qa
.="--$Fa--$Wb";$Ba="--$Fa$Wb$Rc$Wb$Wb";$Rc="Content-Type: multipart/mixed; boundary=\"$Fa\"";}$Rc
.=$Wb."MIME-Version: 1.0$Wb"."X-Mailer: Adminer Editor".($Bc?$Wb."From: ".str_replace("\n","",$Bc):"");return
mail($Ob,email_header($eg),$Ba.$ae.$qa,$Rc);}function
like_bool(array$l){return
preg_match("~bool|(tinyint|bit)\\(1\\)~",$l["full_type"]);}connection()->select_db(adminer()->database());add_driver(DRIVER,lang(35));if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["download"])){$a=$_GET["download"];$m=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$K=array(idf_escape($_GET["field"]));$G=driver()->select($a,$K,array(where($_GET,$m)),$K);$I=($G?$G->fetch_row():array());echo
driver()->value($I[0],$m[$_GET["field"]]);exit;}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$m=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$m):""):where($_GET,$m));$Ug=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($m
as$_=>$l){if(!isset($l["privileges"][$Ug?"update":"insert"])||adminer()->fieldName($l)==""||$l["generated"])unset($m[$_]);}if($_POST&&!$k&&!isset($_GET["select"])){$Md=$_POST["referer"];if($_POST["insert"])$Md=($Ug?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$Md))$Md=ME."select=".urlencode($a);$v=indexes($a);$Pg=unique_array($_GET["where"],$v);$of="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($Md,lang(77),driver()->delete($a,$of,$Pg?0:1));else{$N=array();foreach($m
as$_=>$l){$X=process_input($l);if($X!==false&&$X!==null)$N[idf_escape($_)]=$X;}if($Ug){if(!$N)redirect($Md);queries_redirect($Md,lang(78),driver()->update($a,$N,$of,$Pg?0:1));if(is_ajax()){page_headers();page_messages($k);exit;}}else{$G=driver()->insert($a,$N);$Gd=($G?last_id($G):0);queries_redirect($Md,lang(79,($Gd?" $Gd":"")),$G);}}}$I=null;if($_POST["save"])$I=(array)$_POST["fields"];elseif($Z){$K=array();foreach($m
as$_=>$l){if(isset($l["privileges"]["select"])){$oa=($_POST["clone"]&&$l["auto_increment"]?"''":convert_field($l));$K[]=($oa?"$oa AS ":"").idf_escape($_);}}$I=array();if(!support("table"))$K=array("*");if($K){$G=driver()->select($a,$K,array($Z),$K,array(),(isset($_GET["select"])?2:1));if(!$G)$k=error();else{$I=$G->fetch_assoc();if(!$I)$I=false;}if(isset($_GET["select"])&&(!$I||$G->fetch_assoc()))$I=null;}}if(!support("table")&&!$m){if(!$Z){$G=driver()->select($a,array("*"),array(),array("*"));$I=($G?$G->fetch_assoc():false);if(!$I)$I=array(driver()->primary=>"");}if($I){foreach($I
as$w=>$X){if(!$Z)$I[$w]=null;$m[$w]=array("field"=>$w,"null"=>($w!=driver()->primary),"auto_increment"=>($w==driver()->primary));}}}edit_form($a,$m,$I,$Ug,$k);}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status1($a);$v=indexes($a);$m=fields($a);$zc=column_foreign_keys($a);$se=$S["Oid"];$ha=get_settings("adminer_import");$_f=array();$e=array();$Ff=array();$De=array();$qg="";foreach($m
as$w=>$l){$_=adminer()->fieldName($l);$je=html_entity_decode(strip_tags($_),ENT_QUOTES);if(isset($l["privileges"]["select"])&&$_!=""){$e[$w]=$je;if(is_shortable($l))$qg=adminer()->selectLengthProcess();}if(isset($l["privileges"]["where"])&&$_!="")$Ff[$w]=$je;if(isset($l["privileges"]["order"])&&$_!="")$De[$w]=$je;$_f+=$l["privileges"];}list($K,$Gc)=adminer()->selectColumnsProcess($e,$v);$K=array_unique($K);$Gc=array_unique($Gc);$td=count($Gc)<count($K);$Z=adminer()->selectSearchProcess($m,$v);$Ce=adminer()->selectOrderProcess($m,$v);$x=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Qg=>$I){$oa=convert_field($m[key($I)]);$K=array($oa?:idf_escape(key($I)));$Z[]=where_check($Qg,$m);$H=driver()->select($a,$K,$Z,$K);if($H)echo
first($H->fetch_row());}exit;}$E=$Sg=array();foreach($v
as$u){if($u["type"]=="PRIMARY"){$E=array_flip($u["columns"]);$Sg=($K?$E:array());foreach($Sg
as$w=>$X){if(in_array(idf_escape($w),$K))unset($Sg[$w]);}break;}}if($se&&!$E){$E=$Sg=array($se=>0);$v[]=array("type"=>"PRIMARY","columns"=>array($se));}if($_POST&&!$k){$jh=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Pa=array();foreach($_POST["check"]as$Ma)$Pa[]=where_check($Ma,$m);$jh[]="((".implode(") OR (",$Pa)."))";}$jh=($jh?"\nWHERE ".implode(" AND ",$jh):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Bc=($K?implode(", ",$K):"*").convert_fields($e,$m,$K)."\nFROM ".table($a);$Ic=($Gc&&$td?"\nGROUP BY ".implode(", ",$Gc):"").($Ce?"\nORDER BY ".implode(", ",$Ce):"");$F="SELECT $Bc$jh$Ic";if(is_array($_POST["check"])&&!$E){$Og=array();foreach($_POST["check"]as$X)$Og[]="(SELECT".limit($Bc,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$m).$Ic,1).")";$F=implode(" UNION ALL ",$Og);}adminer()->dumpData($a,"table",$F);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$zc)){if($_POST["save"]||$_POST["delete"]){$G=true;$ia=0;$N=array();if(!$_POST["delete"]){foreach($_POST["fields"]as$_=>$X){$X=process_input($m[$_]);if($X!==null&&($_POST["clone"]||$X!==false))$N[idf_escape($_)]=($X!==false?$X:idf_escape($_));}}if($_POST["delete"]||$N){$F=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($N)).")\nSELECT ".implode(", ",$N)."\nFROM ".table($a):"");if($_POST["all"]||($E&&is_array($_POST["check"]))||$td){$G=($_POST["delete"]?driver()->delete($a,$jh):($_POST["clone"]?queries("INSERT $F$jh".driver()->insertReturning($a)):driver()->update($a,$N,$jh)));$ia=connection()->affected_rows;if(is_object($G))$ia+=$G->num_rows;}else{foreach((array)$_POST["check"]as$X){$ih="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$m);$G=($_POST["delete"]?driver()->delete($a,$ih,1):($_POST["clone"]?queries("INSERT".limit1($a,$F,$ih)):driver()->update($a,$N,$ih,1)));if(!$G)break;$ia+=connection()->affected_rows;}}}$ae=lang(80,$ia);if($_POST["clone"]&&$G&&$ia==1){$Gd=last_id($G);if($Gd)$ae=lang(79," $Gd");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$ae,$G);if(!$_POST["delete"]){$cf=(array)$_POST["fields"];edit_form($a,array_intersect_key($m,$cf),$cf,!$_POST["clone"],$k);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])$k=lang(81);else{$G=true;$ia=0;foreach($_POST["val"]as$Qg=>$I){$N=array();foreach($I
as$w=>$X){$w=bracket_escape($w,true);$N[idf_escape($w)]=(preg_match('~char|text~',$m[$w]["type"])||$X!=""?adminer()->processInput($m[$w],$X):"NULL");}$G=driver()->update($a,$N," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($Qg,$m),($td||$E?0:1)," ");if(!$G)break;$ia+=connection()->affected_rows;}queries_redirect(remove_from_uri(),lang(80,$ia),$G);}}elseif(!is_string($mc=get_file("csv_file",true)))$k=upload_error($mc);elseif(!preg_match('~~u',$mc))$k=lang(82);else{save_settings(array("output"=>$ha["output"],"format"=>$_POST["separator"]),"adminer_import");$G=true;$Xa=array_keys($m);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$mc,$Td);$ia=count($Td[0]);driver()->begin();$L=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$J=array();foreach($Td[0]as$w=>$X){preg_match_all("~((?>\"[^\"]*\")+|[^$L]*)$L~",$X.$L,$Ud);if(!$w&&!array_diff($Ud[1],$Xa)){$Xa=$Ud[1];$ia--;}else{$N=array();foreach($Ud[1]as$r=>$Va)$N[idf_escape($Xa[$r])]=($Va==""&&$m[$Xa[$r]]["null"]?"NULL":q(preg_match('~^".*"$~s',$Va)?str_replace('""','"',substr($Va,1,-1)):$Va));$J[]=$N;}}$G=(!$J||driver()->insertUpdate($a,$J,$E));if($G)driver()->commit();queries_redirect(remove_from_uri("page"),lang(83,$ia),$G);driver()->rollback();}}}$kg=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(47).": $kg",$k);$N=null;if(isset($_f["insert"])||!support("table")){$Oe=array();foreach((array)$_GET["where"]as$X){if(isset($zc[$X["col"]])&&count($zc[$X["col"]])==1&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$X["val"])))))$Oe["set"."[".bracket_escape($X["col"])."]"]=$X["val"];}$N=$Oe?"&".http_build_query($Oe):"";}adminer()->selectLinks($S,$N);if(!$e&&support("table"))echo"<p class='error'>".lang(84).($m?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($K,$e);adminer()->selectSearchPrint($Z,$Ff,$v);adminer()->selectOrderPrint($Ce,$De,$v);adminer()->selectLimitPrint($x);adminer()->selectLengthPrint($qg);adminer()->selectActionPrint($v);echo"</form>\n";$C=$_GET["page"];$Ac=null;if($C=="last"){$Ac=get_val(count_rows($a,$Z,$td,$Gc));$C=floor(max(0,intval($Ac)-1)/$x);}$Gf=$K;$Hc=$Gc;if(!$Gf){$Gf[]="*";$jb=convert_fields($e,$m,$K);if($jb)$Gf[]=substr($jb,2);}foreach($K
as$w=>$X){$l=$m[idf_unescape($X)];if($l&&($oa=convert_field($l)))$Gf[$w]="$oa AS $X";}if(!$td&&$Sg){foreach($Sg
as$w=>$X){$Gf[]=idf_escape($w);if($Hc)$Hc[]=idf_escape($w);}}$G=driver()->select($a,$Gf,$Z,$Hc,$Ce,$x,$C,true);if(!$G)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$C)$G->seek($x*$C);$Qb=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$J=array();while($I=$G->fetch_assoc()){if($C&&JUSH=="oracle")unset($I["RNUM"]);$J[]=$I;}if($_GET["page"]!="last"&&$x&&$Gc&&$td&&JUSH=="sql")$Ac=get_val(" SELECT FOUND_ROWS()");if(!$J)echo"<p class='message'>".lang(12)."\n";else{$za=adminer()->backwardKeys($a,$kg);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$Gc&&$K?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".lang(85)."</a>");$ke=array();$Dc=array();reset($K);$qf=1;foreach($J[0]as$w=>$X){if(!isset($Sg[$w])){$X=idx($_GET["columns"],key($K))?:array();$l=$m[$K?($X?$X["col"]:current($K)):$w];$_=($l?adminer()->fieldName($l,$qf):($X["fun"]?"*":h($w)));if($_!=""){$qf++;$ke[$w]=$_;$d=idf_escape($w);$Xc=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($w);$zb="&desc%5B0%5D=1";echo"<th id='th[".h(bracket_escape($w))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$Cc=apply_sql_function($X["fun"],$_);$Uf=isset($l["privileges"]["order"])||$Cc;echo($Uf?"<a href='".h($Xc.($Ce[0]==$d||$Ce[0]==$w||(!$Ce&&$td&&$Gc[0]==$d)?$zb:''))."'>$Cc</a>":$Cc),"<span class='column hidden'>";if($Uf)echo"<a href='".h($Xc.$zb)."' title='".lang(86)."' class='text'> ↓</a>";if(!$X["fun"]&&isset($l["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.lang(42).'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($w)."');");echo"</span>";}$Dc[$w]=$X["fun"];next($K);}}$Jd=array();if($_GET["modify"]){foreach($J
as$I){foreach($I
as$w=>$X)$Jd[$w]=max($Jd[$w],min(40,strlen(utf8_decode($X))));}}echo($za?"<th>".lang(87):"")."</thead>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($J,$zc)as$ie=>$I){$Pg=unique_array($J[$ie],$v);if(!$Pg){$Pg=array();reset($K);foreach($J[$ie]as$w=>$X){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($K)))$Pg[$w]=$X;next($K);}}$Qg="";foreach($Pg
as$w=>$X){$l=(array)$m[$w];if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$l["type"])&&strlen($X)>64){$w=(strpos($w,'(')?$w:idf_escape($w));$w="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$l["collation"])?$w:"CONVERT($w USING ".charset(connection()).")").")";$X=md5($X);}$Qg
.="&".($X!==null?urlencode("where[".bracket_escape($w)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($w));}echo"<tr>".(!$Gc&&$K?"":"<td>".checkbox("check[]",substr($Qg,1),in_array(substr($Qg,1),(array)$_POST["check"])).($td||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$Qg)."' class='edit'>".lang(88)."</a>"));reset($K);foreach($I
as$w=>$X){if(isset($ke[$w])){$d=current($K);$l=(array)$m[$w];$X=driver()->value($X,$l);if($X!=""&&(!isset($Qb[$w])||$Qb[$w]!=""))$Qb[$w]=(is_mail($X)?$ke[$w]:"");$y="";if(preg_match('~blob|bytea|raw|file~',$l["type"])&&$X!="")$y=ME.'download='.urlencode($a).'&field='.urlencode($w).$Qg;if(!$y&&$X!==null){foreach((array)$zc[$w]as$o){if(count($zc[$w])==1||end($o["source"])==$w){$y="";foreach($o["source"]as$r=>$Vf)$y
.=where_link($r,$o["target"][$r],$J[$ie][$Vf]);$y=($o["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($o["db"]),ME):ME).'select='.urlencode($o["table"]).$y;if($o["ns"])$y=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($o["ns"]),$y);if(count($o["source"])==1)break;}}}if($d=="COUNT(*)"){$y=ME."select=".urlencode($a);$r=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Pg))$y
.=where_link($r++,$W["col"],$W["val"],$W["op"]);}foreach($Pg
as$yd=>$W)$y
.=where_link($r++,$yd,$W);}$Yc=select_value($X,$y,$l,$qg);$s=h("val[$Qg][".bracket_escape($w)."]");$df=idx(idx($_POST["val"],$Qg),bracket_escape($w));$Mb=!is_array($I[$w])&&is_utf8($Yc)&&$J[$ie][$w]==$I[$w]&&!$Dc[$w]&&!$l["generated"];$U=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$d,$z)?$m[idf_unescape($z[2])]["type"]:$l["type"]);$og=preg_match('~text|json|lob~',$U);$ud=preg_match(number_type(),$U)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$d);echo"<td id='$s'".($ud&&($X===null||is_numeric(strip_tags($Yc))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$Mb&&$X!==null)||$df!==null){$Kc=h($df!==null?$df:$I[$w]);echo">".($og?"<textarea name='$s' cols='30' rows='".(substr_count($I[$w],"\n")+1)."'>$Kc</textarea>":"<input name='$s' value='$Kc' size='$Jd[$w]'>");}else{$Od=strpos($Yc,"<i>…</i>");echo" data-text='".($Od?2:($og?1:0))."'".($Mb?"":" data-warning='".h(lang(89))."'").">$Yc";}}next($K);}if($za)echo"<td>";adminer()->backwardKeysPrint($za,$J[$ie]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($J||$C){$bc=true;if($_GET["page"]!="last"){if(!$x||(count($J)<$x&&($J||!$C)))$Ac=($C?$C*$x:0)+count($J);elseif(JUSH!="sql"||!$td){$Ac=($td?false:found_rows($S,$Z));if(intval($Ac)<max(1e4,2*($C+1)*$x))$Ac=first(slow_query(count_rows($a,$Z,$td,$Gc)));else$bc=false;}}$Me=($x&&($Ac===false||$Ac>$x||$C));if($Me)echo(($Ac===false?count($J)+1:$Ac-$C*$x)>$x?'<p><a href="'.h(remove_from_uri("page")."&page=".($C+1)).'" class="loadmore">'.lang(90).'</a>'.script("qsl('a').onclick = partial(selectLoadMore, $x, '".lang(91)."…');",""):''),"\n";echo"<div class='footer'><div>\n";if($Me){$Vd=($Ac===false?$C+(count($J)>=$x?2:1):floor(($Ac-1)/$x));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".lang(92)."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".lang(92)."', '".($C+1)."')); return false; };"),pagination(0,$C).($C>5?" …":"");for($r=max(1,$C-4);$r<min($Vd,$C+5);$r++)echo
pagination($r,$C);if($Vd>0)echo($C+5<$Vd?" …":""),($bc&&$Ac!==false?pagination($Vd,$C):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Vd'>".lang(93)."</a>");}else
echo"<legend>".lang(92)."</legend>",pagination(0,$C).($C>1?" …":""),($C?pagination($C,$C):""),($Vd>$C?pagination($C+1,$C).($Vd>$C+1?" …":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(94)."</legend>";$Db=($bc?"":"~ ").$Ac;$xe="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$Db' : checked); selectCount('selected2', this.checked || !checked ? '$Db' : checked);";echo
checkbox("all",1,0,($Ac!==false?($bc?"":"~ ").lang(95,$Ac):""),$xe)."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':' class="jsonly"'),'><legend>',lang(85),'</legend><div>
<input type="submit" value="',lang(14),'"',($_GET["modify"]?'':' title="'.lang(81).'"'),'>
</div></fieldset>
<fieldset><legend>',lang(96),' <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="',lang(10),'">
<input type="submit" name="clone" value="',lang(97),'">
<input type="submit" name="delete" value="',lang(18),'">',confirm(),'</div></fieldset>
';$_c=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]){unset($_c['sql']);break;}}if($_c){print_fieldset("export",lang(98)." <span id='selected2'></span>");$Je=adminer()->dumpOutput();echo($Je?html_select("output",$Je,$ha["output"])." ":""),html_select("format",$_c,$ha["format"])," <input type='submit' name='export' value='".lang(98)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Qb,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import'>".lang(99)."</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"<span id='import'".($_POST["import"]?"":" class='hidden'").">: ","<input type='file' name='csv_file'> ",html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$ha["format"])," <input type='submit' name='import' value='".lang(99)."'>","</span>";echo
input_token(),"</form>\n",(!$Gc&&$K?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["script"])){if($_GET["script"]=="kill")connection()->query("KILL ".number($_POST["kill"]));elseif(list($R,$s,$_)=adminer()->_foreignColumn(column_foreign_keys($_GET["source"]),$_GET["field"])){$x=11;$G=connection()->query("SELECT $s, $_ FROM ".table($R)." WHERE ".(preg_match('~^[0-9]+$~',$_GET["value"])?"$s = $_GET[value] OR ":"")."$_ LIKE ".q("$_GET[value]%")." ORDER BY 2 LIMIT $x");for($r=1;($I=$G->fetch_row())&&$r<$x;$r++)echo"<a href='".h(ME."edit=".urlencode($R)."&where".urlencode("[".bracket_escape(idf_unescape($s))."]")."=".urlencode($I[0]))."'>".h($I[1])."</a><br>\n";if($I)echo"...\n";}exit;}else{page_header(lang(58),"",false);if(adminer()->homepage()){echo"<form action='' method='post'>\n","<p>".lang(100).": <input type='search' name='query' value='".h($_POST["query"])."'> <input type='submit' value='".lang(42)."'>\n";if($_POST["query"]!="")search_tables();echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^tables\[/);",""),'<th>'.lang(101),'<td>'.lang(102),"</thead>\n";foreach(table_status()as$R=>$I){$_=adminer()->tableName($I);if($_!=""){echo'<tr><td>'.checkbox("tables[]",$R,in_array($R,(array)$_POST["tables"],true)),"<th><a href='".h(ME).'select='.urlencode($R)."'>$_</a>";$X=format_number($I["Rows"]);echo"<td align='right'><a href='".h(ME."edit=").urlencode($R)."'>".($I["Engine"]=="InnoDB"&&$X?"~ $X":$X)."</a>";}}echo"</table>\n","</div>\n","</form>\n",script("tableCheck();");adminer()->pluginsLinks();}}page_footer();