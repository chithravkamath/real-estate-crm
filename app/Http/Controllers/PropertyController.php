<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('property_name', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%')
                  ->orWhere('owner_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('property_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->bedrooms);
        }

        if ($request->filled('price_range')) {
            if ($request->price_range == 'under_500k') {
                $query->where('price', '<', 500000);
            } elseif ($request->price_range == '500k_1.5m') {
                $query->whereBetween('price', [500000, 1500000]);
            } elseif ($request->price_range == '1.5m_5m') {
                $query->whereBetween('price', [1500000, 5000000]);
            } elseif ($request->price_range == '5m_plus') {
                $query->where('price', '>', 5000000);
            }
        }

        $properties = $query->orderBy('property_name')->paginate(10);

        return view('properties', compact('properties'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'property_name' => 'required|string|max:255',
            'property_type' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'area' => 'nullable|integer|min:0',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'garages' => 'nullable|integer|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:100',
            'status' => 'nullable|string|max:100',
            'owner_name' => 'nullable|string|max:255',
            'owner_phone' => 'nullable|digits:10',
            'owner_email' => 'nullable|email|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'document' => 'nullable|mimes:pdf,doc,docx|max:5000',
            'video' => 'nullable|mimes:mp4,mov,avi|max:20480',
        ]);

        $data['amenities'] = $data['amenities'] ?? [];

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('property-images'), $imageName);
            $data['image'] = $imageName;
        }

        if ($request->hasFile('document')) {
            $documentName = time() . '.' . $request->document->extension();
            $request->document->move(public_path('property-documents'), $documentName);
            $data['document'] = $documentName;
        }

        if ($request->hasFile('video')) {
            $videoName = time() . '.' . $request->video->extension();
            $request->video->move(public_path('property-videos'), $videoName);
            $data['video'] = $videoName;
        }

        Property::create($data);

        if ($request->hasFile('video')) {
            AuditLog::create([
                'user_name' => Auth::user()->name,
                'action' => 'Add',
                'module' => 'Property',
                'description' => 'Uploaded video for property: ' . $data['property_name']
            ]);
        } else {
            AuditLog::create([
                'user_name' => Auth::user()->name,
                'action' => 'Add',
                'module' => 'Property',
                'description' => 'Added new property: ' . $data['property_name']
            ]);
        }

        return redirect('/properties');
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);

        return view('add-property', compact('property'));
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $data = $request->validate([
    'property_name' => 'required|string|max:255',
    'property_type' => 'nullable|string|max:100',
    'price' => 'required|numeric|min:0',
    'location' => 'required|string|max:255',
    'description' => 'nullable|string',
    'area' => 'nullable|integer|min:0',
    'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
    'bedrooms' => 'nullable|integer|min:0',
    'bathrooms' => 'nullable|integer|min:0',
    'garages' => 'nullable|integer|min:0',
    'amenities' => 'nullable|array',
    'amenities.*' => 'string|max:100',
    'status' => 'nullable|string|max:100',
    'owner_name' => 'nullable|string|max:255',
    'owner_phone' => 'nullable|digits:10',
    'owner_email' => 'nullable|email|max:255',
    'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    'document' => 'nullable|mimes:pdf,doc,docx|max:5000',
    'video' => 'nullable|mimes:mp4,mov,avi|max:20480',
]);

        $data['amenities'] = $data['amenities'] ?? [];

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('property-images'), $imageName);
            $data['image'] = $imageName;
        }

        if ($request->hasFile('document')) {
            $documentName = time() . '.' . $request->document->extension();
            $request->document->move(public_path('property-documents'), $documentName);
            $data['document'] = $documentName;
        }

        if ($request->hasFile('video')) {
            if ($property->video && file_exists(public_path('property-videos/' . $property->video))) {
                @unlink(public_path('property-videos/' . $property->video));
            }
            $videoName = time() . '.' . $request->video->extension();
            $request->video->move(public_path('property-videos'), $videoName);
            $data['video'] = $videoName;
        }

        $property->update($data);

        if ($request->hasFile('video')) {
            AuditLog::create([
                'user_name' => Auth::user()->name,
                'action' => 'Edit',
                'module' => 'Property',
                'description' => 'Updated property video: ' . $property->property_name
            ]);
        } else {
            AuditLog::create([
                'user_name' => Auth::user()->name,
                'action' => 'Edit',
                'module' => 'Property',
                'description' => 'Edited property: ' . $property->property_name
            ]);
        }

        return redirect('/properties');
    }
    public function show(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        
        $docQuery = $property->documents();
        
        if ($request->filled('doc_search')) {
            $search = $request->doc_search;
            $docQuery->where('document_name', 'like', '%' . $search . '%');
        }
        
        if ($request->filled('doc_type')) {
            $docQuery->where('document_type', $request->doc_type);
        }
        
        if ($request->filled('doc_date')) {
            $docQuery->whereDate('created_at', $request->doc_date);
        }
        
        $documents = $docQuery->orderBy('created_at', 'desc')->get();

        return view('property-details', compact('property', 'documents'));
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        AuditLog::create([
            'user_name' => Auth::user()->name,
            'action' => 'Delete',
            'module' => 'Property',
            'description' => 'Deleted property: ' . $property->property_name
        ]);

        return redirect('/properties');
    }
}
