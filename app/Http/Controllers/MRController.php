<?php

namespace App\Http\Controllers;

use App\Models\ManagementElement;
use App\Models\ManagementSubElement;
use App\Models\ManagementPengawasan;
use App\Models\ManagementTopic;
use App\Models\Uraian;
use Illuminate\Http\Request;

class MRController extends Controller
{
    public function index()
    {
        $active = 18;
        $elements = ManagementElement::with(['ManagementSubElement.ManagementTopic.Uraian'])->get();
        $pengawasan = ManagementPengawasan::all()->groupBy('id_management_uraian');
        $subElements = ManagementSubElement::all();
        $topics = ManagementTopic::all();
        return view('tataKelola.MR', compact('active', 'elements', 'pengawasan', 'subElements', 'topics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'elemen' => 'required|string|max:255',
        ]);

        ManagementElement::create([
            'elemen' => $request->elemen,
        ]);

        return redirect()->route('MR.index')->with('success', 'Elemen berhasil ditambahkan.');
    }

    public function subElemenStore(Request $request)
    {
        $validated = $request->validate([
            'id_management_element' => 'required|exists:management_elements,id',
            'sub_elemen' => 'required|string|max:255',
        ]);
    
        ManagementSubElement::create($validated);
    
        return redirect()->back()->with('success', 'Sub Elemen added successfully.');
    }

    public function uraianStore(Request $request)
    {
        $request->validate([
            'id_management_topic' => 'required|exists:management_topics,id',
            'uraian' => 'required|string',
            'level' => 'required|integer|min:1',
        ]);

        Uraian::create([
            'id_management_topic' => $request->input('id_management_topic'),
            'uraian' => $request->input('uraian'),
            'level' => $request->input('level'),
        ]);

        return redirect()->back()->with('success', 'Uraian berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $active = 18;
        $element = ManagementElement::findOrFail($id);
        return view('elements.edit', compact('active', 'element'));
    }

    public function uraianEdit($id)
    {
        $active = 18;
        $uraian = Uraian::findOrFail($id);
        return view('your-view-name', compact('active', 'uraian')); 
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'elemen' => 'required|string|max:255',
        ]);

        $element = ManagementElement::findOrFail($id);
        $element->update(['elemen' => $request->input('elemen')]);

        return redirect()->route('MR.index')->with('success', 'Elemen berhasil diperbarui.');
    }

    public function subElemenUpdate(Request $request, $id)
    {
        $request->validate([
            'sub_elemen' => 'required|string|max:255',
        ]);

        $subElement = ManagementSubElement::findOrFail($id);
        $subElement->update(['sub_elemen' => $request->input('sub_elemen')]);

        return redirect()->route('MR.index')->with('success', 'Sub Elemen berhasil diperbarui.');
    }

    public function uraianUpdate(Request $request, $id)
    {
        $uraian = Uraian::findOrFail($id);
        $uraian->update($request->all());

        return redirect()->back()->with('success', 'Uraian berhasil diperbarui.');
    }

    public function topicUpdate(Request $request, $id)
    {
        $request->validate([
            'topik' => 'required|string|max:255',
        ]);

        $topic = ManagementTopic::findOrFail($id);
        $topic->update(['topik' => $request->input('topik')]);

        return redirect()->back()->with('success', 'Topik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $element = ManagementElement::findOrFail($id);
        $element->delete();

        return redirect()->route('MR.index')->with('success', 'Elemen berhasil dihapus.');
    }

    public function subElemenDestroy($id)
    {
        $subElement = ManagementSubElement::findOrFail($id);
        $subElement->delete();

        return redirect()->back()->with('success', 'Sub Elemen berhasil dihapus.');
    }

    public function uraianDestroy($id)
    {
        $uraian = Uraian::findOrFail($id);
        $uraian->delete();

        return redirect()->back()->with('success', 'Uraian berhasil dihapus.');
    }
    

    public function topicDestroy($id)
    {
        $topic = ManagementTopic::findOrFail($id);
        $topic->delete();

        return redirect()->back()->with('success', 'Topik berhasil dihapus.');
    }

    public function topicStore(Request $request)
    {
        $request->validate([
            'id_management_element' => 'required|exists:management_elements,id',
            'id_management_sub_element' => 'nullable|exists:management_sub_elements,id',
            'topik' => 'required|string|max:255',
        ]);
    
        $topic = new ManagementTopic();
        $topic->id_management_sub_element = $request->id_management_sub_element;
        $topic->topik = $request->topik;
        $topic->save();
    
        return redirect()->back()->with('success', 'Topik berhasil ditambahkan.');
    }

    public function getSubElements($elementId)
    {
        $subElements = ManagementSubElement::where('id_management_element', $elementId)->get();
        $options = '<option value="" disabled selected>Pilih Sub Elemen</option>';
        foreach ($subElements as $subElement) {
            $options .= "<option value='{$subElement->id}'>{$subElement->sub_elemen}</option>";
        }
        return response($options);
    }
}
