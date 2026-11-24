<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\Exception\PdfCoreStructureException;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Metadata;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PDFObject;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValue;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueList;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueObject;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueReference;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueSimple;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueString;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\SignatureAppearance;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\SignatureObject;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Utils\Str;
/** @internal */
final class SignatureObjectAssembler
{
    public function assemble(PdfDocument $pdfDocument, SignatureAppearance $appearance, Metadata $metadata, ?int $certificationLevel = null) : SignatureObject
    {
        $rootObject = $this->resolveRootObject($pdfDocument);
        $pageObject = $this->resolvePageObject($pdfDocument, $appearance->getPageToAppear());
        $annotationObject = $this->buildAnnotationObject($pdfDocument, $pageObject, $appearance->getRect());
        $signatureObject = $pdfDocument->createObject([], SignatureObject::class, \false);
        \assert($signatureObject instanceof SignatureObject);
        $annotationObject['V'] = new PDFValueReference($signatureObject->getOid());
        if ($appearance->getBackgroundImage() !== null || $appearance->getXObject() !== null || $appearance->getSignatureImage() !== null) {
            $pageRotation = $pageObject['Rotate'] ?? new PDFValueSimple(0);
            $annotationObject = $appearance->withPageRotate($pageRotation)->withAnnotationObject($annotationObject)->withPdfDocument($pdfDocument)->generate();
        }
        $annotationList = $this->resolveListValue($pdfDocument, $pageObject['Annots'] ?? new PDFValueList(), 'annotation list', fallbackWhenMissingReference: \true);
        $annotationList->push(new PDFValueReference($annotationObject->getOid()));
        $pageObject['Annots'] = $annotationList;
        $acroFormObject = $this->resolveOrCreateAcroForm($pdfDocument, $rootObject);
        $acroFormObject['SigFlags'] = 3;
        $fieldsList = $this->resolveListValue($pdfDocument, $acroFormObject['Fields'] ?? new PDFValueList(), 'AcroForm fields');
        $fieldsList->push(new PDFValueReference($annotationObject->getOid()));
        $acroFormObject['Fields'] = $fieldsList;
        $pdfDocument->addObject($pageObject);
        $pdfDocument->addObject($acroFormObject);
        $this->applyCertificationIfRequested($pdfDocument, $rootObject, $signatureObject, $certificationLevel);
        return $signatureObject->withName($metadata->getName())->withLocation($metadata->getLocation())->withReason($metadata->getReason())->withContactInfo($metadata->getContactInfo());
    }
    private function resolveRootObject(PdfDocument $pdfDocument) : PDFObject
    {
        $root = $pdfDocument->getTrailerObject()['Root'] ?? null;
        if ($root === null || ($root = $root->asObjectReferenceOrNull()) === null || \is_array($root)) {
            throw new PdfCoreStructureException('Could not find the root object from the trailer');
        }
        $rootObject = $pdfDocument->getObject($root);
        if ($rootObject === null) {
            throw new PdfCoreStructureException('Invalid root object');
        }
        return $rootObject;
    }
    private function resolvePageObject(PdfDocument $pdfDocument, int $pageIndex) : PDFObject
    {
        $pageObject = $pdfDocument->getPageInfo()->getPage($pageIndex);
        if ($pageObject === null) {
            throw new PdfCoreStructureException('Invalid page');
        }
        return $pageObject;
    }
    private function resolveListValue(PdfDocument $pdfDocument, PDFValue $value, string $context, bool $fallbackWhenMissingReference = \false) : PDFValueList
    {
        if ($value instanceof PDFValueList) {
            return new PDFValueList($value->val());
        }
        $reference = $value->asObjectReferenceOrNull();
        if ($reference !== null && !\is_array($reference)) {
            $listObject = $pdfDocument->getObject($reference);
            if ($listObject === null) {
                if ($fallbackWhenMissingReference) {
                    return new PDFValueList();
                }
                throw new PdfCoreStructureException('Could not resolve ' . $context . ' object');
            }
            return new PDFValueList(\array_values($listObject->getValue()->val()));
        }
        if ($value instanceof PDFValueObject) {
            return new PDFValueList(\array_values($value->val()));
        }
        return new PDFValueList();
    }
    private function resolveOrCreateAcroForm(PdfDocument $pdfDocument, PDFObject $rootObject) : PDFObject
    {
        if (!isset($rootObject['AcroForm'])) {
            $acroFormObject = $pdfDocument->createObject(['Fields' => new PDFValueList()]);
            $rootObject['AcroForm'] = new PDFValueReference($acroFormObject->getOid());
            $pdfDocument->addObject($rootObject);
            return $acroFormObject;
        }
        $acroForm = $rootObject['AcroForm'];
        if (($reference = $acroForm->asObjectReferenceOrNull()) !== null && !\is_array($reference)) {
            $resolvedAcroForm = $pdfDocument->getObject($reference);
            if ($resolvedAcroForm === null) {
                throw new PdfCoreStructureException('Could not resolve AcroForm object');
            }
            return $resolvedAcroForm;
        }
        return $rootObject;
    }
    /**
     * @param  array<int, int|float>  $rect
     */
    private function buildAnnotationObject(PdfDocument $pdfDocument, PDFObject $pageObject, array $rect) : PDFObject
    {
        return $pdfDocument->createObject(['Type' => '/Annot', 'Subtype' => '/Widget', 'FT' => '/Sig', 'V' => new PDFValueString(''), 'T' => new PDFValueString('Signature' . Str::random()), 'P' => new PDFValueReference($pageObject->getOid()), 'Rect' => $rect, 'F' => 132]);
    }
    private function applyCertificationIfRequested(PdfDocument $pdfDocument, PDFObject $rootObject, SignatureObject $signatureObject, ?int $certificationLevel) : void
    {
        if ($certificationLevel === null) {
            return;
        }
        if (isset($rootObject['Perms']) && $this->hasExistingDocMdp($pdfDocument, $rootObject['Perms'])) {
            return;
        }
        $signatureObject['Reference'] = new PDFValueList([new PDFValueObject(['Type' => '/SigRef', 'TransformMethod' => '/DocMDP', 'Data' => new PDFValueReference($rootObject->getOid()), 'TransformParams' => new PDFValueObject(['Type' => '/TransformParams', 'V' => '/1.2', 'P' => $certificationLevel])])]);
        $this->setPermsEntry($pdfDocument, $rootObject, 'DocMDP', new PDFValueReference($signatureObject->getOid()));
    }
    private function hasExistingDocMdp(PdfDocument $pdfDocument, PDFValue $permsValue) : bool
    {
        if ($permsValue instanceof PDFValueObject) {
            return isset($permsValue['DocMDP']);
        }
        $reference = $permsValue->asObjectReferenceOrNull();
        if ($reference === null || \is_array($reference)) {
            return \false;
        }
        $permsObject = $pdfDocument->getObject($reference);
        if ($permsObject === null) {
            return \false;
        }
        return isset($permsObject['DocMDP']);
    }
    /**
     * Writes an entry into the catalog's /Perms dictionary (ISO 32000-1, 12.8.4),
     * whether /Perms is missing, inline or an indirect object.
     */
    private function setPermsEntry(PdfDocument $pdfDocument, PDFObject $rootObject, string $key, PDFValue $value) : void
    {
        $perms = $rootObject['Perms'] ?? null;
        if ($perms !== null && !$perms instanceof PDFValueObject) {
            $reference = $perms->asObjectReferenceOrNull();
            if ($reference !== null && !\is_array($reference)) {
                $permsObject = $pdfDocument->getObject($reference);
                if ($permsObject === null) {
                    throw new PdfCoreStructureException('Could not resolve existing Perms object.');
                }
                $permsObject[$key] = $value;
                $pdfDocument->addObject($permsObject);
                return;
            }
        }
        $entries = $perms instanceof PDFValueObject ? $perms->val() : [];
        $entries[$key] = $value;
        $rootObject['Perms'] = new PDFValueObject($entries);
        $pdfDocument->addObject($rootObject);
    }
}
