<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;

#[ApiResource(graphQlOperations: [])]
final class ProductReportPayload
{
    #[ApiProperty(identifier: true)]
    public string $id;

    #[ApiProperty]
    public string $content; 

}
