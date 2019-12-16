<?php

declare(strict_types=1);

namespace Dovhan\Tab\Block;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Swatches\Helper\Data;
use function __;

class ExtraInfo extends Template
{

    public const DEFAULT_SORT = 21;

    public const ATTR_CODE = 'attribute_code';

    public const ATTR_VALUE = 'attribute_value';

    public const ATTR_VALUE_TEXT = "attribute_value_text";

    protected $_template = "Dovhan_Tab::kit.phtml";

    /**
     * @var Json
     */
    private $json;
    /**
     * @var array
     */
    private $prodAV = [];

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Configurable
     */
    private $configurable;

    /**
     * @var Data
     */
    protected $swatch;

    /**
     * ExtraInfo constructor.
     * @param Json $json
     * @param Configurable $configurable
     * @param Data $swatch
     * @param Registry $registry
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Json $json,
        Configurable $configurable,
        Data $swatch,
        Registry $registry,
        Template\Context $context,
        array $data = []
    ) {
        $this->json = $json;
        $this->configurable = $configurable;
        $this->swatch = $swatch;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @param $product
     * @return bool
     */
    public function isConfigurableProduct(Product $product): bool
    {
        return $product->getTypeId() === Configurable::TYPE_CODE;
    }

    /**
     * @return Product
     */
    public function getCurrentProduct(): ?Product
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return ProductInterface[]
     */
    public function getChildProducts(): array
    {
        $product = $this->getCurrentProduct();

        if ($product && $this->isConfigurableProduct($product)) {
            $this->setAvValues($product);

            return $product->getTypeInstance()->getUsedProducts($product);
        } else {
            return null;
        }
    }

    /**
     * @param Product $product
     */
    private function setAvValues(Product $product): void
    {
        $attributeValue = $this->swatch->getSwatchAttributesAsArray($product);

        foreach ($attributeValue as $key => $attr) {
            $this->prodAV[$key][self::ATTR_CODE] = $attr[self::ATTR_CODE];
        }
    }

    /**
     * @param $product
     * @return array
     */
    public function getSwatchAttribute(Product $product): array
    {
        $res = [];

        foreach ($this->prodAV as $value) {
            $res[$product->getId()][] = [
                self::ATTR_CODE => $value[self::ATTR_CODE],
                self::ATTR_VALUE => $product->getDataByKey($value[self::ATTR_CODE]),
                self::ATTR_VALUE_TEXT => $this->swatch->
                getSwatchesByOptionsId([$product->
                getDataByKey($value[self::ATTR_CODE])])[$product->
                getDataByKey($value[self::ATTR_CODE])]['value'],
            ];
        }

        return $res;
    }

    /**
     * @param $product
     * @return string
     */
    public function getJsonAttrVal(Product $product): string
    {
        $res = [];

        foreach ($this->prodAV as $value) {
            $res[$value[self::ATTR_CODE]] = $product->getDataByKey($value[self::ATTR_CODE]);
        }

        return $this->json->serialize($res);
    }
}
