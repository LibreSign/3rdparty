<?php

namespace OCA\Libresign\Vendor\Mpdf\Tag;

use OCA\Libresign\Vendor\Mpdf\Strict;
use OCA\Libresign\Vendor\Mpdf\Cache;
use OCA\Libresign\Vendor\Mpdf\Color\ColorConverter;
use OCA\Libresign\Vendor\Mpdf\CssManager;
use OCA\Libresign\Vendor\Mpdf\Form;
use OCA\Libresign\Vendor\Mpdf\Image\ImageProcessor;
use OCA\Libresign\Vendor\Mpdf\Language\LanguageToFontInterface;
use OCA\Libresign\Vendor\Mpdf\Mpdf;
use OCA\Libresign\Vendor\Mpdf\Otl;
use OCA\Libresign\Vendor\Mpdf\SizeConverter;
use OCA\Libresign\Vendor\Mpdf\TableOfContents;
/** @internal */
abstract class Tag
{
    use Strict;
    /**
     * @var \Mpdf\Mpdf
     */
    protected $mpdf;
    /**
     * @var \Mpdf\Cache
     */
    protected $cache;
    /**
     * @var \Mpdf\CssManager
     */
    protected $cssManager;
    /**
     * @var \Mpdf\Form
     */
    protected $form;
    /**
     * @var \Mpdf\Otl
     */
    protected $otl;
    /**
     * @var \Mpdf\TableOfContents
     */
    protected $tableOfContents;
    /**
     * @var \Mpdf\SizeConverter
     */
    protected $sizeConverter;
    /**
     * @var \Mpdf\Color\ColorConverter
     */
    protected $colorConverter;
    /**
     * @var \Mpdf\Image\ImageProcessor
     */
    protected $imageProcessor;
    /**
     * @var \Mpdf\Language\LanguageToFontInterface
     */
    protected $languageToFont;
    const ALIGN = ['left' => 'L', 'center' => 'C', 'right' => 'R', 'top' => 'T', 'text-top' => 'TT', 'middle' => 'M', 'baseline' => 'BS', 'bottom' => 'B', 'text-bottom' => 'TB', 'justify' => 'J'];
    public function __construct(Mpdf $mpdf, Cache $cache, CssManager $cssManager, Form $form, Otl $otl, TableOfContents $tableOfContents, SizeConverter $sizeConverter, ColorConverter $colorConverter, ImageProcessor $imageProcessor, LanguageToFontInterface $languageToFont)
    {
        $this->mpdf = $mpdf;
        $this->cache = $cache;
        $this->cssManager = $cssManager;
        $this->form = $form;
        $this->otl = $otl;
        $this->tableOfContents = $tableOfContents;
        $this->sizeConverter = $sizeConverter;
        $this->colorConverter = $colorConverter;
        $this->imageProcessor = $imageProcessor;
        $this->languageToFont = $languageToFont;
    }
    public function getTagName()
    {
        $tag = \get_class($this);
        return \strtoupper(\str_replace('OCA\Libresign\Vendor\\Mpdf\\Tag\\', '', $tag));
    }
    protected function getAlign($property)
    {
        $property = \strtolower($property);
        return \array_key_exists($property, self::ALIGN) ? self::ALIGN[$property] : '';
    }
    public abstract function open($attr, &$ahtml, &$ihtml);
    public abstract function close(&$ahtml, &$ihtml);
}
