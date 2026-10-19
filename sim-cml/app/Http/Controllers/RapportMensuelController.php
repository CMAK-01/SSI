<?php

namespace App\Http\Controllers;

use App\Exports\RapportMensuelsExport;
use App\Imports\RapportMensuelsImport;
use App\Http\Requests\StoreRapportMensuelRequest;
use App\Http\Requests\UpdateRapportMensuelRequest;
use App\Models\Direction;
use App\Models\RapportMensuel;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class RapportMensuelController extends Controller
{
    public function index(Request $request)
    {
        $query = RapportMensuel::query()->with(['direction', 'service'])
            ->orderBy('date_intervention', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('month')) {
            $query->whereMonth('date_intervention', date('m', strtotime($request->string('month'))))
                ->whereYear('date_intervention', date('Y', strtotime($request->string('month'))));
        }

        $items = $query->paginate(15)->withQueryString();

        return view('rapport_mensuels.index', compact('items'));
    }

    public function create()
    {
        $directions = Direction::orderBy('nom')->get();
        return view('rapport_mensuels.create', compact('directions'));
    }

    public function store(StoreRapportMensuelRequest $request)
    {
        RapportMensuel::create($request->validated());

        return redirect()->route('rapport-mensuels.index')->with('success', 'Rapport mensuel enregistré avec succès.');
    }

    public function edit(RapportMensuel $rapportMensuel)
    {
        $directions = Direction::orderBy('nom')->get();
        $services = Service::where('direction_id', $rapportMensuel->direction_id)->orderBy('nom')->get();

        return view('rapport_mensuels.edit', compact('rapportMensuel', 'directions', 'services'));
    }

    public function update(UpdateRapportMensuelRequest $request, RapportMensuel $rapportMensuel)
    {
        $data = $request->validated();
        $clientUpdatedAt = $request->input('updated_at');

        if (!$clientUpdatedAt || optional($rapportMensuel->updated_at)->toISOString() !== $clientUpdatedAt) {
            throw ValidationException::withMessages([
                'concurrency' => "Ce rapport a été modifié par un autre utilisateur. Rechargez la page avant de réessayer.",
            ]);
        }

        $rapportMensuel->update($data);

        return redirect()->route('rapport-mensuels.index')->with('success', 'Rapport mensuel modifié avec succès.');
    }

    public function destroy(RapportMensuel $rapportMensuel)
    {
        $rapportMensuel->delete();

        return redirect()->route('rapport-mensuels.index')->with('success', 'Rapport mensuel supprimé.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'fichier_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $import = new RapportMensuelsImport();
        Excel::import($import, $request->file('fichier_excel'));

        $message = "{$import->getImportedCount()} rapport(s) importé(s) avec succès.";
        $errors = $import->getErrors();

        if (!empty($errors)) {
            return redirect()->route('rapport-mensuels.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        }

        return redirect()->route('rapport-mensuels.index')->with('success', $message);
    }

    public function exportExcel(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        if (!$from && !$to && $request->filled('month')) {
            $monthDate = Carbon::parse($request->string('month'));
            $from = $monthDate->copy()->startOfMonth()->format('Y-m-d');
            $to = $monthDate->copy()->endOfMonth()->format('Y-m-d');
        }

        $title = $this->buildExportTitle($from, $to);

        return Excel::download(new RapportMensuelsExport($from, $to), $title . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = RapportMensuel::with(['direction', 'service'])
            ->orderBy('date_intervention', 'desc')
            ->orderBy('id', 'desc');

        $from = $request->get('from');
        $to = $request->get('to');

        if (!$from && !$to && $request->filled('month')) {
            $monthDate = Carbon::parse($request->string('month'));
            $from = $monthDate->copy()->startOfMonth()->format('Y-m-d');
            $to = $monthDate->copy()->endOfMonth()->format('Y-m-d');
        }

        if ($from) {
            $query->whereDate('date_intervention', '>=', $from);
        }

        if ($to) {
            $query->whereDate('date_intervention', '<=', $to);
        }

        $items = $query->get();
        $title = $this->buildExportTitle($from, $to);

        $pdf = Pdf::loadView('rapport_mensuels.pdf', compact('items', 'title'));
        return $pdf->download($title . '.pdf');
    }

    private function buildExportTitle(?string $from, ?string $to): string
    {
        if ($from && $to) {
            $fromLabel = Carbon::parse($from)->format('d-m-Y');
            $toLabel = Carbon::parse($to)->format('d-m-Y');
            return 'SIM-CML RAPPORT MENSUEL "DU ' . $fromLabel . ' AU ' . $toLabel . '"';
        }

        return 'SIM-CML RAPPORT MENSUEL';
    }
}
