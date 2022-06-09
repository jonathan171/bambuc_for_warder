<?php
namespace App\Service;

use TCPDF;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    protected $_cufe = null;

    // Page footer
    public function Footer() {
        $cur_y = $this->y;
		$this->setTextColorArray('BLACK');
		//set style for cell border
		$line_width = (0.85 / $this->k);
		$this->setLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
		//print document barcode
		$barcode = $this->getBarcode();
		if (!empty($barcode)) {
			$this->Ln($line_width);
			$barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
			$style = array(
				'position' => $this->rtl?'R':'L',
				'align' => $this->rtl?'R':'L',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
				'text' => false
			);
			$this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
		}
		
		$this->writeHTML($this->_cufe, true, false, true, false, 'C');
		$this->html = 'Software DATAICO fabricado por Proveedor Tecnológico DATAICO SAS 901223648 ';
        $this->writeHTML($this->html, true, false, true, false, 'C');

		$w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : '';
		if (empty($this->pagegroups)) {
			$pagenumtxt = 'PÁGINA ' .$w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = 'PÁGINA ' . $w_page.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		$this->setY($cur_y);
		//Print page number
		if ($this->getRTL()) {
			$this->setX($this->original_rMargin);
			$this->Cell(0, 0, $pagenumtxt, 0, 0, 'L');
		} else {
			$this->setX($this->original_lMargin);
			$this->Cell(0, 0, $this->getAliasRightShift().$pagenumtxt, 0, 0, 'R');
		}
    }

	public function setCufe( $Cufe = null) {
        $this->_cufe = $Cufe;
    }
}