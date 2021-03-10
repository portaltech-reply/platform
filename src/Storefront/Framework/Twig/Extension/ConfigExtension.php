<?php declare(strict_types=1);

namespace Shopware\Storefront\Framework\Twig\Extension;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Twig\TemplateConfigAccessor;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigExtension extends AbstractExtension
{
    /**
     * @var TemplateConfigAccessor
     */
    private $config;

    public function __construct(TemplateConfigAccessor $config)
    {
        $this->config = $config;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('config', [$this, 'config'], ['needs_context' => true]),
            new TwigFunction('theme_config', [$this, 'theme'], ['needs_context' => true]),
        ];
    }

    /**
     * @return string|bool|array|float|int|null
     */
    public function config(array $context, string $key)
    {
        return $this->config->config($key, $this->getSalesChannelId($context));
    }

    /**
     * @return string|bool|array|float|int|null
     */
    public function theme(array $context, string $key)
    {
        return $this->config->theme($key, $this->getContext($context), $this->getThemeId($context));
    }

    private function getSalesChannelId(array $context): ?string
    {
        if (!isset($context['context'])) {
            return null;
        }

        $context = $context['context'];

        if (!$context instanceof SalesChannelContext) {
            return null;
        }

        return $context->getSalesChannelId();
    }

    private function getThemeId(array $context): ?string
    {
        return $context['themeId'] ?? null;
    }

    private function getContext(array $context): SalesChannelContext
    {
        if (!isset($context['context'])) {
            throw new \RuntimeException('Missing sales channel context object');
        }

        $context = $context['context'];

        if (!$context instanceof SalesChannelContext) {
            throw new \RuntimeException('Missing sales channel context object');
        }

        return $context;
    }
}