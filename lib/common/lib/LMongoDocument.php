<?php
abstract class LMongoDocument extends EMongoDocument
{
    protected static $cipherKey = array();
    protected $enableCipher = true;

	protected $_inc = null;
	protected $_intFields = array();
	protected $_floatFields = array();
    protected $_orgAttributes = array();
    protected $_modifyData = array();
    protected $_isChange = false;

	public $updateTime;
	public $createTime;

	protected function beforeSave()
	{
		if ($this->getIsNewRecord())
		{
			$this->createTime = time();
		}


        $this->updateTime = time();

		$this->modifyIncKey();
		$this->modifyIntFields();
        $this->encrypt();

		return parent::beforeSave();
	}

    protected function afterSave()
    {
        $this->decrypt();
        parent::afterSave();
    }

    protected function isChange()
    {
        $attributes = $this->getAttributes();
        foreach ($attributes as $attribute => $value)
        {
            if (!isset($this->_orgAttributes[$attribute]) || $this->_orgAttributes[$attribute] !== $value)
            {
                if(in_array($attribute, array('updateTime')))
                {
                    continue;
                }
                $this->_modifyData[$attribute] = $value;
            }
        }

        if(count($this->_modifyData) == 0)
        {
            return false;
        }

        return true;
    }




    protected function afterFind()
    {
        $this->_orgAttributes = $this->getAttributes();
        $this->decrypt();
        parent::afterFind();
    }

	public function setIncKeys($inc)
	{
		return $this->_inc = $inc;
	}

	public function getIntFields()
	{
		return $this->_intFields;
	}

	public function getFloatFields()
	{
		return $this->_floatFields;
	}

	public function setIntFields($fields)
	{
		$this->_intFields = array_merge($fields, $this->_intFields);
	}

	public function setFloatFields($fields)
	{
		$this->_floatFields = array_merge($fields, $this->_floatFields);
	}

	public function modifyIncKey()
	{
		if ($this->_inc)
		{
			foreach ($this->attributes as $key => $val)
			{
				if ($key == $this->_inc)
				{
					if (!$val)
					{
						$inc = new LAIncModel();
						$inc->name = $this->getCollectionName();
						do
						{
							$int = $inc->findAndModify();
							$criteria = new EMongoCriteria();
							$criteria->addCond($key, '==', $int);
						}
							//如果该自增字段是已经存在的，则再取一次自增值
						while (static::model()->find($criteria));

						if ($int)
						{
							$this->$key = $int;
						}
					}
				}
			}
		}
	}

	public function modifyIntFields()
	{
		$attributes = $this->attributes;
		$intFields = $this->getIntFields();
		$floatFields = $this->getFloatFields();
		foreach ($attributes as $attribute => $val)
		{
			if (in_array($attribute, $intFields))
			{
				$this->$attribute = intval($this->$attribute);
			}

			if (in_array($attribute, $floatFields))
			{
				$this->$attribute = floatval($this->$attribute);
			}
		}
	}

    protected function encrypt()
    {
        return $this->processCipher();
    }

    protected function decrypt()
    {
        return $this->processCipher(true);
    }

    protected function processCipher($decrypt = false)
    {
        if ($this->enableCipher)
        {
            foreach ($this->getAttributes() as $name => $value)
            {
                if ($value)
                {
                    if ($decrypt)
                    {
                        $this->$name = static::decryptAttribute($name, $value);
                    }
                    else
                    {
                        $this->$name = static::encryptAttribute($name, $value);
                    }
                }
            }
        }
        return true;
    }

    public static function encryptAttribute($name, $plainText)
    {
        $spec = static::getSpec($name);
        if ($spec && $plainText)
        {
            if (function_exists('yj_encrypt'))
            {
                return yj_encrypt($spec, $plainText);
            }
            else
            {
                return LAESHelper::getInstance()->initKey($spec)->encrypt($plainText);
            }
        }
        else
        {
            return $plainText;
        }
    }

    public static function decryptAttribute($name, $cipherText)
    {
        $spec = static::getSpec($name);
        if ($spec && $cipherText)
        {
            if (function_exists('yj_decrypt'))
            {
                return yj_decrypt($spec, $cipherText);
            }
            else
            {
                return LAESHelper::getInstance()->initKey($spec)->decrypt($cipherText);
            }
        }
        else
        {
            return $cipherText;
        }
    }

    protected static function getSpec($attribute)
    {
        if (array_key_exists($attribute, static::$cipherKey))
        {
            return static::$cipherKey[$attribute];
        }
        else
        {
            return '';
        }
    }

	public function disconnect()
	{
		$emongoDb = parent::getMongoDBComponent();
		if ($emongoDb instanceof LMongoDB)
		{
			parent::$_collections[$this->getCollectionName()] = null;
			$emongoDb->disConnection();
			parent::$_emongoDb = null;
		}
	}
}