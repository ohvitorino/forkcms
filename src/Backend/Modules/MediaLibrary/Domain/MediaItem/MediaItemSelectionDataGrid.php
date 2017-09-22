<?php

namespace Backend\Modules\MediaLibrary\Domain\MediaItem;

use Backend\Core\Engine\DataGridDatabase;
use Backend\Core\Engine\DataGridFunctions as BackendDataGridFunctions;
use Backend\Core\Engine\Model;
use Backend\Core\Language\Language;

/**
 * @TODO replace with a doctrine implementation of the data grid
 */
class MediaItemSelectionDataGrid extends MediaItemDataGrid
{
    protected $query = 'SELECT i.id, i.storageType, i.type, i.url, i.title, i.shardingFolderName,
                COUNT(gi.mediaItemId) AS num_connected, i.mime, UNIX_TIMESTAMP(i.createdOn) AS createdOn,
                i.url AS directUrl
             FROM MediaItem AS i
             LEFT OUTER JOIN MediaGroupMediaItem AS gi ON gi.mediaItemId = i.id
             WHERE i.type = ?';

    public function __construct(Type $type, int $folderId = null)
    {
        parent::__construct($type, $folderId, false, false);

        $this->setExtras($type);
    }

    protected function getColumnsThatNeedToBeHidden(Type $type): array
    {
        if ($type->isImage()) {
            return ['storageType', 'shardingFolderName', 'type', 'mime', 'directUrl'];
        }

        if ($type->isMovie()) {
            return ['shardingFolderName', 'type', 'mime', 'directUrl'];
        }

        return ['storageType', 'shardingFolderName', 'type', 'mime', 'url', 'directUrl'];
    }

    protected function setExtras(Type $type, int $folderId = null): void
    {
        $this->addDataAttributes();
        $this->setHeaderLabels($this->getColumnHeaderLabels($type));
        $this->setColumnsHidden($this->getColumnsThatNeedToBeHidden($type));
        $this->setSortingColumns(
            [
                'createdOn',
                'url',
                'title',
                'num_connected',
                'mime',
            ],
            'title'
        );
        $this->setSortParameter('asc');

        // Add a select button
        $this->addColumn(
            'use_revision',
            Language::lbl('Action'),
            Language::lbl('Select'),
            '#',
            Language::lbl('Select'),
            null,
            1
        );

        // If we have an image, show the image
        if ($type->isImage()) {
            // Add image url
            $this->setColumnFunction(
                [
                    new BackendDataGridFunctions(),
                    'showImage',
                ],
                [
                    Model::get('media_library.storage.local')->getWebDir() . '/[shardingFolderName]',
                    '[url]',
                    '[url]',
                    null,
                    null,
                    null,
                    'media_library_backend_thumbnail',
                ],
                'url',
                true
            );
        }

        $this->setColumnFunction(
            [
                MediaItemSelectionDataGrid::class,
                'generateDirectUrl',
            ],
            [
                '[id]',
                $type,
                '[storageType]',
            ],
            'directUrl',
            true
        );

        // set column functions
        $this->setColumnFunction(
            [new BackendDataGridFunctions(), 'getLongDate'],
            ['[createdOn]'],
            'createdOn',
            true
        );
    }

    private function addDataAttributes(): void
    {
        // our JS needs to know an id, so we can highlight it
        $attributes = [
            'id' => 'row-[id]',
            'data-direct-url' => '[directUrl]',
        ];
        $this->setRowAttributes($attributes);
    }

    protected function generateDirectUrl(string $id, string $type, string $storageType): string
    {
        switch ($type) {
            case Type::MOVIE:
                if ($storageType === StorageType::YOUTUBE) {
                    return Model::get('media_library.storage.youtube')->getAbsoluteWebPath(
                        Model::get('media_library.repository.item')->find($id)
                    );
                }

                if ($storageType === StorageType::VIMEO) {
                    return Model::get('media_library.storage.vimeo')->getAbsoluteWebPath(
                        Model::get('media_library.repository.item')->find($id)
                    );
                }

                return Model::get('media_library.storage.local')->getAbsoluteWebPath(
                    Model::get('media_library.repository.item')->find($id)
                );
            default:
                return Model::get('media_library.storage.local')->getAbsoluteWebPath(
                    Model::get('media_library.repository.item')->find($id)
                );
        }
    }

    public static function getDataGrid(Type $type, int $folderId = null): DataGridDatabase
    {
        return new self($type, $folderId);
    }
}
