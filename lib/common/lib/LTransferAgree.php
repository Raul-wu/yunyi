<?php
class LTransferAgree extends LTcPDFBase{
	protected static $_instance = null;

	const FONTFAMILY = 'simsun';
	const FONTSIZE = 11;
	const MARGINTOP = 23;
	const MARGINLEFT = 12;
	const MARGINRIGHT = 12;
	const MARGINBOTTOM = 15;
	const LOGOHEIGHT = 18;
	const AUTHOR = '运营清算系统';
	const HEADERBORDERCOLOR = "#505050";
	const LOGO = 'logo.png';
	const WATERMARK = 'watermark.png';
	/**
	 * @param LAgreeModel $agree
	 * @return LTransferAgree|null
	 */
	public static function init($subject)
	{
		if(self::$_instance === null)
		{
			self::$_instance = new self(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			self::$_instance->SetAuthor(self::AUTHOR);
			self::$_instance->SetTitle($subject);
			self::$_instance->SetSubject($subject);
//			self::$_instance->SetKeywords($agree::KEYWORD);

			// set margins
			self::$_instance->SetMargins(self::MARGINLEFT,self::MARGINTOP
				,self::MARGINRIGHT);

			// set auto page breaks
			self::$_instance->SetAutoPageBreak(TRUE, self::MARGINBOTTOM);

			// set image scale factor
			self::$_instance->setImageScale(PDF_IMAGE_SCALE_RATIO);

			//chinese simple support
			$l = Array();
			// PAGE META DESCRIPTORS --------------------------------------
			$l['a_meta_charset'] = 'UTF-8';
			$l['a_meta_dir'] = 'ltr';
			$l['a_meta_language'] = 'cn';
			self::$_instance->setLanguageArray($l);

			// ---------------------------------------------------------
			// add a page
			self::$_instance->AddPage();
			// set default font subsetting mode
			self::$_instance->setFontSubsetting(true);
			self::$_instance->SetFont(self::FONTFAMILY,'',self::FONTSIZE,'',true);
		}
		return self::$_instance;
	}

	public function pdfRaw($contentArr,$stamp1,$stamp2, $overWriteTimeStamp = 0)
	{
		$stampOffset = 52;
		$UTF8 = "UTF-8";
		$indentChar = '　';
		$lastSize = self::FONTSIZE;
		$lastBold = false;
		foreach ($contentArr as $arr)
		{
			$size = $lastSize;
			if (!empty($arr['html']))
			{
				$isSign = false;
				if (!empty($arr['sign']) && $arr['sign'])
				{
					if ($this->getPageHeight() - $this->getY() < $stampOffset + 10)
					{
						$this->AddPage();
					}
					$isSign = true;
				}
				$this->writeHTML($arr['html'], false);

				if ($isSign)
				{
					$stampMarginTop = round($this->getY() - $stampOffset);
					if (!empty($stamp1))
					{
						if (is_array($stamp1))
						{
							$this->Image($stamp1['stamp'], $stamp1['xOffset'], $stamp1['yOffset'], 40, 40);
						}
						else
						{
							$this->Image($stamp1, 70, $stampMarginTop, 40, 40);
						}
					}
					if (!empty($stamp2))
					{
						if (is_array($stamp2))
						{
							$this->Image($stamp2['stamp'], $stamp2['xOffset'], $stamp2['yOffset'], 40, 40);
						}
						else
						{
							$this->Image($stamp2, 145, $stampMarginTop, 40, 40);
						}
					}
				}
			}
			else
			{
				$size = $arr['size'];
				$text = $arr['text'];
				$bold = $arr['bold'];
				$align = $arr['align'];
				$indent = $arr['indent'];
				$left = $arr['left'];

				$resetFont = false;
				if ($lastSize!=$size || $lastBold!=$bold)
				{
					$resetFont = true;
					$this->SetFont(self::FONTFAMILY, $bold?"B":'', $size, '', true);
				}

				$charWidth = $this->GetStringWidth($indentChar);
				$marginLeft = $this->GetAbsX();
				$left = $marginLeft + $charWidth * $left;

				if ($indent)
				{
					$indentLineLen = $this->getPageWidth() - $left - $marginLeft - $indent * $charWidth;
					$lineLen = $this->getPageWidth() - $marginLeft - $left;
					$textLen = $this->GetStringWidth($text);

					if ($textLen > $indentLineLen && $textLen - $charWidth <= $lineLen)
					{

						$len = mb_strlen($text, $UTF8);
						while ($len > 0)
						{
							$len--;
							$subStr = mb_substr($text, 0, $len, $UTF8);
							$tmpLen = $this->GetStringWidth($subStr);
							if ($tmpLen <= $indentLineLen - $charWidth)
							{
								$text = $subStr." ".mb_substr($text, $len, null, $UTF8);
								break;
							}
						}
					}
				}

				$this->MultiCell(0, 0, str_repeat($indentChar,$indent).$text, 0, $align, false, 1, $left);

				if ($resetFont)
				{
					$this->SetFont(self::FONTFAMILY,'',self::FONTSIZE,'',true);
				}
			}

			$this->Ln(5/18 * $size);
		}

		if ($overWriteTimeStamp)
		{
			$this->setDocCreationTimestamp($overWriteTimeStamp);
			$this->setDocModificationTimestamp($overWriteTimeStamp);
		}

		$pdf = $this->Output('','S');
		self::$_instance = null;
		return $pdf;
	}

	/**
	 * @param string $content 协议内容
	 * @param string $table 协议表格
	 * @param string $stamp1
	 * @param string $stamp2
	 * @return string
	 * @deprecated
	 */
	public function pdf($content,$table,$stamp1,$stamp2)
	{
		$tmp_pdf = clone $this;
		$tmp_pdf->writeHTML($content, false);
		$stampMarginTop = round($tmp_pdf->getY() - 55);
		unset($tmp_pdf);
		//the signature is separated into 2 pages, so fix it
		if ($stampMarginTop<0)
		{
			$content = preg_replace('/<table width="\d+?" >/','<br pagebreak="true"/>$0',$content);
		}

		$this->writeHTML($content, false);
		unset($content);
		$stampMarginTop = round($this->getY() - 55);

		$this->Image($stamp1, 70, $stampMarginTop, 40, 40);
		$this->Image($stamp2, 145, $stampMarginTop, 40, 40);

		$this->AddPage();
		$this->Cell(0, 0, '', 0, 2);

		$this->writeHTML($table, false);
		unset($table);

		$pdf = $this->Output('','S');

		return $pdf;
	}

	//Page header
	public function Header() {
		// Logo
		$this->Image(self::getImgPath().self::LOGO
			, self::MARGINLEFT, 0, 0
			, 0, 'PNG', ''
			, 'T', false, 300, '', false, false, 0, false, false, false);
		$this->Ln(1);
		$this->Cell(0,self::LOGOHEIGHT-2,''
			,array('B'=>array('color'=>self::getHeaderBorderColor())));

		//watermark
		$this->Image(self::getImgPath().self::WATERMARK
			, self::MARGINLEFT
			, self::MARGINTOP
			, 0, 0, '', '', '', false, 150, 'C', false, false, 0);
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-self::MARGINBOTTOM+5);
		// Set font
		$this->SetFont(self::FONTFAMILY, '', 10);
		// Page number
		$this->Cell(0, 7, '第'.$this->getAliasNumPage().'页，共'.$this->getAliasNbPages().'页'
			, array('T'=>array('color'=>self::getHeaderBorderColor())), 1, 'C');
	}

	public static function getHeaderBorderColor()
	{
		return sscanf(self::HEADERBORDERCOLOR, "#%02x%02x%02x");
	}

	public static function getImgPath()
	{
		return Yii::app()->params['pdfAgreeImgDir'];
	}

	private function formatTable($seg)
	{
		$html = $seg['html'];
		$html = preg_replace('/(<table.*?)\s*width\s*=\s*".*?"(.*?>)/', '$1$2', $html);
		$html = str_replace(
				array('<table ','<tr><th'),
				array('<table style="font-size: 14px;width: 100%;" ','<tr><th style="font-size: 18px" '),
				$html);
		if (!empty($seg['title']))
		{
			$html = "<p>{$seg['title']}</p><p>&nbsp;</p>".$html;
		}
		return $html;
	}
} 