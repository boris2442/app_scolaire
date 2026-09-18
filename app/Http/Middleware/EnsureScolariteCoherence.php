<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Trimestre;
use App\Models\Sequence;
use App\Models\Evaluation;
use App\Services\ScolariteService;

class EnsureScolariteCoherence
{
    public function __construct(protected ScolariteService $scolariteService) {}

    public function handle(Request $request, Closure $next)
    {
        // 1. Extraction unifiée via helper
        $trimestreId = $this->extractParam($request, ['trimestre', 'trimestre_id']);
        $sequenceId  = $this->extractParam($request, ['sequence', 'sequence_id']);

        // 2. Fallback via Évaluation si aucun paramètre direct
        if (!$sequenceId && !$trimestreId) {
            $evaluationId = $this->extractParam($request, ['evaluation', 'evaluation_id', 'id']);
            if ($evaluationId) {
                $eval = $evaluationId instanceof Evaluation ? $evaluationId : Evaluation::find($evaluationId);
                $sequenceId = $eval?->sequence_id;
            }
        }

        // 3. Validation par le Service Métier
        if ($sequenceId) {
            $sequence = $sequenceId instanceof Sequence ? $sequenceId : Sequence::find($sequenceId);
            if ($sequence) {
                $trimestre = $trimestreId 
                    ? ($trimestreId instanceof Trimestre ? $trimestreId : Trimestre::find($trimestreId))
                    : null;
                $this->scolariteService->validateSequence($sequence, $trimestre);
            }
        } elseif ($trimestreId) {
            $trimestre = $trimestreId instanceof Trimestre ? $trimestreId : Trimestre::find($trimestreId);
            if ($trimestre) {
                $this->scolariteService->validateTrimestre($trimestre);
            }
        }

        return $next($request);
    }

    /**
     * Extrait un paramètre depuis la route ou le corps de la requête
     */
    private function extractParam(Request $request, array $keys): mixed
    {
        foreach ($keys as $key) {
            if ($value = $request->route($key) ?? $request->input($key)) {
                return $value;
            }
        }
        return null;
    }
}
