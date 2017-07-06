<?php

/**
 * Created by PhpStorm.
 * User: sangechen
 * Date: 14-4-25
 * Time: 上午9:18
 */
class LAESHelper
{

	const CIPHER_ALGORITHM = "AES-256-CBC"; //"AES/CBC/PKCS5Padding";
	const IV = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

	private $method;
	private $password;
	private $iv;

	public static function getInstance()
	{
		return new self();
	}

	public function __construct()
	{
		$this->method = self::CIPHER_ALGORITHM;
		$this->iv = self::IV;
	}

	/**
	 * @param $password string 32 bytes key
	 * @return $this
	 */
	public function initKey($password)
	{
		$this->password = $password;

		return $this;
	}

	public function initCipher($method, $iv = null)
	{
		$this->method = $method;
		if ($iv !== null)
		{
			$this->iv = $iv;
		}

		return $this;
	}

	/**
	 * @param $plaintext string the data to be encrypted.
	 * @param bool $raw
	 * @return string the encrypted base64 string on success or FALSE on failure.
	 */
	public function encrypt($plaintext, $raw = false)
	{
		return openssl_encrypt($plaintext, $this->method, $this->password, $raw, $this->iv);
	}

	/**
	 * @param $encryptedBase64Str string the base64 string to be decrypted.
	 * @param bool $raw
	 * @return string The decrypted string on success or FALSE on failure.
	 */
	public function decrypt($encryptedBase64Str, $raw = false)
	{
		return openssl_decrypt($encryptedBase64Str, $this->method, $this->password, $raw, $this->iv);
	}

}