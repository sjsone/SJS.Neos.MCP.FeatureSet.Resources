<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Resources\MediaFeatureSet;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Media\Domain\Model\Tag;
use Neos\Media\Domain\Repository\AssetRepository;
use Neos\Media\Domain\Repository\TagRepository;
use SJS\Flow\MCP\Domain\MCP\Tool;
use SJS\Flow\MCP\Domain\MCP\Tool\Annotations;
use SJS\Flow\MCP\Domain\MCP\Tool\Content;
use SJS\Flow\MCP\JsonSchema\ObjectSchema;

class ListTagsTool extends Tool
{
    #[Flow\Inject]
    protected TagRepository $tagRepository;

    #[Flow\Inject]
    protected AssetRepository $assetRepository;

    #[Flow\Inject]
    protected PersistenceManagerInterface $persistenceManager;

    public function __construct()
    {
        parent::__construct(
            name: 'list_tags',
            description: 'Lists all tags with label, optional parent label, and asset count',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'List Tags',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $result = [];
        foreach ($this->tagRepository->findAll() as $tag) {
            /** @var Tag $tag */
            $tagIdentifier = $this->persistenceManager->getIdentifierByObject($tag);

            $result[$tagIdentifier] = [
                'label' => $tag->getLabel(),
                'parent' => $tag->getParent()?->getLabel(),
                'assetCount' => $this->assetRepository->countByTag($tag),
            ];
        }
        return Content::structured($result)->addText(json_encode($result));
    }
}
