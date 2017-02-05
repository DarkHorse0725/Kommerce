<?php
namespace inklabs\kommerce\Service;

use inklabs\kommerce\EntityDTO\UploadFileDTO;
use inklabs\kommerce\Lib\UuidInterface;

interface AttachmentServiceInterface
{
    /**
     * @param UploadFileDTO $uploadFileDTO
     * @param UuidInterface $orderItemId
     * @return void
     */
    public function createAttachmentForOrderItem(UploadFileDTO $uploadFileDTO, UuidInterface $orderItemId);

    /**
     * @param UploadFileDTO $uploadFileDTO
     * @param UuidInterface $userId
     * @param UuidInterface $productId
     * @return void
     */
    public function createAttachmentForUserProduct(
        UploadFileDTO $uploadFileDTO,
        UuidInterface $userId,
        UuidInterface $productId
    );
}
