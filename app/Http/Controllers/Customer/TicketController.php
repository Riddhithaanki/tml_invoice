<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ZipArchive;
use Illuminate\Support\Facades\Http;

class TicketController extends Controller
{
    public function showTickets($id)
    {
        // Validate invoice
        $invoice = \DB::connection('mysql_second')
            ->table('fd_documents')
            ->where('Invoice_No', $id)
            ->first();

        if (!$invoice) {
            return redirect()->route('home')->with('error', 'Invoice not found.');
        }

        // Fetch related images and build proper URLs
        $tickets = \DB::connection('mysql_second')
            ->table('fd_documents as doc')
            ->leftJoin('fd_images as img', 'doc.GUID', '=', 'img.DocGUID')
            ->where('doc.Invoice_No', $id)
            ->select(
                'doc.*',
                'img.sourcefileName',
                'img.StorageRevLocationSubID',
                'img.DocGUID',
                'img.stroageRev'
            )
            ->get()
            ->map(function ($ticket) {

                $location = str_pad($ticket->StorageRevLocationSubID, 8, "0", STR_PAD_LEFT);  // => 00000162
                $rev = str_pad($ticket->stroageRev, 8, "0", STR_PAD_LEFT);                    // => 00000001
                $file = urlencode($ticket->sourcefileName);                                   // Encode spaces in filename

                $ticket->ImagePath = "http://193.117.210.98:8081/WebImages/{$location}/{$ticket->DocGUID}.FDD/{$rev}.REV/Files/{$file}";

                return $ticket;
            });

        return view('customer.pages.invoice.ticketsShow', compact('invoice', 'tickets'));
    }



    public function downloadImages(Request $request)
    {
        $images = $request->input('images', []);
        if (empty($images)) {
            return redirect()->back()->with('error', 'No images selected.');
        }

        $zip = new \ZipArchive(); // Use global ZipArchive
        $zipFileName = 'ticket_images_' . time() . '.zip';
        $zipDirectory = storage_path('app/temp');
        $zipFilePath = $zipDirectory . '/' . $zipFileName;

        // Ensure the temp directory exists
        if (!file_exists($zipDirectory)) {
            mkdir($zipDirectory, 0755, true);
        }

        // Try opening ZIP
        if ($zip->open($zipFilePath, \ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()->with('error', 'Failed to create ZIP file. Please check permissions.');
        }

        $downloaded = 0;

        foreach ($images as $imagePath) {
            $url = str_replace(' ', '%20', $imagePath['path']); // Encode spaces
            $fileName = basename($url);

            try {
                $response = \Http::timeout(10)->get($url);

                if ($response->successful()) {
                    $tempFile = $zipDirectory . '/' . $fileName;
                    file_put_contents($tempFile, $response->body());
                    $zip->addFile($tempFile, $fileName);
                    $downloaded++;
                } else {
                    \Log::error("Failed to fetch image: $url");
                }
            } catch (\Exception $e) {
                \Log::error("Exception downloading image: " . $e->getMessage());
                return redirect()->back()->with('error', 'Error fetching image: ' . $e->getMessage());
            }
        }

        $zip->close();

        if ($downloaded === 0) {
            return redirect()->back()->with('error', 'No images could be downloaded. Please try again.');
        }

        if (!file_exists($zipFilePath)) {
            return redirect()->back()->with('error', 'ZIP file was not created. File not found in directory.');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

}
