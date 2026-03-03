<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Resources;

use Neos\Flow\Annotations as Flow;
use SJS\Flow\MCP\FeatureSet\AbstractFeatureSet;
use SJS\Neos\MCP\FeatureSet\Resources\MediaFeatureSet\ListCollectionsTool;
use SJS\Neos\MCP\FeatureSet\Resources\MediaFeatureSet\ListMediaTool;
use SJS\Neos\MCP\FeatureSet\Resources\MediaFeatureSet\ListTagsTool;

#[Flow\Scope("singleton")]
class MediaFeatureSet extends AbstractFeatureSet
{
    public function initialize(): void
    {
        $this->addTool(ListCollectionsTool::class);
        $this->addTool(ListTagsTool::class);
        $this->addTool(ListMediaTool::class);
    }
}
