<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class SplitExcelExport implements FromCollection, WithHeadings
{
    protected $invoiceItems;

    public function __construct($invoiceItems)
    {
        $this->invoiceItems = $invoiceItems;
    }

    public function collection()
    {
        $flattened = collect();
        
        foreach ($this->invoiceItems as $item) {
            foreach ($item['loads'] as $load) {
                $link = url(config('app.urls.conveyance') ."{$load['ReceiptName']}");
                $flattened->push([
                    // 'Load ID'           => $load['LoadID'],
                    //'Conveyance No'     => $load['ConveyanceNo'],
                     "=HYPERLINK(\"{$link}\", \"{$load['ConveyanceNo']}\")",
                    'Ticket ID'         => $load['TicketID'],
                    'Job Start Date'    => $load['JobStartDateTime'],
                    // 'Driver Name'       => $load['DriverName'],
                    // 'Vehicle Reg No'    => $load['VehicleRegNo'],
                    'Gross (kg)'        => $load['GrossWeight'],
                    'Tare (kg)'         => $load['Tare'],
                    'Net (kg)'          => $load['Net'],
                    'Site In'           => $load['SiteInDateTime'],
                    'Site Out'          => $load['SiteOutDateTime'],
                    'Status'            => $load['Status'],
                    'Price'             => $load['LoadPrice'],
                    'Loads'             => $load['Loads'],
                ]);
            }
        }

        return $flattened;
    }

    public function headings(): array
    {
        return [
            // 'Load ID',
            'ConvTkt No',
            'Ticket ID',
            'Job Start Date',
            // 'Driver Name',
            // 'Vehicle Reg No',
            'Gross (kg)',
            'Tare (kg)',
            'Net (kg)',
            'Site In',
            'Site Out',
            'Status',
            'Price',
            'Loads'
        ];
    }
}
