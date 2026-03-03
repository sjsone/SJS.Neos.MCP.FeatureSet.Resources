<?php

declare(strict_types=1);

namespace SJS\Neos\MCP\FeatureSet\Resources\MediaFeatureSet;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Media\Domain\Model\Asset;
use Neos\Media\Domain\Model\AssetCollection;
use Neos\Media\Domain\Model\Tag;
use Neos\Media\Domain\Repository\AssetCollectionRepository;
use Neos\Media\Domain\Repository\AssetRepository;
use Neos\Media\Domain\Repository\TagRepository;
use SJS\Flow\MCP\Domain\MCP\Tool;
use SJS\Flow\MCP\Domain\MCP\Tool\Annotations;
use SJS\Flow\MCP\Domain\MCP\Tool\Content;
use SJS\Flow\MCP\JsonSchema\ObjectSchema;
use SJS\Flow\MCP\JsonSchema\StringSchema;

class ListMediaTool extends Tool
{
    #[Flow\Inject]
    protected AssetRepository $assetRepository;

    #[Flow\Inject]
    protected TagRepository $tagRepository;

    #[Flow\Inject]
    protected AssetCollectionRepository $assetCollectionRepository;

    #[Flow\Inject]
    protected PersistenceManagerInterface $persistenceManager;

    public function __construct()
    {
        parent::__construct(
            name: 'media_list_media',
            description: 'Lists all media assets; optionally filtered by tag (label) and/or collection (title)',
            inputSchema: new ObjectSchema(properties: [
                'tag' => new StringSchema(description: 'Filter by tag label'),
                'collection' => new StringSchema(description: 'Filter by asset collection title'),
            ]),
            annotations: new Annotations(
                title: 'List Media Assets',
                readOnlyHint: true
            )
        );
    }

    public function run(ActionRequest $_, array $input): Content
    {
        $tag = isset($input['tag']) ? $this->tagRepository->findOneByLabel($input['tag']) : null;
        $collection = isset($input['collection']) ? $this->assetCollectionRepository->findOneByTitle($input['collection']) : null;

        $assets = $this->resolveAssets($tag, $collection);

        $result = [];
        foreach ($assets as $asset) {
            $result[$asset->getIdentifier()] = $this->assetToArray($asset);
        }

        return Content::structured($result)->addText(json_encode($result));
    }

    protected function resolveAssets(?Tag $tag, ?AssetCollection $collection): iterable
    {
        if ($tag !== null && $collection !== null) {
            return $this->assetRepository->findByTag($tag, $collection);
        }
        if ($tag !== null) {
            return $this->assetRepository->findByTag($tag);
        }
        if ($collection !== null) {
            return $this->assetRepository->findAll($collection);
        }
        return $this->assetRepository->findAll();
    }

    protected function assetToArray(Asset $asset): array
    {
        $tags = [];
        foreach ($asset->getTags() as $tag) {
            $tags[$this->persistenceManager->getIdentifierByObject($tag)] = $tag->getLabel();
        }

        $collections = [];
        foreach ($asset->getAssetCollections() as $collection) {
            $collections[$this->persistenceManager->getIdentifierByObject($collection)] = $collection->getTitle();
        }

        return [
            'identifier' => $asset->getIdentifier(),
            'title' => $asset->getTitle(),
            'caption' => $asset->getCaption(),
            'mediaType' => $asset->getMediaType(),
            'filename' => $asset->getResource()->getFilename(),
            'fileSize' => $asset->getResource()->getFileSize(),
            'tags' => $tags,
            'collections' => $collections,
        ];
    }
}
