<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 15/2/2
 * Time: PM4:37
 */

class LLookaheadReader
{
	protected $data_ = '';
	protected $p_;

	public function __construct($p)
	{
		$this->p_ = $p;
	}

	public function read()
	{
		if (strlen($this->data_) ==  0)
		{
			$this->data_ = $this->p_->getTransport()->readAll(1);
		}

		$ret = substr($this->data_, 0, 1);
		$this->data_ = substr($this->data_, 1);
		return $ret;
	}

	public function peek($pos = 1)
	{
		$len = strlen($this->data_);
		if ($len < $pos)
		{
			$this->data_ .= $this->p_->getTransport()->readAll($pos - $len);
		}

		return substr($this->data_, $pos - 1, 1);
	}

	public function peekSize()
	{
		$pos = 0;
		$size = 0;
		$depth = 0;
		$inQuote = false;
		$lastCh = '';
		$quote = 0;
		while (($ch = $this->peek(++$pos)) !== false)
		{
			if ($depth == 0 && $ch == LJSONProtocol::RBRACKET && !$inQuote)
			{
				break;
			}

			if ($pos == 0)
			{
				$pos++;
			}

			if (!$inQuote)
			{
				switch ($ch)
				{
					case LJSONProtocol::LBRACE:
					case LJSONProtocol::LBRACKET:
						$depth++;
						break;
					case LJSONProtocol::RBRACE:
					case LJSONProtocol::RBRACKET:
						$depth--;
						break;
					case LJSONProtocol::QUOTE:
						$inQuote = true;
						break;
					case LJSONProtocol::COMMA:
						if ($depth == 0)
						{
							$size++;
						}
						break;
				}

				if ($quote == 0 && $depth == 0)
				{
					$quote++;
				}
			}
			else
			{
				if ($ch == LJSONProtocol::QUOTE && $lastCh != LJSONProtocol::ESCSEQ)
				{
					$inQuote = false;
				}
			}

			if ($ch == LJSONProtocol::ESCSEQ && $lastCh == LJSONProtocol::ESCSEQ)
			{
				$lastCh = LJSONProtocol::DOUBLEESC;
			}
			else
			{
				$lastCh = $ch;
			}
		}
		return $size + $quote;
	}
}