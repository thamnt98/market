<?php

namespace SM\LazyLoad\Helper;

class Filter
{
    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * Filter constructor.
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->assetRepo = $assetRepo;
    }

    /**
     * Convert content to lazyload html
     *
     * @param string $content
     * @return string
     */
    public function filter($content)
    {
        $content = $this->filterImages($content);
        return $content;
    }

    /**
     * Filter images with placeholders in the content
     *
     * @param string $content
     * @return string
     */
    public function filterImages($content)
    {
        $matches = $search = $replace = [];
        preg_match_all('/<img[\s\r\n]+.*?>/is', $content, $matches);
        $placeHolderUrl = $this->getViewFileUrl('SM_LazyLoad::images/loader.gif');

        $lazyClasses = "lazy lazy-loading";

        if ($placeHolderUrl != 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7') {
            $lazyClasses = str_replace('lazy-blur', '', $lazyClasses);
        }

        foreach ($matches[0] as $imgHTML) {
            if (!preg_match("/src=['\"]data:image/is", $imgHTML) && strpos($imgHTML,
                    'data-src') === false && !$this->isSkipElement($imgHTML)) {

                // replace the src and add the data-src attribute
                $replaceHTML = preg_replace('/<img(.*?)src=/is', '<img$1src="' . $placeHolderUrl . '" data-src=',
                    $imgHTML);

                // add the lazy class to the img element
                if (preg_match('/class=["\']/i', $replaceHTML)) {
                    $replaceHTML = preg_replace('/class=(["\'])(.*?)["\']/is', 'class=$1' . $lazyClasses . ' $2$1',
                        $replaceHTML);
                } else {
                    $replaceHTML = preg_replace('/<img/is', '<img class="' . $lazyClasses . '"', $replaceHTML);
                }

                $search[] = $imgHTML;
                $replace[] = $replaceHTML;
            }
        }

        $content = str_replace($search, $replace, $content);

        return $content;
    }

    /**
     * Check is skip element via specific classes
     * @param string $content
     * @return boolean
     */
    protected function isSkipElement($content)
    {
        $skipClassesQuoted = array_map('preg_quote', $this->getSkipClasses());
        $skipClassesORed = implode('|', $skipClassesQuoted);
        $regex = '/<\s*\w*\s*class\s*=\s*[\'"](|.*\s)' . $skipClassesORed . '(|\s.*)[\'"].*>/isU';
        return preg_match($regex, $content);
    }

    /**
     * @return array
     */
    protected function getSkipClasses()
    {
        $skipClasses = array_map('trim', explode(',', 'no-lazy'));

        foreach ($skipClasses as $k => $_class) {
            if (!$_class) {
                unset($skipClasses[$k]);
            }
        }

        return $skipClasses;
    }

    /**
     * @param $fileId
     * @param array $params
     * @return mixed
     */
    protected function getViewFileUrl($fileId, array $params = []): string
    {
        try {
            $params = array_merge(['_secure' => true], $params);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (\Exception $e) {
            return "";
        }
    }
}
