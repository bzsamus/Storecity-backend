<?php
   include_once("knightlover.php");
   include_once("libs/oauth.php");
 
   class mixiSignatureMethod extends OAuthSignatureMethod_RSA_SHA1 {
     protected function fetch_public_cert(&$request) {
     $cert = <<<EOD
-----BEGIN CERTIFICATE-----
MIICdzCCAeCgAwIBAgIJANCWpLIspxwbMA0GCSqGSIb3DQEBBQUAMDIxCzAJBgNV
BAYTAkpQMREwDwYDVQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcDAeFw0x
MDAzMjMwODE1NTlaFw0xMjAzMjIwODE1NTlaMDIxCzAJBgNVBAYTAkpQMREwDwYD
VQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcDCBnzANBgkqhkiG9w0BAQEF
AAOBjQAwgYkCgYEAtbq5Rns5IEktXldZ+37Fjlavnuc4JuwrD4F+4NQwVwVtR5yw
Vg10EanXWSGIAbUFx6hlppYOb0x/3PBMG80643LmXSJmvv4ViRUBl2Ys9Ie2L/D9
KVQXDWgJjxBGqo5MO6rA/Ip78kbiNbIQJUIJtbuJZWL3LMVe6mpIO2SUi1UCAwEA
AaOBlDCBkTAdBgNVHQ4EFgQU8bp8/6lmt5L8em6dZyoGciUUmuUwYgYDVR0jBFsw
WYAU8bp8/6lmt5L8em6dZyoGciUUmuWhNqQ0MDIxCzAJBgNVBAYTAkpQMREwDwYD
VQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcIIJANCWpLIspxwbMAwGA1Ud
EwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADgYEApalbBgXxnLJW8fM6W7E7GAE4QZbE
lvYqvtQSxwacGYoqF2FW1zrBkmTB12LTddFU01pqDaeels3Ru5TNOnTIJemFWW0D
viKtu9GqsrOye6AZR+XA5Iy/vq3EV1TCGuDNmhJaHAiLeYuLbEqmvH7/l9xNsafH
IpqnsHwF1pm0bTY=
-----END CERTIFICATE-----
EOD;
     return $cert;
     }

     protected function fetch_private_cert(&$request){
     }
   }
 
  class yabageSignatureMethod extends OAuthSignatureMethod_RSA_SHA1 {
     protected function fetch_public_cert(&$request) {
     $cert = <<<EOD
-----BEGIN CERTIFICATE-----
MIICOTCCAaKgAwIBAgIJAK3cE459+jV9MA0GCSqGSIb3DQEBBAUAMB4xHDAaBgNV
BAMTE3NiLm1iZ2EtcGxhdGZvcm0uanAwHhcNMTAwODI1MDkzNzI4WhcNMTEwODI1
MDkzNzI4WjAeMRwwGgYDVQQDExNzYi5tYmdhLXBsYXRmb3JtLmpwMIGfMA0GCSqG
SIb3DQEBAQUAA4GNADCBiQKBgQDZ8xJKX1rPli72IF2L+tRV9Tk1c2kRixEEwzxR
T2bz37w/8XJQaMVxtFQMCYqquZUmHDss4JgF/prE4HGnX0j6x9MZUrt0k2VzDINm
Y+F61QJZCLqqy5MBxR9Dyu87DucPf7WsP3C1EMrfB8c29qVT7is+pMuYDowmsPql
eJ4pswIDAQABo38wfTAdBgNVHQ4EFgQUtNIqfC+B1PmcIhDmIA8+QxALZU4wTgYD
VR0jBEcwRYAUtNIqfC+B1PmcIhDmIA8+QxALZU6hIqQgMB4xHDAaBgNVBAMTE3Ni
Lm1iZ2EtcGxhdGZvcm0uanCCCQCt3BOOffo1fTAMBgNVHRMEBTADAQH/MA0GCSqG
SIb3DQEBBAUAA4GBALN/bYV+Vbr2z4edz2+hogP+PwW5IgV5sCohwcMAVVkmA9qs
RVPDSjm6E5e05kiCNAQQJpu2/d/i1xDuSjPpNMGaawapzNVbXh3xYwNkD8wrs1kM
tjKaDjOi4YhwIlhingNhsrozKW6jHBY/RXi/oRmAKsByIx72I4yFjHwZuXk+
-----END CERTIFICATE-----
EOD;
     return $cert;
     }

     protected function fetch_private_cert(&$request){
     }
   }

