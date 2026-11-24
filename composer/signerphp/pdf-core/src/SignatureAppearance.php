<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Exception\PdfCoreStructureException;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValue;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueObject;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueReference;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueSimple;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Utils\Img;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Utils\Str;
/** @internal */
class SignatureAppearance
{
    private ?string $backgroundImagePath = null;
    private ?AppearanceXObject $xObject = null;
    /** Path to the signature image, placed inside the n2 xObject layer at $signatureImageFrame. */
    private ?string $signatureImagePath = null;
    /** [x, y, w, h] placement within the bbox for the signature image; null = full bbox. */
    private ?array $signatureImageFrame = null;
    private array $rectToAppear = [0, 0, 0, 0];
    private int $pageToAppear = 0;
    private PdfDocument $pdfDocument;
    private PDFObject $annotationObject;
    private PDFValue $pageRotation;
    public static function new() : self
    {
        return new self();
    }
    /** @deprecated Use getRect() */
    public function getReact() : array
    {
        return $this->getRect();
    }
    public function getRect() : array
    {
        return $this->rectToAppear;
    }
    public function getPageToAppear() : int
    {
        return $this->pageToAppear;
    }
    public function getBackgroundImage() : ?string
    {
        return $this->backgroundImagePath;
    }
    public function getSignatureImage() : ?string
    {
        return $this->signatureImagePath;
    }
    public function getXObject() : ?AppearanceXObject
    {
        return $this->xObject;
    }
    public function addSignAppearanceInPage(int $pageToAppear) : self
    {
        $this->pageToAppear = $pageToAppear;
        return $this;
    }
    public function withRect(array $rect) : self
    {
        if (\count($rect) !== 4) {
            throw new PdfCoreStructureException('Signature rectangle must contain exactly 4 coordinates.');
        }
        $this->rectToAppear = $rect;
        return $this;
    }
    public function withBackgroundImage(?string $backgroundImagePath) : self
    {
        $this->backgroundImagePath = $backgroundImagePath;
        return $this;
    }
    public function withXObject(?AppearanceXObject $xObject) : self
    {
        $this->xObject = $xObject;
        return $this;
    }
    public function withSignatureImage(?string $signatureImagePath) : self
    {
        $this->signatureImagePath = $signatureImagePath;
        return $this;
    }
    public function withSignatureImageFrame(?array $signatureImageFrame) : self
    {
        $this->signatureImageFrame = $signatureImageFrame;
        return $this;
    }
    public function withPdfDocument(PdfDocument $pdfDocument) : self
    {
        $this->pdfDocument = $pdfDocument;
        return $this;
    }
    public function withAnnotationObject(PDFObject $annotationObject) : self
    {
        $this->annotationObject = $annotationObject;
        return $this;
    }
    public function withPageRotate(PDFValue $pageRotation) : self
    {
        $this->pageRotation = $pageRotation;
        return $this;
    }
    public function generate() : PDFObject
    {
        $pdfDocument = $this->requirePdfDocument();
        $annotationObject = $this->requireAnnotationObject();
        $pageRotation = $this->requirePageRotation();
        $pageSize = $pdfDocument->getPageInfo()->getPageSize($this->pageToAppear);
        if ($pageSize === null || !isset($pageSize[0])) {
            throw new PdfCoreStructureException('Could not resolve page size for signature appearance');
        }
        $pageSize = \explode(' ', (string) $pageSize[0]->val());
        $pageSizeH = (float) $pageSize[3] - (float) $pageSize[1];
        $bbox = [0, 0, $this->rectToAppear[2] - $this->rectToAppear[0], $this->rectToAppear[3] - $this->rectToAppear[1]];
        $formObject = $pdfDocument->createObject(['BBox' => $bbox, 'Subtype' => '/Form', 'Type' => '/XObject', 'Group' => ['Type' => '/Group', 'S' => '/Transparency', 'CS' => '/DeviceRGB']]);
        // Create n0 layer for background image (if provided)
        $layerN0 = $pdfDocument->createObject(['BBox' => $bbox, 'Subtype' => '/Form', 'Type' => '/XObject', 'Resources' => new PDFValueObject()]);
        if ($this->backgroundImagePath !== null) {
            // "Contain" scaling: scale the image to fit inside the bbox while
            // preserving its natural aspect ratio, then centre it.
            $naturalSize = $this->imageNaturalSize($this->backgroundImagePath);
            if ($naturalSize === null) {
                throw new PdfCoreStructureException('Could not determine natural size of background image.');
            }
            $scale = \min($bbox[2] / $naturalSize[0], $bbox[3] / $naturalSize[1]);
            $drawW = $naturalSize[0] * $scale;
            $drawH = $naturalSize[1] * $scale;
            $drawX = ($bbox[2] - $drawW) / 2.0;
            $drawY = ($bbox[3] - $drawH) / 2.0;
            $result = Img::addImage(static fn(array $value): PDFObject => $pdfDocument->createObject($value), $this->backgroundImagePath, $drawX, $drawY, $drawW, $drawH, (float) $pageRotation->val());
            $layerN0['Resources'] = $result['resources'];
            $layerN0->setStream($result['command'], \false);
        } else {
            $layerN0->setStream('% DSBlank' . \PHP_EOL, \false);
        }
        // Create n2 layer for xObject text
        $layerN2 = $pdfDocument->createObject(['BBox' => $bbox, 'Subtype' => '/Form', 'Type' => '/XObject', 'Resources' => new PDFValueObject()]);
        $n2Stream = $this->xObject?->stream ?? '';
        $n2Resources = $this->xObject?->resources ?? [];
        if ($this->xObject?->resourcesBuilder !== null) {
            $n2Resources = ($this->xObject->resourcesBuilder)($pdfDocument, $n2Resources);
        }
        // Embed user image (e.g. drawn signature) in the n2 layer at the specified rect.
        // This allows the background to live in n0 (full bbox) while the user image
        // occupies only its designated area (e.g. left half) without distortion.
        $imgResult = null;
        if ($this->signatureImagePath !== null) {
            [$imgX, $imgY, $imgW, $imgH] = $this->signatureImageFrame ?? [0, 0, $bbox[2], $bbox[3]];
            $imgResult = Img::addImage(static fn(array $value): PDFObject => $pdfDocument->createObject($value), $this->signatureImagePath, (float) $imgX, (float) $imgY, (float) $imgW, (float) $imgH, (float) $pageRotation->val());
            // Prepend image draw command so it appears behind the text operators
            $n2Stream = $imgResult['command'] . \PHP_EOL . $n2Stream;
        }
        $layerN2['Resources'] = new PDFValueObject($n2Resources);
        $layerN2->setStream($n2Stream, \false);
        // After building the PDFObject, inject the image XObject reference into its resources
        if ($imgResult !== null) {
            if (!isset($layerN2['Resources']['XObject'])) {
                $layerN2['Resources']['XObject'] = new PDFValueObject([]);
            }
            foreach ($imgResult['resources']->val()['XObject']->val() as $imgKey => $imgRef) {
                $layerN2['Resources']['XObject'][$imgKey] = $imgRef;
            }
        }
        $containerFormObject = $pdfDocument->createObject(['BBox' => $bbox, 'Subtype' => '/Form', 'Type' => '/XObject', 'Resources' => ['XObject' => ['n0' => new PDFValueSimple(''), 'n2' => new PDFValueSimple('')]]]);
        $containerFormObject->setStream("q 1 0 0 1 0 0 cm /n0 Do Q\nq 1 0 0 1 0 0 cm /n2 Do Q\n", \false);
        $containerFormObject['Resources']['XObject']['n0'] = new PDFValueReference($layerN0->getOid());
        $containerFormObject['Resources']['XObject']['n2'] = new PDFValueReference($layerN2->getOid());
        $formObject['Resources'] = new PDFValueObject(['XObject' => ['FRM' => new PDFValueReference($containerFormObject->getOid())]]);
        $formObject->setStream('/FRM Do', \false);
        $annotationObject['AP'] = ['N' => new PDFValueReference($formObject->getOid())];
        $annotationObject['Rect'] = [$this->rectToAppear[0], $pageSizeH - $this->rectToAppear[1], $this->rectToAppear[2], $pageSizeH - $this->rectToAppear[3]];
        return $annotationObject;
    }
    private function requirePdfDocument() : PdfDocument
    {
        if (!isset($this->pdfDocument)) {
            throw new PdfCoreStructureException('PDF document is required for signature appearance generation.');
        }
        return $this->pdfDocument;
    }
    private function requireAnnotationObject() : PDFObject
    {
        if (!isset($this->annotationObject)) {
            throw new PdfCoreStructureException('Annotation object is required for signature appearance generation.');
        }
        return $this->annotationObject;
    }
    private function requirePageRotation() : PDFValue
    {
        if (!isset($this->pageRotation)) {
            throw new PdfCoreStructureException('Page rotation value is required for signature appearance generation.');
        }
        return $this->pageRotation;
    }
    /**
     * Returns the natural pixel dimensions [width, height] of the given image
     * source, or null when they cannot be determined.
     *
     * Accepts the same three source formats that Img::addImage() accepts:
     *  • regular file path
     *  • "@" prefix  → raw binary content follows
     *  • base64 string → decoded to binary
     *
     * @return array{0:int,1:int}|null
     */
    private function imageNaturalSize(string $source) : ?array
    {
        if ($source === '') {
            return null;
        }
        if ($source[0] === '@') {
            $info = @\getimagesizefromstring(\substr($source, 1));
        } elseif (Str::isBase64($source)) {
            $decoded = \base64_decode($source, \true);
            $info = $decoded !== \false ? @\getimagesizefromstring($decoded) : \false;
        } else {
            $info = @\getimagesize($source);
        }
        if (!\is_array($info) || $info[0] <= 0 || $info[1] <= 0) {
            return null;
        }
        return [(int) $info[0], (int) $info[1]];
    }
}
