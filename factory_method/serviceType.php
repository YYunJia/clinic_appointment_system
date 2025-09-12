<?php
require_once __DIR__ . '/../IServiceType.php';

// DENTURE
class CompleteDentureUpperService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Denture',
            'name' => 'Complete Denture (Upper)'
        ];
    }
}
class CompleteDentureLowerService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Denture',
            'name' => 'Complete Denture (Lower)'
        ];
    }
}
class CompleteDentureBothService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Denture',
            'name' => 'Complete Denture (Both)'
        ];
    }
}
class PartialDentureAcrylicService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Denture',
            'name' => 'Partial Denture (Acrylic)'
        ];
    }
}
class PartialDentureMetalFrameworkService implements IServiceType {
    public function getServicData(): array {
        return [
            'type' => 'Denture',
            'name' => 'Partial Denture (Metal Framework)'
        ];
    }
}
class FlexiblePartialDentureService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Denture',
            'name' => 'Flexible Partial Denture'
        ];
    }
}
class DentalImplantService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Denture',
            'name' => 'Dental Implant'
        ];
    }
}
class DentureRepairService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Denture',
            'name' => 'Denture Repair'
        ];
    }
}
class DentureReliningService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Denture',
            'name' => 'Denture Relining'
        ];
    }
}

// CHECKUP
class RoutineDentalCheckupService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Checkup',
            'name' => 'Routine Dental Checkup'
        ];
    }
}
class DentalXRayService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Checkup',
            'name' => 'Dental X-Ray'
        ];
    }
}
class OralHygieneInstructionService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Checkup',
            'name' => 'Oral Hygiene Instruction'
        ];
    }
}

// WHITENING
class InOfficeTeethWhiteningService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Whitening',
            'name' => 'In-Office Teeth Whitening'
        ];
    }
}
class TakeHomeWhiteningKitService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Whitening',
            'name' => 'Take-Home Whitening Kit'
        ];
    }
}
class CombinationWhiteningService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Whitening',
            'name' => 'Combination Whitening'
        ];
    }
}

// BRACES
class MetalBracesService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Braces',
            'name' => 'Metal Braces'
        ];
    }
}
class CeramicBracesService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Braces',
            'name' => 'Ceramic Braces'
        ];
    }
}
class LingualBracesService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Braces',
            'name' => 'Lingual Braces'
        ];
    }
}
class InvisibelAlignerService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Braces',
            'name' => 'Invisibela Aligner'
        ];
    }
}

// FILLING
class CompositeFillingService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Filling',
            'name' => 'Composite Filling'
        ];
    }
}
class CeramicFillingService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Filling',
            'name' => 'Ceramic Filling'
        ];
    }
}
class GlassIonomerFillingService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Filling',
            'name' => 'Glass Ionomer Filling'
        ];
    }
}

// ENDODONTIC
class RootCanalTreatmentService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Endodontic',
            'name' => 'Root Canal Treatment'
        ];
    }
}

// PREVENTIVE
class ScalingPolishingService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Preventive',
            'name' => 'Scaling and Polishing'
        ];
    }
}

// CROWN & BRIDGE
class CrownService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Crown and Bridge',
            'name' => 'Crown'
        ];
    }
}

class BridgeService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Crown and Bridge',
            'name' => 'Bridge'
        ];
    }
}

class ExtractionService implements IServiceType {
    public function getServiceData(): array {
        return [
            'type' => 'Surgical',
            'name' => 'Extraction'
        ];
    }
}

