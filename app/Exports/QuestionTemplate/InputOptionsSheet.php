<?php

namespace App\Exports\QuestionTemplate;

use App\Models\Form;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InputOptionsSheet implements
    FromArray,
    WithTitle,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    protected Form $form;

    protected int $maximumRows = 500;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function title(): string
    {
        return 'INPUT_OPTIONS';
    }

    public function array(): array
    {
        $rows = [];

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */
        $rows[] = [
            'kode_pertanyaan',
            'urutan',
            'nama_option',
            'has_child',
            'label_child',
        ];

        /*
        |--------------------------------------------------------------------------
        | Baris kosong untuk input
        |--------------------------------------------------------------------------
        */
        for ($row = 1; $row <= $this->maximumRows; $row++) {
            $rows[] = [
                '',
                '',
                '',
                '',
                '',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],

                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => '7C3AED',
                    ],
                ],

                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 12,
            'C' => 50,
            'D' => 15,
            'E' => 50,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $lastInputRow = $this->maximumRows + 1;

                /*
                |--------------------------------------------------------------------------
                | Tampilan
                |--------------------------------------------------------------------------
                */
                $sheet->freezePane('A2');

                $sheet->setAutoFilter(
                    'A1:E' . $lastInputRow
                );

                $sheet->getRowDimension(1)
                    ->setRowHeight(25);

                $sheet->getStyle(
                    'A1:E' . $lastInputRow
                )->getAlignment()
                    ->setVertical('top');

                $sheet->getStyle(
                    'C2:E' . $lastInputRow
                )->getAlignment()
                    ->setWrapText(true);

                /*
                |--------------------------------------------------------------------------
                | Helper kode pertanyaan
                |--------------------------------------------------------------------------
                | Mengambil kode dari sheet INPUT_PERTANYAAN.
                */
                $sheet->setCellValue(
                    'Z1',
                    'DAFTAR_KODE_PERTANYAAN'
                );

                for ($row = 2; $row <= $lastInputRow; $row++) {
                    $sheet->setCellValue(
                        'Z' . $row,
                        "='INPUT_PERTANYAAN'!A" . $row
                    );
                }

                $sheet->getColumnDimension('Z')
                    ->setVisible(false);

                /*
                |--------------------------------------------------------------------------
                | Dropdown kode pertanyaan
                |--------------------------------------------------------------------------
                */
                for ($row = 2; $row <= $lastInputRow; $row++) {
                    $questionCodeValidation = new DataValidation();

                    $questionCodeValidation->setType(
                        DataValidation::TYPE_LIST
                    );

                    $questionCodeValidation->setErrorStyle(
                        DataValidation::STYLE_STOP
                    );

                    $questionCodeValidation->setAllowBlank(false);

                    $questionCodeValidation->setShowDropDown(true);

                    $questionCodeValidation->setShowErrorMessage(true);

                    $questionCodeValidation->setErrorTitle(
                        'Kode pertanyaan tidak valid'
                    );

                    $questionCodeValidation->setError(
                        'Pilih kode yang tersedia pada sheet INPUT_PERTANYAAN.'
                    );

                    $questionCodeValidation->setShowInputMessage(true);

                    $questionCodeValidation->setPromptTitle(
                        'Kode Pertanyaan'
                    );

                    $questionCodeValidation->setPrompt(
                        'Pilih kode pertanyaan yang akan diberikan option.'
                    );

                    $questionCodeValidation->setFormula1(
                        '$Z$2:$Z$' . $lastInputRow
                    );

                    $sheet->getCell('A' . $row)
                        ->setDataValidation(
                            $questionCodeValidation
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Dropdown has_child
                |--------------------------------------------------------------------------
                */
                for ($row = 2; $row <= $lastInputRow; $row++) {
                    $hasChildValidation = new DataValidation();

                    $hasChildValidation->setType(
                        DataValidation::TYPE_LIST
                    );

                    $hasChildValidation->setErrorStyle(
                        DataValidation::STYLE_STOP
                    );

                    $hasChildValidation->setAllowBlank(false);

                    $hasChildValidation->setShowDropDown(true);

                    $hasChildValidation->setShowErrorMessage(true);

                    $hasChildValidation->setErrorTitle(
                        'Nilai has_child tidak valid'
                    );

                    $hasChildValidation->setError(
                        'Nilai has_child hanya boleh 0 atau 1.'
                    );

                    $hasChildValidation->setShowInputMessage(true);

                    $hasChildValidation->setPromptTitle(
                        'Has Child'
                    );

                    $hasChildValidation->setPrompt(
                        'Pilih 1 jika option memiliki textarea lanjutan, atau pilih 0 jika tidak.'
                    );

                    $hasChildValidation->setFormula1(
                        '"0,1"'
                    );

                    $sheet->getCell('D' . $row)
                        ->setDataValidation(
                            $hasChildValidation
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Warna kolom input
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle(
                    'A2:D' . $lastInputRow
                )->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('FEFCE8');

                $sheet->getStyle(
                    'E2:E' . $lastInputRow
                )->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('EFF6FF');

                /*
                |--------------------------------------------------------------------------
                | Format urutan sebagai angka
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle(
                    'B2:B' . $lastInputRow
                )->getNumberFormat()
                    ->setFormatCode('0');

                /*
                |--------------------------------------------------------------------------
                | Border
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle(
                    'A1:E' . $lastInputRow
                )->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle('thin')
                    ->getColor()
                    ->setRGB('D1D5DB');
            },
        ];
    }
}