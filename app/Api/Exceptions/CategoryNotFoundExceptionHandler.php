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

namespace App\Api\Exceptions;

use App\Exceptions\CategoryNotFoundException;
use Exception;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class CategoryNotFoundExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function manages(Exception $e)
    {
        return $e instanceof CategoryNotFoundException;
    }

    /**
     * @inheritDoc
     */
    public function handle(Exception $e)
    {
        $status = 500;
        $error = [
            'status' => (string) $status,
            'code' => 'category_not_found',
        ];

        return new ResponseBag($status, [$error]);
    }
}
