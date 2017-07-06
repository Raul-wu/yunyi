<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 15/1/26
 * Time: PM3:17
 */

use Thrift\Protocol\TProtocol;
use Thrift\Type\TType;
use Thrift\Exception\TProtocolException;
use Thrift\Exception\TException;
use Thrift\Protocol\JSON\BaseContext;
use Thrift\Protocol\JSON\LookaheadReader;
use Thrift\Protocol\JSON\PairContext;
use Thrift\Protocol\JSON\ListContext;

/**
 * Class LJSONProtocol
 * @property Thrift\Transport\TTransport $trans_
 */
class LJSONProtocol extends TProtocol
{
	const COMMA = ',';
	const COLON = ':';
	const LBRACE = '{';
	const RBRACE = '}';
	const LBRACKET = '[';
	const RBRACKET = ']';
	const QUOTE = '"';
	const BACKSLASH = '\\';
	const ZERO = '0';
	const ESCSEQ = '\\';
	const DOUBLEESC = '__DOUBLE_ESCAPE_SEQUENCE__';

	protected function getTypeID($ch)
	{
		switch ($ch)
		{
			case self::QUOTE:
				$fieldType = TType::STRING;
				break;
			case self::LBRACE:
				$fieldType = TType::STRUCT;
				break;
			case self::LBRACKET:
				$fieldType = TType::LST;
				break;
			case 't':
			case 'f':
				$fieldType = TType::BOOL;
				break;
			case 'n':
				$fieldType = TType::VOID;
				break;
			default:
				$fieldType = TType::DOUBLE;
		}

		return $fieldType;
	}

	public $contextStack_ = array();
	public $context_;
	public $reader_;

	protected function pushContext($c)
	{
		array_push($this->contextStack_, $this->context_);
		$this->context_ = $c;
	}

	protected function popContext()
	{
		$this->context_ = array_pop($this->contextStack_);
	}

	public function __construct($trans)
	{
		parent::__construct($trans);
		$this->context_ = new BaseContext();
		$this->reader_ = new LLookaheadReader($this);
	}

	public function reset()
	{
		$this->contextStack_ = array();
		$this->context_ = new BaseContext();
		$this->reader_ = new LookaheadReader($this);
	}

	public function readJSONSyntaxChar($b)
	{
		$ch = $this->reader_->read();

		if (substr($ch, 0, 1) != $b)
		{
			throw new TProtocolException("Unexpected character: " . $ch, TProtocolException::INVALID_DATA);
		}
	}

