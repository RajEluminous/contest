<?php
############# KEY #############

$secretCode = "LLESSCONTESTLEADS";
$secretKey = "Lim!tLe$#F@ct0r";
$cipherString = safeEncrypt($secretCode,$secretKey);
###############################

$data = ['cb_account'=>'GODFREQ', 'affiliate_id'=>'ASTRAL43'];

// Change the domain name in production.
$url = 'http://127.0.0.1:8000/api/savelead';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("auth_token:$cipherString"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

print_r($result);

############### FUNCTIONS ####################

	/**
     * Encrypt a message
     *
     * @param string $message - message to encrypt
     * @param string $key - encryption key
     * @return string
     * @throws RangeException
     */
    function safeEncrypt(string $plaintext, string $key): string
    {
         $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
         $iv = openssl_random_pseudo_bytes($ivlen);
         $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
         $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
         $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
        return $ciphertext;
    }

?>
