<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Resources\MediaFeatureSet;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Media\Domain\Model\AssetCollection;
use Neos\Media\Domain\Repository\AssetCollectionRepository;
use Neos\Media\Domain\Repository\AssetRepository;
use SJS\Flow\MCP\Domain\MCP\Tool;
use SJS\Flow\MCP\Domain\MCP\Tool\Annotations;
use SJS\Flow\MCP\Domain\MCP\Tool\Content;
use SJS\Flow\MCP\JsonSchema\ObjectSchema;

class ListCollectionsTool extends Tool
{
    #[Flow\Inject]
    protected AssetCollectionRepository $assetCollectionRepository;

    #[Flow\Inject]
    protected AssetRepository $assetRepository;

    #[Flow\Inject]
    protected PersistenceManagerInterface $persistenceManager;

    public function __construct()
    {
        parent::__construct(
            name: 'list_collections',
            description: 'Lists all asset collections with title, asset count, and assigned tag labels',
            inputSchema: new ObjectSchema(),
            annotations: new Annotations(
                title: 'List Asset Collections',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $actionRequest, array $input): Content
    {
        $result = [];
        foreach ($this->assetCollectionRepository->findAll() as $collection) {
            /** @var AssetCollection $collection */
            $tags = array_map(fn($t) => $t->getLabel(), $collection->getTags()->toArray());
            $collectionIdentifier = $this->persistenceManager->getIdentifierByObject($collection);
            $result[$collectionIdentifier] = [
                'title' => $collection->getTitle(),
                'assetCount' => $this->assetRepository->countByAssetCollection($collection),
                'tags' => $tags,
            ];
        }
        return Content::structured($result)->addText(json_encode($result));
    }
}
