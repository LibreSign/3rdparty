<?php

namespace OCA\Libresign\Vendor\Mpdf;

use OCA\Libresign\Vendor\Mpdf\Color\ColorConverter;
use OCA\Libresign\Vendor\Mpdf\Color\ColorModeConverter;
use OCA\Libresign\Vendor\Mpdf\Color\ColorSpaceRestrictor;
use OCA\Libresign\Vendor\Mpdf\File\LocalContentLoader;
use OCA\Libresign\Vendor\Mpdf\Fonts\FontCache;
use OCA\Libresign\Vendor\Mpdf\Fonts\FontFileFinder;
use OCA\Libresign\Vendor\Mpdf\Http\CurlHttpClient;
use OCA\Libresign\Vendor\Mpdf\Http\SocketHttpClient;
use OCA\Libresign\Vendor\Mpdf\Image\ImageProcessor;
use OCA\Libresign\Vendor\Mpdf\Pdf\Protection;
use OCA\Libresign\Vendor\Mpdf\Pdf\Protection\UniqidGenerator;
use OCA\Libresign\Vendor\Mpdf\Writer\BaseWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\BackgroundWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\ColorWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\BookmarkWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\FontWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\FormWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\ImageWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\JavaScriptWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\MetadataWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\OptionalContentWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\PageWriter;
use OCA\Libresign\Vendor\Mpdf\Writer\ResourceWriter;
use OCA\Libresign\Vendor\Psr\Log\LoggerInterface;
/** @internal */
class ServiceFactory
{
    /**
     * @var \Mpdf\Container\ContainerInterface|null
     */
    private $container;
    public function __construct($container = null)
    {
        $this->container = $container;
    }
    public function getServices(Mpdf $mpdf, LoggerInterface $logger, $config, $languageToFont, $scriptToLanguage, $fontDescriptor, $bmp, $directWrite, $wmf)
    {
        $sizeConverter = new SizeConverter($mpdf->dpi, $mpdf->default_font_size, $mpdf, $logger);
        $colorModeConverter = new ColorModeConverter();
        $colorSpaceRestrictor = new ColorSpaceRestrictor($mpdf, $colorModeConverter);
        $colorConverter = new ColorConverter($mpdf, $colorModeConverter, $colorSpaceRestrictor);
        $tableOfContents = new TableOfContents($mpdf, $sizeConverter);
        $cacheBasePath = $config['tempDir'] . '/mpdf';
        $cache = new Cache($cacheBasePath, $config['cacheCleanupInterval']);
        $fontCache = new FontCache(new Cache($cacheBasePath . '/ttfontdata', $config['cacheCleanupInterval']));
        $fontFileFinder = new FontFileFinder($config['fontDir']);
        if ($this->container && $this->container->has('httpClient')) {
            $httpClient = $this->container->get('httpClient');
        } elseif (\function_exists('curl_init')) {
            $httpClient = new CurlHttpClient($mpdf, $logger);
        } else {
            $httpClient = new SocketHttpClient($logger);
        }
        $localContentLoader = $this->container && $this->container->has('localContentLoader') ? $this->container->get('localContentLoader') : new LocalContentLoader();
        $assetFetcher = new AssetFetcher($mpdf, $localContentLoader, $httpClient, $logger);
        $cssManager = new CssManager($mpdf, $cache, $sizeConverter, $colorConverter, $assetFetcher);
        $otl = new Otl($mpdf, $fontCache);
        $protection = new Protection(new UniqidGenerator());
        $writer = new BaseWriter($mpdf, $protection);
        $gradient = new Gradient($mpdf, $sizeConverter, $colorConverter, $writer);
        $formWriter = new FormWriter($mpdf, $writer);
        $form = new Form($mpdf, $otl, $colorConverter, $writer, $formWriter);
        $hyphenator = new Hyphenator($mpdf);
        $imageProcessor = new ImageProcessor($mpdf, $otl, $cssManager, $sizeConverter, $colorConverter, $colorModeConverter, $cache, $languageToFont, $scriptToLanguage, $assetFetcher, $logger);
        $tag = new Tag($mpdf, $cache, $cssManager, $form, $otl, $tableOfContents, $sizeConverter, $colorConverter, $imageProcessor, $languageToFont);
        $fontWriter = new FontWriter($mpdf, $writer, $fontCache, $fontDescriptor);
        $metadataWriter = new MetadataWriter($mpdf, $writer, $form, $protection, $logger);
        $imageWriter = new ImageWriter($mpdf, $writer);
        $pageWriter = new PageWriter($mpdf, $form, $writer, $metadataWriter);
        $bookmarkWriter = new BookmarkWriter($mpdf, $writer);
        $optionalContentWriter = new OptionalContentWriter($mpdf, $writer);
        $colorWriter = new ColorWriter($mpdf, $writer);
        $backgroundWriter = new BackgroundWriter($mpdf, $writer);
        $javaScriptWriter = new JavaScriptWriter($mpdf, $writer);
        $resourceWriter = new ResourceWriter($mpdf, $writer, $colorWriter, $fontWriter, $imageWriter, $formWriter, $optionalContentWriter, $backgroundWriter, $bookmarkWriter, $metadataWriter, $javaScriptWriter, $logger);
        return ['otl' => $otl, 'bmp' => $bmp, 'cache' => $cache, 'cssManager' => $cssManager, 'directWrite' => $directWrite, 'fontCache' => $fontCache, 'fontFileFinder' => $fontFileFinder, 'form' => $form, 'gradient' => $gradient, 'tableOfContents' => $tableOfContents, 'tag' => $tag, 'wmf' => $wmf, 'sizeConverter' => $sizeConverter, 'colorConverter' => $colorConverter, 'hyphenator' => $hyphenator, 'localContentLoader' => $localContentLoader, 'httpClient' => $httpClient, 'assetFetcher' => $assetFetcher, 'imageProcessor' => $imageProcessor, 'protection' => $protection, 'languageToFont' => $languageToFont, 'scriptToLanguage' => $scriptToLanguage, 'writer' => $writer, 'fontWriter' => $fontWriter, 'metadataWriter' => $metadataWriter, 'imageWriter' => $imageWriter, 'formWriter' => $formWriter, 'pageWriter' => $pageWriter, 'bookmarkWriter' => $bookmarkWriter, 'optionalContentWriter' => $optionalContentWriter, 'colorWriter' => $colorWriter, 'backgroundWriter' => $backgroundWriter, 'javaScriptWriter' => $javaScriptWriter, 'resourceWriter' => $resourceWriter];
    }
    public function getServiceIds()
    {
        return ['otl', 'bmp', 'cache', 'cssManager', 'directWrite', 'fontCache', 'fontFileFinder', 'form', 'gradient', 'tableOfContents', 'tag', 'wmf', 'sizeConverter', 'colorConverter', 'hyphenator', 'localContentLoader', 'httpClient', 'assetFetcher', 'imageProcessor', 'protection', 'languageToFont', 'scriptToLanguage', 'writer', 'fontWriter', 'metadataWriter', 'imageWriter', 'formWriter', 'pageWriter', 'bookmarkWriter', 'optionalContentWriter', 'colorWriter', 'backgroundWriter', 'javaScriptWriter', 'resourceWriter'];
    }
}