class nateSignatureMethod extends OAuthSignatureMethod_RSA_SHA1 {
     protected function fetch_public_cert(&$request) {
     $cert = <<<EOD
-----BEGIN CERTIFICATE-----
MIIDEjCCAnugAwIBAgIBADANBgkqhkiG9w0BAQUFADBqMQswCQYDVQQGEwJLUjEO
MAwGA1UECBMFS29yZWExDjAMBgNVBAcTBVNlb3VsMRkwFwYDVQQKExBTa0NvbW11
bmljYXRpb25zMQ0wCwYDVQQLEwRHU0RUMREwDwYDVQQDEwhuYXRlLmNvbTAeFw0w
OTA2MDQwMzExMzFaFw0xMDA2MDQwMzExMzFaMGoxCzAJBgNVBAYTAktSMQ4wDAYD
VQQIEwVLb3JlYTEOMAwGA1UEBxMFU2VvdWwxGTAXBgNVBAoTEFNrQ29tbXVuaWNh
dGlvbnMxDTALBgNVBAsTBEdTRFQxETAPBgNVBAMTCG5hdGUuY29tMIGfMA0GCSqG
SIb3DQEBAQUAA4GNADCBiQKBgQDOqs9CIn3iUNlYPbpyz5VQitoEW85YAQHm0AUz
VoHgFTc3uyefPmqEcfk8idWdWCOkzoR2SM8jmQCSQThyTH+LlhPvPK7TFQgapI0s
1KHHS2nUNpEm5X26IqBthxdPccYkQ4FPaOnZBgxbyUeEVEczF4H0cJ1Hwtnye+2B
X1MAGQIDAQABo4HHMIHEMB0GA1UdDgQWBBQsNFgi61VyYF2TfnQuJbVDJ6GNEDCB
lAYDVR0jBIGMMIGJgBQsNFgi61VyYF2TfnQuJbVDJ6GNEKFupGwwajELMAkGA1UE
BhMCS1IxDjAMBgNVBAgTBUtvcmVhMQ4wDAYDVQQHEwVTZW91bDEZMBcGA1UEChMQ
U2tDb21tdW5pY2F0aW9uczENMAsGA1UECxMER1NEVDERMA8GA1UEAxMIbmF0ZS5j
b22CAQAwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOBgQAbsSxeoWiKgzub
T613jL1MGqOjZ9psiYs+mG7mYGxRY0Ol0M5G8Iz6tx1R7wir5cUNFJt0QRENJ0lH
nAJ7lUrrxMs9HblOILdARKjkIHNhzp6pl6YKOstxxJaNPXma6U9BV+X1Gj7fz/fj
T3h1PXlrMldLhw6Yhn+KO/vdd/m4HQ==
-----END CERTIFICATE-----
EOD;
     return $cert;
     }

     protected function fetch_private_cert(&$request){
     }
   }

   //Build a request object from the current request
   $request = OAuthRequest::from_request(null, null, array_merge($_GET, $_POST));
 
   //Initialize the new signature method
   $method = knightlover::conf()->platform.'SignatureMethod';
   $signature_method = new $method;
 
   //Check the request signature
   @$signature_valid = $signature_method->check_signature($request, null, null, $_GET["oauth_signature"]);
 
   //Build the output object
   $payload = array();
   if ($signature_valid == true) {
	$uid = $request->get_parameter('viewer_id');
        $token = md5($uid.$request->get_parameter('oauth_nonce')).'.'.str_replace('+','',$request->get_parameter('oauth_signature'));
	// save token to match uid
	$user = array();
	if(knightlover::conf()->platform == 'yabage'){
		$uid = str_replace('sb.mbga.jp:','',$uid);  //strip overhead before uid for yabage
	}
	$user['uid'] = $uid;
	$user['gender'] = strtolower($request->get_parameter('gender'));
	$user['username'] = $request->get_parameter('name');
	$user['profilePic'] = $request->get_parameter('photo');
	knightlover::cache()->set($token,$user['uid'],0);
	$platform = knightlover::conf()->platform;
	knightlover::cache()->set($platform.'_'.$user['uid'],$user,0);
	$payload['token'] = $token;
	$payload['validated'] = 'ok';
   } else {
	$payload['validated'] = 'no';
   }
print(json_encode($payload));
?>