	protected function writeJSONString($b)
	{
		$this->context_->write();

		if (is_numeric($b) && $this->context_->escapeNum())
		{
			$this->trans_->write(self::QUOTE);
		}

		$this->trans_->write(json_encode($b, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

		if (is_numeric($b) && $this->context_->escapeNum())
		{
			$this->trans_->write(self::QUOTE);
		}
	}

	protected function writeJSONInteger($num)
	{
		$this->context_->write();

		if ($this->context_->escapeNum())
		{
			$this->trans_->write(self::QUOTE);
		}

		$this->trans_->write($num);

		if ($this->context_->escapeNum())
		{
			$this->trans_->write(self::QUOTE);
		}
	}

	protected function writeJSONDouble($num)
	{
		$this->context_->write();

		if ($this->context_->escapeNum())
		{
			$this->trans_->write(self::QUOTE);
		}

		$this->trans_->write(json_encode($num, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

		if ($this->context_->escapeNum())
		{
			$this->trans_->write(self::QUOTE);
		}
	}

	protected function writeJSONBase64($data)
	{
		$this->context_->write();
		$this->trans_->write(self::QUOTE);
		$this->trans_->write(json_encode(base64_encode($data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		$this->trans_->write(self::QUOTE);
	}

	protected function writeJSONObjectStart()
	{
		$this->context_->write();
		$this->trans_->write(self::LBRACE);
		$this->pushContext(new PairContext($this));
	}

	protected function writeJSONObjectEnd()
	{
		$this->popContext();
		$this->trans_->write(self::RBRACE);
	}

	protected function writeJSONArrayStart()
	{
		$this->context_->write();
		$this->trans_->write(self::LBRACKET);
		$this->pushContext(new ListContext($this));
	}

	protected function writeJSONArrayEnd()
	{
		$this->popContext();
		$this->trans_->write(self::RBRACKET);
	}

	protected function readJSONString($skipContext)
	{
		if (!$skipContext)
		{
			$this->context_->read();
		}

		$jsonString = '';
		$lastChar = NULL;
		while (true)
		{
			$ch = $this->reader_->read();
			$jsonString .= $ch;
			if ($ch == self::QUOTE &&
				$lastChar !== NULL &&
				$lastChar !== self::ESCSEQ)
			{
				break;
			}
			if ($ch == self::ESCSEQ && $lastChar == self::ESCSEQ)
			{
				$lastChar = self::DOUBLEESC;
			}
			else
			{
				$lastChar = $ch;
			}
		}
		return json_decode($jsonString);
	}

	protected function isJSONNumeric($b)
	{
		switch ($b)
		{
			case '+':
			case '-':
			case '.':
			case '0':
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
			case 'E':
			case 'e':
				return true;
		}
		return false;
	}

	protected function readJSONNumericChars()
	{
		$strbld = array();

		while (true)
		{
			$ch = $this->reader_->peek();

			if (!$this->isJSONNumeric($ch))
			{
				break;
			}

			$strbld[] = $this->reader_->read();
		}

		return implode("", $strbld);
	}

	protected function readJSONInteger()
	{
		$this->context_->read();

		if ($this->context_->escapeNum())
		{
			$this->readJSONSyntaxChar(self::QUOTE);
		}

		$str = $this->readJSONNumericChars();

		if ($this->context_->escapeNum())
		{
			$this->readJSONSyntaxChar(self::QUOTE);
		}

		if (!is_numeric($str))
		{
			throw new TProtocolException("Invalid data in numeric: " . $str, TProtocolException::INVALID_DATA);
		}

		return intval($str);
	}

	/**
	 * Identical to readJSONInteger but without the final cast.
	 * Needed for proper handling of i64 on 32 bit machines.  Why a
	 * separate function?  So we don't have to force the rest of the
	 * use cases through the extra conditional.
	 */
	protected function readJSONIntegerAsString()
	{
		$this->context_->read();

		if ($this->context_->escapeNum())
		{
			$this->readJSONSyntaxChar(self::QUOTE);
		}

		$str = $this->readJSONNumericChars();

		if ($this->context_->escapeNum())
		{
			$this->readJSONSyntaxChar(self::QUOTE);
		}

		if (!is_numeric($str))
		{
			throw new TProtocolException("Invalid data in numeric: " . $str, TProtocolException::INVALID_DATA);
		}

		return $str;
	}

	protected function readJSONDouble()
	{
		$this->context_->read();

		if (substr($this->reader_->peek(), 0, 1) == self::QUOTE)
		{
			$arr = $this->readJSONString(true);

			if ($arr == "NaN")
			{
				return NAN;
			}
			else if ($arr == "Infinity")
			{
				return INF;
			}
			else if (!$this->context_->escapeNum())
			{
				throw new TProtocolException("Numeric data unexpectedly quoted " . $arr, TProtocolException::INVALID_DATA);
			}

			return floatval($arr);
		}
		else
		{
			if ($this->context_->escapeNum())
			{
				$this->readJSONSyntaxChar(self::QUOTE);
			}

			return floatval($this->readJSONNumericChars());
		}
	}

	protected function readJSONBase64()
	{
		$arr = $this->readJSONString(false);
		$data = base64_decode($arr, true);

		if ($data === false)
		{
			throw new TProtocolException("Invalid base64 data " . $arr, TProtocolException::INVALID_DATA);
		}

		return $data;
	}

	protected function readJSONObjectStart()
	{
		$this->context_->read();
		$this->readJSONSyntaxChar(self::LBRACE);
		$this->pushContext(new PairContext($this));
	}

	protected function readJSONObjectEnd()
	{
		$this->readJSONSyntaxChar(self::RBRACE);
		$this->popContext();
	}

	protected function readJSONArrayStart()
	{
		$this->context_->read();
		$this->readJSONSyntaxChar(self::LBRACKET);
		$this->pushContext(new ListContext($this));
	}

	protected function readJSONArrayEnd()
	{
		$this->readJSONSyntaxChar(self::RBRACKET);
		$this->popContext();
	}

	/**
	 * Writes the message header
	 * @param string $name Function name
	 * @param int $type    message type TMessageType::CALL or TMessageType::REPLY
	 * @param int $seqid   The sequence id of this message
	 */
	public function writeMessageBegin($name, $type, $seqid)
	{
		// TODO: Implement writeMessageBegin() method.
	}

	/**
	 * Close the message
	 */
	public function writeMessageEnd()
	{
		// TODO: Implement writeMessageEnd() method.
	}

	/**
	 * Writes a struct header.
	 * @param string $name Struct name
	 * @throws TException on write error
	 * @return int How many bytes written
	 */
	public function writeStructBegin($name)
	{
		$this->writeJSONObjectStart();
	}

	/**
	 * Close a struct.
	 * @throws TException on write error
	 * @return int How many bytes written
	 */
	public function writeStructEnd()
	{
		$this->writeJSONObjectEnd();
	}

	public function writeFieldBegin($fieldName, $fieldType, $fieldId)
	{
		$this->writeJSONString($fieldName);
	}

	public function writeFieldEnd()
	{
		// do nothing
	}

	public function writeFieldStop()
	{
		// do nothing
	}

	public function writeMapBegin($keyType, $valType, $size)
	{
		// not support
		throw new TProtocolException("this protocol does not support map (yet).", TProtocolException::UNKNOWN);
	}

	public function writeMapEnd()
	{
		// not support
		throw new TProtocolException("this protocol does not support map (yet).", TProtocolException::UNKNOWN);
	}

	public function writeListBegin($elemType, $size)
	{
		$this->writeJSONArrayStart();
	}

	public function writeListEnd()
	{
		$this->writeJSONArrayEnd();
	}

	public function writeSetBegin($elemType, $size)
	{
		$this->writeJSONArrayStart();
	}

	public function writeSetEnd()
	{
		$this->writeJSONArrayEnd();
	}

	public function writeBool($bool)
	{
		$this->writeJSONInteger($bool ? 1 : 0);
	}

	public function writeByte($byte)
	{
		$this->writeJSONInteger($byte);
	}

	public function writeI16($i16)
	{
		$this->writeJSONInteger($i16);
	}

	public function writeI32($i32)
	{
		$this->writeJSONInteger($i32);
	}

	public function writeI64($i64)
	{
		$this->writeJSONInteger($i64);
	}

	public function writeDouble($dub)
	{
		$this->writeJSONDouble($dub);
	}

	public function writeString($str)
	{
		$this->writeJSONString($str);
	}

	/**
	 * Reads the message header
	 * @param string $name Function name
	 * @param int $type    message type TMessageType::CALL or TMessageType::REPLY
	 * @param int $seqid The sequence id of this message
	 */
	public function readMessageBegin(&$name, &$type, &$seqid)
	{
		// TODO: Implement readMessageBegin() method.
	}

	/**
	 * Read the close of message
	 */
	public function readMessageEnd()
	{
		// TODO: Implement readMessageEnd() method.
	}

	public function readStructBegin(&$name)
	{
		$this->readJSONObjectStart();
		return 0;
	}

	public function readStructEnd()
	{
		$this->readJSONObjectEnd();
	}

	/**
	 * Thrift的field id是int16_t, 正数是手工指定, 负数是系统保留
	 * @param $str
	 * @return int
	 */
	protected function BSD_checksum_modified($str)
	{
		$checksum = 0; /* The checksum mod 2^15. */

		$len = strlen($str);
		for ($i = 0; $i < $len; $i++)
		{
			$checksum = ($checksum >> 1) + (($checksum & 1) << 14);
			$checksum += ord($str[$i]);
			$checksum &= 0x7fff; /* Keep it within bounds. */
		}

		return $checksum;
	}

	public function readFieldBegin(&$name, &$fieldType, &$fieldId)
	{
		$ch = $this->reader_->peek();
		$name = "";

		if (substr($ch, 0, 1) == self::RBRACE)
		{
			$fieldType = TType::STOP;
		}
		else
		{
			$name = $this->readJSONString(false);
			$fieldId = $this->BSD_checksum_modified($name);

			$fieldType = $this->getTypeID($this->reader_->peek(2));
		}
	}

	public function readFieldEnd()
	{
		// do nothing
	}

	public function readMapBegin(&$keyType, &$valType, &$size)
	{
		// not support
		throw new TProtocolException("this protocol does not support map (yet).", TProtocolException::UNKNOWN);
	}

	public function readMapEnd()
	{
		// not support
		throw new TProtocolException("this protocol does not support map (yet).", TProtocolException::UNKNOWN);
	}

	public function readListBegin(&$elemType, &$size)
	{
		$this->readJSONArrayStart();
		$elemType = $this->getTypeID($this->reader_->peek(1));
		$size = $this->reader_->peekSize();
		return true;
	}

	public function readListEnd()
	{
		$this->readJSONArrayEnd();
	}

	public function readSetBegin(&$elemType, &$size)
	{
		$this->readJSONArrayStart();
		$elemType = $this->getTypeID($this->reader_->peek(1));
		$size = $this->reader_->peekSize();
		return true;
	}

	public function readSetEnd()
	{
		$this->readJSONArrayEnd();
	}

	public function readBool(&$bool)
	{
		$bool = $this->readJSONInteger() == 0 ? false : true;
		return true;
	}

	public function readByte(&$byte)
	{
		$byte = $this->readJSONInteger();
		return true;
	}

	public function readI16(&$i16)
	{
		$i16 = $this->readJSONInteger();
		return true;
	}

	public function readI32(&$i32)
	{
		$i32 = $this->readJSONInteger();
		return true;
	}

	public function readI64(&$i64)
	{
		if (PHP_INT_SIZE === 4)
		{
			$i64 = $this->readJSONIntegerAsString();
		}
		else
		{
			$i64 = $this->readJSONInteger();
		}
		return true;
	}

	public function readDouble(&$dub)
	{
		$dub = $this->readJSONDouble();
		return true;
	}

	public function readString(&$str)
	{
		$str = $this->readJSONString(false);
		return true;
	}
}