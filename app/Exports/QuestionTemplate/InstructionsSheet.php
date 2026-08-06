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

class InstructionsSheet implements
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
        return 'PETUNJUK';
    }

    public function array(): array
    {
        return [
            [
                'PETUNJUK IMPORT PERTANYAAN',
                '',
            ],
            [
                'Form',
                $this->form->id . ' - ' . $this->form->name,
            ],
            [
                'Form Type ID',
                $this->form->formtype_id,
            ],
            [
                'Group',
                $this->form->group_id
                . ' - '
                . ($this->form->group?->name ?? '-'),
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
                'Jangan mengubah nama sheet dan nama kolom pada template.',
            ],
            [
                2,
                'Masukkan pertanyaan pada sheet INPUT_PERTANYAAN.',
            ],
            [
                3,
                'Kolom kode_pertanyaan wajib unik dalam satu file. Contoh: Q001, Q002, dan Q003.',
            ],
            [
                4,
                'Kolom form sudah terisi otomatis berdasarkan form tempat template diunduh.',
            ],
            [
                5,
                'Kolom no_header dapat diisi A, B, C, atau kode kelompok pertanyaan lainnya.',
            ],
            [
                6,
                'Kolom no dapat diisi angka atau teks sesuai urutan pertanyaan.',
            ],
            [
                7,
                'Kolom nama_pertanyaan wajib diisi.',
            ],
            [
                8,
                'Pilih tipe_pertanyaan dari dropdown yang tersedia.',
            ],
            [
                9,
                'ID dan nama tipe pertanyaan dapat dilihat pada sheet MASTER_TIPE_PERTANYAAN.',
            ],
            [
                10,
                'Masukkan pilihan jawaban pada sheet INPUT_OPTIONS hanya jika pertanyaan membutuhkan option.',
            ],
            [
                11,
                'Kode pertanyaan pada INPUT_OPTIONS harus sama dengan kode pada INPUT_PERTANYAAN.',
            ],
            [
                12,
                'Satu kode pertanyaan dapat mempunyai lebih dari satu option.',
            ],
            [
                13,
                'Kolom urutan pada INPUT_OPTIONS menentukan urutan tampil option.',
            ],
            [
                14,
                'Isi has_child dengan 1 jika option mempunyai textarea lanjutan.',
            ],
            [
                15,
                'Isi has_child dengan 0 jika option tidak mempunyai textarea lanjutan.',
            ],
            [
                16,
                'Kolom label_child hanya diisi jika has_child bernilai 1.',
            ],
            [
                17,
                'Jangan mengubah isi sheet MASTER_FORM dan MASTER_TIPE_PERTANYAAN.',
            ],
            [
                18,
                'Hapus seluruh baris contoh atau baris yang tidak digunakan sebelum melakukan import.',
            ],
            [
                19,
                'File yang dapat diimport adalah file Excel dengan format XLSX atau XLS.',
            ],
            [
                20,
                'Form tipe Description tidak dapat menerima import pertanyaan.',
            ],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],

                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => '1E3A8A',
                    ],
                ],

                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],

            6 => [
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
            'A' => 15,
            'B' => 100,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $lastRow = 26;

                /*
                |--------------------------------------------------------------------------
                | Judul
                |--------------------------------------------------------------------------
                */
                $sheet->mergeCells('A1:B1');

                $sheet->getRowDimension(1)
                    ->setRowHeight(32);

                /*
                |--------------------------------------------------------------------------
                | Informasi form
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle('A2:A4')
                    ->getFont()
                    ->setBold(true);

                $sheet->getStyle('A2:B4')
                    ->getFill()
                    ->setFillType('solid')
                    ->getStartColor()
                    ->setRGB('EFF6FF');

                $sheet->getStyle('A2:B4')
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle('thin')
                    ->getColor()
                    ->setRGB('BFDBFE');

                /*
                |--------------------------------------------------------------------------
                | Tabel petunjuk
                |--------------------------------------------------------------------------
                */
                $sheet->getStyle(
                    'A6:B' . $lastRow
                )->getAlignment()
                    ->setVertical('top');

                $sheet->getStyle(
                    'B7:B' . $lastRow
                )->getAlignment()
                    ->setWrapText(true);

                $sheet->getStyle(
                    'A6:B' . $lastRow
                )->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle('thin')
                    ->getColor()
                    ->setRGB('D1D5DB');

                $sheet->getStyle(
                    'A7:A' . $lastRow
                )->getAlignment()
                    ->setHorizontal('center');

                /*
                |--------------------------------------------------------------------------
                | Warna baris petunjuk
                |--------------------------------------------------------------------------
                */
                for ($row = 7; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle(
                            'A' . $row . ':B' . $row
                        )->getFill()
                            ->setFillType('solid')
                            ->getStartColor()
                            ->setRGB('F8FAFC');
                    }
                }

                $sheet->freezePane('A6');
            },
        ];
    }
}