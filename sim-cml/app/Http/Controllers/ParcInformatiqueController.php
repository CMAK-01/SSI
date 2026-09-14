<?php

namespace App\Http\Controllers;

use App\Exports\ParcInformatiquesExport;
use App\Imports\ParcInformatiquesImport;
use App\Http\Requests\StoreParcInformatiqueRequest;
use App\Http\Requests\UpdateParcInformatiqueRequest;
use App\Models\Direction;
use App\Models\ParcInformatique;
use App\Models\ParcInformatiqueNote;
use App\Models\ParcInformatiqueNoteItem;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ParcInformatiqueController extends Controller
{
    public function index()
    {
        $items = ParcInformatique::with(['direction', 'service', 'noteItems.note'])->latest()->paginate(15);
        return view('parc_informatiques.index', compact('items'));
    }

    public function create()
    {
        $directions = Direction::orderBy('nom')->get();
        $notes = ParcInformatiqueNote::orderBy('label')->get();

        return view('parc_informatiques.create', compact('directions', 'notes'));
    }

    public function store(StoreParcInformatiqueRequest $request)
    {
        $data = $request->validated();
        $noteIds = $data['note_ids'] ?? [];
        $autrePrecision = $data['autre_precision'] ?? null;
        unset($data['note_ids'], $data['autre_precision']);

        $item = ParcInformatique::create($data);

        foreach ($noteIds as $noteId) {
            $note = ParcInformatiqueNote::find($noteId);
            ParcInformatiqueNoteItem::create([
                'parc_informatique_id' => $item->id,
                'parc_informatique_note_id' => $noteId,
                'autre_precision' => $note && $note->code === 'AUTRE' ? $autrePrecision : null,
            ]);
        }

        return redirect()->route('parc-informatiques.index')->with('success', 'Équipement enregistré avec succès.');
    }

    public function edit(ParcInformatique $parcInformatique)
    {
        $directions = Direction::orderBy('nom')->get();
        $services = Service::where('direction_id', $parcInformatique->direction_id)->orderBy('nom')->get();
        $notes = ParcInformatiqueNote::orderBy('label')->get();
        $selectedNoteIds = $parcInformatique->noteItems()->pluck('parc_informatique_note_id')->all();
        $autrePrecision = optional(
            $parcInformatique->noteItems()->whereHas('note', fn($q) => $q->where('code', 'AUTRE'))->first()
        )->autre_precision;

        return view('parc_informatiques.edit', compact(
            'parcInformatique',
            'directions',
            'services',
            'notes',
            'selectedNoteIds',
            'autrePrecision'
        ));
    }

    public function update(UpdateParcInformatiqueRequest $request, ParcInformatique $parcInformatique)
    {
        $data = $request->validated();
        $clientUpdatedAt = $request->input('updated_at');

        if (!$clientUpdatedAt || optional($parcInformatique->updated_at)->toISOString() !== $clientUpdatedAt) {
            throw ValidationException::withMessages([
                'concurrency' => "Cet équipement a été modifié par un autre utilisateur. Rechargez la page avant de réessayer.",
            ]);
        }

        $noteIds = $data['note_ids'] ?? [];
        $autrePrecision = $data['autre_precision'] ?? null;
        unset($data['note_ids'], $data['autre_precision']);

        DB::transaction(function () use ($parcInformatique, $data, $noteIds, $autrePrecision) {
            $parcInformatique->update($data);
            $parcInformatique->noteItems()->delete();

            foreach ($noteIds as $noteId) {
                $note = ParcInformatiqueNote::find($noteId);
                ParcInformatiqueNoteItem::create([
                    'parc_informatique_id' => $parcInformatique->id,
                    'parc_informatique_note_id' => $noteId,
                    'autre_precision' => $note && $note->code === 'AUTRE' ? $autrePrecision : null,
                ]);
            }
        });

        return redirect()->route('parc-informatiques.index')->with('success', 'Équipement modifié avec succès.');
    }

    public function destroy(ParcInformatique $parcInformatique)
    {
        $parcInformatique->delete();
        return redirect()->route('parc-informatiques.index')->with('success', 'Équipement supprimé.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'fichier_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $import = new ParcInformatiquesImport();
        Excel::import($import, $request->file('fichier_excel'));

        $message = "{$import->getImportedCount()} équipement(s) importé(s) avec succès.";
        $errors = $import->getErrors();

        if (!empty($errors)) {
            return redirect()->route('parc-informatiques.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        }

        return redirect()->route('parc-informatiques.index')->with('success', $message);
    }

    public function exportExcel()
    {
        return Excel::download(new ParcInformatiquesExport(), 'parc_informatique_mine.xlsx');
    }

    public function exportPdf()
    {
        $items = ParcInformatique::with(['direction', 'service', 'noteItems.note'])->latest()->get();
        $pdf = Pdf::loadView('parc_informatiques.pdf', compact('items'));
        return $pdf->download('parc_informatique_mine.pdf');
    }
}
