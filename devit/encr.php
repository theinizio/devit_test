<?php



function char_encode($str){
	
	$pub=file_get_contents(__DIR__."/public.pem");
	$pk  = openssl_get_publickey($pub);
	
	$encrypted="";
	
	openssl_public_encrypt($str, $encrypted, $pk);
	
	return base64_encode($encrypted);
	
}




function char_decode($str){
	
	
	$key=file_get_contents(__DIR__."/private.pem");
	$private_key  = openssl_pkey_get_private($key, "theinizio");
	
	$str = base64_decode($str);
	openssl_private_decrypt($str, $out, $private_key);
	
	
	return $out;
}
