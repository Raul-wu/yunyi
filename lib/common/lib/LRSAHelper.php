<?php

/**
 * Created by PhpStorm.
 * User: sangechen
 * Date: 14-4-25
 * Time: 上午10:08
 */
class LRSAHelper
{
	const KEY_ALGORITHM = "RSA";
	const SIGNATURE_ALGORITHM = OPENSSL_ALGO_SHA1; //"SHA1withRSA";
	const CIPHER_ALGORITHM = OPENSSL_PKCS1_PADDING; //"RSA/ECB/PKCS1Padding"; //加密block需要预留11字节
	const KEYBIT = 2048;
	const RESERVEBYTES = 11;

	private $signature_alg;
	private $padding;

	private $localPrivKey;
	private $peerPubKey;
	private $encryptBlock;
	private $decryptBlock;

	public function __construct()
	{
		$this->signature_alg = self::SIGNATURE_ALGORITHM;
		$this->padding = self::CIPHER_ALGORITHM;

		$this->localPrivKey = null;
		$this->peerPubKey = null;
		$this->decryptBlock = self::KEYBIT / 8; //256 bytes
		$this->encryptBlock = $this->decryptBlock - self::RESERVEBYTES; //245 bytes
	}

	/**
	 * 初始化自己的私钥,对方的公钥以及密钥长度.
	 * `openssl genrsa -out rsa_2048.key 2048` #指定生成的密钥的位数: 2048
	 * `openssl pkcs8 -topk8 -inform PEM -in rsa_2048.key -outform PEM -nocrypt -out pkcs8.txt` #for Java 转换成PKCS#8编码
	 * `openssl rsa -in rsa_2048.key -pubout -out rsa_2048_pub.key` #导出pubkey
	 * @param $localPrivKeyPEMStr mixed a key, returned by openssl_get_publickey() or a PEM formatted key, example, "-----BEGIN PUBLIC KEY----- MIIBCgK..."
	 * @param $peerPubKeyPEMStr mixed a key, returned by openssl_get_publickey() or a PEM formatted key, example, "-----BEGIN PUBLIC KEY----- MIIBCgK..."
	 * @param $keySize int 密钥长度, 一般2048
	 */
	public function initKey($localPrivKeyPEMStr, $peerPubKeyPEMStr, $keySize)
	{
		$this->localPrivKey = $localPrivKeyPEMStr;
		$this->peerPubKey = $peerPubKeyPEMStr;
		$this->decryptBlock = $keySize / 8;
		$this->encryptBlock = $this->decryptBlock - self::RESERVEBYTES;
	}

	public function initSignature($algorithm)
	{
		$this->signature_alg = $algorithm;
	}

	public function sign($plaintext)
	{
		$signature = '';
		if (openssl_sign($plaintext, $signature, $this->localPrivKey, $this->signature_alg))
		{
			return base64_encode($signature);
		}
		else
		{
			return '';
		}
	}

	public function verify($plaintext, $signBase64Str)
	{
		$signature = base64_decode($signBase64Str);
		$ret = openssl_verify($plaintext, $signature, $this->peerPubKey, $this->signature_alg);
		if ($ret === 1)
		{
			return true;
		}
		else
		{
			//($ret == -1)表示出错
			return false;
		}
	}

	public function initCipher($padding)
	{
		$this->padding = $padding;
	}

	public function encrypt($plaintext)
	{
		//计算分段加密的block数 (向上取整)
		$dataLength = strlen($plaintext);
		$nBlock = ($dataLength / $this->encryptBlock);
		if (($dataLength % $this->encryptBlock) != 0) //余数非0block数再加1
		{
			$nBlock = $nBlock + 1;
		}
		//for debug. printf ("encryptBlock: %d/%d ~ %d\n", data.length, encryptBlock, nBlock);

		//输出buffer, 大小为nBlock个decryptBlock
		$outBuf = '';

		//分段加密
		for ($offset = 0; $offset < $dataLength; $offset += $this->encryptBlock)
		{
			//block大小: encryptBlock 或 剩余字节数
			$data = substr($plaintext, $offset, $this->encryptBlock);

			//得到分段加密结果
			$encryptedBlock = '';
			if (!openssl_public_encrypt($data, $encryptedBlock, $this->peerPubKey, $this->padding))
			{
				return ''; //出错返回空
			}
			else
			{
				//追加结果到输出buffer中
				$outBuf .= $encryptedBlock;
			}
		}

		return base64_encode($outBuf); //ciphertext
	}

	public function decrypt($encryptedBase64Str)
	{
		$ciphertext = base64_decode($encryptedBase64Str);

		//计算分段解密的block数 (理论上应该能整除)
		$dataLength = strlen($ciphertext);
		$nBlock = ($dataLength / $this->decryptBlock);
		//for debug. printf("decryptBlock: %d/%d ~ %d\n", data.length, decryptBlock, nBlock);

		//输出buffer, , 大小为nBlock个encryptBlock
		$outBuf = '';

		//分段解密
		for ($offset = 0; $offset < $dataLength; $offset += $this->decryptBlock)
		{
			//block大小: decryptBlock 或 剩余字节数
			$data = substr($ciphertext, $offset, $this->decryptBlock);

			//得到分段解密结果
			$decryptedBlock = '';
			if (!openssl_private_decrypt($data, $decryptedBlock, $this->localPrivKey, $this->padding))
			{
				return ''; //出错返回空
			}
			else
			{
				//追加结果到输出buffer中
				$outBuf .= $decryptedBlock;
			}
		}

		return $outBuf;
	}

	public function setLocalPrivKey($localPrivKey)
	{
		$this->localPrivKey = $localPrivKey;
	}

	public function getLocalPrivKey()
	{
		return $this->localPrivKey;
	}

	public function setPeerPubKey($peerPubKey)
	{
		$this->peerPubKey = $peerPubKey;
	}

	public function getPeerPubKey()
	{
		return $this->peerPubKey;
	}
}