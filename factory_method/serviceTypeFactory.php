<?php
require_once __DIR__ . '/ServiceTypes.php';

class serviceTypeFactory {
    public static function create($service_name): IServiceType {
        $map = [
            'Complete Denture (Upper)' => CompleteDentureUpperService::class,
            'Complete Denture (Lower)' => CompleteDentureLowerService::class,
            'Complete Denture (Both)' => CompleteDentureBothService::class,
            'Partial Denture (Acrylic)' => PartialDentureAcrylicService::class,
            'Partial Denture (Metal Framework)' => PartialDentureMetalFrameworkService::class,
            'Flexible Partial Denture' => FlexiblePartialDentureService::class,
            'Dental Implant' => DentalImplantService::class,
            'Denture Repair' => DentureRepairService::class,
            'Denture Relining' => DentureReliningService::class,
            'Routine Dental Checkup' => RoutineDentalCheckupService::class,
            'Dental X-Ray' => DentalXRayService::class,
            'Oral Hygiene Instruction' => OralHygieneInstructionService::class,
            'In-Office Teeth Whitening' => InOfficeTeethWhiteningService::class,
            'Take-Home Whitening Kit' => TakeHomeWhiteningKitService::class,
            'Combination Whitening' => CombinationWhiteningService::class,
            'Metal Braces' => MetalBracesService::class,
            'Ceramic Braces' => CeramicBracesService::class,
            'Lingual Braces' => LingualBracesService::class,
            'Invisibela Aligner' => InvisibelAlignerService::class,
            'Composite Filling' => CompositeFillingService::class,
            'Ceramic Filling' => CeramicFillingService::class,
            'Glass Ionomer Filling' => GlassIonomerFillingService::class,
            'Root Canal Treatment' => RootCanalTreatmentService::class,
            'Scaling and Polishing' => ScalingPolishingService::class,
            'Crown' => CrownService::class,
            'Bridge' => BridgeService::class,
            'Extraction' => ExtractionService::class,
        ];
        if (!isset($map[$service_name])) {
            throw new Exception("Service type not supported: " . $service_name);
        }
        return new $map[$service_name]();
    }
}
