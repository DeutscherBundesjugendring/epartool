<?php

class Service_VotingResultsExport
{
    /**
     * @param Zend_Db_Table_Row_Abstract $consultation
     * @param int $questionId
     * @return PHPExcel
     */
    public function exportResults(Zend_Db_Table_Row_Abstract $consultation, $questionId)
    {
        $resultsForExport = (new Model_Votes())->getResultsValues($consultation->kid, $questionId);
        
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

        return $objPHPExcel;
    }
    
    /**
     * @param string $title
     * @return string
     */
    private function correctSheetTitle($title)
    {
        $correctedTitle = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '-', $title);
        return mb_substr($correctedTitle, 0, 31);
    }
}
