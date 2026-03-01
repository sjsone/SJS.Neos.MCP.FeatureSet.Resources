# SJS.Neos.MCP.FeatureSet.Resources

MCP FeatureSet package for **Neos.Media**. Provides tools for browsing asset collections, tags, and media assets.

---

## FeatureSets & Tools

### `MediaFeatureSet` — prefix `media`

| Tool | Description |
| --- | --- |
| `media_list_collections` | Lists all asset collections with title, asset count, and assigned tags |
| `media_list_tags` | Lists all tags with label, optional parent label, and asset count |
| `media_list_media` | Lists media assets; optionally filtered by tag label and/or collection title |

---

## `media_list_media` — filter parameters

| Parameter | Type | Description |
| --- | --- | --- |
| `tag` | string (optional) | Filter by tag label |
| `collection` | string (optional) | Filter by asset collection title |

Both filters can be combined (intersection). Omitting both returns all assets.

### Output shape per asset

```json
{
  "<asset-identifier>": {
    "identifier":  "...",
    "title":       "...",
    "caption":     "...",
    "mediaType":   "image/jpeg",
    "filename":    "photo.jpg",
    "fileSize":    204800,
    "tags":        { "<tag-id>": "tag label", ... },
    "collections": { "<collection-id>": "collection title", ... }
  }
}
```

Tags and collections are keyed by their persistence identifier so callers can reference them unambiguously.
