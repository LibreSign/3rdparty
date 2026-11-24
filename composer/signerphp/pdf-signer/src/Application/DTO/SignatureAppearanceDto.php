<?php

declare (strict_types=1);
namespace OCA\Libresign\Vendor\SignerPHP\PdfSigner\Application\DTO;

/** @internal */
final readonly class SignatureAppearanceDto
{
    /**
     * @param  string|null  $backgroundImagePath  Path (or base64 string) for the n0 background image; null = blank n0
     * @param  array{0: float|int, 1: float|int, 2: float|int, 3: float|int}  $rect  Signature bbox [llx, lly, urx, ury] in screen coordinates
     * @param  int  $page  0-based page index
     * @param  SignatureAppearanceXObjectDto|null  $xObject  Optional text/graphics xObject for the n2 layer
     * @param  string|null  $signatureImagePath  Path to the signer's drawn image, placed in the n2 layer at $signatureImageFrame
     * @param  array{0: float|int, 1: float|int, 2: float|int, 3: float|int}|null  $signatureImageFrame  Placement [x,y,w,h] within bbox for the signature image; null = full bbox
     */
    public function __construct(public ?string $backgroundImagePath, public array $rect, public int $page, public ?SignatureAppearanceXObjectDto $xObject = null, public ?string $signatureImagePath = null, public ?array $signatureImageFrame = null)
    {
    }
    /**
     * @return array{0: float|int, 1: float|int, 2: float|int, 3: float|int}
     */
    public function normalizedRect() : array
    {
        return [$this->rect[0] ?? 0, $this->rect[1] ?? 0, $this->rect[2] ?? 0, $this->rect[3] ?? 0];
    }
}
