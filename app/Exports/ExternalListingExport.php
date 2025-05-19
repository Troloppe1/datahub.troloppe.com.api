<?php

namespace App\Exports;

use App\QueryBuilders\PostgresDatahubDbBuilder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ExternalListingExport implements FromQuery, WithHeadings, WithEvents, WithDrawings, WithCustomStartCell
{
    private PostgresDatahubDbBuilder $dbBuilder;

    public function __construct(private string |null $startDate, private string |null $endDate = null)
    {
        $this->dbBuilder = new PostgresDatahubDbBuilder();
    }

    public function query()
    {
        $query = $this->dbBuilder->createQueryBuilder('external_listings.listings_for_export')
            ->selectRaw(' "Date",
            "Region",
            "Location",
            "Section",
            "L.G.A",
            "L.C.D.A",
            "Street",
            "Street Number",
            "Development",
            "Sector",
            "Type",
            "Sub Type",
            "No of Beds",
            "Size",
            "Land Area",
            "Offer",
            "Sale Price",
            "Lease Price",
            "Price/Sqm",
            "Service Charge",
            "Developer",
            "Listing Agent",
            "Contact Number(s)",
            "E-mail",
            "Comment",
            "Source",
            "Updated By"')->orderBy('created_at', 'desc');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            "Date",
            "Region",
            "Location",
            "Section",
            "L.G.A",
            "L.C.D.A",
            "Street",
            "Street Number",
            "Development",
            "Sector",
            "Type",
            "Sub Type",
            "No of Beds",
            "Size",
            "Land Area",
            "Offer",
            "Sale Price",
            "Lease Price",
            "Price/Sqm",
            "Service Charge",
            "Developer",
            "Listing Agent",
            "Contact Number(s)",
            "E-mail",
            "Comment",
            "Source",
            "Updated By"
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A4:BZ4')->applyFromArray([
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
