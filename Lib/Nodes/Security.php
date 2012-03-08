<?php
namespace Nodes;

/**
* Encryption and decryption methods
*
* Used for communication between the Platform and a backend
*
* @see https://developers.facebook.com/docs/authentication/canvas/encryption_proposal/
* @see https://github.com/ptarjan/crypto-request-examples
*/
class Security {

	public static function encrypt($secret, $data) {
		// wrap data inside payload if we are encrypting
		$cipher = MCRYPT_RIJNDAEL_128;
		$mode	= MCRYPT_MODE_CBC;

		$iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher, $mode), MCRYPT_DEV_URANDOM);
		$data = array(
			'payload' => static::base64_url_encode(mcrypt_encrypt($cipher, $secret, json_encode($data), $mode, $iv)),
			'iv'	  => static::base64_url_encode($iv)
		);

		// always present, and always at the top level
		$data['algorithm'] = 'AES-256-CBC HMAC-SHA256';
		$data['issued_at'] = time();

		// sign it
		$payload	= static::base64_url_encode(json_encode($data));
		$sig		= static::base64_url_encode(hash_hmac('sha256', $payload, $secret, $raw=true));

		return $sig . '.' . $payload;
	}

	protected static function base64_url_encode($input) {
		$str = strtr(base64_encode($input), '+/=', '-_.');
		$str = str_replace('.', '', $str); // remove padding
		return $str;
	}

	public static function decrypt($secret, $input, $max_age = 3600) {
		list($encoded_sig, $encoded_envelope) = explode('.', $input, 2);
		$envelope	= json_decode(static::base64_url_decode($encoded_envelope), true);
		$algorithm	= $envelope['algorithm'];

		if ($algorithm != 'AES-256-CBC HMAC-SHA256' && $algorithm != 'HMAC-SHA256') {
			throw new Exception('Invalid request. (Unsupported algorithm.)');
		}

		if ($envelope['issued_at'] < time() - $max_age) {
			throw new Exception('Invalid request. (Too old.)');
		}

		if (static::base64_url_decode($encoded_sig) != hash_hmac('sha256', $encoded_envelope, $secret, $raw = true)) {
			throw new Exception('Invalid request. (Invalid signature.)');
		}

		// otherwise, decrypt the payload
		return json_decode(trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $secret, static::base64_url_decode($envelope['payload']), MCRYPT_MODE_CBC, static::base64_url_decode($envelope['iv']))), true);
	}

	protected static function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}
}