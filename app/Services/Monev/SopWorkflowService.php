<?php

namespace App\Services\Monev;

use App\Models\Sop;

class SopWorkflowService
{
    public function hasMonitoring(Sop $sop): bool
    {
        if (array_key_exists('monitorings_count', $sop->getAttributes())) {
            return (int) $sop->monitorings_count > 0;
        }

        if ($sop->relationLoaded('latestMonitoring')) {
            return $sop->latestMonitoring !== null;
        }

        return $sop->monitorings()->exists();
    }

    public function hasEvaluasi(Sop $sop): bool
    {
        if (array_key_exists('evaluasis_count', $sop->getAttributes())) {
            return (int) $sop->evaluasis_count > 0;
        }

        if ($sop->relationLoaded('latestEvaluasi')) {
            return $sop->latestEvaluasi !== null;
        }

        return $sop->evaluasis()->exists();
    }

    public function canEvaluate(Sop $sop): bool
    {
        return $this->hasMonitoring($sop);
    }

    public function canRevise(Sop $sop): bool
    {
        return $this->hasMonitoring($sop) && $this->hasEvaluasi($sop);
    }

    public function revisionState(Sop $sop): array
    {
        $hasMonitoring = $this->hasMonitoring($sop);
        $hasEvaluasi = $this->hasEvaluasi($sop);

        if (!$hasMonitoring) {
            return [
                'has_monitoring' => false,
                'has_evaluasi' => $hasEvaluasi,
                'can_revise' => false,
                'label' => 'Menunggu Monitoring',
                'message' => 'Revisi belum dapat dilakukan. Buat monitoring SOP terlebih dahulu.',
            ];
        }

        if (!$hasEvaluasi) {
            return [
                'has_monitoring' => true,
                'has_evaluasi' => false,
                'can_revise' => false,
                'label' => 'Menunggu Evaluasi',
                'message' => 'Revisi belum dapat dilakukan. Lengkapi evaluasi setelah monitoring.',
            ];
        }

        return [
            'has_monitoring' => true,
            'has_evaluasi' => true,
            'can_revise' => true,
            'label' => 'Siap Revisi',
            'message' => 'SOP siap direvisi. Monitoring dan evaluasi sudah lengkap.',
        ];
    }
}
