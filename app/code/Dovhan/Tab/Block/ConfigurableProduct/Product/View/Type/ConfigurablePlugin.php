<?php

declare(strict_types=1);

namespace Dovhan\Tab\Block\ConfigurableProduct\Product\View\Type;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as ConfigurableSubject;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Serialize\Serializer\Json;

class ConfigurablePlugin
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * ConfigurablePlugin constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param Json $json
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Json $json
    ) {
        $this->json = $json;
        $this->productRepository = $productRepository;
    }


    /**
     * @param ConfigurableSubject $subject
     * @param $result
     * @return bool|false|string
     * @throws NoSuchEntityException
     */
    public function afterGetJsonConfig(ConfigurableSubject $subject, $result)
    {
        $sku = [];
        $config = $this->json->unserialize($result);

        foreach ($subject->getAllowProducts() as $prod) {
            $id = $prod->getId();
            $product = $this->productRepository->getById($id);
            $sku[$id] = $product->getSku();
        }
        $config['sku'] = $sku;

        return $this->json->serialize($config);
    }
}
