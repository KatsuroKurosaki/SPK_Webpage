<pre><?php

require_once '../class/GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();

$secret = $ga->createSecret();

echo "Secret is: ".$secret."\n\n";

$qrCodeUrl = $ga->getQRCodeGoogleUrl('S.P.K. Staff', $secret,"KiruhiShay");
echo "Google Charts URL for the QR-Code: ".$qrCodeUrl."\n\n";


$oneCode = $ga->getCode($secret);
//$oneCode = "029188";
echo "Checking Code '$oneCode' and Secret '$secret':\n";

$checkResult = $ga->verifyCode($secret, $oneCode, 2);    // 2 = 2*30sec clock tolerance
if ($checkResult) {
    echo 'OK';
} else {
    echo 'FAILED';
}
?></pre>
