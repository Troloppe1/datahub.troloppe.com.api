<?php

namespace App\Exports;

use App\Models\StreetData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class StreetDataExport implements
    FromQuery,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithEvents,
    WithDrawings,
    WithCustomStartCell
{

    public function query()
    {
        return StreetData::query()
            ->with('creator', 'location', 'section', 'sector', 'subSector');
    }

    public function headings(): array
    {
        return [

            '#',
            'Creator Name',
            'Unique_code',
            'Street Address',
            'Development Name',
            'Description',
            'Location',
            'Section',
            'Sector',
            'Sub Sector',
            'Contact Name',
            'Contact Numbers',
            'Contact Email',
            'Construction Status',
            'Verified',
            'Image Link',
            'Geolocation',
            'Created At',
        ];
    }
    public function map($streetDatum): array
    {
        return [
            $streetDatum->id,
            $streetDatum->creator->name,
            $streetDatum->unique_code,
            $streetDatum->street_address,
            $streetDatum->development_name,
            $streetDatum->description,
            $streetDatum->location->name,
            $streetDatum->section->name,
            $streetDatum->sector->name,
            $streetDatum->subSector->name,
            $streetDatum->contact_name,
            $streetDatum->contact_numbers,
            $streetDatum->contact_email,
            $streetDatum->construction_status,
            $streetDatum->is_verified,
            $streetDatum->image_path,
            $streetDatum->geolocation,
            $streetDatum->created_at,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A4:R4')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            }
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('DataHUB');
        $drawing->setDescription('DataHub for Troloppe Property Services');
        $drawing->setPath(public_path('/logos/BlackDataHUBLogo.png'));
        $drawing->setHeight(30);
        $drawing->setCoordinates('B2');

        return $drawing;
    }

    public function startCell(): string
    {
        return 'A4';
    }
}
