<?php

namespace App\Http\Controllers;

use App\Exports\RapportDiagnosticsExport;
use App\Http\Requests\StoreRapportDiagnosticRequest;
use App\Http\Requests\UpdateRapportDiagnosticRequest;
use App\Models\Direction;
use App\Models\RapportDiagnostic;
use App\Models\Service;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class RapportDiagnosticController extends Controller
{
    public function index(Request $request)
    {
        $query = RapportDiagnostic::query()->with(['direction', 'service'])
            ->orderBy('date_diagnostic', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('month')) {
            $query->whereMonth('date_diagnostic', date('m', strtotime($request->string('month'))))
                ->whereYear('date_diagnostic', date('Y', strtotime($request->string('month'))));
        }

        $items = $query->paginate(15)->withQueryString();

        return view('rapport_diagnostics.index', compact('items'));
    }

    public function create()
    {
        $directions = Direction::orderBy('nom')->get();
        return view('rapport_diagnostics.create', compact('directions'));
    }

    public function store(StoreRapportDiagnosticRequest $request)
    {
        $data = $request->validated();

        if (empty($data['lieu_intervention'])) {
            $data['lieu_intervention'] = 'SITE MINIER LAUZOUA';
        }

        RapportDiagnostic::create($data);

        return redirect()->route('rapport-diagnostics.index')->with('success', 'Rapport de diagnostic enregistré avec succès.');
    }

    public function edit(RapportDiagnostic $rapportDiagnostic)
    {
        $directions = Direction::orderBy('nom')->get();
        $services = Service::where('direction_id', $rapportDiagnostic->direction_id)->orderBy('nom')->get();

        return view('rapport_diagnostics.edit', compact('rapportDiagnostic', 'directions', 'services'));
    }

    public function update(UpdateRapportDiagnosticRequest $request, RapportDiagnostic $rapportDiagnostic)
    {
        $data = $request->validated();
        $clientUpdatedAt = $request->input('updated_at');

        if (!$clientUpdatedAt || optional($rapportDiagnostic->updated_at)->toISOString() !== $clientUpdatedAt) {
            throw ValidationException::withMessages([
                'concurrency' => "Ce rapport de diagnostic a été modifié par un autre utilisateur. Rechargez la page avant de réessayer.",
            ]);
        }

        if (empty($data['lieu_intervention'])) {
            $data['lieu_intervention'] = 'SITE MINIER LAUZOUA';
        }

        $rapportDiagnostic->update($data);

        return redirect()->route('rapport-diagnostics.index')->with('success', 'Rapport de diagnostic modifié avec succès.');
    }

    public function destroy(RapportDiagnostic $rapportDiagnostic)
    {
        $rapportDiagnostic->delete();

        return redirect()->route('rapport-diagnostics.index')->with('success', 'Rapport de diagnostic supprimé.');
    }

    public function exportExcel(Request $request)
    {
        [$from, $to] = $this->resolveDateRange($request);
        $title = $this->buildExportTitle($from, $to);

        return Excel::download(new RapportDiagnosticsExport($from, $to), $title . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = RapportDiagnostic::with(['direction', 'service'])
            ->orderBy('date_diagnostic', 'desc')
            ->orderBy('id', 'desc');

        [$from, $to] = $this->resolveDateRange($request);

        if ($from) {
            $query->whereDate('date_diagnostic', '>=', $from);
        }

        if ($to) {
            $query->whereDate('date_diagnostic', '<=', $to);
        }

        $items = $query->get();
        $title = $this->buildExportTitle($from, $to);

        $pdf = Pdf::loadView('rapport_diagnostics.pdf', compact('items', 'title'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($title . '.pdf');
    }

    public function exportSinglePdf(RapportDiagnostic $rapportDiagnostic)
    {
        $rapportDiagnostic->load(['direction', 'service']);
        $items = collect([$rapportDiagnostic]);
        $title = 'SIM-CML RAPPORT DE DIAGNOSTIC DU '
            . optional($rapportDiagnostic->date_diagnostic)->format('d-m-Y');

        $pdf = Pdf::loadView('rapport_diagnostics.pdf', compact('items', 'title'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($title . '.pdf');
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function resolveDateRange(Request $request): array
    {
        $from = $request->get('from');
        $to = $request->get('to');

        if (!$from && !$to && $request->filled('month')) {
            $monthDate = Carbon::parse($request->string('month'));
            $from = $monthDate->copy()->startOfMonth()->format('Y-m-d');
            $to = $monthDate->copy()->endOfMonth()->format('Y-m-d');
        }

        return [$from, $to];
    }

    private function buildExportTitle(?string $from, ?string $to): string
    {
        if ($from && $to) {
            $fromLabel = Carbon::parse($from)->format('d-m-Y');
            $toLabel = Carbon::parse($to)->format('d-m-Y');
            return 'SIM-CML RAPPORT DE DIAGNOSTIC "DU ' . $fromLabel . ' AU ' . $toLabel . '"';
        }

        return 'SIM-CML RAPPORT DE DIAGNOSTIC';
    }
}
