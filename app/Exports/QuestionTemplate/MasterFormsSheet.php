<?php

namespace App\Exports\QuestionTemplate;

use App\Models\Form;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterFormsSheet implements
    FromArray,
    WithTitle,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    protected Form $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function title(): string
    {
        return 'MASTER_FORM';
    }

    public function array(): array
    {
        $groupName = $this->form->group?->name ?? '-';

        return [
            [
                'form_id',
                'group_id',
                'nama_group',
                'formtype_id',
                'nama_form',
                'referensi_form',
            ],
            [
                $this->form->id,
                $this->form->group_id,
                $groupName,
                $this->form->formtype_id,
                $this->form->name,
                $this->form->id . ' - ' . $this->form->name,
            ],
        ];
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
                        'rgb' => '059669',
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
            'A' => 14,
            'B' => 14,
            'C' => 35,
            'D' => 16,
            'E' => 50,
            'F' => 60,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->freezePane('A2');

                $sheet->setAutoFilter('A1:F2');

                $sheet->getRowDimension(1)
                    ->setRowHeight(25);

                $sheet->getStyle('A1:F2')
                    ->getAlignment()
                    ->setVertical('top');

                $sheet->getStyle('C2:F2')
                    ->getAlignment()
                    ->setWrapText(true);

                $sheet->getStyle('A1:F2')
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle('thin')
                    ->getColor()
                    ->setRGB('D1D5DB');

                /*
                |--------------------------------------------------------------------------
                | Sheet master hanya untuk referensi
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle('A2:F2')
                    ->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('ECFDF5');
            },
        ];
    }
}