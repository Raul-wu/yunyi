<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/4
 * Time: 11:56
 */

class LAssetProveAgree  extends LTransferAgree
{

	public static function init()
	{
		if(self::$_instance === null)
		{
			self::$_instance = new self(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			self::$_instance->SetAuthor(LAgreeModel::AUTHOR);
			self::$_instance->SetMargins(LAgreeModel::MARGINLEFT,LAgreeModel::MARGINTOP
				,LAgreeModel::MARGINRIGHT);
			self::$_instance->SetAutoPageBreak(TRUE, LAgreeModel::MARGINBOTTOM);
			self::$_instance->setImageScale(PDF_IMAGE_SCALE_RATIO);

			$l = Array();
			// PAGE META DESCRIPTORS --------------------------------------
			$l['a_meta_charset'] = 'UTF-8';
			$l['a_meta_dir'] = 'ltr';
			$l['a_meta_language'] = 'cn';
			self::$_instance->setLanguageArray($l);

			// add a page
			self::$_instance->AddPage();

			// set default font subsetting mode
			self::$_instance->setFontSubsetting(true);
			self::$_instance->SetFont(LAgreeModel::FONTFAMILY,'',LAgreeModel::FONTSIZE,'',true);
		}
		return self::$_instance;
	}

	public function  pdf($content)
	{
		$lastPageFoot ='<div><p></p><span style="text-align: right"></span>上海诺亚易捷金融科技有限公司<br><br>
	<span style="text-align: right">'.date("Y",time()).'年'.date("m",time()).'月'.date("d",time()).'日</span></div>';

		$this->writeHTML($content, false);
		if($this->getPageHeight()-$this->GetY() < 50)
		{
			$this->AddPage();
			$this->writeHTML($lastPageFoot, false);
		}
		else
		{
			$this->writeHTML($lastPageFoot, false);
		}

		$signed = LAgreeService::getSigned(2, 1);
		$stamp = '@' . $signed[1002]['stamp'];
		$stamp = array(
			'stamp' => $stamp,
			'xOffset' => $this->GetAbsX() + 140 ,
			'yOffset' => $this->GetY() - 35,
		);
		$this->Image($stamp['stamp'], $stamp['xOffset'], $stamp['yOffset'], 40, 40);
		$pdf = $this->Output('', 'S');
		self::$_instance = null;
		return $pdf;
	}
} 