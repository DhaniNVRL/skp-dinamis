<?php

namespace App\Services;

use App\Models\Form;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuestionTemplateSpreadsheet
{
    protected int $maximumRows = 500;

    public function create(
        Form $form,
        Collection $questionTypes
    ): Spreadsheet {
        $spreadsheet = new Spreadsheet;

        $questionSheet = $spreadsheet->getActiveSheet();
        $questionSheet->setTitle('INPUT_PERTANYAAN');

        $optionSheet = $spreadsheet->createSheet();
        $optionSheet->setTitle('INPUT_OPTIONS');

        $masterFormSheet = $spreadsheet->createSheet();
        $masterFormSheet->setTitle('MASTER_FORM');

        $masterTypeSheet = $spreadsheet->createSheet();
        $masterTypeSheet->setTitle(
            'MASTER_TIPE_PERTANYAAN'
        );

        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('PETUNJUK');

        $this->createQuestionSheet(
            $questionSheet,
            $form,
            $questionTypes
        );

        $this->createOptionSheet(
            $optionSheet
        );

        $this->createMasterFormSheet(
            $masterFormSheet,
            $form
        );

        $this->createMasterTypeSheet(
            $masterTypeSheet,
            $form,
            $questionTypes
        );

        $this->createInstructionSheet(
            $instructionSheet,
            $form
        );

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function createQuestionSheet(
        Worksheet $sheet,
        Form $form,
        Collection $questionTypes
    ): void {
        $headers = [
            'kode_pertanyaan',
            'form',
            'no_header',
            'no',
            'nama_pertanyaan',
            'tipe_pertanyaan',
        ];

        $sheet->fromArray(
            $headers,
            null,
            'A1'
        );

        $formLabel = $form->id
            .' - '
            .$form->name;

        $safeFormLabel = str_replace(
            '"',
            '""',
            $formLabel
        );

        $lastRow = $this->maximumRows + 1;

        /*
        |--------------------------------------------------------------------------
        | Form muncul hanya saat nama pertanyaan diisi
        |--------------------------------------------------------------------------
        */
        for ($row = 2; $row <= $lastRow; $row++) {
            $sheet->setCellValue(
                'B'.$row,
                '=IF(E'
                    .$row
                    .'="","","'
                    .$safeFormLabel
                    .'")'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Daftar tipe pertanyaan pada kolom tersembunyi
        |--------------------------------------------------------------------------
        */
        $sheet->setCellValue(
            'Z1',
            'DAFTAR_TIPE_PERTANYAAN'
        );

        $typeRow = 2;

        foreach ($questionTypes as $questionType) {
            $sheet->setCellValue(
                'Z'.$typeRow,
                $questionType['id']
                    .' - '
                    .$questionType['name']
            );

            $typeRow++;
        }

        $lastTypeRow = max(
            2,
            $typeRow - 1
        );

        $sheet->getColumnDimension('Z')
            ->setVisible(false);

        /*
        |--------------------------------------------------------------------------
        | Dropdown tipe pertanyaan
        |--------------------------------------------------------------------------
        */
        for ($row = 2; $row <= $lastRow; $row++) {
            $validation = new DataValidation;

            $validation->setType(
                DataValidation::TYPE_LIST
            );

            $validation->setErrorStyle(
                DataValidation::STYLE_STOP
            );

            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowInputMessage(true);

            $validation->setErrorTitle(
                'Tipe pertanyaan tidak valid'
            );

            $validation->setError(
                'Pilih tipe pertanyaan dari dropdown.'
            );

            $validation->setPromptTitle(
                'Tipe Pertanyaan'
            );

            $validation->setPrompt(
                'Pilih tipe pertanyaan yang sesuai dengan form.'
            );

            $validation->setFormula1(
                '$Z$2:$Z$'.$lastTypeRow
            );

            $sheet->getCell('F'.$row)
                ->setDataValidation($validation);
        }

        $this->styleHeader(
            $sheet,
            'A1:F1',
            '2563EB'
        );

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:F'.$lastRow);

        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(42);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(65);
        $sheet->getColumnDimension('F')->setWidth(55);

        $sheet->getStyle('A2:A'.$lastRow)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FEF3C7');

        $sheet->getStyle('C2:F'.$lastRow)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FEFCE8');

        $sheet->getStyle('E2:F'.$lastRow)
            ->getAlignment()
            ->setWrapText(true);

        // Simpan nomor sebagai teks agar kode seperti L1A dan angka berawalan nol tidak diubah Excel.
        $sheet->getStyle('D2:D'.$lastRow)
            ->getNumberFormat()
            ->setFormatCode('@');

        $this->addBorders(
            $sheet,
            'A1:F'.$lastRow
        );
    }

    private function createOptionSheet(
        Worksheet $sheet
    ): void {
        $headers = [
            'kode_pertanyaan',
            'urutan',
            'nama_option',
            'has_child',
            'answer_text2',
        ];

        $sheet->fromArray(
            $headers,
            null,
            'A1'
        );

        /*
        |--------------------------------------------------------------------------
        | Penjelasan pada header has_child
        |--------------------------------------------------------------------------
        */
        $sheet->getComment('D1')
            ->getText()
            ->createTextRun(
                "Has Child:\n"
                ."0 - Tidak: option tidak mempunyai input lanjutan.\n"
                .'1 - Iya: option mempunyai input lanjutan.'
            );

        $sheet->getComment('E1')
            ->getText()
            ->createTextRun(
                'answer_text2 bersifat opsional dan dapat dikosongkan.'
            );

        $lastRow = $this->maximumRows + 1;

        /*
        |--------------------------------------------------------------------------
        | Mengambil kode dari INPUT_PERTANYAAN
        |--------------------------------------------------------------------------
        */
        $sheet->setCellValue(
            'Z1',
            'DAFTAR_KODE_PERTANYAAN'
        );

        for ($row = 2; $row <= $lastRow; $row++) {
            $sheet->setCellValue(
                'Z'.$row,
                "='INPUT_PERTANYAAN'!A{$row}"
            );
        }

        $sheet->getColumnDimension('Z')
            ->setVisible(false);

        /*
        |--------------------------------------------------------------------------
        | Dropdown kode pertanyaan dan has_child
        |--------------------------------------------------------------------------
        */
        for ($row = 2; $row <= $lastRow; $row++) {
            $codeValidation = new DataValidation;

            $codeValidation->setType(
                DataValidation::TYPE_LIST
            );

            $codeValidation->setErrorStyle(
                DataValidation::STYLE_STOP
            );

            $codeValidation->setAllowBlank(true);
            $codeValidation->setShowDropDown(true);
            $codeValidation->setShowErrorMessage(true);
            $codeValidation->setShowInputMessage(true);

            $codeValidation->setErrorTitle(
                'Kode tidak valid'
            );

            $codeValidation->setError(
                'Pilih kode dari sheet INPUT_PERTANYAAN.'
            );

            $codeValidation->setPromptTitle(
                'Kode Pertanyaan'
            );

            $codeValidation->setPrompt(
                'Pilih kode pertanyaan yang akan diberi option.'
            );

            $codeValidation->setFormula1(
                '$Z$2:$Z$'.$lastRow
            );

            $sheet->getCell('A'.$row)
                ->setDataValidation($codeValidation);

            /*
            |--------------------------------------------------------------------------
            | Dropdown has_child
            |--------------------------------------------------------------------------
            */
            $childValidation = new DataValidation;

            $childValidation->setType(
                DataValidation::TYPE_LIST
            );

            $childValidation->setErrorStyle(
                DataValidation::STYLE_STOP
            );

            $childValidation->setAllowBlank(true);
            $childValidation->setShowDropDown(true);
            $childValidation->setShowErrorMessage(true);
            $childValidation->setShowInputMessage(true);

            $childValidation->setErrorTitle(
                'Nilai has_child tidak valid'
            );

            $childValidation->setError(
                'Pilih 0 - Tidak atau 1 - Iya dari dropdown.'
            );

            $childValidation->setPromptTitle(
                'Has Child'
            );

            $childValidation->setPrompt(
                '0 - Tidak berarti tidak memiliki input lanjutan. '
                .'1 - Iya berarti memiliki input lanjutan.'
            );

            $childValidation->setFormula1(
                '"0 - Tidak,1 - Iya"'
            );

            $sheet->getCell('D'.$row)
                ->setDataValidation($childValidation);
        }

        $this->styleHeader(
            $sheet,
            'A1:E1',
            '7C3AED'
        );

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:E'.$lastRow);

        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(24);
        $sheet->getColumnDimension('E')->setWidth(50);

        $sheet->getStyle('A2:E'.$lastRow)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('FEFCE8');

        $sheet->getStyle('C2:E'.$lastRow)
            ->getAlignment()
            ->setWrapText(true);

        $this->addBorders(
            $sheet,
            'A1:E'.$lastRow
        );
    }

    private function createMasterFormSheet(
        Worksheet $sheet,
        Form $form
    ): void {
        $groupName = $form->group?->name ?? '-';

        $sheet->fromArray([
            [
                'form_id',
                'group_id',
                'nama_group',
                'formtype_id',
                'nama_form',
                'referensi_form',
            ],
            [
                $form->id,
                $form->group_id,
                $groupName,
                $form->formtype_id,
                $form->name,
                $form->id
                    .' - '
                    .$form->name,
            ],
        ]);

        $this->styleHeader(
            $sheet,
            'A1:F1',
            '059669'
        );

        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getColumnDimension('F')->setWidth(60);

        $sheet->setAutoFilter('A1:F2');

        $this->addBorders(
            $sheet,
            'A1:F2'
        );
    }

    private function createMasterTypeSheet(
        Worksheet $sheet,
        Form $form,
        Collection $questionTypes
    ): void {
        $sheet->fromArray([
            [
                'questiontype_id',
                'nama_tipe',
                'keterangan',
                'referensi_tipe',
            ],
        ]);

        $row = 2;

        foreach ($questionTypes as $questionType) {
            $sheet->fromArray(
                [
                    $questionType['id'],
                    $questionType['name'],
                    $questionType['description'] ?? '',
                    $questionType['id']
                        .' - '
                        .$questionType['name'],
                ],
                null,
                'A'.$row
            );

            $row++;
        }

        $lastRow = max(
            2,
            $row - 1
        );

        $this->styleHeader(
            $sheet,
            'A1:D1',
            'D97706'
        );

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(48);
        $sheet->getColumnDimension('C')->setWidth(75);
        $sheet->getColumnDimension('D')->setWidth(58);

        $sheet->getStyle('B2:D'.$lastRow)
            ->getAlignment()
            ->setWrapText(true);

        $sheet->setAutoFilter(
            'A1:D'.$lastRow
        );

        $this->addBorders(
            $sheet,
            'A1:D'.$lastRow
        );

        $informationRow = $lastRow + 3;

        $sheet->setCellValue(
            'A'.$informationRow,
            'Form'
        );

        $sheet->setCellValue(
            'B'.$informationRow,
            $form->id
                .' - '
                .$form->name
        );
    }

    private function createInstructionSheet(
        Worksheet $sheet,
        Form $form
    ): void {
        $instructions = [
            [
                'PETUNJUK IMPORT PERTANYAAN',
                '',
            ],
            [
                'Form',
                $form->id
                    .' - '
                    .$form->name,
            ],
            [
                'Form Type ID',
                $form->formtype_id,
            ],
            [
                'Group',
                $form->group_id
                    .' - '
                    .($form->group?->name ?? '-'),
            ],
            [
                '',
                '',
            ],
            [
                'NO',
                'PETUNJUK',
            ],
            [
                1,
                'Jangan mengubah nama sheet dan nama kolom.',
            ],
            [
                2,
                'Masukkan pertanyaan pada sheet INPUT_PERTANYAAN.',
            ],
            [
                3,
                'Kode pertanyaan wajib unik, misalnya Q001.',
            ],
            [
                4,
                'Kolom form terisi otomatis setelah nama pertanyaan diisi.',
            ],
            [
                5,
                'Kolom no_header boleh dikosongkan.',
            ],
            [
                6,
                'Kolom no wajib diisi.',
            ],
            [
                7,
                'Kolom nama_pertanyaan wajib diisi.',
            ],
            [
                8,
                'Pilih tipe pertanyaan dari dropdown.',
            ],
            [
                9,
                'Option dimasukkan pada sheet INPUT_OPTIONS.',
            ],
            [
                10,
                'Kode option harus sama dengan kode pertanyaan.',
            ],
            [
                11,
                'Has child: pilih 0 - Tidak jika tidak mempunyai input lanjutan, atau 1 - Iya jika mempunyai input lanjutan.',
            ],
            [
                12,
                'Kolom answer_text2 bersifat opsional dan dapat dikosongkan.',
            ],
            [
                13,
                'Jangan mengubah isi sheet master.',
            ],
            [
                14,
                'File import harus berformat XLSX atau XLS.',
            ],
        ];

        $sheet->fromArray($instructions);

        $sheet->mergeCells('A1:B1');

        $this->styleHeader(
            $sheet,
            'A1:B1',
            '1E3A8A'
        );

        $this->styleHeader(
            $sheet,
            'A6:B6',
            '2563EB'
        );

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(100);

        $sheet->getStyle(
            'B2:B'.count($instructions)
        )->getAlignment()
            ->setWrapText(true);

        $this->addBorders(
            $sheet,
            'A6:B'.count($instructions)
        );
    }

    private function styleHeader(
        Worksheet $sheet,
        string $range,
        string $color
    ): void {
        $sheet->getStyle($range)
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],

                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => $color,
                    ],
                ],

                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ]);
    }

    private function addBorders(
        Worksheet $sheet,
        string $range
    ): void {
        $sheet->getStyle($range)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                Border::BORDER_THIN
            )
            ->getColor()
            ->setRGB('D1D5DB');
    }
}
