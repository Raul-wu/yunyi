<?php
/**
 * Created by PhpStorm.
 * User: WorkPC
 * Date: 2015/11/11
 * Time: 17:41
 */
class LCaptchaAction extends CCaptchaAction
{

	const SESSION_VAR_PREFIX='Yii.LCaptchaAction.';
	public function run()
	{
		if(isset($_GET[self::REFRESH_GET_VAR]))  // AJAX request for regenerating code
		{
			$answer = $this->getVerifyCode(true);
			echo CJSON::encode(array(
				'hash1'=>$this->generateValidationHash($answer),
				'hash2'=>$this->generateValidationHash(strtolower($answer)),
				// we add a random 'v' parameter so that FireFox can refresh the image
				// when src attribute of image tag is changed
				'url'=>$this->getController()->createUrl($this->getId(),array('v' => uniqid())),
			));
		}
		else{
			$data = $this->getVerifyCode(false, 0);
			$this->renderImage($data['pic']);
		}

		Yii::app()->end();
	}

	public function getVerifyCode($regenerate=false, $returnFlag = 1)
	{
		if($this->fixedVerifyCode !== null)
			return $this->fixedVerifyCode;

		$session = Yii::app()->session;
		$session->open();
		$name = $this->getSessionKey();
		if($session[$name] === null || $regenerate)
		{
			$session[$name] = $this->generateVerifyCode();
			$session[$name . 'count'] = 1;
		}
		if ($returnFlag == 1)
		{
			return $session[$name]['answer'];
		}
		return $session[$name];
	}

	public function validate($input,$caseSensitive)
	{
		$answer = $this->getVerifyCode(false);
		$valid = $caseSensitive ? ($input === $answer) : strcasecmp($input, $answer)===0;
		$session = Yii::app()->session;
		$session->open();
		$name = $this->getSessionKey() . 'count';
		$session[$name] = $session[$name] + 1;
		if($session[$name] > $this->testLimit && $this->testLimit > 0)
			$this->getVerifyCode(true);
		return $valid;
	}


	protected function generateVerifyCode()
	{
		$operations = array('+', '-');
		$largeNum = mt_rand(1000, 9999);
		$smallNum = mt_rand(0, 9);
		$operation = $operations[mt_rand(0, 1)];
		if ($operation == '+')
		{
			$answer = $largeNum;
			$random = mt_rand(0, 9);
			//大数和小数在加号两边的位置随机
			if ($random >= 5)
			{
				$firstNum = ($largeNum - $smallNum);
				$secondNum = $smallNum;
			}
			else
			{
				$firstNum = $smallNum;
				$secondNum = ($largeNum - $smallNum);
			}
		}
		else
		{
			$answer = $largeNum - $smallNum;
			$firstNum = $largeNum;
			$secondNum = $smallNum;
		}

		$code = $firstNum.$operation.$secondNum."=";
		return array(
			'answer'	=>	$answer,
			'pic'	=>	$code);
	}

	/**
	 * Renders the CAPTCHA image based on the code using library specified in the {@link $backend} property.
	 * @param string $code the verification code
	 */
	protected function renderImage($code)
	{
		if($this->backend===null && CCaptcha::checkRequirements('imagick') || $this->backend==='imagick')
			$this->renderImageImagick($code);
		else if($this->backend===null && CCaptcha::checkRequirements('gd') || $this->backend==='gd')
			$this->renderImageGD($code);
	}

	/**
	 * Returns the session variable name used to store verification code.
	 * @return string the session variable name
	 */
	protected function getSessionKey()
	{
		return self::SESSION_VAR_PREFIX . Yii::app()->getId() . '.' . $this->getController()->getUniqueId() . '.' . $this->getId();
	}
}