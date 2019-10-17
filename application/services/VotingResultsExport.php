<?php

use  \PhpOffice\PhpSpreadsheet\Spreadsheet;

class Service_VotingResultsExport
{
    public function exportResults(Zend_Db_Table_Row_Abstract $consultation, int $questionId): ?Spreadsheet
    {
        $resultsForExport = (new Model_Votes())->getResultsValues($consultation->kid, $questionId);
        if(!$resultsForExport) {
            return null;
        }
        $spreadsheet = new Spreadsheet();

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $spreadsheet->getProperties()->setCreator($auth->getIdentity()->email)
                ->setTitle($consultation->titl_short . ' ' . $questionId);
        }

        $translator = Zend_Registry::get('Zend_Translate');

        $sheet = $spreadsheet->setActiveSheetIndex(0);
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
        return $spreadsheet;
    }

    private function correctSheetTitle(string $title): string
    {
        $correctedTitle = str_replace(['*', ':', '/', '\\', '?', '[', ']'], '-', $title);
        return mb_substr($correctedTitle, 0, 31);
    }
}
