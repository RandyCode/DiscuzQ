<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


namespace App\Api\Controller\Topic;

use App\Commands\Topic\EditTopic;
use Discuz\Api\Controller\AbstractListController;
use Illuminate\Contracts\Bus\Dispatcher;
use Discuz\Auth\AssertPermissionTrait;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use App\Api\Serializer\TopicSerializer;
use Tobscure\JsonApi\Document;

class BatchUpdateTopicController extends AbstractListController
{
    use AssertPermissionTrait;
    /**
     * {@inheritdoc}
     */
    public $serializer = TopicSerializer::class;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {

        $actor = $request->getAttribute('actor');
        $this->assertAdmin($actor);

        $data = $request->getParsedBody()->get('data', []);
        $ids = explode(',', Arr::get($request->getQueryParams(), 'ids'));
        $idsCollect = collect($ids);

        $result = ['data' => [], 'meta' => []];
        $idsCollect->each(function ($id) use ($data, &$result) {
            try {
                $result['data'][] = $this->bus->dispatch(
                    new EditTopic($id, $data)
                );
            } catch (\Exception $e) {
                $result['meta'][] = ['id' => $id, 'message' => $e->getMessage()];
            }
        });

        $document->setMeta($result['meta']);

        return $result['data'];
    }
}
