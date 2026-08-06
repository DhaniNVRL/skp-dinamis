<?php

namespace App\Exports\QuestionTemplate;

use App\Models\Form;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InputQuestionsSheet implements
    FromArray,
    WithTitle,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    protected Form $form;

    protected Collection $questionTypes;

    protected int $maximumRows = 500;

    public function __construct(
        Form $form,
        Collection $questionTypes
    ) {
        $this->form = $form;
        $this->questionTypes = $questionTypes;
    }

    public function title(): string
    {
        return 'INPUT_PERTANYAAN';
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
            'form',
            'no_header',
            'no',
            'nama_pertanyaan',
            'tipe_pertanyaan',
        ];

        $formLabel = $this->form->id
            . ' - '
            . $this->form->name;

        /*
        |--------------------------------------------------------------------------
        | Siapkan 500 baris input
        |--------------------------------------------------------------------------
        */
        for ($row = 1; $row <= $this->maximumRows; $row++) {
            $rows[] = [
                '',
                $formLabel,
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
                        'rgb' => '2563EB',
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
            'B' => 40,
            'C' => 15,
            'D' => 12,
            'E' => 65,
            'F' => 48,
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
                | Tampilan sheet
                |--------------------------------------------------------------------------
                */
                $sheet->freezePane('A2');

                $sheet->setAutoFilter(
                    'A1:F' . $lastInputRow
                );

                $sheet->getRowDimension(1)
                    ->setRowHeight(25);

                $sheet->getStyle(
                    'A1:F' . $lastInputRow
                )->getAlignment()
                    ->setVertical('top');

                $sheet->getStyle(
                    'E2:E' . $lastInputRow
                )->getAlignment()
                    ->setWrapText(true);

                $sheet->getStyle(
                    'F2:F' . $lastInputRow
                )->getAlignment()
                    ->setWrapText(true);

                /*
                |--------------------------------------------------------------------------
                | Helper daftar tipe pertanyaan di kolom Z
                |--------------------------------------------------------------------------
                | Kolom Z disembunyikan dan digunakan untuk dropdown.
                */
                $sheet->setCellValue(
                    'Z1',
                    'DAFTAR_TIPE_PERTANYAAN'
                );

                $helperRow = 2;

                foreach ($this->questionTypes as $questionType) {
                    $label = $questionType['id']
                        . ' - '
                        . $questionType['name'];

                    $sheet->setCellValue(
                        'Z' . $helperRow,
                        $label
                    );

                    $helperRow++;
                }

                $lastHelperRow = max(
                    2,
                    $helperRow - 1
                );

                $sheet->getColumnDimension('Z')
                    ->setVisible(false);

                /*
                |--------------------------------------------------------------------------
                | Dropdown tipe pertanyaan
                |--------------------------------------------------------------------------
                */
                for ($row = 2; $row <= $lastInputRow; $row++) {
                    $validation = new DataValidation();

                    $validation->setType(
                        DataValidation::TYPE_LIST
                    );

                    $validation->setErrorStyle(
                        DataValidation::STYLE_STOP
                    );

                    $validation->setAllowBlank(false);

                    $validation->setShowDropDown(true);

                    $validation->setShowErrorMessage(true);

                    $validation->setErrorTitle(
                        'Tipe pertanyaan tidak valid'
                    );

                    $validation->setError(
                        'Pilih tipe pertanyaan dari daftar yang tersedia.'
                    );

                    $validation->setShowInputMessage(true);

                    $validation->setPromptTitle(
                        'Tipe Pertanyaan'
                    );

                    $validation->setPrompt(
                        'Pilih tipe pertanyaan sesuai jenis form.'
                    );

                    $validation->setFormula1(
                        '$Z$2:$Z$' . $lastHelperRow
                    );

                    $sheet->getCell('F' . $row)
                        ->setDataValidation($validation);
                }

                /*
                |--------------------------------------------------------------------------
                | Warna area input
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle(
                    'A2:A' . $lastInputRow
                )->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('FEF3C7');

                $sheet->getStyle(
                    'C2:F' . $lastInputRow
                )->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('FEFCE8');

                /*
                |--------------------------------------------------------------------------
                | Border
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle(
                    'A1:F' . $lastInputRow
                )->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle('thin')
                    ->getColor()
                    ->setRGB('D1D5DB');
            },
        ];
    }
}