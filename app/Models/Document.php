<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Services\TenantFileService;

class Document extends Model
{
    protected $fillable = [
        'file_name',
        'description',
        'price',
        'date',
        'is_paid',
        'type',
        'contragent_type',
        'contragent_id',
        'order_id',
        'document_category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'date' => 'date',
        'is_paid' => 'boolean',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function contragent(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the tenant file service
     */
    protected function getTenantFileService(): TenantFileService
    {
        return app(TenantFileService::class);
    }

    /**
     * Get the full file path for this document
     */
    public function getFilePath(): string
    {
        return $this->getTenantFileService()->getTenantStoragePath() . '/' . $this->file_name;
    }

    /**
     * Get the public URL for this document
     */
    public function getFileUrl(): string
    {
        return $this->getTenantFileService()->getTenantPublicUrl($this->file_name);
    }

    /**
     * Check if the file exists in tenant storage
     */
    public function fileExists(): bool
    {
        return $this->getTenantFileService()->fileExists($this->file_name);
    }

    /**
     * Delete the file from tenant storage
     */
    public function deleteFile(): bool
    {
        if ($this->fileExists()) {
            return $this->getTenantFileService()->deleteFile($this->file_name);
        }
        return false;
    }

    /**
     * Get file contents
     */
    public function getFileContents(): string
    {
        return $this->getTenantFileService()->getFileContents($this->file_name);
    }
}
