<?php

namespace OCA\Libresign\3rdparty\Mpdf\Tag;

use OCA\Libresign\3rdparty\Mpdf\Strict;
use OCA\Libresign\3rdparty\Mpdf\Cache;
use OCA\Libresign\3rdparty\Mpdf\Color\ColorConverter;
use OCA\Libresign\3rdparty\Mpdf\CssManager;
use OCA\Libresign\3rdparty\Mpdf\Form;
use OCA\Libresign\3rdparty\Mpdf\Image\ImageProcessor;
use OCA\Libresign\3rdparty\Mpdf\Language\LanguageToFontInterface;
use OCA\Libresign\3rdparty\Mpdf\Mpdf;
use OCA\Libresign\3rdparty\Mpdf\Otl;
use OCA\Libresign\3rdparty\Mpdf\SizeConverter;
use OCA\Libresign\3rdparty\Mpdf\TableOfContents;
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
        return \strtoupper(\str_replace('OCA\Libresign\3rdparty\\Mpdf\\Tag\\', '', $tag));
    }
    protected function getAlign($property)
    {
        $property = \strtolower($property);
        return \array_key_exists($property, self::ALIGN) ? self::ALIGN[$property] : '';
    }
    public abstract function open($attr, &$ahtml, &$ihtml);
    public abstract function close(&$ahtml, &$ihtml);
}
