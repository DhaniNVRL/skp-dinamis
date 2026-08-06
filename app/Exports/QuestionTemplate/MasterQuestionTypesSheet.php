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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterQuestionTypesSheet implements
    FromArray,
    WithTitle,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    protected Form $form;

    protected Collection $questionTypes;

    public function __construct(
        Form $form,
        Collection $questionTypes
    ) {
        $this->form = $form;
        $this->questionTypes = $questionTypes;
    }

    public function title(): string
    {
        return 'MASTER_TIPE_PERTANYAAN';
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = [
            'questiontype_id',
            'nama_tipe',
            'keterangan',
            'referensi_tipe',
        ];

        foreach ($this->questionTypes as $questionType) {
            $rows[] = [
                $questionType['id'],
                $questionType['name'],
                $questionType['description'] ?? '',
                $questionType['id']
                    . ' - '
                    . $questionType['name'],
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
                        'rgb' => 'D97706',
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
            'A' => 20,
            'B' => 48,
            'C' => 75,
            'D' => 58,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $lastRow = max(
                    2,
                    $this->questionTypes->count() + 1
                );

                $sheet->freezePane('A2');

                $sheet->setAutoFilter(
                    'A1:D' . $lastRow
                );

                $sheet->getRowDimension(1)
                    ->setRowHeight(25);

                $sheet->getStyle(
                    'A1:D' . $lastRow
                )->getAlignment()
                    ->setVertical('top');

                $sheet->getStyle(
                    'B2:D' . $lastRow
                )->getAlignment()
                    ->setWrapText(true);

                $sheet->getStyle(
                    'A1:D' . $lastRow
                )->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle('thin')
                    ->getColor()
                    ->setRGB('D1D5DB');

                $sheet->getStyle(
                    'A2:D' . $lastRow
                )->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('FFFBEB');

                /*
                |--------------------------------------------------------------------------
                | Informasi form
                |--------------------------------------------------------------------------
                */
                $informationRow = $lastRow + 3;

                $sheet->setCellValue(
                    'A' . $informationRow,
                    'Form'
                );

                $sheet->setCellValue(
                    'B' . $informationRow,
                    $this->form->id
                    . ' - '
                    . $this->form->name
                );

                $sheet->setCellValue(
                    'A' . ($informationRow + 1),
                    'Form Type ID'
                );

                $sheet->setCellValue(
                    'B' . ($informationRow + 1),
                    $this->form->formtype_id
                );

                $sheet->getStyle(
                    'A' . $informationRow
                    . ':A'
                    . ($informationRow + 1)
                )->getFont()
                    ->setBold(true);

                $sheet->getStyle(
                    'A' . $informationRow
                    . ':B'
                    . ($informationRow + 1)
                )->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle('thin')
                    ->getColor()
                    ->setRGB('D1D5DB');
            },
        ];
    }
}