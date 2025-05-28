<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;
use Psr\Log\LoggerInterface;

class ProductListingMessage
{
    public const ACTION_LIST = 'list';
    public const ACTION_UPDATE_LIST = 'update_list';
    public const ACTION_UNLIST = 'unlist';
    public const ACTION_UPDATE_PRICE = 'update_price';
    public const ACTION_UPDATE_STOCK = 'update_stock';
    public const PRIORITY_LOW = -1;
    public const PRIORITY_NORMAL = 0;
    public const PRIORITY_HIGH = 1;
    public const TARGET_LIVE = 'live';
    public const TARGET_TEST = 'test';

    private string $traceId;
    private string $actionType;
    private int $targetMarketplaceId;
    private int $referenceMarketplaceId;

    private string $userName;
    private array $variantIds;
    private array $payload; // update price update stock ?
    private int $priority;
    private ?string $targetAccountKey;
    private \DateTimeImmutable $createdAt;
    private LoggerInterface $logger;

    public function __construct(
        string $actionType,
        int $targetMarketplaceId,
        int $referenceMarketplaceId,
        string $userName,
        array $variantIds,
        array $payload = [],
        int $priority = self::PRIORITY_NORMAL,
        string $targetAccountKey,
        LoggerInterface $logger,
        ?string $traceId = null
    ) {
        $validActions = [
            self::ACTION_LIST,
            self::ACTION_UNLIST,
            self::ACTION_UPDATE_LIST,
            self::ACTION_UPDATE_PRICE,
            self::ACTION_UPDATE_STOCK
        ];
        if (!in_array($actionType, $validActions)) {
            throw new \InvalidArgumentException(sprintf('Geçersiz aksiyon tipi: "%s". İzin verilenler: %s', $actionType, implode(', ', $validActions)));
        }
        $validPriority = [
            self::PRIORITY_NORMAL,
            self::PRIORITY_HIGH,
            self::PRIORITY_LOW
        ];
        if (!in_array($priority, $validPriority)) {
            throw new \InvalidArgumentException(sprintf('Geçersiz oncelik seviyesi: "%s". İzin verilenler: %s', $priority, implode(', ', $validPriority)));
        }
        $validAccountKeys = [
            self::TARGET_LIVE,
            self::TARGET_TEST
        ];
        if (!in_array($targetAccountKey, $validAccountKeys)) {
            throw new \InvalidArgumentException(sprintf('Geçersiz hedef (Live, Test): "%s". İzin verilenler: %s', $targetAccountKey, implode(', ', $validAccountKeys)));
        }

        $this->actionType = $actionType;
        $this->$targetMarketplaceId = $targetMarketplaceId;
        $this->userName = $userName;
        $this->variantIds = $variantIds;
        $this->payload = $payload;
        $this->priority = $priority;
        $this->targetAccountKey = $targetAccountKey;
        $this->createdAt = new \DateTimeImmutable();
        $this->referenceMarketplaceId = $referenceMarketplaceId;
        $this->traceId = $traceId ?? Uuid::v4()->toRfc4122();
        $this->logger = $logger;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function getTargetMarketplaceId(): int
    {
        return $this->$targetMarketplaceId;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getVariantIds(): array
    {
        return $this->variantIds;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getTargetAccountKey(): string
    {
        return $this->targetAccountKey;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

}