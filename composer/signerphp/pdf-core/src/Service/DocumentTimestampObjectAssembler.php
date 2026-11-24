<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfCore\Service;

use OCA\Libresign\Vendor\SignerPHP\PdfCore\DocumentTimestampObject;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Exception\PdfCoreStructureException;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfDocument;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PDFObject;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValue;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueList;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueObject;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueReference;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueSimple;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\PdfValue\PDFValueString;
use OCA\Libresign\Vendor\SignerPHP\PdfCore\Utils\Str;
/**
 * Attaches a document timestamp (ISO 32000-2, 12.8.5) the way signatures are attached:
 * the /DocTimeStamp dictionary is the /V of a signature field listed in /AcroForm /Fields.
 * When the document has a page, the field is merged with an invisible widget annotation
 * on the first page; without pages it is a plain field dictionary (no widget).
 * @internal
 */
final class DocumentTimestampObjectAssembler
{
    public function assemble(PdfDocument $pdfDocument) : DocumentTimestampObject
    {
        $rootObject = $this->resolveRootObject($pdfDocument);
        $pageObject = $pdfDocument->getPageInfo()->getPage(0);
        // Validate both existing lists before adding objects or updating page annotations.
        $annotations = null;
        if ($pageObject !== null) {
            $annotations = $this->resolveListValue($pdfDocument, $pageObject['Annots'] ?? new PDFValueList(), 'annotation list');
        }
        $acroFormObject = $this->resolveOrCreateAcroForm($pdfDocument, $rootObject);
        $fields = $this->resolveListValue($pdfDocument, $acroFormObject['Fields'] ?? new PDFValueList(), 'AcroForm fields');
        $timestampObject = $pdfDocument->createObject([], DocumentTimestampObject::class, \false);
        \assert($timestampObject instanceof DocumentTimestampObject);
        $field = ['FT' => '/Sig', 'T' => new PDFValueString('DocTimeStamp' . Str::random()), 'V' => new PDFValueReference($timestampObject->getOid())];
        if ($pageObject !== null) {
            $field += ['Type' => '/Annot', 'Subtype' => '/Widget', 'P' => new PDFValueReference($pageObject->getOid()), 'Rect' => [0, 0, 0, 0], 'F' => 132];
        }
        $fieldObject = $pdfDocument->createObject($field);
        if ($pageObject !== null && $annotations !== null) {
            $annotations->push(new PDFValueReference($fieldObject->getOid()));
            $pageObject['Annots'] = $annotations;
            $pdfDocument->addObject($pageObject);
        }
        $acroFormObject['SigFlags'] = 3;
        $fields->push(new PDFValueReference($fieldObject->getOid()));
        $acroFormObject['Fields'] = $fields;
        $pdfDocument->addObject($acroFormObject instanceof PDFObject ? $acroFormObject : $rootObject);
        return $timestampObject;
    }
    private function resolveRootObject(PdfDocument $pdfDocument) : PDFObject
    {
        $root = $pdfDocument->getTrailerObject()['Root'] ?? null;
        if ($root === null || ($root = $root->asObjectReferenceOrNull()) === null || \is_array($root)) {
            throw new PdfCoreStructureException('Could not resolve root object to attach document timestamp.');
        }
        $rootObject = $pdfDocument->getObject($root);
        if ($rootObject === null || !$rootObject->hasDictionaryValue()) {
            throw new PdfCoreStructureException('Invalid root object while attaching document timestamp.');
        }
        return $rootObject;
    }
    private function resolveOrCreateAcroForm(PdfDocument $pdfDocument, PDFObject $rootObject) : PDFObject|PDFValueObject
    {
        if (!isset($rootObject['AcroForm']) || $this->isPdfNull($rootObject['AcroForm'])) {
            $acroFormObject = $pdfDocument->createObject(['Fields' => new PDFValueList()]);
            $rootObject['AcroForm'] = new PDFValueReference($acroFormObject->getOid());
            $pdfDocument->addObject($rootObject);
            return $acroFormObject;
        }
        $acroForm = $rootObject['AcroForm'];
        if ($acroForm instanceof PDFValueObject) {
            return $acroForm;
        }
        $reference = $acroForm->asObjectReferenceOrNull();
        if (\is_int($reference)) {
            $acroFormObject = $pdfDocument->getObject($reference);
            if ($acroFormObject === null) {
                throw new PdfCoreStructureException('Could not resolve AcroForm object while attaching document timestamp.');
            }
            if ($acroFormObject->hasDictionaryValue()) {
                return $acroFormObject;
            }
        }
        throw new PdfCoreStructureException('Invalid AcroForm dictionary while attaching document timestamp.');
    }
    private function resolveListValue(PdfDocument $pdfDocument, PDFValue $value, string $context) : PDFValueList
    {
        if ($this->isPdfNull($value)) {
            return new PDFValueList();
        }
        if ($value instanceof PDFValueList) {
            $list = new PDFValueList($value->val());
        } else {
            $list = null;
            $reference = $value->asObjectReferenceOrNull();
            if (\is_int($reference)) {
                $listObject = $pdfDocument->getObject($reference);
                if ($listObject === null) {
                    throw new PdfCoreStructureException('Could not resolve ' . $context . ' object while attaching document timestamp.');
                }
                $list = $listObject->getListValue();
            }
        }
        if ($list === null) {
            throw new PdfCoreStructureException('Invalid ' . $context . ' while attaching document timestamp.');
        }
        // The parser can group successive indirect references into a single
        // simple value. Validate the complete sequence, not a partial match.
        foreach ($list->val() as $entry) {
            if (!$entry instanceof PDFValueSimple || \preg_match('/\\A[ \\t\\r\\n\\f\\x00]*(?:[0-9]++[ \\t\\r\\n\\f\\x00]++[0-9]++[ \\t\\r\\n\\f\\x00]++R(?:[ \\t\\r\\n\\f\\x00]++|\\z))++\\z/', (string) $entry) !== 1) {
                throw new PdfCoreStructureException('Invalid ' . $context . ' while attaching document timestamp.');
            }
        }
        return $list;
    }
    private function isPdfNull(PDFValue $value) : bool
    {
        return $value instanceof PDFValueSimple && (string) $value === 'null';
    }
}
