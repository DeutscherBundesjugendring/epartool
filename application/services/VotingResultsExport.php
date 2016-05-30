<?php

class Service_VotingResultsExport
{
    /**
     * @param Zend_Db_Table_Row_Abstract $consultation
     * @param int $questionId
     */
    public function exportResults(Zend_Db_Table_Row_Abstract $consultation, $questionId)
    {
        $resultsForExport = (new Model_Votes())->getResultsValues($consultation->kid, $questionId);
        
        $fileName = $consultation->titl_short . ' ' . $questionId . '.ods';
        
        $objPHPExcel = new PHPExcel();
        
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $objPHPExcel->getProperties()->setCreator($auth->getIdentity()->email)
                ->setTitle($consultation->titl_short . ' ' . $questionId);
        }

        $translator = Zend_Registry::get('Zend_Translate');
        
        $sheet = $objPHPExcel->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', $consultation->titl)
            ->setCellValue('B1', $resultsForExport['currentQuestion']['q']);
        
        $sheet->setCellValue('A3', $translator->translate('Contribution'))
            ->setCellValue('B3', $translator->translate('Contribution explanation'))
            ->setCellValue('C3', $translator->translate('Rank'));
        
        $row = 4;
        foreach ($resultsForExport['votings'] as $result) {
            $sheet->setCellValue('A' . $row, $result['thes'])->setCellValue('B' . $row, $result['expl'])
                ->setCellValue('C' . $row, $result['rank']);
            $row++;
        }

        $sheet->setTitle($this->correctSheetTitle($resultsForExport['currentQuestion']['q']));

        // Redirect output to a client’s web browser (OpenDocument)
        header('Content-Type: application/vnd.oasis.opendocument.spreadsheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OpenDocument');
        $objWriter->save('php://output');
        exit;
    }
    
    /**
     * @param string $title
     * @return string
     */
    private function correctSheetTitle($title)
    {
        $correctedTitle = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '-', $title);
        return substr($correctedTitle, 0, 31);
    }
}
