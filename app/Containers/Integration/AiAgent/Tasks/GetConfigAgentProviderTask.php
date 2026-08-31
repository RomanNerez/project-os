<?php

namespace App\Containers\Integration\AiAgent\Tasks;

use App\Ship\Parents\Tasks\Task as ParentTask;
use InvalidArgumentException;
use LLPhant\GeminiOpenAIConfig;
use LLPhant\OpenAIConfig;

final class GetConfigAgentProviderTask extends ParentTask
{
    /**
     * @var string
     */
    private string $configKey = 'integration-aiAgent';

    /**
     * @return OpenAIConfig
     */
    public function run(): OpenAIConfig
    {
        $provider = config("{$this->configKey}.default");

        return match ($provider) {
            'openai' => $this->createOpenAiConfig(),
            'openrouter' => $this->createOpenRouterConfig(),
            'gemini' => $this->createGeminiConfig(),
            default => throw new InvalidArgumentException("Provider [{$provider}] is not supported."),
        };
    }

    /**
     * @param string $provider
     * @param string $key
     * @return mixed
     */
    private function providerConfig(string $provider, string $key): mixed
    {
        return config("{$this->configKey}.providers.{$provider}.{$key}");
    }

    /**
     * @return OpenAIConfig
     */
    private function createOpenAiConfig(): OpenAIConfig
    {
        return new OpenAIConfig(
            apiKey: $this->providerConfig('openai', 'api_key'),
            model: $this->providerConfig('openai', 'model')
        );
    }

    /**
     * @return OpenAIConfig
     */
    private function createOpenRouterConfig(): OpenAIConfig
    {
        return new OpenAIConfig(
            apiKey: $this->providerConfig('openrouter', 'api_key'),
            url: $this->providerConfig('openrouter', 'base_url'),
            model: $this->providerConfig('openrouter', 'model')
        );
    }

    /**
     * @return GeminiOpenAIConfig
     */
    private function createGeminiConfig(): GeminiOpenAIConfig
    {
        $config = new GeminiOpenAIConfig(
            apiKey: $this->providerConfig('gemini', 'api_key')
        );

        $config->model = $this->providerConfig('gemini', 'model');

        return $config;
    }
}