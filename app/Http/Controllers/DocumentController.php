<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Deal;
use App\Models\Property;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DocumentController extends Controller
{
    /**
     * Show the upload document form.
     */
    public function create(Request $request)
    {
        $preselected_deal_id = $request->query('deal_id');
        $preselected_property_id = $request->query('property_id');

        $deals = Deal::orderBy('property_name')->get();
        $properties = Property::orderBy('property_name')->get();

        return view('upload-document', compact('deals', 'properties', 'preselected_deal_id', 'preselected_property_id'));
    }

    /**
     * Store the uploaded document.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|string|in:agreement document,booking confirmation,sale document,invoice,ID proof',
            'file' => 'required|file|mimes:pdf,docx,jpg,png|max:10240', // max 10MB
            'deal_id' => 'nullable|integer|exists:deals,id',
            'property_id' => 'nullable|integer|exists:properties,id',
        ], [
            'document_type.in' => 'Please select a valid document type.',
            'file.mimes' => 'Only PDF, DOCX, JPG, and PNG files are allowed.',
            'file.max' => 'Maximum file upload size is 10MB.',
        ]);

        if (empty($data['deal_id']) && empty($data['property_id'])) {
            return back()->withErrors(['association' => 'The document must be associated with either a Deal or a Property.'])->withInput();
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $file->getClientOriginalName());
            
            // Create uploads directory if not exists
            $uploadPath = public_path('uploads/documents');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            
            $file->move($uploadPath, $fileName);
            $data['file_path'] = 'uploads/documents/' . $fileName;
        }

        $data['uploaded_by'] = Auth::user()->name;

        $document = Document::create($data);

        // Audit Log entry
        $assocDetails = '';
        if ($document->deal_id) {
            $assocDetails = "Deal #{$document->deal_id}";
        } else {
            $assocDetails = "Property #{$document->property_id}";
        }

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Upload',
            'module' => 'Documents',
            'description' => "Uploaded document '{$document->document_name}' ({$document->document_type}) for {$assocDetails}"
        ]);

        // Redirect back contextually
        if ($document->deal_id) {
            return redirect('/deal-details/' . $document->deal_id)->with('success', 'Document uploaded successfully!');
        } else {
            return redirect('/properties/' . $document->property_id)->with('success', 'Document uploaded successfully!');
        }
    }

    /**
     * Download the specified document.
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);
        $fullPath = public_path($document->file_path);

        if (!File::exists($fullPath)) {
            return back()->with('warning', 'The physical file could not be found on the server.');
        }

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Download',
            'module' => 'Documents',
            'description' => "Downloaded document '{$document->document_name}' (#{$document->id})"
        ]);

        return response()->download($fullPath, $document->document_name . '.' . pathinfo($fullPath, PATHINFO_EXTENSION));
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $fullPath = public_path($document->file_path);

        // Delete physical file
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }

        $dealId = $document->deal_id;
        $propertyId = $document->property_id;
        $docName = $document->document_name;

        $document->delete();

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Delete',
            'module' => 'Documents',
            'description' => "Deleted document '{$docName}'"
        ]);

        return redirect()->back()->with('success', 'Document deleted successfully!');
    }
}
